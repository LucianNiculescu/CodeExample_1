/**
 * AnimateFlipper will do the animation of the flipper in the portal page
 * Where preview image is the front div and the tinymce and templates are in the back
 */
function animateFlipper() {
	$('body.portals .flipper').not('body.portals .flipper.flipped').addClass('flipped');
	// add timer for 1 secound, after timer make tinymce (i.e. .back) width to 100% and height to screensize -200px
	setTimeout(function() {
		$('body.portals .back').show();
		$('body.portals .front').hide();
		$('body.portals #preview-container').removeClass('col-md-offset-2 col-md-8').addClass('col-md-12');
		$('body.portals #tinymce').attr('style','min-height:600px');
		$('body.portals .flipped').attr('style','min-height:0px');
	}, 300);
}

/**
 * the default template and background will be set to the hidden fields associated
 */
function fillTemplatesFields() {
	setTemplateSrcHiddenValue(false);
	$('.portal-preview').attr('href', '');
}


/**
 * the default template and background will be set to the hidden fields associated
 */
function emptyTemplatesFields() {
	setTemplateSrcHiddenValue(true);
	$('.portal-preview').attr('href', '#portal-preview');
}

/**
 * Setting the template-src hidden field with the template number
 */
function setTemplateSrcHiddenValue(empty) {
	if(empty){
		$('body.portals #template-src').val('none');
	} else {
		$('body.manage.portals #template-src')
			.val($('body.manage.portals .templates-wrapper')
				.find('.template-item.active')
				.attr('data-id')
			);
	}
}

/**
 * Toggelling the template system
 */
function toggleTemplates(element) {
	//Remove the active class from all the elements and add it to the selected one
	$('body.manage.portals .template-item').removeClass('active');
	element.addClass('active');

	//Change the preview picture with the selected element
	$('body.manage.portals #template_preview').attr('src', '/'+element.attr('data-img'));

}

/**
 * Function is called with modal is opened
 * An Ajax call will be sent to the server to get the portal preview link and fill it in the iframe
 * @param portalId
 */
function iframeModalOpen(portalId) {
	var loadingDiv = $(".manage.portals .loading-preview");
	var iFrame = $(".manage.portals #portal-preview").find("iframe");

	loadingDiv.show();
	$.ajax({
		url : '/manage/portals/' + portalId + '/portal-preview',
		type: "post",
		data: {
			'_method' 	: 'GET',
			'_token'	: $('input[name=_token]').val()
		}})
		.success (function(data){
			iFrame.attr({
				'src': data,
				'scrolling': 'yes',
				'frameborder': '0'
			});
		})
		.fail (function(data){alertError("Error Loading Iframe "+data);});
}

