

// Start after the DOM is in place
$(document).ready(function() {

	// If we are on the vouchers page
	if ( $("body.manage.vouchers.create, body.manage.vouchers.edit").length){

		//Datepicker selector
		var voucherDatePicker = $("body.manage.vouchers .datepicker");

		// On change of the package list, set the package info
		var packageInfo = $('body.manage.vouchers #package');
		var guestInfo 	= $('body.manage.vouchers #code');

		packageInfo.change( function () {
			var packageId = this.value;
			setPackageInfo(packageId);
			getGatewaysByPackage(packageId);
		});

		// Set the package info on document ready
		setPackageInfo( packageInfo.val() );

		// Set the Guest info on document ready (voucher codes injected on guest-info.blade.php)
		if(typeof voucherCodes !== 'undefined' && voucherCodes !== '')
			setGuestInfo(voucherCodes);

		// Functionality of the datepicker to highlight the selected period
		voucherDatePicker.datepicker({
			firstDay: 1, // First day Monday (0 for Sunday)
			numberOfMonths: 1,
			minDate: 0, // Don't allow past dates
			//changeYear: 1,
			beforeShowDay: function(date) {
				var periodFromInput 	= voucherDatePicker.parent().find(".period-from");
				var periodToInput 		= voucherDatePicker.parent().find(".period-to");

				var periodFromDate 	= $.datepicker.parseDate($.datepicker._defaults.dateFormat, periodFromInput.val());
				var periodToDate 	= $.datepicker.parseDate($.datepicker._defaults.dateFormat, periodToInput.val());
				return [true, periodFromDate && ((date.getTime() == periodFromDate.getTime()) || (periodToDate && date >= periodFromDate && date <= periodToDate)) ? "dp-highlight" : ""];
			},
			onSelect: function(dateText, inst) {
				var periodFromInput 	= voucherDatePicker.parent().find(".period-from");
				var periodToInput 		= voucherDatePicker.parent().find(".period-to");

				var periodFromDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, 	periodFromInput.val());
				var periodToDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, 	periodToInput.val());
				var selectedDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, 	dateText);

				if (!periodFromDate || periodToDate) {
					periodFromInput.val(dateText);
					periodToInput.val("");
					$(this).datepicker();
				} else if( selectedDate < periodFromDate ) {
					periodToInput.val( periodFromInput.val() );
					periodFromInput.val( dateText );
					$(this).datepicker();
				} else {
					periodToInput.val(dateText);
					$(this).datepicker();
				}
				updateVoucherDatePickerText(voucherDatePicker);
			}
		});
		// On Click of the cancel btn
		$('.periods .datepicker-cancel').click( function () {

			// Reset the calendar
			voucherDatePicker
				.datepicker("setDate", new Date()) // Reset back to today's date
				.find('td').removeClass('dp-highlight'); // Remove the highlights

			// Reset the form
			voucherDatePicker.parent().find(".period-from, .period-to").val('');

			// Reset text
			resetVoucherDatePickerText(voucherDatePicker);
		});
	}


});

$('body.manage.vouchers form').submit(function(e){

	var $this = $(this);
	var updateTransactionsInput = $('body.manage.vouchers input[name="update_transactions"]');
	var gatewayIdInput = $('body.manage.vouchers input[name="gateway_id"]');

	/**
	 * Get a translation embedded in the DOM
	 * @param key
	 */
	var trans = function (key) {
		return $('.embedded-translations .' + key).html();
	};

	// If we have transactions (available in an element on the blade),
	// display an alert to the user
	var transactionsCount = $('body.manage.vouchers .transactions-count').html();
	if (transactionsCount > 0) {
		e.preventDefault();

		swal({
			title:  trans('update-transactions-title'),
			text: trans('update-transactions-text'),
			input: 'select',
			type: "warning",
			inputOptions: {
				'no': trans('update-package-not-transactions'),
				'yes': trans('update-package-with-transactions')
			},
			showCancelButton: true,
			confirmButtonText: trans('submit')
		}).then(function(selection) {
			// Insert selection into form
			updateTransactionsInput.val(selection);

			switch(selection) {
				// If the user wants to update transactions, check whether we have Gateways
				// for them to select
				case 'yes':
					// gatewaysList injected into body of form.blade.php
					switch(Object.keys(gatewaysList).length)
					{
						// If we have no Gateways, submit the form as if we're not updating
						case 0:
							updateTransactionsInput.val('no');
							$this.off('submit').submit();
							break;
						// If we have only one Gateway, insert that ID into the form and submit
						case 1:
							gatewayIdInput.val( Object.keys(gatewaysList)[0] );
							$this.off('submit').submit();
							break;
						// Show an alert for the user to select a Gateway which the transactions will be updated with
						default:
							swal({
								title:  trans('packages-select-gateway'),
								text: trans('packages-select-gateway-info'),
								type: 'warning',
								input: 'select',
								inputOptions: gatewaysList,
								showCancelButton: true,
								confirmButtonText: trans('submit')
							}).then(function(gatewayId) {
								$('body.manage.vouchers input[name="gateway_id"]').val(gatewayId);
								$this.off('submit').submit();
							}).catch(swal.noop);
							break;
					}
					break;
				default:
					// Submit
					$this.off('submit').submit();
					break;
			}
		}).catch(swal.noop);
	}
});

