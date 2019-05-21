$(document).ready(function() {
	/*Draw the datatable*/
	if($('body.networking.walled-garden').length)
		walledGardenDatatable(showWalledGardenActions);

	$('body.networking.walled-garden .widget-sub-menu .widget-sub-item .mac').each( function () {

		if($(this).data('item-mac') == gatewayMac)
			$(this).parent().addClass('active');
		else
			$(this).parent().removeClass('active');
	});

	$('body.networking.walled-garden .widget-sub-item').tooltip();
});

/**
 * Getting Substable data via AJAX
 * @param mac
 */
function getWalledGardenDataViaAjax(mac)
{
	showLoadingDiv();
	// Calling Ajax to toggle the status
	$.ajax({
		url : "/json/networking/walled-garden/" + mac,
		type: "get",
		data: {
			'_token'	: $('input[name=_token]').val()
		}}).success (function(data){

		data = $.parseJSON(data);
		table.html('');
		table.fnDestroy();
		table.html(data);
		walledGardenDatatable(showWalledGardenActions);
		hideLoadingDiv();
	}).fail(function(data){

		table.html('');
		table.fnDestroy();
		alertError(errorMsg);
		hideLoadingDiv();
	});
}

function walledGardenDatatable(showActions)
{
	table.dataTable({
		"order"     : [0, "asc"],
		"aoColumns" : [
			null,
			null,
			null,
			null,
			{"bVisible" : showActions, "bSortable" : false }	// Actions
		]
	});
}

$('body.networking.walled-garden').on("click", ".widget-sub-item .mac.api", function (e) {
	e.preventDefault();

	resetMenuItems("body.walled-garden .widget-sub-menu", $(this).parent().index() + 1);
	$(this).closest('.gateway-menu').find('.gateway-name').html(($(this).html()));

	if($(this).data('item-mac') != '') {
		getWalledGardenDataViaAjax($(this).data('item-mac'));
	}
});