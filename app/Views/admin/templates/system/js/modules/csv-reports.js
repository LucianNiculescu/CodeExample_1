$(document).ready(function() {

	//change the option so we don't allow future dates
	$("body.reports.csv .datepicker").datepicker("option", { maxDate: 0 });

	//set the element to be always active when it is clicked (because the focus gets lost when you click something else)
	$('body.reports.csv .csv-reports .csv-selector .list-group-item').click(function(){
		//reset all the elements
		$('body.reports.csv .csv-reports .csv-selector .list-group-item').removeClass('active');
		//add 'active' class to the selected element
		$(this).addClass('active');
		//set the value of the hidden input of the selected report type
		$('body.reports.csv input[name="type"]').val($(this).attr('data-value'));
	});

	//check if the submit button mush be activated, on click of the page
	$('body.reports.csv .page-content.container-fluid').click( function(e) {
		//need a small delay before checking the values (else we have bugs because the other events are fired after this one)
		postpone(checkSubmit);
	});

	//Because I can't trigger anything over the hidden input that is being manipulated by the datepicker,
	//I'm listening to the title .dashboard-description and calling checkSubmit() whenever the div changes
	$("body.reports.csv").on('DOMSubtreeModified', ".dashboard-description", function() {
		postpone(checkSubmit);
	});

	//Set a default period of 24 hours
	$('body.reports.csv #last-24-hours').click();

	function checkSubmit() {
		//check if the dates have been selected and the report type
		if ($('body.reports.csv input[name="type"]').val() != '') {
			if (
				$('body.reports.csv .csv-reports .period-btn').hasClass('active') ||
				($('body.reports.csv input[name="period-from"]').val() != '' &&
				$('body.reports.csv input[name="period-to"]').val() != '' )
			) {
				//activate the submit button
				$('#csv-submit').prop('disabled', false);
			} else {
				//deactivate the submit button
				$('#csv-submit').prop('disabled', true);
			}
		}
	}

	/**
	 * Adding a delay of 100 ms
	 * @param func
	 */
	function postpone(func) {
		window.setTimeout(func, 100);
	}


	/**
	 * For the CSV reports, we have functions for controlling the period which originally were shared with the settings widget.
	 * The functions it calls are shared with the settings bar for the reports widgets
	 */
	// Calling the period button function
	$('#csvSettings .period-buttons .period-btn:not(.disabled)').click(function(){setPeriodSettings($(this));});

	// Resetting the custom period when any other button is clicked
	$('#csvSettings .period-buttons .period-btn:not(.custom-period)').click(function(){resetCustomPeriod();});
});