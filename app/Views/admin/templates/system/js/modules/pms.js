
var body = $('body');

/**
 * Changing the pms type will show and hide the right form
 */
body.on('change', '#pms_type', function(){

	var pmsType  = $('#pms_type').val();
	var upmsType = $('#upms_type').val();

	if(pmsType != 'Upms' && upmsType!='oracle')
		$('.config-btn').remove();

	$('#validateRoomForm').validate();

	$('#'+pmsType + 'Form').validate();

	$.each($('.pms_form').not('#'+pmsType + 'Form'), function(){

		$(this).validate().resetForm();
	});

	$('#upmsConfigForm').hide();
	$('.pms_form').hide();
	$('#'+pmsType+'Form.pms_form').show();
	$('#pmsButtons').show();
	$('.pms_type').val(pmsType);
	$('#pmsDefaultHelp').hide();

	changeUPMSUi($('#upms_type'));
});

/**
 * Changing the upms type will show and hide the right form
 */
body.on('change', '#upms_type', function(){
	changeUPMSUi($(this));
});


/**
 * Saving the PMS page
 */
body.on('click', '.pms-submit', function(e){
	e.preventDefault();
	var type = $(this).data('type');
	$('#'+type + 'Form').submit();
});

function changeUPMSUi(upmsType)
{
	var upmsType = upmsType.val();

	$('#upmsConfigForm').hide();

	if(upmsType!='oracle')
		$('.config-btn').hide();
	else
		$('.config-btn').show();

	if(upmsType!='vinn')
		$('.hotel-id-section').hide();
	else
		$('.hotel-id-section').show();
	
}

/**
 * Getting the UPMS configuration
 */
body.on('click', '#upmsGetConfig', function(e){
	e.preventDefault();

	var upmsHelp = $('#upmsHelp');
	upmsHelp.find('.waiting').remove();
	upmsHelp.show();
	$('#upmsConfigForm').hide();
	var spinnerIcon = '<div class="center-text waiting"><i class="fa fa-spinner font-size-50 fa-spin spinner-icon margin-top-50" ></i></div>';
	upmsHelp.append(spinnerIcon);

	$.ajax({
		url : "/json/manage/pms/:/get_upms_config",
		type: "get",
		data:{
			protocol 	: $('#protocol').val(),
			uri			: $('#uri').val(),
			port		: $('#port').val(),
			upms_type	: $('#upms_type').val()

		}
	}).success (function(data){

		data = JSON.parse(data);

		if(data.status == "OK")
		{
			var payload = data.payload;

			$('#upms-config-uri').val(payload.Uri);
			$('#upms-config-port').val(payload.Port);
			$('#upms-config-hotel-code').val(payload.HotelCode);
			$('#upms-config-target').val(payload.Target);
			$('#upms-config-retry-freq').val(payload.retryFrequency);
			$('#upms-config-warn-freq').val(payload.warnFrequency);

			upmsHelp.find('.waiting').remove();
			upmsHelp.hide();

			$('#upmsConfigForm').fadeIn();
		}
		else
		{
			$('.spinner-icon').removeClass('fa-spin fa-spinner').addClass('fa-times');
		}


	}).fail (function(data){
		alertError(data);
		console.log(data);
	});
});


/**
 * Set UPMS configuration
 */
body.on('click', '#upmsSetConfig', function(e){
	e.preventDefault();
	// Sweet Alert call to confirm
	swal({
		title				: trans["are-you-sure"],
		text				: 'setting and restarting hardware',
		type				: "warning",
		showCancelButton	: true,
		confirmButtonColor	: "#00adef" ,
		confirmButtonText	: trans["yes-please"],
		cancelButtonText	: trans["cancel"]
	}).then(function(){

		$('#upmsConfigForm').hide();
		$('#upmsHelp').fadeIn();
		var spinnerIcon = '<div class="center-text waiting"><i class="fa fa-spinner font-size-50 fa-spin spinner-icon margin-top-50" ></i></div>';

		$('#upmsHelp').append(spinnerIcon);

		$.ajax({
			url : "/json/manage/pms/:/set_upms_config",
			type: "get",
			data:{
				protocol 			: $('#protocol').val(),
				uri					: $('#uri').val(),
				port				: $('#port').val(),
				upms_type			: $('#upms_type').val(),
				configUri			: $('#upms-config-uri').val(),
				configPort			: $('#upms-config-port').val(),
				configHotelCode		: $('#upms-config-hotel-code').val(),
				configSiteId		: $('#upms-config-site-id').val(),
				configTarget		: $('#upms-config-target').val(),
				configRetryFrequency: $('#upms-config-retry-freq').val(),
				configWarnFrequency	: $('#upms-config-warn-freq').val()

			}
		}).success (function(data){
			data = JSON.parse(data);

			if(data.status == "OK") {
				$('.spinner-icon').hide();
				swal({
					title: trans["done"],
					text: "hardware is set and restarted!",
					type: "success"
				});
			}
			else
			{
				swal({
					title: trans["error"],
					text: "Failed to set the upms configuration!",
					type: "error"
				});
			}

		}).fail (function(data){
			$('.loading_page').fadeOut();
			alertError(data);
			console.log(data);
		});
	}, function (dismiss) {

	});
});


