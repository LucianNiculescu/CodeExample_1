<div class="loading text-center {{$loadingClass or ''}}" style="{{$loadingStyle or ''}}">
	<h3>
		<i class="fa fa-spinner fa-spin {{$spinnerClasses or ''}}"  style="{{$spinnerStyle or ''}}"></i>
	</h3>
</div>
<div class="no-data-found " style="{{$spinnerStyle or ''}}">
	<h3 style="margin:0;">
		<div class="text-center description {{$spinnerClasses or ''}}">{{trans('admin.no-data-found')}}</div>
	</h3>
</div>