<?php
	$activeWidgetFound = false;
	$userHasWidgets = true;
?>
@forelse ($widgets as $widgetId => $widget)

	@can('access', 'widgets.'. $widget['title'])
{{--		Widgets that are inactive meaning that they are not possible to be shown in the grid
		Widgets that are active means that they are new to the user and came directly from the admin_widget or role_widget--}}
		@if($widget['status'] != 'inactive')
			<?php $activeWidgetFound = true;?>
			@include('admin.widgets.widget-skeleton')
		@endif
	@endcan
@empty
	<?php $userHasWidgets = false;?>
	{!! trans('admin.no-widgets') !!}
@endforelse

@if(isset($widgets) and !empty($widgets))
	<div id="noActiveWidgets" class="col-md-12 center-text" @if($activeWidgetFound or !$userHasWidgets)style="display:none"@endif>
		{!! trans('admin.no-active-widgets') !!}
	</div>
@endif

