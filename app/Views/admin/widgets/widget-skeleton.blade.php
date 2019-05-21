
<div class="grid-item widget-{{$widgetId}} {{$widget['status']}} {{$widget['view']}}
	@if($widget['status'] == 'toggled')
		@foreach(explode('.', $widget['toggle_class']) as $toggle)
			grid-item--{{$toggle}}
		@endforeach
	@else
		@foreach(explode('.', $widget['default_classes']) as $class)
			grid-item--{{$class}}
		@endforeach
	@endif
		"
	 data-item-id="{{$widgetId}}"
     data-toggle-class="@foreach(explode('.', $widget['toggle_class']) as $toggle) grid-item--{{$toggle}} @endforeach"
     data-default-class="@foreach(explode('.', $widget['default_classes']) as $class) grid-item--{{$class}} @endforeach"
     data-title="{{$widget['title']}}" title="{{trans('admin.'.$widget['title'])}}"
     style="overflow:hidden" >

    <div class="panel-heading">
        <div class="panel-draggable">
            <i class="fa fa-arrows-alt" aria-hidden="true"></i>
            <p class="panel-title">{{trans('admin.'.strtolower($widget['title']))}} </p>
        </div>

        <div class="panel-actions">
            @if($widget['toggle_class'] != null)
                <a class="panel-action toggle-size" aria-hidden="true">
                     <span class="fa-stack fa toggle-container">
                        <i class="fa fa-circle fa-stack-2x" ></i>
                        <i class="fa fa-expand fa-stack-1x fa-inverse font-size-10" ></i>
                    </span>
                </a>
            @endif
            <a class="panel-action icon fa-times-circle fa remove-widget font-size-20" data-toggle="panel-close" aria-hidden="true"></a>

            @if($widget['help'] != null)
				@include('admin.templates.system.input-fields.help', ['help' => trans( $widget['help']), 'extraHelpClass' => 'widget-help'])
					{{--<i class="fa fa-question-circle form-help pull-right widget-help" style="margin-top:6px" data-html="true" data-content="{!! trans( $widget['help']) !!}" data-container="body" tabindex="0" data-trigger="focus" data-toggle="popover" data-original-title="Help" aria-hidden="true" data-placement="auto right"></i>--}}
            @endif
        </div>
    </div>

    <div class="widget-inner">
		@if($widget['view'] != null)
			@include('admin.widgets.list.' . $widget['view'])
		@else
			{!! $widget['content'] !!}
		@endif
    </div>

</div>
