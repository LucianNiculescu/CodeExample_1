
<form autocomplete="off" class="form-horizontal" method="post" action="/search" id="search-form">
	{!! csrf_field() !!}
	<div class="input-group form-material">
		<input type="text" class="search-guests form-control" name="search" placeholder="{{trans('admin.search')}}" value="@if(\Route::currentRouteName() == 'search'){{$search ?? ''}}@endif" minlength="2" required>
		<span class="input-group-btn">
		  <button type="submit" class="btn search-guests"><i class="icon wb-search" aria-hidden="true"></i></button>
		</span>
	</div>
	<input type="hidden" class="from" name="from" value="0">
	<input type="hidden" class="search_type" name="search_type" value="all">
</form>

