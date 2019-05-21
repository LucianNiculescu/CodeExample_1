<ul class="nav nav-tabs nav-tabs-line col-xs-4 padding-right-0 padding-left-0 categories" data-plugin="nav-tabs">

	{{-- Item with search box --}}
	<li class="nav-search">
		<div class="input-group form-material">
			<span class="input-group-addon"><i class="fa fa-search"></i></span>
			<input type="text"  id="searchHelpCategories" class="form-control" name="search" placeholder="{{trans('admin.search-help')}}">
		</div>
	</li>
	@if(is_array($helpArray))
		@foreach($helpArray as $index => $content)
			<li class="clearfix help_{{$index}} help-categories searchable">
				<a href="#{{ $index }}Main" data-toggle="tab" class="pull-right" id="{{$index}}" aria-controls="{{ $index }}Main" title="{{trans('help.'.$index.'.index')}}">
					<div class="pull-right">
						{{ trans('admin.'.$index) }}
					</div>
				</a>
			</li>
		@endforeach
	@endif

	{{-- Loop through the array and create the index
	@if(is_array($helpArray))
		@foreach( $helpArray as $index => $content )
			<li role="presentation"><a data-toggle="tab" href="#{{ $index }}Main" aria-controls="{{ $index }}Main" role="tab">{{ trans( 'admin.' .$index .'') }}</a></li>
		@endforeach
	@endif
	--}}
</ul>