/**
 * Datatable upgrade to V3
 */
body.on("click",".action_upgrade" , function(e) {
	e.preventDefault();

	var thisLink 		= $(this);
	var name			= thisLink.data("name");
	var url 			= thisLink.attr("href");
	var id 				= thisLink.data("id");
	var version			= thisLink.data("version");
	var route			= thisLink.data("route");

	// Sweet Alert call to confirm
	swal({
		title				: trans["are-you-sure"],
		text				: trans["to-upgrade"] +" '" + name +"'?",
		type				: "warning",
		showCancelButton	: true,
		confirmButtonColor	: "#00adef" ,
		confirmButtonText	: trans["yes-please"],
		cancelButtonText	: trans["cancel"]
	}).then(function(){
		// Call back function if user press yes please

		// Showing an over-lay div to avoid clicking on the screen while calling Ajax
		$(".loading_page").fadeIn("fast");

		// Calling Ajax to toggle the status
		$.ajax({
			url : url,
			type: "post",
			data: {
				'id' 		: id ,
				version 	: version,
				route 		: route,
				'_method' 	: 'PUT',
				'_token'	: $('input[name=_token]').val()
			}})
			.success (function(data){

				if(Array.isArray(data) && data[0] == 1)
				{
					// Remove all disabled spans from the row
					var cells = thisLink.closest('tr').children().find('span.disabled');

					// Converting all cells to clickable links to dashboard
					cells.each(function(){
						var cell 		= $(this);
						var cellValue 	= cell.html();
						var cellParent  = cell.parent();
						cell.remove();
						cellParent.append('<a href="/dashboard/' + id + '">' + cellValue + '</a>');
					});

					// Clearing the action cell
					actionCell = thisLink.parent();
					thisLink.remove();

					// Putting real actions into the cell
					actionCell.append(data[1]);

					$(".loading_page").fadeOut("fast");

					swal({
						title:	trans["done"],
						text: 	"'" + name + "' = " + trans["upgraded"] + " !",
						type:	"success"
					});
				}
				else
				{
					alertError(data);
				}
			}).fail (function(data){
			alertError(data);
		});
	}, function (dismiss) {

	});
});

$('body.sites #crudForm').validate({
	ignore: [], //need this because our select boxes have "display: none" and it was ignoring the field
	rules : {
		gateway_id: {
			customAdjets : true
		},
		package_id: {
			customAdjets : true
		}
	},
	errorElement: "div",
	wrapper: "div",
	errorPlacement: function(error, element) {
		offset = element.offset();
		// Because we have different select boxes, we need to add the error message after the box
		if (element.attr("name") == "gateway_id" )
			error.insertAfter("#gateway_id_chosen");
		else if  (element.attr("name") == "package_id" )
			error.insertAfter("#package_id_chosen");
		else
			error.insertAfter(element);
		error.css('color','red');
	}
});

//Checks if the PRTG fields have been completed so we can make the ajax call
function checkPrtgFields() {
	var server 		= $('body.sites #prtg_api_server').val();
	var username 	= $("body.sites #prtg_api_username").val();
	var passhash 	= $("body.sites #prtg_api_passhash").val();
	if(typeof server !== 'undefined' && typeof username !== 'undefined' && typeof passhash !== 'undefined')
		if(server !== '' && username !== '' && passhash !== '')
			return false;

	return true;
}

//If the PRTG fields have been completed, calls the api that retrieves the sensors and adds them into prtg_sensors table
function setUpPrtg() {

	var callAjax 	= checkPrtgFields();
	var server 		= $('body.sites #prtg_api_server').val();
	var username 	= $("body.sites #prtg_api_username").val();
	var passhash 	= $("body.sites #prtg_api_passhash").val();
	var prtgButton  = $('body.sites #getPrtgIds');

	if(!callAjax) {
		$.ajax({
			url : '/json/manage/sites/prtg',
			type: "POST",
			beforeSend: function() {
				//Set the button to loading
				toggleLoadingButton(prtgButton, true);
			},
			complete: function() {
				//Remove loading from the button
				toggleLoadingButton(prtgButton, false);
			},
			data: {
				'action'	: 'create',
				'url'		: server,
				'content' 	: 'sensor',
				'columns'	: 'objid,sensor,device,group,status,parentid',
				'username'	: username,
				'passhash'	: passhash,
				'siteId'	: SITE_ID
			}})
			.success (function(data){
				//Refresh the input with the new sensors
				$('body.sites #prtg_sensors').val(data);
				swal({
					title	: trans["done"],
					text	: trans["prtg-sensors-succes"],
					type	: "success"});
			})
			.fail (function(data){
				alertError(trans["prtg-sensors-fail"]);
				$('body.sites #prtg_sensors').val(0);
			});
	}

	return false;
}


