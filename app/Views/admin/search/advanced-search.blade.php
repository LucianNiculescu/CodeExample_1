@extends('admin.templates.system.master')

@section('content')
	<div class="title">
		<h2>
			{{$title}} <small>{{$description}}</small>
		</h2>
	</div>
	<div class="row">
		<div class="col-sm-12 search-panel">
			<form autocomplete="off" class="" method="post" action="/search" id="advanced-search-form">
				{!! csrf_field() !!}
				<div class="row">
					<div class="col-md-5">
						@include('admin.templates.system.input-fields.basic.select', [
							'tabindex' 		=> 1,
							'columnName'    => 'search_type',
							'list'		    => $searchTypeList,
							'label'         => trans('admin.search-type'),
							'placeholder'   => trans('admin.search-type-placeholder'),
							'value'			=> $searchType ?? 'all',
							'class'			=> 'search_type'

						])
					</div>
					<div class="col-md-5">
						@include('admin.templates.system.input-fields.basic.input', [
							'tabindex' 		=> 2,
							'columnName'    => 'search',
							'label'         => trans('admin.search'),
							'placeholder'   => trans('admin.search-placeholder'),
							'value'			=> $search ?? '',
							'extraClass'	=> 'advanced-search-field',
						])
					</div>
					<div class="col-md-2">
						<button type="submit" class="btn btn-info config-btn" >{{trans('admin.search')}}</button>
					</div>
				</div>

				<input type="hidden" class="from" name="from" value="0">
			</form>
		</div>
	</div>
	@if(isset($result))
		<div>
			@include('admin.search.results')
		</div>
	@endif

	@push('footer-js')
		<script>
			$( document ).ready(function() {

				enableDisableSearchButton()
			});
			var body = $('body');

			body.on('change keyup', '.advanced-search-field', function(){
				enableDisableSearchButton();
			});

			function enableDisableSearchButton()
			{
				var advancedSearchForm = $('#advanced-search-form');
				if(advancedSearchForm.find('#search_type').find(":selected").text() != '' && advancedSearchForm.find('#search').val() != '')
					advancedSearchForm.find('button').prop("disabled", false);
				else
					advancedSearchForm.find('button').prop("disabled", "disabled");
			}
		</script>
	@endpush
@endsection
