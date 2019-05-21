@extends('admin.templates.system.master')

@section('content')

	<div class="title">
		<h2>
			{{$title}} <small>{{$description}}</small>
		</h2>
	</div>

	<form
			autocomplete="off"
			id="crudForm"
			class="form-horizontal form_blade validate-me"
			method="post"
			action="{{ $actionUrl }}"
			@if(isset($uploadFiles)) enctype="multipart/form-data" @endif>

		{!! csrf_field() !!}
		{{ method_field( isset($hiddenMethod) ? $hiddenMethod : 'POST') }}

		{{--top right div with action buttons, add save and cancel--}}
		<div class="form_actions">

			@include('admin.help-pages.button')

			{{--Cancel button--}}
			@if(!isset($hideCancel))
				<a href="{{$cancelUrl or $actionUrl}}" class="btn btn-default btn-sm pull-right cancel-btn" title="{{trans('admin.cancel')}}">
					<i class="fa fa-ban fa_circle"></i>
				</a>
			@endif

			{{--Save button--}}
			@if(!isset($hideSave))
				<button type="submit" class="btn btn-info btn-sm pull-right submit-btn" title="{{trans('admin.save')}}">
					<i class="fa fa-save"></i>
				</button>
			@endif
			{{--extra button1--}}

			@if(isset($extraButton1))
				@can('access', (isset($extraButton1Access)? $extraButton1Access : ''))
					<a href="{{$extraButton1Url or ''}}" class="btn btn-default btn-sm pull-right {{$extraButton1Classes or ''}}" title="{{$extraButton1}}" {{$extraButton1Params or ''}}>
						<i class="fa fa_circle {{$extraButton1Icon}}"></i>
					</a>
				@endcan
			@endif

			{{--extra button2--}}

			@if(isset($extraButton2))
				@can('access', (isset($extraButton2Access)? $extraButton2Access : ''))
					<a href="{{$extraButton2Url or ''}}" class="btn btn-default btn-sm pull-right {{$extraButton2Classes or ''}}" title="{{$extraButton2}}" {{$extraButton2Params or ''}}>
						<i class="fa fa_circle {{$extraButton2Icon}}"></i>
					</a>
				@endcan
			@endif

		</div>

		{{--Contents of the form will go here--}}
		@if( isset($container) && $container===false)
			@yield('form-contents')
		@else
			<div class="container contents_frame">
				@yield('form-contents')
			</div>
		@endif

		<div class="buttons col-sm-12 padding-0">
			{{--<a href="{{$actionUrl}}" type="button" class="btn btn-default pull-right" title="{{trans('admin.help')}}">--}}
				{{--<i class="fa padding-right-5 fa-question-circle "></i>--}}
				{{--{{trans('admin.help')}}--}}
			{{--</a>--}}
			@if(!isset($hideCancel))
				<a tabindex="111" href="{{$cancelUrl or $actionUrl}}" type="button" class="btn btn-default pull-right cancel-btn" title="{{trans('admin.cancel')}}">
					<i class="fa fa-ban padding-right-5"></i>
					{{trans('admin.cancel')}}
				</a>
			@endif
			@if(isset($saveAndPreview))
				<button type="submit" id="saveAndPreview" name="preview" class="btn btn-default pull-right margin-right-5 action action_preview" title="{{ trans('admin.save-preview') }}">
					<i class="fa fa-eye padding-right-5"></i>
					{{ trans('admin.save-preview') }}
				</button>
			@endif
			@if(!isset($hideSave))
				<button type="submit" id="submitButton" tabindex="112" class="btn save_all btn-info pull-right margin-right-5 submit-btn" title="{{trans('admin.save')}}" >
					<i class="fa fa-save padding-right-5"></i>
					{{trans('admin.save')}}
				</button>
			@endif
			@if(isset($showDelete) && is_object($deleteObject))
				@can('access', $deletePermission)
					<a href="{{$deleteObject->deleteUrl}}" id="deleteButton" data-id = "{{$deleteObject->id}}" data-name="{{$deleteObject->title}}" class="btn btn-danger pull-right margin-right-5 action action_delete">
						<i class="fa fa-remove padding-right-5"></i> {{ trans('admin.delete') }}
					</a>
				@endcan
			@endif
		</div>
	</form>
@endsection

