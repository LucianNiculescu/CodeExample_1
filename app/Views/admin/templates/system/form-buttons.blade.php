<div class="buttons col-sm-12 padding-0 margin-0">
	{!! $extraButton or '' !!}

	@if(!isset($hideCancel))
		<a tabindex="111" href="{{$cancelUrl or $actionUrl}}" type="button" class="btn btn-default pull-right cancel-btn" title="{{trans('admin.cancel')}}">
			<i class="fa fa-ban padding-right-5"></i>
			{{trans('admin.cancel')}}
		</a>
	@endif

	@if(!isset($hideSave))
		<button type="submit" tabindex="112" class="btn save_all btn-info pull-right margin-right-5 submit-btn" title="{{trans('admin.save')}}" >
			<i class="fa fa-save padding-right-5"></i>
			{{trans('admin.save')}}
		</button>
	@endif
</div>