/**
 * Getting series data for area charts
 * @param obj
 * @returns {Array}
 */
function getSeries(obj)
{
	var series = [];
	$.each(obj[1], function(objIndex, objValue)
	{
		var showLegend = false;
		intValues = [];
		// Loop into Values to decide weather to show it in the legend or not
		$.each(objValue, function(index, value)
		{
			// Resetting showlegend flag
			if(index === 0)
				showLegend = false;

			// setting showLegend flag if value >0
			if(value > 0)
				showLegend = true;

			// Generating the intValues array
			intValues.push(parseInt(value));
		});

		// Pushing the int values in the series
		series.push({
			showInLegend: showLegend,
			name: objIndex ,
			data: intValues
		});
	});
	return series;
}


/**
 * Initializing Hardware widget
 */
function initHardwareGatewaysWidget(thisItem)
{
	$('#hardwareGatewaysContainer').html('');
	$('.grid-item.hardware-gateways .loading').show();
	$('.grid-item.hardware-gateways .no-data-found').hide();

	// Calling Ajax to get Dwelling time Widget data
	$.ajax({
		url: "/networking/hardware",
		type: "get",
		data: {

		},
		success: function (data) {
			$('.grid-item.hardware-gateways .loading').hide();
			$('.grid-item.hardware-gateways .no-data-found').hide();

			$('#hardwareGatewaysContainer').html(data);
		},
		error: function (data) {
		}
	});
}


/**
 * Initializing AP List widget
 */
function initApListWidget(thisItem)
{
	$('#apListContainer').html('');
	$('.grid-item.ap-list .loading').show();
	$('.grid-item.ap-list .no-data-found').hide();

	$.ajax({
		url: "/ap-list-widget",
		type: "get",
		data: {

		},
		success: function (data) {
			$('.grid-item.ap-list .loading').hide();
			$('.grid-item.ap-list .no-data-found').hide();

			$('#apListContainer').html(data);
		},
		error: function (data) {
		}
	});
}

/**
 * Initiate Accumulated Guests widget
 */
function initAccumulatedGuestsWidget()
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clear widget
	clearAccumulatedGuestsData();

	// Calling Ajax to get the accumulated guests
	$.ajax({
		url: "/json/widgets/accumulated-guests/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			if(data[0] == 0)
			{
				// No Data is back so showing the no data found div and hide the loading div
				$('.grid-item.accumulated-guests .loading').hide();
				$('.grid-item.accumulated-guests .no-data-found').show();
			}
			else
			{
				// Drawing the highchart for the accumulated guests
				drawAccumulatedGuestsChart(data);
			}
		},
		error: function (data) {
		}
	});

}

/**
 * Clearing out the Accumulated Guests Widget
 */
function clearAccumulatedGuestsData()
{
	$('.grid-item.accumulated-guests .loading').show();
	$('.grid-item.accumulated-guests .no-data-found').hide();
	$('#accumulatedGuestsContainer').hide();
}

/**
 * Draw the accumulated guests highcharts
 * @param data
 */
function drawAccumulatedGuestsChart(data)
{
	$('.grid-item.accumulated-guests .loading').hide();
	$('#accumulatedGuestsContainer').show();

	$('#accumulatedGuestsContainer').highcharts({
		chart: {
			"zoomType": "x",
			type: 'column'
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		exporting: { enabled: false },
		xAxis: {
			categories: data[0],
			title: {
				text: null
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: widgetTrans['guests'],

				style: {color: 'white'}
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		tooltip: {
			formatter: function() {
				return this.x + ':  <b>' + this.y + '</b> '+ widgetTrans['guests'];
			}
		},
		legend: {
			enabled: false
		},
		credits: {
			enabled: false
		},
		plotOptions: {
			column: {
				borderWidth: 0
			}
		},
		series: [{
			name: ' ',
			data: data[1]
		}],
		colors: ['#004966']
	});
}



/**
 * Initializing Average Gateway Latency widget
 */
function initAverageGatewayLatencyWidget(thisItem)
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();
	var mac 		= firstGatewayMac;

	if ( thisItem !== undefined)
	{
		mac = thisItem.attr("data-item-mac");
	}
	/*		else
		 {
		 resetMenuItems(".grid-item.average-gateway-latency .widget-sub-menu");
		 }*/

	var spinnerIcon = '<i class="fa fa-spinner font-size-20 fa-spin " ></i>';
	$('#averageLatencyData').html(spinnerIcon);

	// Calling Ajax to get Dwelling time Widget data
	$.ajax({
		url: "/json/widgets/average-gateway-latency/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo,
			mac:	mac,
			route:  ROUTE
		},
		success: function (data) {
			$('#averageLatencyData').html(data);
			$('#averageLatencyDesc').html("Ms");
		},
		error: function (data) {
		}
	});
}


/**
 * Initializing Average Traffic widget
 */
function initAverageTrafficWidget(thisItem)
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();


	var spinnerIcon = '<i class="fa fa-spinner font-size-20 fa-spin " ></i>';
	$('#averageTrafficData').html(spinnerIcon);

	// Calling Ajax to get Widget data
	$.ajax({
		url: "/json/widgets/average-traffic/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {

			$('#averageTrafficData').html(data);
			$('#averageTrafficDesc').html("Mbs");

		},
		error: function (data) {
		}
	});
}


/**
 * Initializing browserTrends Widget
 */
function initBrowserTrendsWidget() {

	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clearing the new guests data
	clearBrowserTrendsData();

	// Calling Ajax to get the browserTrends data
	$.ajax({
		url: "/json/widgets/browser-trends/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			var obj = jQuery.parseJSON( data);
			if(obj[0].length > 1)
			{
				drawBrowserTrendsChart([obj[0],getSeries(obj)]);
			}
			else
			{
				// No data
				$('.grid-item.browser-trends .loading').hide();
				$('.grid-item.browser-trends .no-data-found').show();
			}


		},
		error: function (data) {
		}
	});

}

/**
 * Clearing new guests data
 */
function clearBrowserTrendsData()
{
	$('.grid-item.browser-trends .loading').show();
	$('.grid-item.browser-trends .no-data-found').hide();
	$('#browserTrendsContainer').hide();
}

/**
 * Drawing browserTrends chart
 * @param data
 */
function drawBrowserTrendsChart(data)
{
	$('.grid-item.browser-trends .loading').hide();
	$('#browserTrendsContainer').show();

	$('#browserTrendsContainer').highcharts({
		chart: {
			"zoomType": "x",
			type: 'area',
			// Edit chart spacing
			spacingBottom: 15,
			spacingTop: 10,
			spacingLeft: 10,
			spacingRight: 10,

			// Explicitly tell the width and height of a chart
			width: null,
			height: null
		},
		legend: {
			enabled: true,
			floating: true,
			verticalAlign: 'top',
			align:'right',
			itemStyle: {
				'color': 'white',
				'font-weight':'normal'
			}
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		exporting: { enabled: false },
		xAxis: {
			categories: data[0],
			title: {
				text: null
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: widgetTrans['guests'],

				style: {color: 'white'}
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		tooltip: {
			formatter: function() {
				return this.x + ':  <b>' + this.y  + ' (' + this.series.name + ') ' + '</b> '+ widgetTrans['guests'];
			}
		},

		credits: {
			enabled: false
		},
		plotOptions: {
			area: {
				minPadding: 0,
				marker: {
					enabled: false,
					symbol: 'circle',
					radius: 2,
					states: {
						hover: {
							enabled: true
						}
					}
				}
			}
		},

		series:  data[1],
		colors: ['#ffaf93',
			'#ED4423',
			'#fff562',
			'#ffaef3',
			'#3c8f39',
			'#001122',
			'#7994F2',
			'#00b7ff',
			'#0eff70',
			'#ffaf0a',
			'#f1fffd']
	});
}

/**
 * Initializing browser Usage Widget
 */
function initBrowserUsageWidget() {

	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clearing the browser usage widget
	clearBrowserUsageData(period);

	// Calling Ajax to get data for browser usage widget
	$.ajax({
		url: "/json/widgets/browser-usage/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			if(data == 0)
			{
				// No Data
				$('.grid-item.browser-usage .loading').hide();
				$('.grid-item.browser-usage .no-data-found').show();
			}
			else
			{
				data = JSON.parse(data);
				highChartData = [];
				$.each(data, function( index, value )  {
					highChartData.push([ index , value ] );
				});

				// Drawing chart for Login Types
				drawBrowserUsageChart(highChartData);
			}


		},
		error: function (data) {
		}
	});
}

/**
 * Clearing the Login Types widget
 * @param period
 */
function clearBrowserUsageData(period)
{
	$('.grid-item.browser-usage .loading').show();
	$('.grid-item.browser-usage .no-data-found').hide();
	$('#browserUsageContainer').hide();
}


/**
 * Drawing the browser-usage chart
 * @param data
 */
function drawBrowserUsageChart(data)
{
	$('.grid-item.browser-usage .loading').hide();
	$('#browserUsageContainer').show();
	$('#browser-usage-info').show();

	$('#browserUsageContainer').highcharts({
		chart: {
			type: 'pie',
			options3d: {
				enabled: true,
				alpha: 45
			}
		},
		credits: {
			enabled: false
		},
		exporting: { enabled: false },
		title: {
			text: null
		},
		tooltip: {
			pointFormat: '<b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				size: 300,
				showInLegend: true,
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false
				},
				innerSize: 100,
				depth: 50,
				center: [220, 250]
			}
		},
		legend: {
			align: 'left',
			width: '500',
			maxHeight: '150',
			layout: 'horizontal',
			verticalAlign: 'center',
			x: 0,
			y: 0,
			itemMarginTop: 5,
			itemMarginBottom: 5,

			useHTML: true,
			labelFormatter: function() {
				return '<div class="white" style="font-weight: normal; text-align: left; width:115px;float:left;">' + this.name + '&nbsp;(' + parseFloat(this.percentage.toFixed(1)) + '%)</div>';
			}
		},
		series: [{
			name: ' ',
			data: data.filter(function(d) {return d[1] > 0})
		}],
		colors: ['#ffaf93',
			'#ED4423',
			'#fff562',
			'#ffaef3',
			'#3c8f39',
			'#001122',
			'#7994F2',
			'#00b7ff',
			'#0eff70',
			'#ffaf0a',
			'#f1fffd']
	});
}

/**
 * Initiate CumulativeNetIncome widget
 */
