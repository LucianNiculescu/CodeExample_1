<?php

namespace App\Admin\Json\Charts\Type;
use App\Models\Reports\HourlyHardwareHealth;
use DB;

class Latency extends AllTypes
{
	/**
	 * Latency Data Builder
	 * Gets all the latency data between the start and end dates
	 */
	protected function dataBuilder()
	{

		$dataBuilder = HourlyHardwareHealth::where('site', $this->siteId)
			->whereBetween('report_date', [$this->startDate, $this->endDate])
			->groupBy(['report_date', 'report_hour'])
			->orderBy('report_date', 'asc')
			->orderBy('report_hour', 'asc');

		if ($this->hourly)
			$dataBuilder->select(
				DB::raw('MIN(min_latency) AS min_latency'),
				DB::raw('MAX(max_latency) AS max_latency'),
				DB::raw('AVG(avg_latency) AS avg_latency'),
				DB::raw('CONCAT(report_date, " ", LPAD(report_hour, 2, 0), ":00:00") AS report_date')
			);
		else
			$dataBuilder->select(
				DB::raw('MIN(min_latency) AS min_latency'),
				DB::raw('MAX(max_latency) AS max_latency'),
				DB::raw('AVG(avg_latency) AS avg_latency'),
				DB::raw('report_date')
			);

		if (!is_null($this->mac))
			$dataBuilder->where('mac', $this->mac);

		$this->data = $dataBuilder->get()->toArray();
	}

	/**
	 * Gets the data for the Latency Chart
	 * passes the start and end dates to the fillDates() function in the AllTypes class uses the results to build
	 * the chart
	 */
	protected function getData()
	{
		// Get the data
		$this->data = $this->fillDates($this->startDate, $this->endDate, 'report_date', $this->hourly);

		// If there is no data, show empty
		if(empty($this->data)){
			// 0 everything
			$min_latency = '0';
			$max_latency = '0';
			$avg_latency = '0';

			// Set the start date as today
			$start = strtotime(date('Y-m-d')) * 1000;;

		}else{
			// Go through the data and add the latency
			foreach ($this->data as $data) {
				$min_latency[] = $data['min_latency'];
				$max_latency[] = $data['max_latency'];
				$avg_latency[] = $data['avg_latency'];
			}

			// Turn the array into a csv
			$min_latency = implode(",", $min_latency);
			$max_latency = implode(",", $max_latency);
			$avg_latency = implode(",", $avg_latency);

			$start = strtotime($this->startDate) * 1000;
		}

		$maxLatency = 120;

		$graph_interval = 3600000;

		// labels to be translated and passed into the chart
		$good = trans('admin.good');
		$reasonable = trans('admin.reasonable');
		$unacceptable = trans('admin.unacceptable');
		$latency_milliseconds = trans('admin.latency-in-milliseconds');
		$min_latency_label = trans('admin.min-latency');
		$avg_latency_label = trans('admin.avg-latency');
		$max_latency_label = trans('admin.max-latency');

		// TODO: pass three colours in and think about branding here!!
		$colours = array('#00adef', '#002433', '#79d0f2', '#daebf2', '#8899aa', '#333333', '#DDDDBB', '#DDCCAA', '#ADB7D8', '#7994F2', '#0079A8', '#3F4E7F', '#005566', '#004966');

		echo <<<EOT
	{
		"chart": {
			"spacingBottom": 0,
			"spacingTop": 20,
			"spacingLeft": 0,
			"spacingRight": 0,
	
			"width": null,
			"height": null,
			
			"zoomType": "x",
			"backgroundColor": "white" },
		"exporting": { "enabled": false },
		"title": {
			"text": "" },
		"xAxis": {
			"type": "datetime",
			"labels": {
				"overflow": "justify",
				"style": {"color": "white"}
			},
			"dateTimeLabelFormats": {
				"Hour": "%e %b" },
			"title": {
				 }
		},
		"yAxis": {
			"title": {
				"text": "$latency_milliseconds" ,
				"style": {"color": "white"}
				},
			"valueSuffix": " Ms",
			"minorGridLineWidth": "0",
			"gridLineWidth": "0",
			"minPadding": 0.05,
			"maxPadding": 0.10,
			"startOnTick": false,
			"endOnTick": false,
			"min": 0,
			"max": $maxLatency,
			"labels": {
				"overflow": "justify",
				"style": {"color": "white"}
			},
			"plotBands": [{
				"from": "0",
				"to": "50",
				"color": "#eefaee",
				"label": {
					"text": "$good",
					"style": {"color": "#484471"}
				}
			},{
				"from": "50",
				"to": "100",
				"color": "#fafaee",
				"label": {
					"text": "$reasonable",
					"style": {"color": "#484471"}
				}
			},{
				"from": 100,
				"to": 150,
				"color": "#faeeee",
				"label": {
					"text": "$unacceptable",
					"style": {"color": "#484471"}
				}
			}]
		},
		"tooltip": {
			"shared": true,
			"xDateFormat": "%e %b %Y" },
		"legend": {
			"itemStyle": {
				"color": "white",
				"font-weight":"normal"
				}
			},
			
		"plotOptions": {
			"areaspline": {
				"lineWidth": 1,
				"marker": { "enabled": false },
				"shadow": false,
				"states": { "hover": { "lineWidth": 1 } },
				"threshold": 0,
				"pointInterval": $graph_interval,
				"pointStart": $start,
				"tooltip": {
					"valueSuffix": " Ms" } } },
		"series": [{
			"type": "areaspline",
			"name": "$min_latency_label",
			"data": [$min_latency],
			"color": "$colours[0]",
			"fillColor": {
				"linearGradient": { "x1": 0, "y1": 0, "x2": 0, "y2": 1},
				"stops": [
					[0, "$colours[0]"],
					[1, "$colours[3]"] ] },
			"zIndex": 3
		}, {
			"type": "areaspline",
			"name": "$avg_latency_label",
			"data": [ $avg_latency ],
			"color": "$colours[1]",
			"fillColor": {
				"linearGradient": { "x1": 0, "y1": 0, "x2": 0, "y2": 1},
				"stops": [
					[0, "$colours[1]"],
					[1, "$colours[4]"] ] },
			"zIndex": 2
		}, {
			"type": "areaspline",
			"name": "$max_latency_label",
			"data": [ $max_latency ],
			"color": "$colours[2]",
			"fillColor": {
				"linearGradient": { "x1": 0, "y1": 0, "x2": 0, "y2": 1},
				"stops": [
					[0, "$colours[2]"],
					[1, "$colours[5]"] ] },
			"zIndex": 1  }],
		"credits": { "enabled": false } }
EOT;
		exit;

	}


}