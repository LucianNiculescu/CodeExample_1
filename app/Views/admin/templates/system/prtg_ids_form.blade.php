<div class="add-ptrg-ids width-full padding-20 text-center">
	<form action="/site-attributes/save" class="clearfix validate-me" method="post">
		{!! csrf_field() !!}
		<h2>{{trans('admin.prtg-ids-title')}}</h2>
		<p>{{trans('admin.prtg-ids-description')}}</p>

		<div class="row  text-left" style="width:250px; margin:0 auto 20px auto;">
			<input tabindex="1" id="tags" class="form-control tagging" type="text" value="@if(!empty($prtgIds)) @foreach($prtgIds as $key => $value) {{$value}}, @endforeach @endif" name="tags" placeholder="{{trans('admin.prtg-ids-placeholder')}}">
		</div>

		<button type="submit" tabindex="10" class="center-block btn save_all btn-info " title="{{trans('admin.save')}}">
			<i class="fa fa-save padding-right-5"></i> {{trans('admin.save')}}
		</button>
	</form>
</div>
