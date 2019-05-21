var body = $('body');

// An Array to store all Ajax Calls
$.xhrPool = [];

// Function to abort all ajax calls and remove them from the pool
$.xhrPool.abortAll = function() {
	$(this).each(function(idx, jqXHR) {
		jqXHR.abort();
		$.xhrPool.splice(idx, 1);
	});
};

window.ajaxEnabled = true;
// Setting up the AJAX so it will store the ajax call in an array
$.ajaxSetup({
	beforeSend: function(jqXHR) {
		if(window.ajaxEnabled === true)
			$.xhrPool.push(jqXHR);
		return window.ajaxEnabled;
	},
	complete: function(jqXHR) {
		var index = $.xhrPool.indexOf(jqXHR);
		if (index > -1) {
			$.xhrPool.splice(index, 1);
		}
	}
});
// Another way of doing it
// Automatically cancel unfinished ajax requests
// when the user navigates elsewhere.
/*	var xhrPool = [];
	// Fills the xhrPool everytime an ajax is sent
	$(document).ajaxSend(function(e, jqXHR, options){
		xhrPool.push(jqXHR);
	});
	// Removes the ajax call from the xhrPool once it is completed
	$(document).ajaxComplete(function(e, jqXHR, options) {
		xhrPool = $.grep(xhrPool, function(x){return x!=jqXHR});
	});
	// Aborting all ajax calls in the xhrPool
	var abortAjax = function() {
		$.each(xhrPool, function(idx, jqXHR) {
			jqXHR.abort();
		});
	};

	// Abort ajax when you leave the current page
	var oldbeforeunload = window.onbeforeunload;
	window.onbeforeunload = function() {
		var r = oldbeforeunload ? oldbeforeunload() : undefined;
		if (r === undefined) {
			// only cancel requests if there is no prompt to stay on the page
			// if there is a prompt, it will likely give the requests enough time to finish
			abortAjax();
		}
		return r;
	};*/


