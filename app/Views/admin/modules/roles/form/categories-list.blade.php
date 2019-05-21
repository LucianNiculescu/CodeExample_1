<ul class="nav nav-tabs nav-tabs-line col-xs-4 padding-right-0 padding-left-0 categories" data-plugin="nav-tabs">

{{-- Item with search box --}}
	<li class="nav-search">
		<div class="input-group form-material">
			<span class="input-group-addon"><i class="fa fa-search"></i></span>
			<input type="text"  id="searchPermissionsCategories" class="form-control" name="search" placeholder="{{trans('admin.search-permissions')}}">
		</div>
	</li>

	@if(empty($categories) )
		<p class="clearfix margin-top-10">{{trans('admin.no-permissions-categories')}}</p>
	@endif

	@foreach($categories as $id => $category)
		@if(!in_array($category, ['role-manage', 'widgets']) )
			<li class="clearfix category_{{$id}} permissions-categories searchable">
				{{--<a href="" title="{{ trans('help.'.$category) }}" data-container="body" data-toggle="tooltip" data-placement="right" style="{{$style ?? ''}}" class="category-header"  data-category="{{$category}}">--}}
				<a href="#category_{{$id}}_tab" data-toggle="tab" class="pull-right" id="{{$id}}" title="{{trans('help.'.$category)}}">
					<div class="pull-right">
						{{ trans('admin.'.$category) }}
					</div>
				</a>
			</li>
		@endif
	@endforeach
</ul>