function initCumulativeNetIncomeWidget()
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clear widget
	clearCumulativeNetIncomeData();

	// Calling Ajax to get the accumulated guests
	$.ajax({
		url: "/json/widgets/cumulative-net-income/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			if(data[0] == 0)
			{
				// No Data is back so showing the no data found div and hide the loading div
				$('.grid-item.cumulative-net-income .loading').hide();
				$('.grid-item.cumulative-net-income .no-data-found').show();
			}
			else
			{
				// Drawing the highchart for the accumulated guests
				drawCumulativeNetIncomeChart(data);
			}
		},
		error: function (data) {
		}
	});

}

/**
 * Clearing out the CumulativeNetIncome Widget
 */
function clearCumulativeNetIncomeData()
{
	$('.grid-item.cumulative-net-income .loading').show();
	$('.grid-item.cumulative-net-income .no-data-found').hide();
	$('#cumulativeNetIncomeContainer').hide();
}

/**
 * Draw the CumulativeNetIncome highcharts
 * @param data
 */
function drawCumulativeNetIncomeChart(data)
{
	$('.grid-item.cumulative-net-income .loading').hide();
	$('#cumulativeNetIncomeContainer').show();

	$('#cumulativeNetIncomeContainer').highcharts({
		chart: {
			"zoomType": "x",
			type: 'area'
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		exporting: { enabled: false },
		xAxis: {
			categories: data[0],
			title: {
				text: null
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: widgetTrans['revenue'],

				style: {color: 'white'}
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		tooltip: {
			formatter: function() {
				return this.x + ':  <b> ' + currencySymbol + this.y + '</b> '+ widgetTrans['net-income'];
			}
		},
		legend: {
			enabled: false
		},
		credits: {
			enabled: false
		},
		plotOptions: {
			column: {
				borderWidth: 0
			}
		},
		series: [{
			name: ' ',
			data: data[1],
			marker: {
				enabled: false
			}
		}],
		colors: ['#004966']
	});
}




/**
 * Initiate dailyCashflow widget
 */
function initDailyCashflowWidget()
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clear widget
	clearDailyCashflowData();

	// Calling Ajax to get the accumulated guests
	$.ajax({
		url: "/json/widgets/daily-cashflow/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			if(data[0] == 0)
			{
				// No Data is back so showing the no data found div and hide the loading div
				$('.grid-item.daily-cashflow .loading').hide();
				$('.grid-item.daily-cashflow .no-data-found').show();
			}
			else
			{
				// Drawing the highchart for the accumulated guests
				drawDailyCashflowChart(data);
			}
		},
		error: function (data) {
		}
	});

}

/**
 * Clearing out the CumulativeNetIncome Widget
 */
function clearDailyCashflowData()
{
	$('.grid-item.daily-cashflow .loading').show();
	$('.grid-item.daily-cashflow .no-data-found').hide();
	$('#dailyCashflowContainer').hide();
}

/**
 * Draw the dailyCashflow highcharts
 * @param data
 */
function drawDailyCashflowChart(data)
{
	$('.grid-item.daily-cashflow .loading').hide();
	$('#dailyCashflowContainer').show();

	$('#dailyCashflowContainer').highcharts({
		chart: {
			"zoomType": "x",
			type: 'column'
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		exporting: { enabled: false },
		xAxis: {
			categories: data[0],
			title: {
				text: null
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: widgetTrans['revenue'],

				style: {color: 'white'}
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		tooltip: {
			formatter: function() {
				return this.x + ':  <b> ' + currencySymbol + this.y + '</b> '+ widgetTrans['net-income'];
			}
		},
		legend: {
			enabled: false
		},
		credits: {
			enabled: false
		},
		plotOptions: {
			column: {
				borderWidth: 0
			}
		},
		series: [{
			name: ' ',
			data: data[1],
			marker: {
				enabled: false
			}
		}],
		colors: ['#004966']
	});
}





/**
 * Initializing dataTransferred Widget
 */
function initDataTransferredWidget() {

	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clearing the new guests data
	clearDataTransferredData();

	// Calling Ajax to get the dataTransferred data
	$.ajax({
		url: "/json/widgets/data-transferred/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			if(data[0] == 0)
			{
				// No data
				$('.grid-item.data-transferred .loading').hide();
				$('.grid-item.data-transferred .no-data-found').show();
			}
			else
			{
				var obj = jQuery.parseJSON( data);

				drawDataTransferredChart([obj[0],getSeries(obj)]);
			}
		},
		error: function (data) {
		}
	});

}

/**
 * Clearing dataTransferred data
 */
function clearDataTransferredData()
{
	$('.grid-item.data-transferred .loading').show();
	$('.grid-item.data-transferred .no-data-found').hide();
	$('#dataTransferredContainer').hide();
}

/**
 * Drawing dataTransferred chart
 * @param data
 */
function drawDataTransferredChart(data)
{
	$('.grid-item.data-transferred .loading').hide();
	$('#dataTransferredContainer').show();

	$('#dataTransferredContainer').highcharts({
		chart: {
			"zoomType": "x",
			type: 'area',
			// Edit chart spacing
			spacingBottom: 15,
			spacingTop: 10,
			spacingLeft: 10,
			spacingRight: 10,

			// Explicitly tell the width and height of a chart
			width: null,
			height: null
		},
		legend: {
			enabled: true,
			floating: true,
			verticalAlign: 'top',
			align:'right',
			itemStyle: {
				'color': 'white',
				'font-weight':'normal'
			}
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		exporting: { enabled: false },
		xAxis: {
			categories: data[0],
			title: {
				text: null
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: widgetTrans['data-transferred'],

				style: {color: 'white'}
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		tooltip: {
			formatter: function() {
				return this.x + ':  <b>' + formatNumber(this.y) + '</b> Mb <b>' + this.series.name + '</b>';
			}
		},

		credits: {
			enabled: false
		},
		plotOptions: {
			area: {
				minPadding: 0,
				marker: {
					enabled: false,
					symbol: 'circle',
					radius: 2,
					states: {
						hover: {
							enabled: true
						}
					}
				}
			}
		},

		series:  data[1],
		colors: ['#ffaf93',
			'#ED4423',
			'#fff562',
			'#ffaef3',
			'#3c8f39',
			'#001122',
			'#7994F2',
			'#00b7ff',
			'#0eff70',
			'#ffaf0a',
			'#f1fffd']
	});
}



/**
 * Initiating the gateway logs widget
 * @param thisItem
 */
function initGatewayLogsWidget(thisItem)
{
	var mac 		= firstGatewayMac;
	var gatewayLogsContainer = $('#gatewayLogsContainer');
	var loadingDiv = $('.grid-item.gateway-logs .loading');
	var noDataDiv = $('.grid-item.gateway-logs .no-data-found');

	if ( thisItem !== undefined)
	{
		mac = thisItem.attr("data-item-mac");
	}

	if(mac.length > 0)
	{
		loadingDiv.show();
		noDataDiv.hide();
		gatewayLogsContainer.hide();
		// Calling Ajax to get Dwelling time Widget data
		$.ajax({
			url: "/json/widgets/gateway-logs/0/get-report-data",
			type: "get",
			timeout: 20000,
			data: {
				mac: mac
			},
			success: function (data) {
				loadingDiv.hide();
				noDataDiv.hide();
				data = $.parseJSON(data);

				$('#gatewayLogsContainer').html(data.contents);
				gatewayLogsContainer.show();
				gatewayLogsDatatable();

			},
			error: function (data) {
				loadingDiv.hide();
				noDataDiv.show().find('.description').html(widgetTrans['gateway-connection-error']);
			}
		});
	}
	else
	{
		loadingDiv.hide();
		noDataDiv.hide();
	}
}

function gatewayLogsDatatable()
{
	$('#gateway-logs-table').dataTable({
		"order"     : [0, "desc"],
		"aoColumns" : [
			null,
			null,
			null,
			{"bVisible" : false, "bSortable" : false }
		]
	});
}


function initGatewayControlWidget() {
	var loadingDiv 	= $('.grid-item.gateway-control .loading');
	var noDataDiv 	= $('.grid-item.gateway-control .no-data-found');

	$('.action_aaa').each(function(){
		getAaaStatus($(this));
	});
	loadingDiv.hide();
	noDataDiv.hide();
}

/**
 * Gets the gateway status
 * @param thisItem
 */
function getAaaStatus(thisItem)
{
	// Showing the loading icon
	thisItem.parent().find('a').addClass('disabled');
	thisItem.parent().find('.fa').addClass('fa-spinner fa-spin text-default').removeClass('text-success text-danger aaa').text('');

	$.ajax({
		url: "/json/widgets/gateway-control/0/get-report-data",
		type: "get",
		timeout: 30000,
		data: {
			mac: thisItem.data('mac'),
			route: ROUTE
		},
		success: function (data) {

			if(data == 1)
			{
				thisItem.parent().find('a').removeClass('disabled');
				thisItem.parent().find('.gateway-reboot').removeClass('fa-spinner fa-spin text-default').addClass('text-danger fa-power-off ').prop('title', trans["reboot"]);

				thisItem.data('status', 'disabled');
				thisItem.parent().find('.gateway-aaa').addClass('text-danger aaa').text('AAA');
				thisItem.prop('title', trans["enable-aaa"]);
			}
			else if(data == 0)
			{
				thisItem.parent().find('a').removeClass('disabled');
				thisItem.parent().find('.gateway-reboot').removeClass('fa-spinner fa-spin text-default').addClass('fa-power-off text-danger').prop('title', trans["reboot"]);

				thisItem.data('status', 'enabled');
				thisItem.parent().find('.gateway-aaa').addClass('text-success aaa').text('AAA');
				thisItem.prop('title', trans["disable-aaa"]);
			}
			else
			{
				thisItem.find('.fa').addClass('fa-ban text-danger').prop('title', data);
			}

			thisItem.parent().find('.fa').removeClass('fa-spinner fa-spin text-default');
		},
		error: function (data) {
			thisItem.parent().find('.fa').removeClass('fa-spinner fa-spin text-default');
			thisItem.find('.fa').addClass('fa-ban text-danger').prop('title', widgetTrans['gateway-connection-error']);
		}
	});
}




/**
 * Initializing browserTrends Widget
 */
function initGatewaySpeedWidget(thisItem) {
	var mac = firstGatewayMac;

	if ( thisItem !== undefined)
	{
		mac = thisItem.attr("data-item-mac");
	}
	clearGatewaySpeedData();

	if(mac != '')
	{
		// Calling Ajax to get the data
		$.ajax({
			url: "/json/widgets/gateway-speed/0/get-report-data",
			type: "get",
			data: {
				mac: mac
			},
			success: function (data) {
				if(data != 0)
				{
					drawGatewaySpeedChart(data[0], $('#gatewayUpSpeedContainer'), widgetTrans['upload']);
					drawGatewaySpeedChart(data[1], $('#gatewayDownSpeedContainer'), widgetTrans['download']);
				}
				else
				{
					// No data
					$('.grid-item.gateway-speed .loading').hide();
					$('.grid-item.gateway-speed .no-data-found').show();
				}


			},
			error: function (data) {
			}
		});
	}
	else
	{
		// No gateways
		$('.grid-item.gateway-speed .loading').hide();
		$('.grid-item.gateway-speed .no-data-found').hide();
	}
}

/**
 * Clearing new guests data
 */
function clearGatewaySpeedData()
{
	$('.grid-item.gateway-speed .loading').show();
	$('.grid-item.gateway-speed .no-data-found').hide();
	$('#gatewayUpSpeedContainer').hide();
	$('#gatewayDownSpeedContainer').hide();
}

/**
 * Drawing Gateway Speed chart
 * @param data
 */
function drawGatewaySpeedChart(data, container, title)
{
	$('.grid-item.gateway-speed .loading').hide();
	container.show();

	container.highcharts({
		chart: {
			type: 'gauge',
			// Edit chart spacing
			spacingBottom: 0,
			spacingTop: 0,
			spacingLeft: 0,
			spacingRight: 0,

			// Explicitly tell the width and height of a chart
			width: 236,
			height: 236,
			plotBackgroundColor: null,
			plotBackgroundImage: null,
			plotBorderWidth: 0,
			plotShadow: false,
			margin: 30
		},

		title: {
			text: title,
			floating: true,
			align: 'center',
			x: 0,
			y: 25
		},
		subtitle: {
			text: ''
		},
		exporting: { enabled: false },
		credits: {
			enabled: false
		},
		pane: {
			startAngle: -150,
			endAngle: 150,
			background: [{
				backgroundColor: {
					linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
					stops: [
						[0, '#FFF'],
						[1, '#333']
					]
				},
				borderWidth: 0,
				outerRadius: '109%'
			}, {
				backgroundColor: {
					linearGradient: { x1: 0, y1: 0, x2: 0, y2: 1 },
					stops: [
						[0, '#333'],
						[1, '#FFF']
					]
				},
				borderWidth: 1,
				outerRadius: '107%'
			}, {
				// default background
			}, {
				backgroundColor: '#DDD',
				borderWidth: 0,
				outerRadius: '105%',
				innerRadius: '103%'
			}]
		},

		// the value axis
		yAxis: {
			min: 0,
			max: data[3],

			minorTickInterval: 'auto',
			minorTickWidth: 1,
			minorTickLength: 10,
			minorTickPosition: 'inside',
			minorTickColor: '#666',

			tickPixelInterval: 30,
			tickWidth: 2,
			tickPosition: 'inside',
			tickLength: 10,
			tickColor: '#666',
			labels: {
				step: 2,
				rotation: 'auto'
			},
			title: {
				text: 'mb/s',
				y: 105
			},
			plotBands: [{
				from: 0,
				to: data[1],
				color: '#DF5353' // red

			}, {
				from: data[1],
				to: data[2],
				color: '#DDDF0D' // yellow
			}, {
				from: data[2],
				to: data[3],
				color: '#55BF3B' // green
			}]
		},

		series: [{
			name: 'Speed',
			data: [data[0]],
			tooltip: {
				valueSuffix: ' mb/s'
			}
		}]

	});
}


/**
 * Initializing Site List widget
 */
function initSiteListWidget(thisItem)
{
	$('.grid-item.site-list .loading').show();
	$('.grid-item.site-list .no-data-found').hide();

	// Calling Ajax to get Dwelling time Widget data
	$.ajax({
		url: "/site-list-widget",
		type: "get",
		data: {

		},
		success: function (data) {
			$('.grid-item.site-list .loading').hide();
			$('.grid-item.site-list .no-data-found').hide();

			$('#siteListContainer').html(data);
		},
		error: function (data) {
		}
	});
}

/**
 * Initializing Highest Gateway Latency widget
 */
function initHighestGatewayLatencyWidget(thisItem)
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();
	var mac 		= firstGatewayMac;

	if ( thisItem !== undefined)
	{
		mac = thisItem.attr("data-item-mac");
	}
	/*
else
{
resetMenuItems(".grid-item.highest-gateway-latency .widget-sub-menu");
}
*/


	var spinnerIcon = '<i class="fa fa-spinner font-size-20 fa-spin " ></i>';
	$('#highestLatencyData').html(spinnerIcon);

	// Calling Ajax to get Dwelling time Widget data
	$.ajax({
		url: "/json/widgets/highest-gateway-latency/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo,
			mac:	mac
		},
		success: function (data) {
			$('#highestLatencyData').html(data);
			$('#highestLatencyDesc').html("Ms");
		},
		error: function (data) {
		}
	});
}

/**
 * Initiate Demographics Widget
 */

function initDemographicsWidget(siteId) {


	if(ROUTE != "dashboard")
	{
		var period 		= $("#period").val();
		var periodFrom 	= $("#period-from").val();
		var periodTo 	= $("#period-to").val();

		// Clearing the Demographics Widget
		clearDemographicsData(siteId);

		// Calling Ajax to retrieve the data for  Demographics Widget
		$.ajax({
			url: "/json/widgets/demographics/0/get-report-data",
			type: "get",
			data: {
				period: period,
				from:	periodFrom,
				to:		periodTo
			},
			success: function (data) {
				// Filling the Demographics Widget
				fillDemographicsData(data);
			},
			error: function (data) {
			}
		});
	}
	else
	{ // Dashboard data comes from the view composer DashboardDataComposer

		if(siteId === undefined)
		{
			fillDemographicsData(demographicsData);
		}
		else
		{
			// Clearing the Demographics Widget
			clearDemographicsData(siteId);
			// Calling Ajax to retrieve the data for  Demographics Widget
			$.ajax({
				url: "/json/widgets/demographics/0/get-demographics-data",
				type: "get",
				data: {
					site: siteId,
					route: ROUTE
				},
				success: function (data) {
					// Filling the Demographics Widget
					fillDemographicsData(data, siteId);
				},
				error: function (data) {
				}
			});
		}
	}
}

/**
 * Filling Demographics Widget with data
 * @param data
 */
function fillDemographicsData(data, siteId) {
	var demoData = [];
	var demoSelector = $('.demographicsBody');

	if (siteId === undefined)
		demoSelector = $('#demographicsBody');

	demoSelector.html('');

	// Looping into the data to filter out the 0 values
	$.each(data, function (index, value) {
		// First value is always the sum of the registered users
		if (parseInt(value[0]) > 0) {
			demoData.push(value);
		}
	});

	// Filling the widget and resizing it depending on the count of the Demographics data
	if (demoData.length == 0) // When no data
	{
		if (siteId === undefined)
			demoSelector.html('<div class="white text-center margin-top-70">' + widgetTrans['no-data-found'] + '</div>');
		else
			demoSelector.html('<div class="text-center">' + widgetTrans['no-data-found'] + '</div>');
	}
	else if (demoData.length == 2) // When only 1 login method, show the loging type only
		$.each(demoData, function (index, value) {
			if (index != 0)
				demoSelector.append(addDemoHtml(value, 'super-demo'));
		});
	else if (demoData.length == 3) // when 2 login methods are used
	{
		$.each(demoData, function (index, value) {
			if (index == 0)
				demoSelector.append(addDemoHtml(value, 'big-demo'));

			else
				demoSelector.append(addDemoHtml(value, 'normal-demo'));

		});
	}
	else {
		$.each(demoData, function (index, value) {
			if (index == 0)
				demoSelector.append(addDemoHtml(value, 'big-demo'));
			else
				demoSelector.append(addDemoHtml(value, 'small-demo'));

		});
	}

	/* list of the sizes from the system.css file
.grid-item--height2 { height:530px!important; }
.grid-item--height3 { height:810px!important; }
.grid-item--height4 { height:1088px!important; }
*/
	if (siteId === undefined)
	{
		if (demoData.length > 3)
			$('.grid-item.demographics').removeClass("grid-item--height1 grid-item--height3 grid-item--height4").addClass("grid-item--height2");
		else
			$('.grid-item.demographics').removeClass("grid-item--height2 grid-item--height3 grid-item--height4").addClass("grid-item--height1");


		// Rebuild the packery grid because the size of the widget may have changed
		$('.widgets-editor .grid').packery('layout');
	}

}

/**
 * Adding Demographics item to the widget
 * @param value
 * @param className
 * @returns {string}
 */
function addDemoHtml(value , className)
{
	var hr = '';
	if (className == 'big-demo')
		hr = '<hr class="margin-0">';

	return '<div class="demo-item"><div class="'+className+'">' +
		'<div class="demo-icon-container ">' +
		'<i class="demo-icon fa ' + value[1] + ' "></i>' +
		'</div>' +
		'<div class="demo-details center-text">' +
		'<div class=" demo-value">' +value[0] +'</div><div class=" demo-desc">' +value[2] + '</div>' +
		'</div>' +
		'</div></div>' + hr;
}

/**
 * Clearing the Demographics widget
 */
function clearDemographicsData(siteId)
{
	if(siteId === undefined)
		$('#demographicsBody').html('<i class="fa fa-spinner font-size-20 fa-spin white" style="margin-top: 85px; margin-left: 100px;"></i>');
	else
		$('.demographicsBody').html('<i class="fa fa-spinner font-size-20 fa-spin " style="margin-top: 30px; margin-left: 60px;"></i>');

}


/**
 * Initialize Dwelling time Widget
 */
function initDwellTimeWidget()
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clearing Dwelling time Widget
	clearDwellTimeData(period);

	// Calling Ajax to get Dwelling time Widget data
	$.ajax({
		url: "/json/widgets/dwell-time/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			showDwellTimeData(data, period);
		},
		error: function (data) {
		}
	});
}

/**
 * Clearing Dwelling time Widget
 * @param period
 */
function clearDwellTimeData(period)
{
	var spinnerIcon = '<i class="fa fa-spinner font-size-20 fa-spin white" ></i>';
	$('#dwellTimeData').html(spinnerIcon);
}

/**
 * Showing Dwelling time Widget data
 * @param data
 * @param period
 */
function showDwellTimeData(data, period)
{
	$('.grid-item.dwell-time .loading').hide();
	$('#dwellTimeData').html(data + '</br>'  + widgetTrans['hours'] );
}

/**
 * Initializing Latency widget
 */
function initLatencyWidget()
{
	$('.grid-item.latency .loading').show();
	$('.grid-item.latency .no-data-found').hide();

	// Making first chart active
	$("div[id^='latencyChart']").removeClass('active').first().addClass('active');

	// Trigger select first gateway click or the only gateway
	if($(".grid-item.latency .widget-tab-menu .gateway-link:first").length)
		$(".grid-item.latency .widget-tab-menu .gateway-link:first").trigger('click');
	else
		drawChart('latency', 'latencyChart0', firstGatewayMac );
}



/**
 *  Running the latency chart
 * @param thisItem
 */
function runLatencyChart(thisItem)
{
	var id = thisItem.attr("data-item-index");
	var mac = thisItem.attr("data-item-mac");
	$('.grid-item.latency .loading').show();
	$('.grid-item.latency .no-data-found').hide();

	$('.grid-item.latency #latency-chart .highcharts-container ').hide();
	drawChart('latency', id, mac );
}

/**
 * Creates the Chart for the latency
 * @param type
 * @param id
 * @param mac
 */
function drawChart(type, id, mac)
{
	var url = "/json/widgets/"+ type + "/0/charts/"+ mac;
	//Route::get('{type}/{mac?}/{siteId?}/{startDate?}/{endDate?}', 	'\App\Admin\Json\Charts\Controller@chart');
	if(ROUTE != "dashboard")
	{
		var period 		= $("#period").val();
		var periodFrom 	= $("#period-from").val();
		var periodTo 	= $("#period-to").val();
		var newDate 	=  new Date();
		var today 		= $.datepicker.formatDate('yy-mm-dd', newDate);


		switch(period) {
			case "last-24-hours":
				var yesterday = $.datepicker.formatDate('yy-mm-dd', new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate() - 1));
				url = url + "/" + SITE_ID + "/" + yesterday  + "/" + today + "/true";
				break;
			case "last-week":
				var lastWeek = $.datepicker.formatDate('yy-mm-dd', new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate() - 7));
				url = url + "/" + SITE_ID + "/" + lastWeek  + "/" + today ;
				break;
			case "last-month":
				var lastMonth = $.datepicker.formatDate('yy-mm-dd', new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate() - 30));
				url = url + "/" + SITE_ID + "/" + lastMonth  + "/" + today ;
				break;
			case "last-year":
				var lastYear = $.datepicker.formatDate('yy-mm-dd', new Date(newDate.getFullYear(), newDate.getMonth(), newDate.getDate() - 365));
				url = url + "/" + SITE_ID + "/" + lastYear  + "/" + today ;
				break;
			default:
				url = url + "/" + SITE_ID + "/" + periodFrom  + "/" + periodTo ;
		}
	}

	$.ajax({
		url: url,
		type: "get",
		success: function (data) {
			$('.grid-item.latency .loading').hide();
			$('.grid-item.latency .widget-chart-container').show();
			$('.grid-item.latency #latency-chart .highcharts-container ').show();
			$('#' + id).highcharts(JSON.parse(data));
		},
		error: function (data) {
		}
	}); //end  $.ajax
}


