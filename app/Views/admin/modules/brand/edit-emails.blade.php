@extends('admin.templates.system.form')


@section('form-contents')

	{{--Notes--}}
{{--	@include('admin.templates.system.input-fields.basic.textarea',
	[
		'tabindex'      => 1,
		'columnName'    => 'value',
		'label'         => trans('admin.email-template'),
		'placeholder'   => trans('admin.email-template-placeholder'),
		'help'          => trans('help.brand|fields|email'),
		'value'			=> $emailContents,
	] +$viewMode )--}}

	<div class="col-md-12 col-sm-12 col-xs-12">
		<div class="form-group">
            <textarea class="form-control"  autofocus tabindex="1" id="value" name="value"  rows=20  placeholder="{{trans('admin.email-template-placeholder')}}"
			@if($view) READONLY @endif
			>{{$emailContents}}</textarea>
		</div>
	</div>

	{{--Template name--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'name',
		'value'			=> $emailTemplateName,
	])

	{{--Language--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'language',
		'value'			=> $portalLanguage,
	])

	{{--Portal--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'portal',
		'value'			=> $portalId,
	])

	{{--Type--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'type',
		'value'			=> 'email',
	])

	{{--Status--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'status',
		'value'			=> 'active',
	])


@endsection