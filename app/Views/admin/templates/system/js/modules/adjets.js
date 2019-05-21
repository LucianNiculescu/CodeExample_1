$(document).ready(function() {
	// If we switch on the date then show the dates, If we switch off, hide them
	var enableClose = $('body.manage.injectionjets #enable-close');
	enableClose.on('change', function (event) {
		//If input is not showing, show
		if (event.target.checked ==  true) {
			$('body.manage.injectionjets #closeTimeChooser').slideDown('fast');
			$('body.manage.injectionjets #close-time').val(1);
		} else {
			$('body.manage.injectionjets #closeTimeChooser').slideUp('fast'); // Hide
			$('body.manage.injectionjets #close-time').val(null);
		}
	});

	// On load choose if we should show or hide the 'Time until close'
	if( enableClose.is(':checked') == true )
		$('body.manage.injectionjets #closeTimeChooser').slideDown('fast'); // Show
	else
		$('body.manage.injectionjets #closeTimeChooser').slideUp('fast'); // Hide

	//This was outside of .ready()
	// On change of the days, hours, minutes we need to change to minutes
	$('body.manage.injectionjets input[name="durationDays"], body.manage.injectionjets input[name="durationHours"], body.manage.injectionjets input[name="durationMinutes"]').change(function (e) {

		var minutes = $('#durationMinutes').val();
		var hours = $('#durationHours').val();
		var days = $('#durationDays').val();


		if( days > 0 ) // If we have days
			minutes =  (days * 24) * 60 + +minutes; // Add the days, as minutes, to minutes

		if( hours > 0 ) // If we have hours
			minutes =  (hours * 60) + +minutes; // Add the hours, as minutes, to minutes

		$('#duration').val( minutes ); // Set the duration in minutes*/
	});

	//THIS WAS OUTSIDE TOO
	// Get html for icon uploader
	//var iconUploaderHTML = $('.icon-upload-group').html();
	var iconUploaderHTML = '<div class="custom-icons">' + $('.icon-upload-clone').html() + '</div>';

	// Start input count
	var inputCount = 0;
	// Get the last icon number
	var iconNumber = $('body.manage.injectionjets .icon-number').last().text();

	// Add more extra inputs
	$('body.manage.injectionjets .add-icon').click(function (e) {

		// Add the new input after the last one
		var thisFormGroup = $('body.manage.injectionjets .icon-upload-group .custom-icons:last');
		thisFormGroup.after(iconUploaderHTML).attr("id", "iconCount" + inputCount);
		$('body.manage.injectionjets .remove-icon:last')
			.css("display", "inline-block")
			.css("margin-bottom", "20px")
			.attr("id", "iconCount" + inputCount);

		inputCount++; // Next icon
		iconNumber++; // Next iconNumber
		$('body.manage.injectionjets .icon-number:last').text(iconNumber);
		e.preventDefault();
		return false;
	});

	// Remove an input
	$('body.manage.injectionjets .icon-upload-group').on("click", ".remove-icon", function (e) {

		// Remove this button
		$(this).parents('body.manage.injectionjets .custom-icons').remove();
		iconNumber--;
		e.preventDefault();
		return false;
	});

	var addLogo = $('body.manage.injectionjets #add-logo');

	addLogo.on('change', function (event) {
		//If input is not showing, show
		if (event.target.checked ==  true)
			$('body.manage.injectionjets #imageUploadFromGroup').slideDown('fast');
		else // Else hide and reset the input form
			$('body.manage.injectionjets #imageUploadFromGroup').slideUp('fast');
	});
	// On load choose if we should show or hide the 'Time until close'
	if( addLogo.is(':checked') == true )
		$('body.manage.injectionjets #imageUploadFromGroup').slideDown('fast'); // Show
	else
		$('body.manage.injectionjets #imageUploadFromGroup').slideUp('fast'); // Hide

	// If we have a button with URL preview and an input group. Inside the input group we have a select box with the
	// http type and a video-url we can send the browser to a new tab with that URL
	$('body.manage.injectionjets .url-preview').click( function(){

		var thisGroup = $(this).parents('.input-group'); // Find the group
		var thisSelected = $.trim(thisGroup.find('#secure-video-redirect option:selected').text()); // Find the selected option
		var thisVideoUrl = thisGroup.find('#video-redirect-url').val(); // Find the video Url

		if( thisVideoUrl != '' && thisSelected != '' ) {

			// Check if this is a YouTube video and does not contain the watch?v=
			if( thisVideoUrl.indexOf('youtube') > -1 && thisVideoUrl.indexOf('watch?v=') == -1 )
			{
				// Change the URL accordingly
				// Split the URL at the /
				var URLArr = thisVideoUrl.split('/');

				// Insert the 'watch?v=' after the / and put the URL back together
				thisVideoUrl = URLArr[0] + '/watch?v=' + URLArr[1];
			}

			// Create the preview URL
			var previewUrl = thisSelected + thisVideoUrl;

			// Change the link
			$(this).attr("href", previewUrl);

		} else {
			return false;
		}
	});
});