/**
 * Initializing Login Types Widget
 */
function initLoginTypesWidget() {

	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clearing the Login Types widget
	clearLoginTypeData(period);

	// Calling Ajax to get data for Login Types widget
	$.ajax({
		url: "/json/widgets/login-types/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo,
			route:	ROUTE
		},
		success: function (data) {
			data = JSON.parse(data);
			// If the array is empty then there are no login types and we can't display a chart
			if(data.length === 0)
			{
				// No Data
				$('.grid-item.login-types .loading').hide();
				$('.grid-item.login-types .no-data-found').show();
			}
			else
			{
				// Drawing chart for Login Types
				drawLoginTypesChart(data);
			}
		},
		error: function (data) {
		}
	});
}

/**
 * Clearing the Login Types widget
 * @param period
 */
function clearLoginTypeData(period)
{
	$('.grid-item.login-types .loading').show();
	$('.grid-item.login-types .no-data-found').hide();
	$('#loginTypesContainer').hide();
}

/**
 * Drawing the Login Types chart
 * @param data
 */
function drawLoginTypesChart(data)
{
	$('.grid-item.login-types .loading').hide();
	$('#loginTypesContainer').show();
	$('#login-types-info').show();

	var highChartData = [];
	$.each(data, function( index, value )  {
		highChartData.push([ index , value ] );
	});

	$('#loginTypesContainer').highcharts({
		chart: {
			type: 'pie',
			options3d: {
				enabled: true,
				alpha: 45
			}
		},
		credits: {
			enabled: false
		},
		exporting: { enabled: false },
		title: {
			text: null
		},
		tooltip: {
			headerFormat: '<b>{point.key}: {point.y}</b><br>',
			pointFormat: '{point.percentage:.1f}%'
		},
		plotOptions: {
			pie: {
				showInLegend: true,
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false
				},
				innerSize: 95,
				depth: 45
			}
		},
		legend: {
			align: 'left',
			layout: 'vertical',
			verticalAlign: 'top',
			x: 50,
			y: 30,
			itemMarginTop: 5,
			itemMarginBottom: 5,
			useHTML: true,
			labelFormatter: function() {
				return '<div class="white" style="font-weight: normal; width:130px;float:left;display:inline-block;"><span>' + this.name + ':&nbsp;(' + parseFloat(this.percentage).toFixed(1) + '%)</span><div style="float:right;">' + String(this.y) + '</div></div>';
			}
		},
		series: [{
			name: ' ',
			data: highChartData.filter(function(dataItem) {return dataItem[1] > 0}).sort(function(firstItem, secondItem) {return secondItem[1] - firstItem[1];}).map(function(dataItem){ return [dataItem[0], dataItem[1]];})
		}],
		colors: ['#004966',
			'#ED4423',
			'#00678f',
			'#58398f',
			'#3c8f39',
			'#001122',
			'#7994F2',
			'#0079A8',
			'#3F4E7F',
			'#005566',
			'#004966']
	});
}