/**
 * Validate Room using captive API
 */
function validateRoomCaptive(e) {

	e.preventDefault();
	var icon = $('#validate-room-icon');
	var desc = $('#validateRoomDesc');
	var surname = $('#validate-room-surname');
	var room = $('#validate-room-no');
	var ip = $('#ip');
	var sharedSecret = $('#shared-secret');

	// if Surname or Room are not valid then return
	if (!surname.valid() || !room.valid())
		return;

	icon.addClass('fa-spin fa-circle-o-notch').removeClass('fa-times fa-check ');
	$.ajax({
		url: "/json/manage/pms/:/validate_room",
		type: "get",
		data: {
			surname: surname.val(),
			room: room.val(),
			ip: ip.val(),
			shared_secret: sharedSecret.val(),
			pms_type: 'captive'
		}
	}).success(function (data) {

		data = JSON.parse(data);
		icon.removeClass('fa-times fa-check fa-spin fa-circle-o-notch ');

		if (data.response.status == "true") {
			icon.addClass('fa-check');
			desc.html("Valid user");
		}
		else {
			icon.addClass('fa-times');
			desc.html("Unable to validate");
		}
	}).fail(function (data) {
		alertError(data);
		console.log(data);
	});
}


/**
 * Validate room using UPMS
 */
function validateRoomUPMS(e) {

	e.preventDefault();
	var icon = $('#validate-room-icon');
	var desc = $('#validateRoomDesc');
	var surname = $('#validate-room-surname');
	var room = $('#validate-room-no');

	// if Surname or Room are not valid then return
	if ((!surname.valid() && !room.valid()) || (!surname.valid() || !room.valid()))
		return;

	icon.addClass('fa-spin fa-circle-o-notch').removeClass('fa-times fa-check ');
	$.ajax({
		url: "/json/manage/pms/:/validate_room",
		type: "get",
		data: {
			surname: surname.val(),
			room: room.val(),
			protocol: $('#protocol').val(),
			uri: $('#uri').val(),
			port: $('#port').val(),
			upms_type: $('#upms_type').val(),
			hotel_id: $('#hotel_id').val()

		}
	}).success(function (data) {

		data = JSON.parse(data);
		icon.removeClass('fa-times fa-check fa-spin fa-circle-o-notch ');

		if (data.status == "OK") {
			icon.addClass('fa-check');
			desc.html("Connection done!");
		}
		else {
			icon.addClass('fa-times');
			desc.html("Failed to connect!");
		}
	}).fail(function (data) {
		alertError(data);
		console.log(data);
	});
}


/**
 * Validate Room
 */
body.on('click', '#validateRoom', function(e){
	var pmsType = $(this).data('pms-type');
	if (pmsType == 'captive') {
		validateRoomCaptive(e);
	} else {
		validateRoomUPMS(e);
	}

});

$(document).ready(function() {
	var dynamicIp = $('body.manage.pms .dynamic_ip');
	var dynamicGateways = $('body.manage.pms .dynamic-gateway');
	if(dynamicIp.is(":checked")) {
		//Hide/show the dynamic gateways
		dynamicGateways.removeClass('hidden').addClass('show');
		//This fixes a bug with switchery, when it is readonly, it disables the switch and gets the value of a hidden input
		$('body.manage.pms input[name="dynamic_ip"]').val("true");
	}

	// Show/Hide the recurrency container and enable/disable duration input (because it is triggered by frequency selector)
	dynamicIp.on('change', function (event) {
		//If input is not showing, show
		if (event.target.checked ==  true) {
			dynamicGateways.removeClass('hidden').addClass('show');
		} else {
			dynamicGateways.removeClass('show').addClass('hidden');
		}
	});

});
