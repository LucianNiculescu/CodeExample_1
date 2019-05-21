<div id="gatewaySpeedWidget" class="panel-body">
	@include('admin.templates.system.loading', ['loadingStyle' => 'position: absolute; top: 250px;'])
	@if( isset($gateways) && !$gateways->isEmpty() )
		@if($gateways->count() > 1)
			@include('admin.widgets.gateways-dropdown', ['customStyle' => 'display:block !important'])
		@endif
		<div id="gatewayUpSpeedContainer"></div>
		<div id="gatewayDownSpeedContainer"></div>
	@else
		@include('admin.widgets.no-gateways')
	@endif
</div>