$(document).ready(function () {
	var accumulatedGuestsWidget 	= $('.grid-item.accumulated-guests');
	var averageGatewayLatencyWidget = $('.grid-item.average-gateway-latency');
	var averageTrafficWidget 		= $('.grid-item.average-traffic');
	var browserTrendsWidget 		= $('.grid-item.browser-trends');
	var browserUsageWidget 			= $('.grid-item.browser-usage');
	var cumulativeNetIncomeWidget	= $('.grid-item.cumulative-net-income');
	var dailyCashflowWidget			= $('.grid-item.daily-cashflow');
	var dataTransferredWidget 		= $('.grid-item.data-transferred');
	var demographicsWidget 			= $('.grid-item.demographics');
	var dwellTimeWidget 			= $('.grid-item.dwell-time');
	var gatewaySpeedWidget 			= $('.grid-item.gateway-speed');
	var gatewayLogsWidget 			= $('.grid-item.gateway-logs');
	var gatewayControlWidget 		= $('.grid-item.gateway-control');
	var genderWidget 				= $('.grid-item.gender');
	var highestGatewayLatencyWidget = $('.grid-item.highest-gateway-latency');
	var latencyWidget 				= $('.grid-item.latency');
	var loginTypesWidget 			= $('.grid-item.login-types');
	var mapWidget 					= $('.grid-item.map');
	var messagesWidget 				= $('.grid-item.messages');
	var mostUsedPackageWidget		= $('.grid-item.most-used-package');
	var newGuestsWidget 			= $('.grid-item.new-guests');
	var netPackagesWidget 			= $('.grid-item.net-packages');
	var netIncomeWidget 			= $('.grid-item.net-income');
	var averageNetIncomeWidget		= $('.grid-item.average-net-income');
	var osUsageWidget 				= $('.grid-item.os-usage');
	var osTrendsWidget 				= $('.grid-item.os-trends');
	var regUsersWidget 				= $('.grid-item.registered-users');
	var packageSalesIncomeWidget 	= $('.grid-item.package-sales-income');
	var hardwareGatewaysWidget		= $('.grid-item.hardware-gateways');
	var siteListWidget				= $('.grid-item.site-list');
	var prtgWidget					= $('.grid-item.prtg');
	var apListWidget 				= $('.grid-item.ap-list');
	var wanThroughPutWidget 		= $('.grid-item.wan-throughput');
    var loginsInLastNWidget 		= $('.grid-item.logins-in-last-n');

	// If widget is on the page and is active then initiate it
	if (accumulatedGuestsWidget.length > 0 && !accumulatedGuestsWidget.hasClass('inactive'))
		initAccumulatedGuestsWidget();

	if (browserTrendsWidget.length > 0 && !browserTrendsWidget.hasClass('inactive'))
		initBrowserTrendsWidget();

	if (browserUsageWidget.length > 0 && !browserUsageWidget.hasClass('inactive'))
		initBrowserUsageWidget();

	if (cumulativeNetIncomeWidget.length > 0 && !cumulativeNetIncomeWidget.hasClass('inactive'))
		initCumulativeNetIncomeWidget();

	if (dailyCashflowWidget.length > 0 && !dailyCashflowWidget.hasClass('inactive'))
		initDailyCashflowWidget();

	if (dataTransferredWidget.length > 0 && !dataTransferredWidget.hasClass('inactive'))
		initDataTransferredWidget();

	if (demographicsWidget.length > 0 && !demographicsWidget.hasClass('inactive'))
		initDemographicsWidget();

	if (dwellTimeWidget.length > 0 && !dwellTimeWidget.hasClass('inactive'))
		initDwellTimeWidget();

	if (latencyWidget.length > 0 && !latencyWidget.hasClass('inactive'))
		initLatencyWidget();

	if (averageGatewayLatencyWidget.length > 0 && !averageGatewayLatencyWidget.hasClass('inactive'))
		initAverageGatewayLatencyWidget();

	if (averageTrafficWidget.length > 0 && !averageTrafficWidget.hasClass('inactive'))
		initAverageTrafficWidget();

	if (gatewaySpeedWidget.length > 0 && !gatewaySpeedWidget.hasClass('inactive'))
		initGatewaySpeedWidget();

	if (gatewayLogsWidget.length > 0 && !gatewayLogsWidget.hasClass('inactive'))
		initGatewayLogsWidget();

	if (gatewayControlWidget.length > 0 && !gatewayControlWidget.hasClass('inactive'))
		initGatewayControlWidget();

	if (highestGatewayLatencyWidget.length > 0 && !highestGatewayLatencyWidget.hasClass('inactive'))
		initHighestGatewayLatencyWidget();

	if (hardwareGatewaysWidget.length > 0 && !hardwareGatewaysWidget.hasClass('inactive'))
		initHardwareGatewaysWidget();

	if (siteListWidget.length > 0 && !siteListWidget.hasClass('inactive'))
		initSiteListWidget();

	if (loginTypesWidget.length > 0 && !loginTypesWidget.hasClass('inactive'))
		initLoginTypesWidget();

	if (osUsageWidget.length > 0 && !osUsageWidget.hasClass('inactive'))
		initOsUsageWidget();

	if (mapWidget.length > 0 && !mapWidget.hasClass('inactive'))
		initMapWidget();

	if (messagesWidget.length > 0 && !messagesWidget.hasClass('inactive'))
		initMessagesWidget();

	if (mostUsedPackageWidget.length > 0 && !mostUsedPackageWidget.hasClass('inactive'))
		initMostUsedPackageWidget();

	if (newGuestsWidget.length > 0 && !newGuestsWidget.hasClass('inactive'))
		initNewGuestsWidget();

	if (netIncomeWidget.length > 0 && !netIncomeWidget.hasClass('inactive'))
		initNetIncomeWidget();

	if (averageNetIncomeWidget.length > 0 && !averageNetIncomeWidget.hasClass('inactive'))
		initAverageNetIncomeWidget();

	if (netPackagesWidget.length > 0 && !netPackagesWidget.hasClass('inactive'))
		initNetPackagesWidget();

	if (osTrendsWidget.length > 0 && !osTrendsWidget.hasClass('inactive'))
		initOsTrendsWidget();

	if (regUsersWidget.length > 0 && !regUsersWidget.hasClass('inactive'))
		initRegUsersWidget();

	if (packageSalesIncomeWidget.length > 0 && !packageSalesIncomeWidget.hasClass('inactive'))
		initPackageSalesIncomeWidget();

	if (wanThroughPutWidget.length > 0 && !wanThroughPutWidget.hasClass('inactive'))
		initWanThroughputWidget();

	if (genderWidget.length > 0 && !genderWidget.hasClass('inactive'))
		initGenderWidget();

	if (prtgWidget.length > 0 && !prtgWidget.hasClass('inactive'))
		initPrtgWidget();

	if (apListWidget.length > 0 && !apListWidget.hasClass('inactive'))
		initApListWidget();

    if (loginsInLastNWidget.length > 0 && !loginsInLastNWidget.hasClass('inactive')) {
        initLoginsInLastNWidget();
	}

	// Functionality of the datepicker to highlight the selected period
	//$(".grid-item.settings .datepicker, body.reports.csv .datepicker").datepicker({

	if (!body.hasClass('csv'))
	// Getting the setting from the buttons and update the report, by default it is showing last week's data
		updateReportDetails("last-week");

	/**
	 * The reports period settings are now controlled by a top bar rather than a widget.
	 * These functions control interactions between the buttons including the display of a custom datepicker.
	 */
	// This is used to identify a custom-period selection.
	// For other periods, the id on the button is used.
	var customPeriodType = 'custom-period';

	/**
	 * Functionality of the datepicker to highlight the selected period
	 */
	$("#reports-settings .datepicker, body.reports.csv .datepicker").datepicker({
		numberOfMonths: 1,
		beforeShowDay: function (date) {
			var periodFromInput = $("#period-from");
			var periodToInput = $("#period-to");
			var periodFromDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, periodFromInput.val());
			var periodToDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, periodToInput.val());
			return [true, periodFromDate && ((date.getTime() == periodFromDate.getTime()) || (periodToDate && date >= periodFromDate && date <= periodToDate)) ? "dp-highlight" : ""];
		},
		onSelect: function (dateText, inst) {
			var periodFromInput = $("#period-from");
			var periodToInput = $("#period-to");

			var periodFromDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, periodFromInput.val());
			var periodToDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, periodToInput.val());
			var selectedDate = $.datepicker.parseDate($.datepicker._defaults.dateFormat, dateText);

			if (!periodFromDate || periodToDate) {
				periodFromInput.val(dateText);
				periodToInput.val("");
				$(this).datepicker();
			} else if (selectedDate < periodFromDate) {
				periodToInput.val(periodFromInput.val());
				periodFromInput.val(dateText);
				$(this).datepicker();
			} else {
				periodToInput.val(dateText);
				$(this).datepicker();
			}

			updateReportDetails(customPeriodType);
		}
	});

	//Event for prtg-group change
	$('#prtgSettingsWidget .prtg-group').on('change', function (event) {

		//reset name and type
		$('#prtgSettingsWidget .prtg-name').empty().attr('disabled', true);
		$('#prtgSettingsWidget .prtg-type').empty().attr('disabled', true);

		var prtgButton = $('#prtgSettingsWidget .prtg-custom-period');

		//Ajax call to get all names
		$.ajax({
			url : '/json/widgets/prtg/0/get-report-data',
			type: "GET",
			data: {
				method: '\\App\\Admin\\Widgets\\Prtg::getPrtgNames',
				id:	this.value
			},
			beforeSend: function() {
				//Set the button to loading
				toggleLoadingButton(prtgButton, true);
			}
		}).success (function(data) {
			//Refresh the select name with the new names
			$.each(JSON.parse(data), function(idx, obj){
				$('#prtgSettingsWidget .prtg-name').append('<option value="' + obj.id + '">' + obj.name + '</option>');
			});
			//Remove loading from the button
			toggleLoadingButton(prtgButton, false);
			//Enable the chosen dropdown and trigger the chosen changes
			$('#prtgSettingsWidget .prtg-name').attr('disabled', false);
			$('#prtgSettingsWidget .prtg-name').trigger("chosen:updated");
			$('#prtgSettingsWidget .prtg-name').trigger("liszt:updated");
			$('#prtgSettingsWidget .prtg-name').chosen();
			$('#prtgSettingsWidget .prtg-name').change();

		}).fail (function(data){alertError(trans["error"]);});
	});

	//Event for prtg-name change
	$('#prtgSettingsWidget .prtg-name').on('change', function (event) {

		var prtgButton = $('#prtgSettingsWidget .prtg-custom-period');
		//reset type dropdown
		$('#prtgSettingsWidget .prtg-type').empty().attr('disabled', true);

		//Ajax call to get all names
		$.ajax({
			url : '/json/widgets/prtg/0/get-report-data',
			type: "GET",
			data: {
				method: '\\App\\Admin\\Widgets\\Prtg::getPrtgTypes',
				id:	this.value
			},
			beforeSend: function() {
				//Set the button to loading
				toggleLoadingButton(prtgButton, true);
			}
		}).success (function(data) {
			//Refresh the select name with the new names
			$.each(JSON.parse(data), function(idx, obj){
				$('#prtgSettingsWidget .prtg-type').append('<option value="' + obj.id + '">' + obj.type + '</option>');
			});
			//Remove loading from the button
			toggleLoadingButton(prtgButton, false);
			//Enable the chosen dropdown and trigger the chosen changes
			$('#prtgSettingsWidget .prtg-type').attr('disabled', false);
			$('#prtgSettingsWidget .prtg-type').trigger("chosen:updated");
			$('#prtgSettingsWidget .prtg-type').trigger("liszt:updated");
			$('#prtgSettingsWidget .prtg-type').chosen();
			$('#prtgSettingsWidget .prtg-type').change();

		}).fail (function(data){alertError(trans["error"]);});
	});

	//Event for prtg-name change
	$('#prtgSettingsWidget .prtg-type').on('change', function (event) {
		togglePrtgSettingsWidgetButton();
	});


	togglePrtgSettingsWidgetButton();

	// Functionality of the prtg datepicker to highlight the selected period
	$(".grid-item.prtg .datepicker").datepicker({

		numberOfMonths: 1,
		beforeShowDay: function(date) {
			var periodFromInput 	= $("#prtg-period-from");
			var periodToInput 		= $("#prtg-period-to");

			var periodFromDate 	= $.datepicker.parseDate($.datepicker._defaults.dateFormat, periodFromInput.val());
			var periodToDate 	= $.datepicker.parseDate($.datepicker._defaults.dateFormat, periodToInput.val());
			return [true, periodFromDate && ((date.getTime() == periodFromDate.getTime()) || (periodToDate && date >= periodFromDate && date <= periodToDate)) ? "dp-highlight" : ""];
		},
		onSelect: function(dateText, inst) {
			var periodFromInput 	= $("#prtg-period-from");
			var periodToInput 		= $("#prtg-period-to");
			var customPeriodButton 	= $(".prtg-custom-period");

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

			updatePrtgReportDetails("custom-period");
		}
	} );

	if(!body.hasClass('csv'))
	// Getting the setting from the buttons and update the report, by default it is showing last week's data
		updateReportDetails("last-week");
	/**
	 * Process the clicks on a period button in the report settings bar, which are periods for the report.
	 * If the button is disabled, we stop the click making the button active.
	 * If the button is for a custom period we show the datepicker
	 * If the button is enabled we hide the datepicker, change the period to match the button and update the report widgets.
	 */
	$('#reports-settings .period-btn').click(function (event) {
		var clickedBtn = this;
		//If the button is disabled we don't want it to respond to the click
		//Likewise when the clicked element is the dont-touch mask over a disabled button.
		if ($(clickedBtn).hasClass('disabled') || $(event.target).hasClass('dont-touch')) {
			event.stopPropagation();
		} else if ($(clickedBtn).attr('id') === 'custom-report-period') {
			// We need to set the "Go" button to disabled or not
			updateReportDetails(customPeriodType);
			$('#reports-settings .datepicker-toggle').show();
		} else {
			setPeriodSettings(clickedBtn);
			$('#reports-settings .datepicker-toggle').hide();
			resetCustomPeriod();
		}
	});

	/**
	 * When user clicks the "Go" button for the reports settings datepicker, we hide the datepicker and update the widgets.
	 */
	$('#use-custom-period').click(function () {
		//Only respond to clicks on the button if it is not disabled.
		if ($('#use-custom-period').attr('disabled') !== 'disabled') {
			//Hide the datepicker
			$('#reports-settings .datepicker-toggle').hide();

			// Store the period type in a hidden form field
			var periodInput = $("#period");
			var period = customPeriodType;
			periodInput.val(period);

			updateReportWidgets(period);
		}
	});

});

