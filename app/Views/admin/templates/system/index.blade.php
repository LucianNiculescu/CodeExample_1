@extends('admin.templates.system.master')



@section('content')


	<div>
		<h2 class="title {{$titleClass or ''}}">
			{{$title}}<small class="dashboard-description">{{$description}}</small>
		</h2>
	</div>

	<div class="form_actions">

		@include('admin.help-pages.button')

		@if(!isset($hideCreate))
			@can('access', $createAccess)
				<a href="{{$createUrl}}" class="btn btn-info btn-sm pull-right" title="{{trans('admin.create')}}">
					<i class="fa fa-plus fa_circle"></i>
				</a>
			@endcan
		@endif

		{{--extra button1--}}

		@if(isset($extraButton1))
			@can('access', (isset($extraButton1Access)? $extraButton1Access : ''))
				<a href="{{$extraButton1Url or ''}}" class="btn btn-default btn-sm pull-right {{$extraButton1Classes or ''}}" title="{{$extraButton1}}" {{$extraButton1Params or ''}}>
					<i class="fa fa_circle {{$extraButton1Icon}}"></i>
				</a>
			@endcan
		@endif
	</div>


	{{--Contents of the index --}}
	@yield('index-contents')

	@if(!isset($hideCreate))
		<div class="buttons pull-right">
			@can('access', $createAccess)
				<a href="{{$createUrl}}" class="btn btn-info pull-right" title="{{trans('admin.create')}}">
					<i class="fa fa-plus padding-right-5"></i>
					{{trans('admin.create')}}
				</a>
			@endcan
		</div>
	@endif


@include('admin.templates.system.loading-page')

@endsection


