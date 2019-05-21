<div class="panel-body bg-technology-reports" id="highestGatewayLatency">
	@if( isset($gateways) && !$gateways->isEmpty() )
		@if(count($gateways) > 1)
			@include('admin.widgets.gateways-dropdown', ['customStyle' => 'display:block !important'])
		@endif

		@include('admin.widgets.single-data', [
		'widgetName'	=> 'highest-latency',
		'widgetId'		=> 'highestLatency',
		'widgetIcon'	=> 'fa-hourglass-start',
		])
	@else
		@include('admin.widgets.no-gateways')
	@endif

</div>