$(document).ready(function() {
	var portalContents = $('.manage.portals .preview-contents');
	var rotateDevice = $('.manage.portals .rotate-device');
	var loadingDiv = $(".manage.portals .loading-preview");
	var portalPreview = $(".manage.portals #portal-preview");
	var iFrame = portalPreview.find("iframe");

	// When iframe has completed loading the portal preview then the loading div will fadeout and the iframe will show
	iFrame.load(function(){
		loadingDiv.fadeOut("fast");
		iFrame.fadeIn("fast");
	}); // before setting 'src'

	// When clicking on the link to show the modal , the id is stored in the data-id parameter of the link
	portalPreview.on('shown.bs.modal', function(event) {
		var portalId = $(event.relatedTarget).data('id');
		if(portalPreview.hasClass('tablet-landscape'))
			rotateDevice.hide();
		// portal ID is passed to the framemodalopen function to call the right route
		iframeModalOpen(portalId);
	});

	// When closing the modal, the iframe will be hidden too
	portalPreview.on('hidden.bs.modal', function () {
		iFrame.hide();
	});

	// RotateDevice button will give the right class to the portalpreview and give also the right background
	rotateDevice.on('click', function(){
		if(portalPreview.hasClass('phone-portrait'))
		{
			portalPreview.removeClass('phone-portrait').addClass('phone-landscape');
			portalContents.css("background-image", "url('/admin/templates/system/images/devices/phone-landscape.png')");
		}
		else if(portalPreview.hasClass('phone-landscape'))
		{
			portalPreview.removeClass('phone-landscape').addClass('phone-portrait');
			portalContents.css("background-image", "url('/admin/templates/system/images/devices/phone-portrait.png')");
		}
	});

	// Mobile select will show the rotate button and give the right class and right background to the portal preview
	$('#select-mobile').on('click', function(){
		rotateDevice.show();
		portalPreview.removeClass('phone-landscape tablet-landscape').addClass('phone-portrait');
		portalContents.css("background-image", "url('/admin/templates/system/images/devices/phone-portrait.png')");
	});

	// Tablet select will hide the rotate button and will give the right class and background to the portal preview
	$('#select-tablet').on('click', function(){
		rotateDevice.hide();
		portalPreview.removeClass('phone-portrait phone-landscape').addClass('tablet-landscape');
		portalContents.css("background-image", "url('/admin/templates/system/images/devices/tablet-landscape.png')");
	});

	$('body.portals .flipper').not('body.portals .flipper.flipped').click(animateFlipper);

	// Disabling the auto sliding for the carrousel
	$('body.portals .carousel').carousel({
		interval: false
	});

	// When changing the template the hidden field template-src will be set
	$('body.portals .template-item').on('click', function () {
		toggleTemplates($(this));
		setTemplateSrcHiddenValue(false);
	});

	// Fill the default template and background when selecting the templates tab in the portal edit page
	$('.portals.edit #templates-tab').on('click', function () {
		fillTemplatesFields();
	});

	$('.portals.edit #tinymce-tab').on('click', function () {
		emptyTemplatesFields();
	});


	// Adding extra warning when submitting the form in the portal edit mode
	$('.portals.edit .submit-btn').on('click', function(e) {

		//var backgroundSrc = $('body.portals #background-src').val();
		var templateSrc = $('body.portals #template-src').val();
		console.log(templateSrc);
		if( templateSrc != '' && templateSrc != 'none' && typeof templateSrc !== 'undefined') {

			e.preventDefault();
			swal({
				title		        : trans["apply-template"],
				text				: trans["portal-template-overwrite-warning"],
				type				: "warning",
				showCancelButton	: true,
				confirmButtonColor	: "#00adef" ,
				confirmButtonText	: trans["yes-please"]
			}).then(  function(){
				$('.portals.edit #crudForm').submit();
			}).catch(swal.noop);
		}

	});

	// Adding extra warning when previewing the portal
	$('.portals.edit .portal-preview').on('click', function(e) {

		//var backgroundSrc = $('body.portals #background-src').val();
		var templateSrc = $('body.portals #template-src').val();

		if( templateSrc != '' && templateSrc != 'none' && typeof templateSrc !== 'undefined' ) {
			e.preventDefault();
			swal({
				title		        : trans["apply-template"],
				text				: trans["portal-preview-save-warning"],
				type				: "warning",
				showCancelButton	: true,
				confirmButtonColor	: "#00adef" ,
				confirmButtonText	: trans["yes-please"]
			}).then (  function() {
				//fillTemplatesFields();
				$('.portals.edit #crudForm').submit();
			}).catch(swal.noop);
			//}
		}
	});

	// Clear Test data, i.e. guests and transactionos for this site
	$('.portals .clear-test-data').on('click', function(e) {
		e.preventDefault();
		var url = $(this).attr("href");

		swal({
			title		        : trans["are-you-sure"],
			text				: trans["to-clear-test-data"],
			type				: "question",
			showCancelButton	: true,
			confirmButtonColor	: "#00adef" ,
			confirmButtonText	: trans["yes-please"]
		}).then (  function() {
			$(".loading_page").fadeIn("fast");
			$.ajax({
				url : url,
				type: "post",
				data: {
					'_token'	: $('input[name=_token]').val()
				}})
				.success (function(data){
					//console.log("DATA: " + data);
					if(data ==1){
						$(".loading_page").fadeOut("fast");
						swal({
							title:	trans["done"],
							text: trans["test-data-cleared"],
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
		}).catch(swal.noop);

	});




	// If the background file changes
	$('.background-file-upload .fileinput').on('change.bs.fileinput', function (e) {
		$('#background-src').val(''); // Clear the upload value
	});

	// Background image change
	$('.background-list li').click(function (e) {
		// Get the URLs
		var thumbURL = $(this).find('img').attr('src');
		var imageURL = thumbURL.replace("/thumbs/", "/");
		var imageName = /[^/]*$/.exec(imageURL)[0];

		$('#background-file-preview').find('img').attr('src', imageURL); // Replace the preview
		$('#background-src').val(imageName); // Replace the upload value
	});

	//Slider for landing page utm post auth
	var utmPostAuth = $('body.manage.portals .utm-post-auth');
	var utmCampaign = $('body.manage.portals .utm-campaign');
	if(utmPostAuth.is(":checked")) {
		//Hide/show the div
		utmCampaign.removeClass('hidden').addClass('show');
		//This fixes a bug with switchery, when it is readonly, it disables the switch and gets the value of a hidden input
		$('body.manage.portals input[name="utm_post_auth"]').val("true");
	}

	// Show/Hide the recurrency container and enable/disable duration input (because it is triggered by frequency selector)
	utmPostAuth.on('change', function (event) {
		//If input is not showing, show
		if (event.target.checked ==  true) {
			utmCampaign.removeClass('hidden').addClass('show');
		} else {
			utmCampaign.removeClass('show').addClass('hidden');
		}
	});
});