/**
 * Initializing OS Usage Widget
 */
function initOsUsageWidget() {

	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clearing the os usage widget
	clearOsUsageData(period);

	// Calling Ajax to get data for OS usage widget
	$.ajax({
		url: "/json/widgets/os-usage/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			if(data == 0)
			{
				// No Data
				$('.grid-item.os-usage .loading').hide();
				$('.grid-item.os-usage .no-data-found').show();
			}
			else
			{
				data = JSON.parse(data);
				highChartData = [];
				$.each(data, function( index, value )  {
					highChartData.push([ index , value ] );
				});

				// Drawing chart for Login Types
				drawOsUsageChart(highChartData);
			}


		},
		error: function (data) {
		}
	});
}

/**
 * Clearing the Login Types widget
 * @param period
 */
function clearOsUsageData(period)
{
	$('.grid-item.os-usage .loading').show();
	$('.grid-item.os-usage .no-data-found').hide();
	$('#osUsageContainer').hide();
}


/**
 * Drawing the os-usage chart
 * @param data
 */
function drawOsUsageChart(data)
{
	$('.grid-item.os-usage .loading').hide();
	$('#osUsageContainer').show();
	$('#os-usage-info').show();

	$('#osUsageContainer').highcharts({
		chart: {
			type: 'pie',
			options3d: {
				enabled: true,
				alpha: 45
			}
		},
		credits: {
			enabled: false
		},
		exporting: { enabled: false },
		title: {
			text: null
		},
		tooltip: {
			pointFormat: '<b>{point.percentage:.1f}%</b>'
		},
		plotOptions: {
			pie: {
				size: 300,
				showInLegend: true,
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false
				},
				innerSize: 100,
				depth: 50,
				center: [220, 250]
			}
		},
		legend: {
			align: 'left',
			width: '500',
			maxHeight: '150',
			layout: 'horizontal',
			verticalAlign: 'center',
			x: 0,
			y: 0,
			itemMarginTop: 5,
			itemMarginBottom: 5,

			useHTML: true,
			labelFormatter: function() {
				return '<div class="white" style="font-weight: normal; text-align: left; width:115px;float:left;">' + this.name + '&nbsp;(' + parseFloat(this.percentage.toFixed(1)) + '%)</div>';
			}
		},
		series: [{
			name: ' ',
			data: data.filter(function(d) {return d[1] > 0})
		}],
		colors: ['#ffaf93',
			'#ED4423',
			'#fff562',
			'#ffaef3',
			'#3c8f39',
			'#001122',
			'#7994F2',
			'#00b7ff',
			'#0eff70',
			'#ffaf0a',
			'#f1fffd']
	});
}


/**
 * Initializing Net Packages widget
 */
function initNetPackagesWidget(thisItem)
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();


	var spinnerIcon = '<i class="fa fa-spinner font-size-20 fa-spin " ></i>';
	$('#paidPackageData').html(spinnerIcon);
	$('#freePackageData').html(spinnerIcon);

	// Calling Ajax to get Widget data
	$.ajax({
		url: "/json/widgets/net-packages/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo,
			route:	ROUTE
		},
		success: function (data) {

			$('#paidPackageData').html(data[0]);
			$('#freePackageData').html(data[1]);

		},
		error: function (data) {
		}
	});
}


/**
 * Initializing Most Used Package widget
 */
function initMostUsedPackageWidget(thisItem)
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();


	var spinnerIcon = '<i class="fa fa-spinner font-size-20 fa-spin " ></i>';
	$('#mostUsedPackageData').html(spinnerIcon);

	// Calling Ajax to get Widget data
	$.ajax({
		url: "/json/widgets/most-used-package/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {

			$('#mostUsedPackageData').html(data);

		},
		error: function (data) {
		}
	});
}
/**
 * Initializing Net Income widget
 */
function initNetIncomeWidget(thisItem)
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();


	var spinnerIcon = '<i class="fa fa-spinner font-size-20 fa-spin " ></i>';
	$('#netIncomeData').html(spinnerIcon);

	// Calling Ajax to get Widget data
	$.ajax({
		url: "/json/widgets/net-income/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {

			$('#netIncomeData').html(data);

		},
		error: function (data) {
		}
	});
}

/**
 * Initializing Net Income widget
 */
function initAverageNetIncomeWidget(thisItem)
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();


	var spinnerIcon = '<i class="fa fa-spinner font-size-20 fa-spin " ></i>';
	$('#averageNetIncomeData').html(spinnerIcon);

	// Calling Ajax to get Widget data
	$.ajax({
		url: "/json/widgets/average-net-income/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo,
			route:	ROUTE
		},
		success: function (data) {

			$('#averageNetIncomeData').html(data);

		},
		error: function (data) {
		}
	});
}

/**
 * Initializing New Guests Widget
 */
