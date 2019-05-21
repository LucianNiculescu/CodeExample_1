<div class="drop-zone clearfix widgets-editor table-no-focus">
	<!-- title and actions area need to be outside the drop zone so it doesn't show on edit mode-->
	<div class="grid-item grid-item--width4 title-widget">
		<h2 class="title">{{$title}} <small class="dashboard-description">{{ $description }}</small> <small class="drag-guide">{{trans("admin.widgets-edit-desc")}}</small></h2>
		@if (isset($gatewaysNotContactable) && ! empty($gatewaysNotContactable))
			<div id="dashboard-gateway-alert" class="text-center">{{ trans('admin.dashboard-gateway-alert') . implode(',', $gatewaysNotContactable)}}</div>
		@endif
		<div class="form_actions">

			@include('admin.help-pages.button')

			@can('access', 'manage.sites.edit')
				@if(isset($url) && $url != null)
					<a href="{{$url}}" class="btn btn-info btn-sm pull-right" title="{{trans('admin.edit-site-title')}}" aria-hidden="true">
						<i class="fa fa-edit fa_circle"></i>
					</a>
				@endif
			@endcan

			@can('access', 'widgets-editor')
				<a class="btn btn-info btn-sm pull-right edit-widgets default" title="{{trans('admin.edit-widgets')}}" aria-hidden="true">
					<i class="fa fa-th fa_circle"></i>
				</a>
			@endcan
		</div>
	</div>

	@if ( isset($widgetSettings) and ($widgetSettings === 'bar') )
		@include('admin.widgets.settings-bar')
	@endif
	<!-- widgetised area $widgets comes fromthe widgetsComposer-->
	<div class="grid  show-mode clearfix">
		<div class="gutter-sizer"></div>
		@include('admin.widgets.widgets-grid')
	</div>
</div>


<script>
	// Default gatewayMac
	var firstGatewayMac = "@if(isset($gateways[0])){{$gateways[0]->mac}}@endif";
</script>

