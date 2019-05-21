$( document ).ready(function() {
	var siteList        = $("body.gateways #site");
	var locationInput 	= $("body.gateways #location");

	siteList.change(function(){
		locationInput.val($(this).find(':selected').data('location')).trigger('change');
	});

	var changeOfAuthSwitch = $('body.networking.gateways .change_of_auth');
	var changeOfAuthPort = $('body.networking.gateways .coa-port');
	if(changeOfAuthSwitch.is(":checked")) {
		//Hide/show the dynamic gateways
		changeOfAuthPort.removeClass('hidden').addClass('show');
		//This fixes a bug with switchery, when it is readonly, it disables the switch and gets the value of a hidden input
		$('body.networking.gateways input[name="change_of_auth"]').val("true");
	}

	// Show/Hide the recurrency container and enable/disable duration input (because it is triggered by frequency selector)
	changeOfAuthSwitch.on('change', function (event) {
		//If input is not showing, show
		if (event.target.checked ==  true) {
			changeOfAuthPort.removeClass('hidden').addClass('show');
		} else {
			changeOfAuthPort.removeClass('show').addClass('hidden');
		}
	});
});


/**
 * Datatable view function
 */
body.on("click",".action_view, #networking-gateways-table td a:not('.action_edit, .action_delete, .action_status'), #networking-hardware-table td a:not('.action_edit, .action_delete, .action_status')" , function(e)
{
	e.preventDefault();

	var thisLink 		= $(this);
	var url 			= thisLink.attr("href");
	var id 				= thisLink.data("id");
	var name 			= thisLink.data("name");

	thisLink.closest('table').find('tr').removeClass('active');
	thisLink.closest('tr').addClass('active');

	// Don't do anything if the table is in a widget
	if($(this).closest('.grid-item').length > 0)
		return false;

	// Create a spinner in the view details section
	$('.view-section .contents').html('<i class="fa fa-spinner fa-spin fa-2x"  style="position:absolute; left:200px; top:50px"></i>');

	// Call an ajax to get the view details from the server
	$.ajax({
		url : url,
		type: "get",
		data: {
			'id' 		: id ,
			'_method' 	: 'GET',
			'_token'	: $('input[name=_token]').val()
		}})
		.success (function(data){
			// Showing an over-lay div to avoid clicking on the screen while calling Ajax

			$('.view-section .contents').html(data);
			$('#permissionName').html(name);
			$('#showTable').dataTable({
				"aoColumns"	: [
					null,
					null
				]
			});
		}).fail (function(data){
		alertError(data);
	});

});