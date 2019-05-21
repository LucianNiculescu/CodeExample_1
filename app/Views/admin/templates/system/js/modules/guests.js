$(document).ready(function () {
	if ($("body.manage.guests.transactions.edit").length) {
		var expiresDate = $('body.manage.guests.transactions.edit .datepicker');
		var expiresInput = $('body.manage.guests.transactions.edit #expires');
		expiresDate.datepicker({
			numberOfMonths: 1, //The number of months to show at once
			//minDate: 0 ,// Don't allow past dates
			onSelect: function (dateText) {
				expiresInput.val(dateText);
				$('this').addClass('dp-highlight');
			}
		});
		expiresDate.datepicker('setDate', new Date($('#expires').val()));
	}
});


/**
 * Function to block and unblock devices
 */
$('body.guests .action_block').click(function(e){
	e.preventDefault();

	var thisLink 		= $(this);
	var mac				= thisLink.data("mac");
	var currentStatus	= thisLink.data("status");
	// Default sweetalert parameters
	var sweetAlert = {
		title				: trans["are-you-sure"],
		type				: "question",
		showCancelButton	: true,
		confirmButtonColor	: "#00adef" ,
		confirmButtonText	: trans["yes-please"],
		cancelButtonText	: trans["cancel"]
	};

	// Adding extra sweetalert parameters as needed
	if (currentStatus == "blocked") {
		sweetAlert['text'] = trans["to-unblock"] +" '" + mac +"'?";
	} else {
		sweetAlert['text'] = trans["to-block"] +" '" + mac +"'?";
		sweetAlert['input'] = "text";
		sweetAlert['inputPlaceholder'] = trans["block-reason-placeholder"];
		sweetAlert['inputValidator'] = function (reason) {
			return new Promise(function (resolve, reject) {
				if (reason.length > 0) {
					if (reason.length < 128) {
						resolve()
					} else {
						reject(trans["reason-is-long"])
					}
				} else {
					reject(trans["reason-is-required"])
				}

			})
		};
	}

	swal(sweetAlert).then(function(reason){
		//Ajax URL
		var url 			= thisLink.attr("href");
		//child is the icon with the toggle font awesome
		var child 			= thisLink.find(">:first-child");
		var newStatus 		= "";
		var currentIcon		= "";
		var newIcon 		= "";
		var currentTextColor= "";
		var newTextColor 	= "";
		var newTitle		= "";

		// Toggle the status
		if (currentStatus == "blocked") {

			currentIcon		= "fa-toggle-off";
			currentTextColor= "text-danger";
			// New status details for icon, color and text
			newStatus 		= "unblocked";
			newIcon 		= "fa-toggle-on";
			newTextColor 	= "text-success";
			newTitle		= trans["block-device"] +" '"+mac+"'";
		} else {
			currentIcon		= "fa-toggle-on";
			currentTextColor= "text-success";
			// New status details for icon, color and text
			newStatus 		= "blocked";
			newIcon 		= "fa-toggle-off";
			newTextColor 	= "text-danger";
			newTitle		= trans["unblock-device"] +" '"+mac+"'";
		}

		// Showing an over-lay div to avoid clicking on the screen while calling Ajax
		$(".loading_page").fadeIn("fast");

		// Calling Ajax to toggle the status
		$.ajax({
			url : url,
			type: "post",
			data: {
				'mac' 		: mac ,
				'status' 	: currentStatus,
				'new-status' 	: newStatus,
				'site_or_estate': 'site',
				'reason'	: reason,
				'_method' 	: 'POST',
				'_token'	: $('input[name=_token]').val()
			}})
			.then (function(data){
				//console.log("DATA: " + data);
				if(data ==1) {

					thisLink.data("status",newStatus);
					thisLink.attr("title", newTitle);
					child.removeClass(currentIcon);
					child.removeClass(currentTextColor);
					child.addClass(newIcon);
					child.addClass(newTextColor);

					// Hiding the overlay div
					$(".loading_page").fadeOut("fast");

					if(newStatus == 'blocked') {
						newStatus = trans["blocked"];
						thisLink.parent().prev('td').text(reason);
					} else {
						newStatus = trans["unblocked"];
						thisLink.parent().prev('td').text('');
					}
					swal({
						title:	trans["done"],
						text: 	"'" + mac + "' = " + newStatus + " !",
						type:	"success"
					});
				} else {
					alertError(data);
				}
			}, function(data){
				// Build a string of server validation errors to pass to sweet alert
				var resp = JSON.parse(data.responseText);
				var errorMessage = '';
				$.each(resp, function(field, validationErrors) {
					// Add the field name
					errorMessage += field.charAt(0).toUpperCase() + field.slice(1).toLowerCase() + ' - ';
					// Add validation errors for the field
					errorMessage += validationErrors.join(', ') + '.';
				});
			alertError(errorMessage);
		});
	}, function (dismiss) {

	});
});

