@extends('admin.templates.system.index')

@section('index-contents')

	<div class="nav-tabs-vertical">

		{{-- Left hand navigation with search and list of Help categories --}}
		@include('admin.modules.help.help-list')
		{{--showing tabs per help--}}
		@include('admin.modules.help.help-content')

	</div>

	<script>
		$(document).ready(function(){
			$("body.help #searchHelpCategories").keyup(function(){
				return filter($(this), ".help-categories.searchable");
			});
			autoScrollDiv($('body.help .scrollable'), 150);
		})
	</script>

@endsection


