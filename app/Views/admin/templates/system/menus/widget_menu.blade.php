
<div class="site-menubar" id="inactive-widget-list" >
	@if(isset($widgets) and !empty($widgets))
		<div id="noInactiveWidgets" class="col-md-12 center-text">{!! trans('admin.no-inactive-widgets') !!}</div>
	@endif
	<ul class="site-menu inactive-widgets padding-top-30" style="position:relative;">
		@if(isset($widgets))
			<!-- loops through widgets and passes the widget data into the widget blade-->
			@forelse ($widgets as $widgetId => $widget)
				@can('access', 'widgets.'. $widget['title'])
					@if($widget['status'] == 'inactive')
						<div class="panel inactive-widget" style="z-index:90000;" data-item-id="{{$widgetId}}" id="{{$widgetId}}">
							<li class="panel-heading inactive-drag" >
								@include('admin.widgets.widget-skeleton')
							</li>
						</div>
					@endif
				@endcan
			@empty
				<p>{{trans('admin.no-widgets')}}</p>
			@endforelse
		@endif
	</ul>
</div>
