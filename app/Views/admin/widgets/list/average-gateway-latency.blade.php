<div class="panel-body bg-technology-reports" id="averageGatewayLatency">
	@if( isset($gateways) && !$gateways->isEmpty() )
		@if($gateways->count() > 1)
			@include('admin.widgets.gateways-dropdown', ['customStyle' => 'display:block !important'])
		@endif

		@include('admin.widgets.single-data', [
		'widgetName'	=> 'average-latency',
		'widgetId'		=> 'averageLatency',
		'widgetIcon'	=> 'fa-hourglass-half',
		])
	@else
		@include('admin.widgets.no-gateways')
	@endif

</div>

