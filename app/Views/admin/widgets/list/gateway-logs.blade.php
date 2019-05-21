<div id="gatewayLogsWidget" class="panel-body">
	@include('admin.templates.system.loading', ['loadingStyle' => 'position: absolute; top: 250px;'])
	@if( isset($gateways) && !$gateways->isEmpty() )

		@include('admin.widgets.gateways-dropdown', ['customStyle' => 'display:block !important'])

		<div id="gatewayLogsContainer"></div>
	@else
		@include('admin.widgets.no-gateways')
	@endif
</div>

