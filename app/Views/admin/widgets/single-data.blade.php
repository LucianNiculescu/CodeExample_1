<div class="{{$widgetName or ''}}-container text-center white @if(count($gateways ?? 0) > 1) padding-top-50 @else padding-top-30 @endif"  style="{{$extraStyle ?? ''}}">
	<div class="day-icon center-text ">
		<i class="fa {{$widgetIcon or ''}} center-text font-size-70"></i>
	</div>
	<div class="{{$widgetName or ''}}-value center-text  font-size-40" id="{{$widgetId or ''}}Data" style="{{$dataStyle ?? 'display: inline'}}">
		{{--Value--}}
	</div>
	<div class="{{$widgetName or ''}}-desc center-text  small-line-height"  id="{{$widgetId or ''}}Desc" style="{{$descStyle ?? 'display: inline'}}">
		{{--Desc--}}
	</div>
</div>