//Delete the PRTG sensors and remove site attributes
function deletePrtg() {

	$.ajax({
		url : '/json/manage/sites/prtg',
		type: "POST",
		beforeSend: function() {
			//Set the button to loading
			toggleLoadingButton($('body.sites #deletePrtgIds'), true);
		},
		complete: function() {
			//Remove loading from the button
			toggleLoadingButton($('body.sites #deletePrtgIds'), false);
		},
		data: {
			'action'	: 'delete',
			'siteId'	: SITE_ID
		}})
		.success (function(data){
			//Reset all inputs
			$('body.sites #prtg_sensors').val(0);
			$('body.sites #prtg_api_server').val('');
			$("body.sites #prtg_api_username").val('');
			$("body.sites #prtg_api_passhash").val('');
			swal({
				title	: trans["done"],
				text	: trans["prtg-sensors-delete-success"],
				type	: "success"});
		})
		.fail (function(data){
			alertError(trans["error"]);
			$('body.sites #prtg_sensors').val(0);
		});

	return false;
}

$(document).ready(function() {

	$('body.sites .address1, body.sites .address2, body.sites .town, body.sites .postcode').bind('keyup blur', function()
	{
		$('body.sites #fakeAddress').val($('body.sites .address1').val() + ' ' +
			$('body.sites .address2').val() + ' ' +
			$('body.sites .town').val() + ' ' +
			$('body.sites .postcode').val()).trigger('keyup');
	});

	/**
	 * Hide / Show site, company or estate classes
	 */
	function hideShowFields()
	{
		var siteType = $('body.sites .sitetype');
		var estateVisible = $('body.sites .estate-visible');
		var siteVisible = $('body.sites .site-visible');
		var companyVisible = $('body.sites .company-visible');
		//var addressDiv = $('.address-container');

		// Hide all
		estateVisible.hide();
		siteVisible.hide();
		companyVisible.hide();
		//addressDiv.show();

		if (siteType.val() == 'site'){
			siteVisible.show();
			//addressDiv.show();
			initMap();
		}
		else if(siteType.val() == 'company'){
			companyVisible.show();
			//addressDiv.show();
			initMap();
		}
		else if(siteType.val() == 'estate'){
			estateVisible.show();
			//addressDiv.hide();
		}

		/*else
		 {
		 addressDiv.show();
		 initMap();
		 }*/
	}

	hideShowFields();

	$('body.sites .sitetype').change(function(e)
	{
		hideShowFields();
	});

	$("body.sites").on( "click", ".switchery, i.warning",function(){
		return false;
	});

	// SMS Provider checkboxes should behave as radio buttons
	$("#smsProvidersAccordion input.js-switch-small").change(function() {
		$('#smsProvidersAccordion input.js-switch-small').not(this).each(function() {
			$(this).next().removeAttr("style").addClass("switched_off").children().removeAttr("style");
			$(this).prop("checked", false);
		});
	});

	var prtgButton  = $('body.sites #getPrtgIds');

	//Disable the Prtg sensors input (as it is populated only by the button)
	$('body.sites #prtg_sensors').prop('disabled', true);
	// Enable/Disable PRTG button if the server/username/passhash hasn't been completed already
	prtgButton.prop('disabled', checkPrtgFields());

	//On update of prtg fields, enable/disable button
	$('body.sites input[name="prtg_api_server"], body.sites input[name="prtg_api_username"], body.sites input[name="prtg_api_passhash"]').on('input', function() {
		prtgButton.prop('disabled', checkPrtgFields());
	});

	//onClick of getPrtgIds
	prtgButton.click(function() {
		//Set up PRTG Sensors
		setUpPrtg();
	});

	//onClick of deletePrtg
	$('body.sites #deletePrtgIds').click(function() {
		deletePrtg();
	});
});