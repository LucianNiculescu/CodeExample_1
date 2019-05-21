/**
 * Show the datatable of the specific gateway i.e. substable
 * @param showActions
 */
function onlineNowDatatable(showActions)
{
	$('#subs-table').dataTable({
		"order"     : [4, "desc"],
		"aoColumns" : [
			null,						//	Status
			null,						//	Guest
			{ "bSortable" : false },	//	Device
			{ "bSortable" : false },	//	Session Details
			{"iDataSort": 6, "aTargets": [6] },						//	Time
			null,						//	Vlan
			{"bVisible" : false, "aTargets": [6] },						//	Time1
			{"bVisible" : (showActions==true), "bSortable" : false }	// Actions
		],
		initComplete: function () {
			this.api().columns().every( function (columnNo) {
				if(columnNo == 0)
				{
					var column = this;
					var select = $('<select class="dropdown-filter chosen"><option value=""></option></select>')
						.appendTo( $(column.header()) )
						.on( 'change', function () {
							var val = $.fn.dataTable.util.escapeRegex(
								$(this).val()
							);
							column
								.search( val ? '^'+val+'$' : '', true, false )
								.draw();
						} );


					column.data().unique().sort().each( function ( d, j ) {
						var stauts = ($(d).text());
						select.append( '<option value="'+stauts+'">'+stauts+'</option>' )
					} );
				}
			} );
		}
	});
}

/**
 * Getting Substable data via AJAX
 * @param mac
 */
function getOnlineDataViaAjax(mac)
{
	showLoadingDiv();
	// Calling Ajax to toggle the status
	$.ajax({
		url : "/online-now/" + mac,
		type: "get",
		data: {
			'_token'	: $('input[name=_token]').val()
		}}
	).success (function(data){
		data = $.parseJSON(data);

		$('.online-now-datatable-container').html(data);
		onlineNowDatatable(showSubsTableActions);

		hideLoadingDiv();
	}).fail(function(data){
		alertError(data);
	});
}

var body = $('body');

body.on("click", ".online-now .widget-sub-item .mac", function (e) {
	e.preventDefault();

	resetMenuItems("body.online-now .widget-sub-menu", $(this).parent().index() + 1);
	$(this).closest('.gateway-menu').find('.gateway-name').html(($(this).html()));

	if($(this).data('item-mac') == '')
	{
		showLoadingDiv();
		window.location.replace("/online-now/");
	}
	else
	{
		getOnlineDataViaAjax($(this).data('item-mac'));
	}
});


/**
 * Datatable delete function
 */
body.on("click",".action_signout" , function(e)
{
	e.preventDefault();
	var thisLink 		= $(this);
	var name			= thisLink.data("guest-mac") !== undefined ? thisLink.data("guest-mac") : thisLink.data("name");

	// Sweet Alert call to confirm
	swal({
		title				: trans["are-you-sure"],
		text				: trans["to-signout"] + " '" + name + "'?",
		type				: "warning",
		showCancelButton	: true,
		confirmButtonColor	: "#00adef" ,
		confirmButtonText	: trans["yes-please"],
		cancelButtonText	: trans["cancel"]
	}).then(function() {
		// Call back function if user press yes please

		// Ajax URL
		var url 			= thisLink.attr("href");
		var session 		= thisLink.data("session");
		var id 				= thisLink.data("id");

		var gatewayMac 		= thisLink.data("gateway-mac");
		// Showing an overlary div to avoid clicking while calling the function
		$(".loading_page").fadeIn("fast");

		// Calling Ajax to toggle the status
		$.ajax({
			url : url,
			type: "post",
			data: {
				'id' 		: id ,
				'session'	: session ,
				'gatewayMac': gatewayMac ,
				'_token'	: $('input[name=_token]').val()
			}}).success (function(data){
			if (data == 1)
			{
				// Hiding the affected row
				thisLink.parent().parent().hide();

				// Hiding the loading div
				$(".loading_page").fadeOut("fast");
				swal({
					title:	trans["done"],
					text: "'" + name + "' " + trans["is-signed-out"],
					type: "success"});
			}
			else
			{
				alertError(data);
			}
		}).fail(function(data){
			alertError(data);
		});
	}, function (dismiss) {

	});
});


/**
 * Datatable delete function
 */
body.on("click",".action_signin" , function(e)
{
	e.preventDefault();
	var thisLink 		= $(this);
	var name			= thisLink.data("guest-mac") !== undefined ? thisLink.data("guest-mac") : thisLink.data("name");

	// Sweet Alert call to confirm
	swal({
		title				: trans["pick-a-package"],
		text				: trans["to-signin"],
		type				: "info",
		input				: 'select',
		inputOptions		: JSON.parse(whitelistPackages),
		showCancelButton	: true,
		confirmButtonColor	: "#00adef" ,
		confirmButtonText	: trans["ok"],
		cancelButtonText	: trans["cancel"]
	}).then(function(package) {
		// Call back function if user press yes please
		var url 			= thisLink.attr("href");
		var guestMac 		= thisLink.data("guest-mac");
		// Showing an overlary div to avoid clicking while calling the function
		$(".loading_page").fadeIn("fast");

		// Calling Ajax to toggle the status
		$.ajax({
			url : url,
			type: "post",
			data: {
				'package'	: package ,
				'guestMac'	: guestMac ,
				'_token'	: $('input[name=_token]').val()
			}}).success (function(data){

			if (data == 1)
			{
				// Hiding the loading div
				$(".loading_page").fadeOut("fast");
				swal({
					title:	trans["done"],
					text: "'" + name + "' " + trans["is-signed-in"],
					type: "success"});
			}
			else
			{
				$(".loading_page").fadeOut("fast");
				swal({
					title	: trans["info"],
					text	: data,
					type	: "info"
				})
			}
		}).fail(function(data){
			alertError(data);
		});
	}, function (dismiss) {

	});
});


$(document).ready(function() {

	$('body.online-now .widget-sub-menu .widget-sub-item .mac').each( function () {

		if($(this).data('item-mac') == gatewayMac)
			$(this).parent().addClass('active');
		else
			$(this).parent().removeClass('active');
	});
});
