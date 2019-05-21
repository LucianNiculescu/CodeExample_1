@extends('admin.templates.system.master')


@section('content')

<div class="show_blade">
	<div class="title">
		<h2>
			{{$title}}<small>{{$description}}</small>
		</h2>
	</div>

	<div class="form_actions">
		@can('access', $editAccess)
			<a href="{{$editUrl}}" class="btn btn-info btn-sm pull-right" title="Edit">
				<i class="fa fa-pencil fa_circle"></i>
			</a>
		@endcan
	</div>

	<div class="contents_frame">
		{{--Contents of the show will be here--}}
		@yield('show-contents')
	</div>

	<div class="buttons pull-left">
		@can('access', $editAccess)
			<a href="{{$editUrl}}" class="btn btn-info pull-right" title="Edit">
				<i class="fa fa-plus padding-right-5"></i>
				{{trans('admin.edit')}}
			</a>
		@endcan
	</div>
</div>

@include('admin.templates.system.loading-page')

@endsection