// Making the help popover work after dragging new widget
body.on('initWidget', '.grid-item', function(e) {
	$('[data-toggle="popover"]').popover();
});

/////////////////Init Widget Triggers

// Binding initWigit to initGenderWidget
body.on('initWidget', '.grid-item.gender', function(e){
	e.stopImmediatePropagation();
	initGenderWidget();
});

body.on('initWidget', '.grid-item.latency', function(e){
	e.stopImmediatePropagation();
	initLatencyWidget();
});

body.on('initWidget', '.grid-item.average-gateway-latency', function(e){
	e.stopImmediatePropagation();
	initAverageGatewayLatencyWidget();
});

body.on('initWidget', '.grid-item.average-traffic', function(e){
	e.stopImmediatePropagation();
	initAverageTrafficWidget();
});

body.on('initWidget', '.grid-item.gateway-speed', function(e){
	e.stopImmediatePropagation();
	initGatewaySpeedWidget();
});

body.on('initWidget', '.grid-item.gateway-logs', function(e){
	e.stopImmediatePropagation();
	initGatewayLogsWidget();
});

body.on('initWidget', '.grid-item.gateway-control', function(e){
	e.stopImmediatePropagation();
	initGatewayControlWidget();
});

body.on('initWidget', '.grid-item.highest-gateway-latency', function(e){
	e.stopImmediatePropagation();
	initHighestGatewayLatencyWidget();
});