function initNewGuestsWidget() {

	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clearing the new guests data
	clearNewGuestsData();

	// Calling Ajax to get the new guests data
	$.ajax({
		url: "/json/widgets/new-guests/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			if(data[0] == 0)
			{
				// No data
				$('.grid-item.new-guests .loading').hide();
				$('.grid-item.new-guests .no-data-found').show();
			}
			else
			{
				// Draw new guests chart
				drawNewGuestsChart(data);
			}
		},
		error: function (data) {
		}
	});

}

/**
 * Clearing new guests data
 */
function clearNewGuestsData()
{
	$('.grid-item.new-guests .loading').show();
	$('.grid-item.new-guests .no-data-found').hide();
	$('#newGuestsContainer').hide();
}

/**
 * Drawing new guests chart
 * @param data
 */
function drawNewGuestsChart(data)
{
	$('.grid-item.new-guests .loading').hide();
	$('#newGuestsContainer').show();

	$('#newGuestsContainer').highcharts({
		chart: {
			"zoomType": "x",
			type: 'area'
		},
		legend: {
			enabled: true,
			floating: true,
			verticalAlign: 'top',
			align:'right',
			itemStyle: {
				"color": "white",
				"font-weight":"normal"
			}
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		exporting: { enabled: false },
		xAxis: {
			categories: data[0],
			title: {
				text: null
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: widgetTrans['guests'],

				style: {color: 'white'}
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		tooltip: {
			formatter: function() {
				return this.x + ':  <b>' + this.y + '</b> '+ widgetTrans['guests'];
			}
		},

		credits: {
			enabled: false
		},
		plotOptions: {
			area: {
				minPadding: 0,
				marker: {
					enabled: false,
					symbol: 'circle',
					radius: 2,
					states: {
						hover: {
							enabled: true
						}
					}
				}
			}
		},

		series: [
			{
				name: widgetTrans['previous'],
				data: data[2]
			},
			{
				name: widgetTrans['current'],
				data: data[1]
			}
		],
		colors: [
			'#d7a635',
			'#004966']
	});
}


/**
 * Initializing Login in last n hours/days/weeks Widget
 */
function displayLoginsInLastNWidget(data) {

	var widgetElement = $('.grid-item.logins-in-last-n');
    widgetElement.find('.no-data-found').hide();

    var baseContainer = $('#loginsInLastNContainer');
    var siteLogins = baseContainer.find('#siteLoginsInLastN');

    $.each(data, function (index, periods) {
    	var periodstate;
    	// Have a visual indication for any count which is zero
    	if (periods['site'] === 0) {
			loginState = "list-group-item-danger";
		} else {
			loginState = "list-group-item-success";
		}

		// Show the login count for the period
		siteLogins.append('<li class="list-group-item ' + loginState + '">' + periods['label'] + '<span class="badge badge-default badge-pill">' + periods['site'] + '</span></li>');
	});

	var allLogins = baseContainer.find('#allLoginsInLastN');

	$.each(data, function (index, periods) {
		var loginState;
		// Have a visual indication for any count which is zero
		if (periods['all'] === 0) {
			loginState = "list-group-item-danger";
		} else {
			loginState = "list-group-item-success";
		}

		// Show the login count for the period
		allLogins.append('<li class="list-group-item ' + loginState + '">' + periods['label'] + '<span class="badge badge-default badge-pill">' + periods['all'] + '</span></li>');
	});

	widgetElement.find('.loading').hide();

}


/**
 * Initializing Login in last n hours/days/weeks Widget
 */
function initLoginsInLastNWidget() {

    var period 		= $("#period").val();
    var periodFrom 	= $("#period-from").val();
    var periodTo 	= $("#period-to").val();

    // Clearing the "logins in last n" data
    //clearLoginsInLastNData();

    // Calling Ajax to get the "logins in last n" data
    $.ajax({
        url: "/json/widgets/logins-in-last-n/0/get-report-data",
        type: "get",
        data: {
            period: period,
            from:	periodFrom,
            to:		periodTo
        },
        success: function (data) {
            if(data.length === 0)
            {
                // No data
                $('.grid-item.logins-in-last-n .loading').hide();
                $('.grid-item.logins-in-last-n .no-data-found').show();
            }
            else
            {
                // Draw logins data
                displayLoginsInLastNWidget(data);
            }
        },
        error: function (data) {
        }
    });

}


/**
 * Initializing osTrends Widget
 */
function initOsTrendsWidget() {

	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clearing the new guests data
	clearOsTrendsData();

	// Calling Ajax to get the osTrends data
	$.ajax({
		url: "/json/widgets/os-trends/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			var obj = jQuery.parseJSON( data);
			var intValues = [];
			if(obj[0].length > 1)
			{
				drawOsTrendsChart([obj[0],getSeries(obj)]);
			}
			else
			{
				// No data
				$('.grid-item.os-trends .loading').hide();
				$('.grid-item.os-trends .no-data-found').show();
			}

		},
		error: function (data) {
		}
	});

}

/**
 * Clearing new guests data
 */
function clearOsTrendsData()
{
	$('.grid-item.os-trends .loading').show();
	$('.grid-item.os-trends .no-data-found').hide();
	$('#osTrendsContainer').hide();
}

/**
 * Drawing osTrends chart
 * @param data
 */
function drawOsTrendsChart(data)
{
	$('.grid-item.os-trends .loading').hide();
	$('#osTrendsContainer').show();

	$('#osTrendsContainer').highcharts({
		chart: {
			"zoomType": "x",
			type: 'area',
			// Edit chart spacing
			spacingBottom: 15,
			spacingTop: 10,
			spacingLeft: 10,
			spacingRight: 10,

			// Explicitly tell the width and height of a chart
			width: null,
			height: null
		},
		legend: {
			enabled: true,
			floating: true,
			verticalAlign: 'top',
			align:'right',
			itemStyle: {
				'color': 'white',
				'font-weight':'normal'
			}
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		exporting: { enabled: false },
		xAxis: {
			categories: data[0],
			title: {
				text: null
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: widgetTrans['guests'],

				style: {color: 'white'}
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		tooltip: {
			formatter: function() {
				return this.x + ':  <b>' + this.y  + ' (' + this.series.name + ') ' + '</b> '+ widgetTrans['guests'];
			}
		},

		credits: {
			enabled: false
		},
		plotOptions: {
			area: {
				minPadding: 0,
				marker: {
					enabled: false,
					symbol: 'circle',
					radius: 2,
					states: {
						hover: {
							enabled: true
						}
					}
				}
			}
		},

		series:  data[1],
		colors: ['#ffaf93',
			'#ED4423',
			'#fff562',
			'#ffaef3',
			'#3c8f39',
			'#001122',
			'#7994F2',
			'#00b7ff',
			'#0eff70',
			'#ffaf0a',
			'#f1fffd']
	});
}



/**
 * Initiate Package Sales Income widget
 */
function initPackageSalesIncomeWidget()
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clear widget
	clearPackageSalesIncomeData();

	// Calling Ajax to get the accumulated guests
	$.ajax({
		url: "/json/widgets/package-sales-income/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			if(data[0] == 0)
			{
				// No Data is back so showing the no data found div and hide the loading div
				$('.grid-item.package-sales-income .loading').hide();
				$('.grid-item.package-sales-income .no-data-found').show();
			}
			else
			{
				// Drawing the highchart for the accumulated guests
				drawPackageSalesIncomeChart(data);
			}
		},
		error: function (data) {
		}
	});

}

/**
 * Clearing out the CumulativeNetIncome Widget
 */
function clearPackageSalesIncomeData()
{
	$('.grid-item.package-sales-income .loading').show();
	$('.grid-item.package-sales-income .no-data-found').hide();
	$('#packageSalesIncomeContainer').hide();
}

/**
 * Draw the PackageSalesIncome highcharts
 * @param data
 */
function drawPackageSalesIncomeChart(data)
{
	$('.grid-item.package-sales-income .loading').hide();
	$('#packageSalesIncomeContainer').show();

	$('#packageSalesIncomeContainer').highcharts({
		chart: {
			"zoomType": "x",
			type: 'bar'
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		exporting: { enabled: false },
		xAxis: {
			categories: data[0],
			title: {
				text: null
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: widgetTrans['income']+" ("+currencySymbol+")",

				style: {color: 'white'}
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		legend: {
			enabled: true,
			itemStyle: {
				"color": "white",
				"font-weight":"normal"
			}
		},
		credits: {
			enabled: false
		},
		plotOptions: {
			column: {
				borderWidth: 0
			}
		},
		series: [{
			name: widgetTrans['sales'],
			data: data[1],
			"tooltip": {
				"valueSuffix": " " + widgetTrans['packages'] }
		},
			{
				name: widgetTrans['income'],
				data: data[2],
				"tooltip": {
					"valuePrefix": currencySymbol
				}
			}],
		colors: ['#004966',
			'#00b1f7']
	});
}





/**
 * Initializing Registered Users widget
 */
function initRegUsersWidget()
{
	var period 		= $("#period").val();
	var periodFrom 	= $("#period-from").val();
	var periodTo 	= $("#period-to").val();

	// Clearing the reg users data
	clearRegUsersData(period);

	// Calling ajax to get reg users data
	$.ajax({
		url: "/json/widgets/registered-users/0/get-report-data",
		type: "get",
		data: {
			period: period,
			from:	periodFrom,
			to:		periodTo
		},
		success: function (data) {
			showRegUsersData(data, period);
		},
		error: function (data) {
		}
	});
}

/**
 * Clearing reg users data
 */
function clearRegUsersData(period)
{
	var spinnerIcon = '<i class="fa fa-spinner font-size-20 fa-spin white" ></i>';
	$('#registered-users-data').html(spinnerIcon);
	$('#registered-users-period').text( widgetTrans['registered-users-info'] + ' - ' + period );
	$('#registered-users-trend').text( '' );

	$("#trend-icon").hide();
}

/**
 * Showing reg users data
 * @param data
 * @param period
 */
function showRegUsersData(data, period)
{
	$('#registered-users-data').text( data[0] );
	//$('#trend-data').text( data[1] + "%");

	if(period == "custom-period" || data[0] == 0 || data[1] == 0)
	{
		$('#registered-users-period').text( widgetTrans['registered-users-info']);
	}
	else
	{
		$('#registered-users-trend').text( '(' + data[1] + '%)');

		if(data[1] > 0)
			$("#trend-icon").addClass('fa-arrow-up').removeClass('fa-arrow-down').show();
		else
			$("#trend-icon").addClass('fa-arrow-down').removeClass('fa-arrow-up').show();
	}
}



/**
 * Hide the reports settings bar
 */
function hideReportSettingsBar()
{
	$('#reports-settings').hide();
}


/**
 * Show the reports settings bar
 */
function showReportSettingsBar()
{
	$('#reports-settings').show();
}


/**
 * Update the report details then re-initialise each widget that is not inactive
 * @param period
 */
function updateReportWidgets(period)
{
	updateReportDetails(period);

	$.xhrPool.abortAll();

	$(".grid-item").not(".inactive").each(function(){
		$(this).trigger('initWidget');
	});
}


/**
 * Setting the report widget period data
 * @param clickedBtn The button (html element) which has been clicked.
 * 					We should never get here if the button is disabled (click event is not propagated).
 */
function setPeriodSettings(clickedBtn)
{
	if($(clickedBtn).attr('disabled') !== 'disabled') {
		var periodFromInput 	= $("#period-from");
		var periodToInput 		= $("#period-to");
		var periodInput 		= $("#period");

		var period;
		period = $(clickedBtn).find('input').attr('id');
		periodInput.val(period);

		updateReportWidgets(period);
	}

}

/**
 * Setting the Settings widget data
 */
function setPrtgPeriodSettings()
{
	if($(this).attr('disabled') == 'disabled')
		return false;

	var periodFromInput 	= $("#prtg-period-from");
	var periodToInput 		= $("#prtg-period-to");
	var periodInput 		= $("#prtg-period");

	var period;
	period = $(this).find('input').attr('default');
	periodInput.val(period);

	updatePrtgReportDetails(period);

	$.xhrPool.abortAll();

	$(".grid-item").not(".inactive").each(function() {
		$(this).trigger('initWidget');
	});
}


/**
 * Resetting the custom period, particularly the from and to dates
 */
function resetCustomPeriod()
{
	$("#period-from").val('');
	$("#period-to").val('');
	$('.ui-datepicker-calendar td').removeClass('dp-highlight');
	$('.ui-datepicker-calendar td a').removeClass(' ui-state-active ui-state-highlight');
	//$(".custom-period").attr("disabled", true);
}

/**
 * Resetting the PRTG custom period
 */
function resetPrtgCustomPeriod()
{
	$("#prtg-period-from").val('');
	$("#prtg-period-to").val('');
	$('.ui-datepicker-calendar td').removeClass('dp-highlight');
	$('.ui-datepicker-calendar td a').removeClass(' ui-state-active ui-state-highlight');
	togglePrtgSettingsWidgetButton();
}


/**
 * Update information on the page regarding the report period
 * e.g. main description and the custom button disability
 * @param period of the report e.g. last-week
 */
function updateReportDetails(period)
{
	var periodFromInput 	= $("#period-from");
	var periodToInput 		= $("#period-to");
	var customPeriodButton 	= $("#use-custom-period");
	var pageTitle 			= $('body:not(.dashboard) .title-widget');


	// If the from and to have values then remove the disabled attribute from the customPeriod(button)
	if(periodFromInput.val() && periodToInput.val() )
		customPeriodButton.attr("disabled", false);
	else
		customPeriodButton.attr("disabled", true);

	pageTitle.find('.dashboard-description').text(widgetTrans[period]);

	// Showing in the description the exact period
	if(period == "custom-period")
	{
		if(periodFromInput.val())
			pageTitle.find('.dashboard-description').append(' : ' + String($.datepicker.parseDate($.datepicker._defaults.dateFormat, periodFromInput.val())).substring(0, 15));

		if(periodToInput.val())
			pageTitle.find('.dashboard-description').append(' - ' + String($.datepicker.parseDate($.datepicker._defaults.dateFormat, periodToInput.val())).substring(0, 15));
	}
}

/**
 * Disables or enables the PRTG settings GO button
 * @returns {boolean}
 */
function togglePrtgSettingsWidgetButton() {
	var prtgGroup 	= $('#prtgSettingsWidget .prtg-group');
	var prtgName 	= $('#prtgSettingsWidget .prtg-name');
	var prtgType 	= $('#prtgSettingsWidget .prtg-type');
	var prtgButton  = $('#prtgSettingsWidget .prtg-custom-period');

	if(typeof prtgGroup !== 'undefined' && typeof prtgName !== 'undefined' && typeof prtgType !== 'undefined' && typeof prtgButton !== 'undefined')
		if(prtgGroup.val() !== '' && prtgName.val() !== '' && prtgType.val() !== '') {

			prtgButton.attr("disabled", false);
			return true;
		}

	prtgButton.attr("disabled", true);
	return false;

}

/**
 * Updating Report details: ex: main description and the custom button disability
 */
function updatePrtgReportDetails(period)
{
	var periodFromInput 	= $("#prtg-period-from");
	var periodToInput 		= $("#prtg-period-to");
	var pageTitle 			= $('body:not(.dashboard) .title-widget');

	pageTitle.find('.dashboard-description').text(widgetTrans[period]);

	// Showing in the discription the exact period
	if(period == "custom-period")
	{
		if(periodFromInput.val())
			pageTitle.find('.dashboard-description').append(' : ' + String($.datepicker.parseDate($.datepicker._defaults.dateFormat, periodFromInput.val())).substring(0, 15));

		if(periodToInput.val())
			pageTitle.find('.dashboard-description').append(' - ' + String($.datepicker.parseDate($.datepicker._defaults.dateFormat, periodToInput.val())).substring(0, 15));
	}

	//Toggle Go Button for PRTG
	togglePrtgSettingsWidgetButton();
}

var thisChart;
var delayTime = 2000;
var timeOut  = 120000;
var d = new Date();
var tzOffset = d.getTimezoneOffset();

/**
 * Setting timezone for the highchart
 */
Highcharts.setOptions({
	global: {
		timezoneOffset: tzOffset
	}
});

/**
 * Initializing WanThroughput widget
 */
function initWanThroughputWidget()
{
	//	drawWanThroughput('wanThroughputChart0');
	// Activating the first chart
	$("div[id^='wanThroughputChart']").removeClass('active').first().addClass('active');

	// Trigger select first gateway click or the only gateway
	if($(".grid-item.wan-throughput .widget-tab-menu .gateway-link:first").length)
		$(".grid-item.wan-throughput .widget-tab-menu .gateway-link:first").trigger('click');
	else
		getAndDrawWanThroughputChart('wanThroughputChart0', firstGatewayMac );
}

/**
 * Runs the wanthroughput drawing functionality on the given menu item
 */
function runWanThroughputChart(thisItem)
{
	var divId = thisItem.attr("data-item-index");
	var mac = thisItem.attr("data-item-mac");
	getAndDrawWanThroughputChart(divId, mac);
}

/**
 * Get the Wan Throughput Data
 */
function getAndDrawWanThroughputChart(divId, mac )
{
	handleDivsVisibility("loading");
	if(mac == '')
	{
		handleDivsVisibility("error");
		return false;
	}
	else
	{
		$.ajax({
			url: "/json/widgets/wan-throughput/0/get_wan_throughput_chart_data/" + mac + "/" + SITE_ID,
			type: "get",

			success: function (data) {

				if(data.length == 2)
				{
					handleDivsVisibility("error");
				}
				else
				{
					handleDivsVisibility("success");
					// parse the data
					data = jQuery.parseJSON(data);

					drawWanThroughputChart(divId, mac, data);
				}

			},
			error: function (data) {
				handleDivsVisibility("error");
				return false;
			}
		});
	}
}

/**
 * Getting live data and draw them
 */
function getAndPlotData(divId, mac, chart )
{
	// avoid running when widget is inactive or if gateway is not selected
	if ($('.grid-item.wan-throughput').hasClass('inactive') || !$('#' + divId).hasClass('active'))
		return false;

	if(mac == '')
	{
		handleDivsVisibility("error");
		return false;
	}
	else
	{
		$.ajax({
			url: "/json/widgets/wan-throughput/0/get_wan_throughput_chart_data/" + mac + "/" + SITE_ID,
			type: "get",
			timeout: timeOut,
			success: function (data) {
				data = jQuery.parseJSON(data);

				if (chart && chart.options) {
					$.each(chart.series, function (index, value) {
						var x = (new Date()).getTime(), // current time
							y = data[index].data;

						chart.series[index].addPoint([x, y], false, true);

					});
					chart.redraw();
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				handleDivsVisibility("error");
				// if times out it shows a different message
				if (textStatus === "timeout") {
					$('.grid-item.wan-throughput .no-data-found').html('<div class="text-center"><h2>' + widgetTrans['chart-timeout'] + '</h2><p>' + widgetTrans['chart-timeout-description'] + '</p></div>');
				}
			}
		});
	}
}


/**
 * Draws the WanThroughputChart
 */
function drawWanThroughputChart(divId, mac, data)
{
	$('#' + divId + '.active').highcharts({
		chart: {
			"type": "spline",
			"spacingBottom": 0,
			"spacingTop": 60,
			"spacingLeft": 0,
			"spacingRight": 10,

			"width": null,
			"height": null,
			events: {
				load: function(){
					thisChart = this;

					// Adding the series to the chart
					$.each(data, function(index, value)
					{
						// Once series are added it will draw empty 20 points
						value.data = generateEmpty20Points();
						thisChart.addSeries(value);
					});

					// This will call the plotting functionality
					setInterval(function() {
						getAndPlotData(divId, mac, thisChart );
					}, delayTime);
				}
			}
		},
		title: {
			text: ''
		},
		credits: {
			enabled: false
		},
		xAxis: {
			type: 'datetime',
			tickPixelInterval: 150,
			"labels": {
				"overflow": "justify",
				"style": {"color": "white"}
			}
		},
		yAxis: [{
			"labels": {
				"overflow": "justify",
				"style": {"color": "white"}
			},
			"title": {
				"text": "mb/s",
				"style": {"color": "white"}
			}
		}],
		legend: {
			"enabled": true,
			"itemStyle": {
				"color": "white",
				"font-weight":"normal"
			}
		},
		exporting: {
			"enabled": false
		},
		tooltip: {
			formatter: function() {
				return '<b>'+ this.series.name +'</b><br/>'+
					Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', this.x) +'<br/>'+
					Highcharts.numberFormat(this.y, 2);
			}
		}
	});
}

function generateEmpty20Points() {
	var data = [],
		time = (new Date()).getTime(),
		i;

	for (i = -19; i <= 0; i += 1) {
		data.push({
			x: time + i * 1000,
			y: 0
		});
	}
	return data;
}

/**
 * Dealing with the visibility of the chart, loading and error div for the wanthroughput widget
 * A potential for extending for other widgets
 * @param mode
 */
function handleDivsVisibility(mode)
{
	var chartDiv 		= $('.grid-item.wan-throughput .highcharts-container');
	var failedDiv 		= $('.grid-item.wan-throughput .no-data-found');
	var loadingChartDiv = $('.grid-item.wan-throughput .loading');

	switch(mode) {
		case 'error':
			failedDiv.show();
			chartDiv.hide();
			loadingChartDiv.hide();
			break;
		case 'success':
			chartDiv.show();
			failedDiv.hide();
			loadingChartDiv.hide();
			break;
		case 'loading':
			loadingChartDiv.show();
			chartDiv.hide();
			failedDiv.hide();
			break;
		default:
			chartDiv.hide();
			failedDiv.hide();
			loadingChartDiv.hide();
	}
}


/**
 *
 * @param cname
 * @returns {*}
 */
function getCookie(cname) {
	var name = cname + "=";
	var decodedCookie = decodeURIComponent(document.cookie);
	var ca = decodedCookie.split(';');
	for(var i = 0; i <ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') {
			c = c.substring(1);
		}
		if (c.indexOf(name) == 0) {
			return c.substring(name.length, c.length);
		}
	}
	return false;
}

/**
 * Initiating the PRTG widget
 */
function initPrtgWidget() {
	var period 				= $("#prtg-period").val();
	var periodFrom 			= $("#prtg-period-from").val();
	var periodTo 			= $("#prtg-period-to").val();
	var prtgAvg 			= $("#prtg_avg").val();
	var prtgSensor 			= $("#prtg_type").val();
	var prtgButton  		= $('#prtgSettingsWidget .prtg-custom-period');
	var prtgCookieSensors 	= getCookie('prtgWidget_'+SITE_ID+'_'+USER_ID);
	var prtgFlag 			= $('#prtg_init_widget');
	var prtgError			= $('.grid-item.prtg .prtg-error');

	if(prtgError.length > 0)
		return false;

	if( prtgFlag.data('flag') === 1 ) {
		//Add the cog icon to toggle the settings
		$('.grid-item.prtg .panel-actions').append(
			'<a class="panel-action toggle-prtg-settings" aria-hidden="true">\n' +
			'    <span class="fa-stack fa toggle-container">\n' +
			'       <i class="fa fa-circle fa-stack-2x" ></i>\n' +
			'       <i class="fa fa-cog fa-stack-1x fa-inverse font-size-10" ></i>\n' +
			'    </span>\n' +
			'</a>'
		);

		//Add the close button only if there are multiple sensors
		if(prtgCookieSensors !== false) {
			addPrtgSettingsCloseButton();
		}

		$('.grid-item.prtg .no-data-found').hide();

		if( typeof prtgCookieSensors === 'string' && !($.cookie('prtgWidget_'+SITE_ID+'_'+USER_ID) === null)) {
			//Ajax call to get the data for all the sensors that are in cookie
			// Calling Ajax to get the Prtg data
			$.ajax({
				url: "/json/widgets/prtg/0/get-report-data",
				type: "get",
				data: {
					method:		'\\App\\Admin\\Widgets\\Prtg::getCookieSensorsData'
				},
				beforeSend: function() {
					//Add a loading before the actual data is coming back
					$('.grid-item.prtg .loading').show();
				},
				success: function (data) {
					var obj = jQuery.parseJSON( data);
					for (var i = 0, len = obj.length; i < len; i++) {
						if(obj[i][0].length > 0) {
							drawPrtgChart([obj[i][0],getSeries(obj[i])], obj[i]['uniqueId']);
						} else {
							// No data
							alertError(trans["error"]);
						}
					}

					$('.grid-item.prtg .loading').hide();
				}
			});
		}
	}

	//This is just for the first time on page
	prtgFlag.attr('data-flag', 0);
	//Set the period to custom if we have period-from and period-to set
	if(periodFrom == '' || periodTo =='')
		resetPrtgCustomPeriod();
	else
		period = 'custom-period';

	//If we don't have the sensorid, don't do anything
	if(prtgSensor == '') {
		return false;
	}

	// Calling Ajax to get the Prtg data
	$.ajax({
		url: "/json/widgets/prtg/0/get-report-data",
		type: "get",
		data: {
			period: 	period,
			from:		periodFrom,
			to:			periodTo,
			average: 	prtgAvg,
			id: 		prtgSensor,
			method:		'\\App\\Admin\\Widgets\\Prtg::getPrtgData'
		},
		beforeSend: function() {
			//Set the button to loading
			toggleLoadingButton(prtgButton, true);

		},
		success: function (data) {

			var obj = jQuery.parseJSON( data);
			if(obj[0].length > 0)
			{
				drawPrtgChart([obj[0],getSeries(obj)], obj['uniqueId']);
			}
			else
			{
				// No data
				alertError(trans["error"]);
			}
			//Remove loading from the button
			toggleLoadingButton(prtgButton, false);
			//Toggle settings content div
			togglePrtgSettingsContent();
		}
	});
}

/**
 * Add close button for the settings
 */
function addPrtgSettingsCloseButton() {
	if($('.grid-item.prtg .prtg-settings-content .toggle-prtg-settings').length < 1) {
		//Add close button for the settings
		$('.grid-item.prtg .prtg-settings-content .period-buttons .prtg-dropdown-settings')
			.find('.col-md-12.col-sm-12.input-container.input-container-select .col-md-1.hidden-xs.hidden-sm.padding-0')
			.first().append(
			'<a class="panel-action toggle-prtg-settings pull-right" aria-hidden="true">\n' +
			'    <span class="fa-stack fa toggle-container">\n' +
			'       <i class="fa fa-circle fa-stack-2x" ></i>\n' +
			'       <i class="fa fa-close fa-stack-1x fa-inverse font-size-10" ></i>\n' +
			'    </span>\n' +
			'</a>'
		);
	}
}

/**
 * Drawing PRTG chart
 * @param data
 * @param uniqueId
 */
function drawPrtgChart(data, uniqueId)
{
	addPrtgNavbarTab('prtg_navbar', 'prtg_tab_content', uniqueId, uniqueId, true, 'prtgWidget_'+SITE_ID+'_'+USER_ID);

	var prtg = $('#'+uniqueId);
	prtg.attr('class', 'chart-container bg-technology-reports');
	prtg.show();

	prtg.highcharts({
		chart: {
			"zoomType": "x",
			type: 'area',
			// Edit chart spacing
			spacingBottom: 15,
			spacingTop: 10,
			spacingLeft: 10,
			spacingRight: 10,

			// Explicitly tell the width and height of a chart
			width: null,
			height: null
		},
		legend: {
			enabled: true,
			floating: true,
			verticalAlign: 'top',
			align:'right',
			itemStyle: {
				'color': 'white',
				'font-weight':'normal'
			}
		},
		title: {
			text: null
		},
		subtitle: {
			text: null
		},
		exporting: { enabled: false },
		xAxis: {
			categories: data[0],
			title: {
				text: null
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		yAxis: {
			min: 0,
			title: {
				text: widgetTrans['prtg'],

				style: {color: 'white'}
			},
			labels: {
				overflow: 'justify',
				style: {color: 'white'}
			}
		},
		tooltip: {
			formatter: function() {
				return this.x + ':  <b>' + this.y  + ' (' + this.series.name + ') ' + '</b> ';
			}
		},

		credits: {
			enabled: false
		},
		plotOptions: {
			area: {
				minPadding: 0,
				marker: {
					enabled: false,
					symbol: 'circle',
					radius: 2,
					states: {
						hover: {
							enabled: true
						}
					}
				}
			}
		},

		series:  data[1],
		colors: ['#ffaf93',
			'#ED4423',
			'#fff562',
			'#ffaef3',
			'#3c8f39',
			'#001122',
			'#7994F2',
			'#00b7ff',
			'#0eff70',
			'#ffaf0a',
			'#f1fffd']
	});
	//Remove the active class from the list
	$('#prtg_navbar li').removeClass('active');
	//Add active class to our generated list item
	$('#'+uniqueId + '_tab_tab_id').addClass('active');

	//Remove active class from tab-contents
	$('#prtg_tab_content .tab-pane').removeClass('active');
	//Add class 'active' to the tab-content with our generated graph
	$('#'+uniqueId+'_tab').addClass('active');
}

/**
 * Initiating the Gender widget
 */
function initGenderWidget()
{
//	if(ROUTE != "dashboard")
//	{
		var period 		= $("#period").val();
		var periodFrom 	= $("#period-from").val();
		var periodTo 	= $("#period-to").val();

		clearGenderData(period);

		$.ajax({
			url: "/json/widgets/gender/0/get-report-data",
			type: "get",
			data: {
				period: period,
				from:	periodFrom,
				to:		periodTo,
				route:  ROUTE
			},
			success: function (data) {
				drawGenderChart(data);
			},
			error: function (data) {
			}
		});
//	}
//	else
//		drawGenderChart(genderDataArray);
}

/**
 * Clearing Gender data
 * @param period
 */
function clearGenderData(period)
{
	$('.female-bar').width('0%');
	$('.male-bar').width('0%');
	var spinnerIcon = '<i class="fa fa-spinner font-size-15 fa-spin white" ></i>';
	$('#male-percentage').html(spinnerIcon);
	$('#female-percentage').html(spinnerIcon);
}

function drawGenderChart(data)
{
	if(data !== undefined)
	{
		$('#female-percentage').text(data[0] + '%');
		$('#male-percentage').text(data[1] + '%');
		$('.female-bar').width(data[0] + '%');
		$('.male-bar').width(data[1] + '%');
	}

}


/**
 * Initializing the Map widget
 */
function initMapWidget() {
	//gets the locations array and sets in a variable
	var locations =[];
	$.ajax({
		url: "/json/widgets/map/0/get-map-data",
		type: "get",
		data: {
		},
		success: function (data) {
			// Plotting the markers
			processMapLocations(data);
			// Fade out the loading
			$('.grid-item.map .loading').fadeOut('slow');
		},
		error: function (data) {
		}
	});
}


/**
 * Plotting the markers in the right position and showing all markers on the map
 * @param locations
 */
function processMapLocations(locations)
{

	if(locations.length == 0)
	{
		var noMap = $('<div class="center-text"><h2 class="margin-0">' + widgetTrans['no-map-title'] + '</h2>' +
			'<p>' + widgetTrans['no-map-text'] + '</p></div>');
		$('.grid-item.map .widget-inner').html(noMap);
	}
	else
	{
		var latLngList = [	];
		//this passes in the first lat and long from the array as the default center
		var latLng = null;

		//sets the map options
		var mapOptions = {
//				center: latLng,
			mapTypeId: google.maps.MapTypeId.HYBRID,
			zoom: 10,
			minZoom: 2,
			mapTypeControl: false,
			navigationControl: false
		};

		// targets div with map id to build the map
		var map = new google.maps.Map(document.getElementById('map'), mapOptions);
		// sets up the window that the info will show in
		var infoWindow = new google.maps.InfoWindow();
		// sets empty vars for the count and marker
		var marker, i, content, clusterFolder;
		var markers = [];

		// Setting up the spiderfier object
		var spiderfier = new OverlappingMarkerSpiderfier(map,
			{ spiralLengthFactor: 15,
				circleSpiralSwitchover: 0,
				keepSpiderfied: true
			});

		// loops through the locations array and sets the marker for each one based and the lat and long
		// locations comes from the widgets/map.php
		for (i = 0; i < locations.length; i++)
		{
			// If lat and long are set then create the maker ...etc
			if(locations[i][0][0] != 0 && locations[i][0][1] != 0)
			{
				latLng = new google.maps.LatLng(locations[i][0][0], locations[i][0][1]);
				// latLngList holds all latLng values to be used later to zoom into these locations
				latLngList.push(latLng);

				// Contents of the infoWindow
				content = 	'<div class="map-marker-container" title="'+locations[i][0][3] + '">' +
					'<div class="map-marker ' + locations[i][1][0] + '"></div>' +
					'<div class="map-marker-data">' + locations[i][1][1] + '</div>'+
					'</div>';

				// setting up richmarker
				marker = new RichMarker({
					position: latLng,
					map: map,
					shadow: false,
					content: content
				});

				// Adding markers to the spiderfier
				spiderfier.addMarker(marker);
				markers.push(marker);

				// listens for a button click on the marker
				google.maps.event.addListener(marker, 'click', (function(marker, i) {
					//shows the info window
					return function(e) {
						var contents = '';

						// Looping into the html code that is stored in locations[i][2]
						for(var j=0; j < locations[i][2].length; j++)
							contents += locations[i][2][j];

						// Stopping propagation, this will stop google map to show other infoWindows when clicking on the marker
						if(e !== undefined)
							e.stopPropagation();

						infoWindow.setContent(contents);
						infoWindow.open(map, marker);

						// Run Demographics call if locations[i][0][2] has an id of the site, if gateway it will be ''
						if($.isNumeric(locations[i][0][2]))
							initDemographicsWidget(locations[i][0][2]);
					}
				})(marker, i));
			}
		}

		// Setting up the clusterer folder
		if(SITE_TYPE != 'site')
			clusterFolder = '/admin/templates/system/images/clusters/site/m';
		else
			clusterFolder = '/admin/templates/system/images/clusters/gateway/m';

		// Creating the clusterer object
		var clusterer = new MarkerClusterer(map, markers, {imagePath: clusterFolder ,maxZoom: 15});

		//  Create a new viewpoint bound to show all markers in the map
		var bounds = new google.maps.LatLngBounds ();
		//  Go through each...
		for (var x = 0; x < latLngList.length; x++) {
			//  And increase the bounds to take this point
			bounds.extend (latLngList[x]);
		}
		//  Fit these bounds to the map
		map.fitBounds (bounds);

		// function to open all Spiders
		function openAllSpiders() {
			var spiders = spiderfier.markersNearAnyOtherMarker();

			// open up spider
			$.each(spiders, function (i, marker) {
				google.maps.event.trigger(markers[i], 'click');
			});
			// Close the infoWindow
			infoWindow.close();
		}

		// Opens all spiders
		setTimeout(openAllSpiders, 1000);

		google.maps.event.addListener(clusterer, 'clusterclick', function(cluster) {
			setTimeout(openAllSpiders, 1000);
		});

		// hooks into the custom event orderItems:finish set in the orderItems function in widgets-editor.js
		$( "body" ).on( "saveWidgetsOrder:finish", function( e ) {
			// selects the outer widget-inner of the map widget
			var $map = $( ".grid" ).find('.grid-item.map .widget-inner');
			// gets the lat and long of the map center set above
			var center = map.getCenter();
			e.preventDefault();
			//sets the width and height dynamically based on the values given back when the custom event is called
			$("#map").css({
				width: $map.width(),
				height: $map.height()
			});
			// actually triggers the resize of the map
			google.maps.event.trigger(map, "resize");
			//resets the center of the map after the resize
			map.setCenter(center);

		});
	}
}


/**
 * Initiating the messages widget
 */
function initMessagesWidget()
{
	$.getJSON("/json/widgets/messages/"+SITE_ID+"/30", function (data) {
		// Loop through the data and create the message html
		var strHTML = "";
		$.each(data, function (index, value) {
			var userId = value.id;
			var desc = value.description;
			var email = (typeof value.admin != 'undefined') && (  value.admin != null) ? value.admin.username : 'No user';

			// TODO: date might need to be pulled in differently once the date function is done
			var dateCreated = value.created;
			var hashedEmail = md5(email);

			strHTML +=
				'<li class="list-group-item">' +
				'<div class="message-info">' +
				'<div class="message-first clearfix">' +

				'<div class="message-details clearfix"><p class="pull-left message-type ' + value.type + '">' + value.type + '</p>' +
				'<p class="pull-left message-created">' + dateCreated + '</p></div>' +
				'<div class="image"><span class="avatar avatar-online pull-right">' +
				'<img src="https://www.gravatar.com/avatar/' + hashedEmail + '" alt=""></span></div>' +
				'</div>' +

				'<div class="message-second clearfix ">' +
				'<h5 class="list-group-item-heading">' + email + '</h5>' +
				'<p class="list-group-item-text">' + desc + '</p>' +
				'</div>' +
				'</div>' +
				'</li>';

		});
		// Add the html to the correct UL
		$(strHTML).appendTo(".list-group");
	});
	// Fade out the loading
	$('.grid-item.messages .loading').fadeOut('slow');
}


/**
 * Reseting the menus to have the first one selected and active
 *
 */
function resetMenuItems(ulMenu, nth)
{
	$(ulMenu +" li").removeClass("active");

	if(nth === undefined)
		$(ulMenu +" li:first").addClass("active");
	else
		$(ulMenu +" li:nth-child("+nth+")").addClass("active");
}

/**
 * formatting numbers
 * @param nStr
 */
function formatNumber(nStr)
{
	nStr += '';
	x = nStr.split('.');
	x1 = x[0];
	x2 = x.length > 1 ? '.' + x[1] : '';
	var rgx = /(\d+)(\d{3})/;
	while (rgx.test(x1)) {
		x1 = x1.replace(rgx, '$1' + ',' + '$2');
	}
	return x1 + x2;
}

var body = $("body");
/**
 * Turning AAA On and Off
 */
body.on("click",".action_aaa" , function(e){
	e.preventDefault();

	var thisLink 		= $(this);
	var name			= thisLink.data("name");
	var currentStatus	= thisLink.data("status");
	var sweetText 		= "";

	if (currentStatus == "disabled")
	{
		sweetText = trans["to-enable-aaa"] +" '" + name +"'?";
	}
	else
	{
		sweetText = trans["to-disable-aaa"] +" '" + name +"'?";
	}

	// Sweet Alert call to confirm
	swal({
		title				: trans["are-you-sure"],
		text				: sweetText,
		type				: "warning",
		input				: "text",
		inputPlaceholder	: trans["reason-placeholder"],
		inputValidator		: function (reason) {
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
		},
		showCancelButton	: true,
		confirmButtonColor	: "#00adef" ,
		confirmButtonText	: trans["yes-please"],
		cancelButtonText	: trans["cancel"]
	}).then(function(reason){
		// Call back function if user press yes please

		//Ajax URL
		var url 			= thisLink.attr("href");
		var mac 			= thisLink.data("mac");
		var name 			= thisLink.data("name");
		//child is the icon with the toggle font awesome
		var child 			= thisLink.find(">:first-child");
		var newStatus 		= "";
		var currentIcon		= "";
		var newIcon 		= "";
		var currentTextColor= "";
		var newTextColor 	= "";
		var newTitle		= "";

		// Toggle the status
		if (currentStatus == "disabled"){

			currentTextColor= "text-danger";
			// New status details for icon, color and text
			newStatus 		= "enabled";
			newTextColor 	= "text-success";
			newTitle		= trans["disable-aaa"];

		}else{
			currentTextColor= "text-success";
			// New status details for icon, color and text
			newStatus 		= "disabled";
			newTextColor 	= "text-danger";
			newTitle		= trans["enable-aaa"];
		}

		// Showing an over-lay div to avoid clicking on the screen while calling Ajax
		$(".loading_page").fadeIn("fast");

		//console.log("URL: " + url);
		// Calling Ajax to toggle the status
		$.ajax({
			url : url,
			type: "post",
			data: {
				'mac' 		: mac ,
				'status' 	: newStatus,
				'name' 		: name,
				'reason'	: reason
			}})
			.success (function(data){
				//console.log("DATA: " + data);
				if(data ==1){

					thisLink.data("status",newStatus);
					thisLink.attr("title", newTitle);
					child.removeClass(currentTextColor);
					child.addClass(newTextColor);

					// Hiding the overlay div
					$(".loading_page").fadeOut("fast");

					if(newStatus == 'enabled'){
						newStatus = trans["aaa-enabled"];
					}
					else{
						newStatus = trans["aaa-disabled"];
					}

					swal({
						title:	trans["done"],
						text: 	"'" + name + "' = " + newStatus + " !",
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


/**
 * Rebooting gateway action
 */
body.on("click",".action_reboot" , function(e){
	e.preventDefault();

	var thisLink 		= $(this);
	var mac			= thisLink.data("mac");
	var name		= thisLink.data("name");

	// Sweet Alert call to confirm
	swal({
		title				: trans["are-you-sure"],
		text				: trans["to-reboot"] + " '" + name + "'",
		type				: "warning",
		input				: "text",
		inputPlaceholder	: trans["reason-placeholder"],
		inputValidator		: function (reason) {
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
		},
		showCancelButton	: true,
		confirmButtonColor	: "#00adef" ,
		confirmButtonText	: trans["yes-please"],
		cancelButtonText	: trans["cancel"]
	}).then(function(reason){
		// Call back function if user press yes please

		// Showing an over-lay div to avoid clicking on the screen while calling Ajax
		$(".loading_page").fadeIn("fast");

		//console.log("URL: " + url);
		// Calling Ajax to toggle the status
		$.ajax({
			url : thisLink.attr("href"),
			type: "post",
			data: {
				'name' 		: name,
				'mac' 		: mac,
				'reason'	: reason
				//'_token'	: $('input[name=_token]').val()
			}})
			.success (function(data){
				//console.log("DATA: " + data);
				if(data ==1){
					getAaaStatus(thisLink);
					// Hiding the overlay div
					$(".loading_page").fadeOut("fast");

					swal({
						title:	trans["done"],
						text: 	"'" + name + "' = " + trans["is-rebooted"] + " !",
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