/************************************* FUNCTIONS ******************************************/

/**
 * Reset the start and stop text
 */
function resetVoucherDatePickerText(voucherDatePicker) {
	var startEl = voucherDatePicker.parents('.periods').find(".start-date .value");
	var stopEl = voucherDatePicker.parents('.periods').find(".stop-date .value");

	startEl.text( startEl.data('default') );
	stopEl.text( stopEl.data('default') );
}

/**
 * Update the start and stop text
 */
function updateVoucherDatePickerText(voucherDatePicker) {
	var startEl = voucherDatePicker.parents('.periods').find(".start-date .value");
	var stopEl = voucherDatePicker.parents('.periods').find(".stop-date .value");

	var startDate = voucherDatePicker.parent().find(".period-from").val();
	startDate = formatDate(startDate);
	var stopDate = voucherDatePicker.parent().find(".period-to").val();
	stopDate = formatDate(stopDate);

	startEl.text( startDate );
	stopEl.text( stopDate );
}


/**
 * Expects a date like '2017-02-20' and returns a formatted date
 * @param date string  	MySql formatted date (no time)
 * @return date string 	Human readable date (no time)
 * TODO: Translate
 */
function formatDate(date)
{
	date = date.split(/[- :]/);
	date = new Date(Date.UTC(date[0], date[1]-1, date[2]));
	return date.toDateString();
}


/**
 * Set the content of the package
 * @param packageId
 */
function setPackageInfo(packageId)
{
	// Set loading
	$('body.manage.vouchers .package-info .loading').show();

	// Get the package data
	$.getJSON('/json/manage/vouchers/' + packageId + '/get_human_readable_by_package/attributes', function ( data ) {

		renderPackageInfo(data);

		// Fade the loading
		$('body.manage.vouchers .package-info .loading').fadeOut('fast');

	});
}

/**
 * Set the content of the Guest
 * @param codes
 */
function setGuestInfo(codes) {
	// Set loading
	$('body.manage.vouchers .guest-info .loading').show();

	// Get the package data
	$.getJSON('/json/manage/vouchers/' +SITE_ID+ '/get_guests_by_codes/'+ codes +'/voucherAttributes', function ( data ) {

		renderGuestInfo(data);

		// Fade the loading
		$('body.manage.vouchers .guest-info .loading').fadeOut('fast');

		/*Draw the datatable*/
		var dataTable=$('#guestUsers').dataTable({
			"order"     : [1, "asc"],
			"aoColumns"	: [
				{ className: "text-uppercase" },
				null,
				null,
				null,
				null,
				{"bVisible" : true, "bSortable" : false }
			]
		});

		// Hide the voucher code if it is the voucher edit page
		if (singleVoucher) {
			dataTable.fnSetColumnVis(1,false);
		}
	});
}

/**
 * Create the HTML and add it to the correct element in the DOM
 * @param data
 */
function renderPackageInfo(data) {

	// Set the html
	var html = "<h4>" + data['name'] + " <small>" + data['description'] + "</small></h4>";

	// Set the Package Info
	$('body.manage.vouchers .package-info .content').html( html );

	var attributes = data['attributes'];
	var tbody = '';
	for (var i = 0, len = attributes.length; i < len; i++) {
		tbody +=
			"<tr>" +
			"<td>" + attributes[i]['name'] + "</td>" +
			"<td>" + attributes[i]['type'] + "</td>" +
			"<td>" + attributes[i]['value'] + "</td>" +
			"</tr>";
	}

	// Set the table body
	$('body.manage.vouchers .package-info table tbody').html( tbody );
}

/**
 * Create the HTML and add it to the correct element in the DOM
 * @param data
 */
function renderGuestInfo(data) {

	// Set the html
	var html = "";

	// Set the Package Info
	$('body.manage.vouchers .guest-info .content').html( html );

	var attributes = data;
	var tbody = '';
	for (var key in attributes) {
		tbody +=
			"<tr>" +
			"<td>" + (attributes[key]['mac']?attributes[key]['mac'] :'') + "</td>" +
			"<td>" + (attributes[key]['code']?attributes[key]['code']:'') + "</td>" +
			"<td>" + (attributes[key]['user']['user']?attributes[key]['user']['user']:'') + "</td>" +
			"<td>" + (attributes[key]['user']['name']?attributes[key]['user']['name']:'') + "</td>" +
			"<td>" + (attributes[key]['user']['lastUpdated']?attributes[key]['user']['lastUpdated']:'') + "</td>" +
			"<td>" + (attributes[key]['user']['id']?(
			"<a title='View Guest' href='/manage/guests/"+attributes[key]['user']['id']+"/edit'><i class='fa fa-eye action text-info'></i></a>"):'') +
			"</td>" +
			"</tr>";
	}

	// Set the table body
	$('body.manage.vouchers .guest-info table tbody').html( tbody );
}


/**
 *
 * @param packageId
 */
function getGatewaysByPackage(packageId) {
	// Get the gateways data
	$.getJSON('/json/manage/vouchers/'+packageId+'/get_gateways_by_package/' , function ( data ) {
		gatewaysList 	=  data;
	});
}