body.on('initWidget', '.grid-item.hardware-gateways', function(e){
	e.stopImmediatePropagation();
	initHardwareGatewaysWidget();
});

body.on('initWidget', '.grid-item.site-list', function(e){
	e.stopImmediatePropagation();
	initSiteListWidget();
});

body.on('initWidget', '.grid-item.login-types', function(e){
	e.stopImmediatePropagation();
	initLoginTypesWidget();
});

body.on('initWidget', '.grid-item.os-usage', function(e){
	e.stopImmediatePropagation();
	initOsUsageWidget();
});

body.on('initWidget', '.grid-item.map',  function(e){
	e.stopImmediatePropagation();
	initMapWidget();
});

body.on('initWidget', '.grid-item.messages',  function(e){
	e.stopImmediatePropagation();
	initMessagesWidget();
});

body.on('initWidget', '.grid-item.most-used-package', function(e){
	e.stopImmediatePropagation();
	initMostUsedPackageWidget();
});

body.on('initWidget', '.grid-item.new-guests', function(e){
	e.stopImmediatePropagation();
	initNewGuestsWidget();
});

body.on('initWidget', '.grid-item.net-income', function(e){
	e.stopImmediatePropagation();
	initNetIncomeWidget();
});

body.on('initWidget', '.grid-item.average-net-income', function(e){
	e.stopImmediatePropagation();
	initAverageNetIncomeWidget();
});