// To Enable the assign packages button
$('body.manage.guests #packages').change(function(){
	getGatewaysByPackage(this.value, $('body.manage.guests #assignPackage'));
});

// On Submit of the Assign Package form, show the alert if there are more than 1 Gateways set on the site
$('body.manage.guests #assignPackageForm').submit(function(e){
	e.preventDefault();
	var $this = $(this);
	var gatewayIdInput = $('body.manage.guests input[name="gateway_id"]');

	// gatewaysList injected into body of form.blade.php
	switch(Object.keys(gatewaysList).length)
	{
		// If we have only one Gateway, insert that ID into the form and submit
		case 1:
			gatewayIdInput.val( Object.keys(gatewaysList)[0] );
			$('.loading_page').fadeIn();
			$this.off('submit').submit();
			break;
		// Show an alert for the user to select a Gateway which the transactions will be updated with
		default:
			swal({
				title: translateByClass('packages-select-gateway'),
				text: translateByClass('packages-select-gateway-info'),
				type: 'warning',
				input: 'select',
				inputOptions: gatewaysList,
				showCancelButton: true,
				confirmButtonText: translateByClass('submit')
			}).then(function(gatewayId) {
				$('body.manage.guests input[name="gateway_id"]').val(gatewayId);
				$('.loading_page').fadeIn();
				$this.off('submit').submit();
			}).catch(swal.noop);
			break;
	}

});

// Sending reset password
$('body.guests #resetPassword').click(function(e){
	e.preventDefault();

	var thisLink 		= $(this);
	var user			= thisLink.data("user");
	var id				= thisLink.data("id");

	// Sweet Alert call to confirm
	swal({
		title				: trans["are-you-sure"],
		text				: trans["to-send-reset-email"],
		type				: "warning",
		showCancelButton	: true,
		confirmButtonColor	: "#00adef" ,
		confirmButtonText	: trans["yes-please"],
		cancelButtonText	: trans["cancel"]
	}).then(function(){
		// Call back function if user press yes please
		var url 			= thisLink.data("route");
		// TODO: Setting some variables
		// Showing an over-lay div to avoid clicking on the screen while calling Ajax
		$(".loading_page").fadeIn("fast");

		// Calling Ajax to toggle the status
		$.ajax({
			url : url,
			type: "post",
			data: {
				'user' 		: user ,
				'_method' 	: 'POST',
				'_token'	: $('input[name=_token]').val()
			}})
			.success (function(data){
				//console.log("DATA: " + data);
				if(data ==1) {
					$(".loading_page").fadeOut("fast");
					swal({
						title:	trans["done"],
						text: 	trans['reset-email-sent'],
						type:	"success"
					});
				} else {
					alertError(data);
				}
			}).fail (function(data){
			alertError(data);
		});
	}, function (dismiss) {

	});
});

// ----------------------------------------- FUNCTIONS ------------------------------------------- //

/**
 * Ajax call to get the gateways and set the Loading to the button
 * @param packageId
 * @param element
 */
function getGatewaysByPackage(packageId, element) {

	if (element !== null)
		toggleLoadingButton(element, true);
	// Get the gateways data
	$.getJSON('/json/manage/guests/'+packageId+'/get_gateways_by_package/' , function ( data ) {
		gatewaysList 	=  data;
	}).done(function (){
		if (element !== null)
			toggleLoadingButton(element, false)
	});
}

/**
 * Get a translation embedded in the DOM
 * @param key
 */
function translateByClass(key) {
	return $('.embedded-translations .' + key).html();
}