body.on('initWidget', '.grid-item.net-packages', function(e){
	e.stopImmediatePropagation();
	initNetPackagesWidget();
});

body.on('initWidget', '.grid-item.os-trends', function(e){
	e.stopImmediatePropagation();
	initOsTrendsWidget();
});

body.on('initWidget', '.grid-item.registered-users',  function(e){
	e.stopImmediatePropagation();
	initRegUsersWidget();
});

body.on('initWidget', '.grid-item.package-sales-income',  function(e){
	e.stopImmediatePropagation();
	initPackageSalesIncomeWidget();
});

body.on('initWidget', '.grid-item.wan-throughput', function(e){
	e.stopImmediatePropagation();
	initWanThroughputWidget();
});

body.on('initWidget', '.grid-item.accumulated-guests', function(e){
	e.stopImmediatePropagation();
	initAccumulatedGuestsWidget();
});

body.on('initWidget', '.grid-item.browser-trends', function(e){
	e.stopImmediatePropagation();
	initBrowserTrendsWidget();
});

body.on('initWidget', '.grid-item.browser-usage', function(e){
	e.stopImmediatePropagation();
	initBrowserUsageWidget();
});

body.on('initWidget', '.grid-item.cumulative-net-income', function(e){
	e.stopImmediatePropagation();
	initCumulativeNetIncomeWidget();
});

body.on('initWidget', '.grid-item.daily-cashflow', function(e){
	e.stopImmediatePropagation();
	initDailyCashflowWidget();
});

body.on('initWidget', '.grid-item.data-transferred', function(e){
	e.stopImmediatePropagation();
	initDataTransferredWidget();
});

body.on('initWidget', '.grid-item.demographics', function(e){
	e.stopImmediatePropagation();
	initDemographicsWidget();
});

body.on('initWidget', '.grid-item.dwell-time', function(e){
	e.stopImmediatePropagation();
	initDwellTimeWidget();
});

body.on('click', '.grid-item.prtg .prtg-custom-period', function(e){
	e.stopImmediatePropagation();
	initPrtgWidget();
});

body.on('initWidget', '.grid-item.ap-list', function(e){
	e.stopImmediatePropagation();
	initApListWidget();
});

body.on('initWidget', '.grid-item.logins-in-last-n', function(e){
    e.stopImmediatePropagation();
    console.log('initLoginsLastN');
    initLoginsInLastNWidget();
});

// Handling sub menu
body.on("click", ".grid-item.wan-throughput .widget-sub-menu .mac", function(e)
{
	var index = $(this).parent().index() + 1;
	$('.grid-item.wan-throughput .widget-tab-menu li:nth-child('+index+') a' ).trigger('click');
});

//tabbed menu ajax call - larger screens
body.on("click", ".grid-item.wan-throughput .widget-tab-menu .gateway-link", function(e)
{
	// Running the Chart
	runWanThroughputChart($(this));
	// Getting the gatewayName from the tab
	var gatewayName = $(this).find('.gateway-name').text();
	// Changing the selected gateway in the small widget
	$(".grid-item.wan-throughput .gateway-menu").find('.gateway-name').text(gatewayName);
	// Syncronizing small widget with big widget
	resetMenuItems(".grid-item.wan-throughput .widget-sub-menu", $(this).parent().index()+1);
});

//  Handling widget sub-menu
body.on("click", ".grid-item.average-gateway-latency .widget-sub-menu .mac", function(e)
{
	e.preventDefault();
	$('.grid-item.average-gateway-latency .btn-group').removeClass("open");
	initAverageGatewayLatencyWidget($(this));
});
//  Handling widget sub-menu
body.on("click", ".grid-item.gateway-logs .widget-sub-menu .mac", function(e)
{
	e.preventDefault();
	$('.grid-item.gateway-logs .btn-group').removeClass("open");
	initGatewayLogsWidget($(this));
});

//  Handling widget sub-menu
body.on("click", ".grid-item.gateway-speed .widget-sub-menu .mac", function(e)
{
	e.preventDefault();
	$('.grid-item.gateway-speed .btn-group').removeClass("open");
	initGatewaySpeedWidget($(this));
});


//  Handling widget sub-menu
body.on("click", ".grid-item.highest-gateway-latency .widget-sub-menu .mac", function(e)
{
	e.preventDefault();
	$('.grid-item.highest-gateway-latency .btn-group').removeClass("open");
	initHighestGatewayLatencyWidget($(this));
});

// Reseting the custom period for prtg
body.on('click', '#prtgSettingsWidget .period-buttons .period-btn:not(.prtg-custom-period)', resetPrtgCustomPeriod);

// Calling the period button function for prtg
body.on('click', '#prtgSettingsWidget .period-buttons .period-btn:not(.disabled)', setPrtgPeriodSettings);

//  Handling widget sub-menu
body.on("click", ".grid-item.latency .widget-sub-menu .mac", function(e)
{
	// Will trigger the big widget tab menu
	var index = $(this).parent().index() + 1;
	$('.grid-item.latency .widget-tab-menu li:nth-child('+index+') a' ).trigger('click');
});

//tabbed menu ajax call - larger screens
body.on("click", ".grid-item.latency .widget-tab-menu .gateway-link", function(e)
{
	// Running the Chart
	runLatencyChart($(this));
	// Showing the right chart div
	$(".grid-item.latency div[id^='latencyChart']").removeClass('active').eq($(this).parent().index()).addClass('active');
	// Getting the gatewayName from the tab
	var gatewayName = $(this).find('.gateway-name').text();
	// Changing the selected gateway in the small widget
	$(".grid-item.latency .gateway-menu").find('.gateway-name').text(gatewayName);
	// Syncronizing small widget with big widget
	resetMenuItems(".grid-item.latency .widget-sub-menu", $(this).parent().index()+1);
});