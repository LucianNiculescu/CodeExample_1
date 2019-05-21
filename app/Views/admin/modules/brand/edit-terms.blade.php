@extends('admin.templates.system.form')

@section('form-contents')

	@include('admin.wysiwyg')
	@include('admin.templates.system.input-fields.basic.textarea',[
		'tabindex'      	=> 1,
		'autofocus' 		=> 'autofocus',
		'extraClass' 		=> ($view) ? 'read-only-wysiwyg' : 'full-wysiwyg',
		'customLabelSize' 	=> 'hidden',
		'customInputSize' 	=> 'col-md-12',
		'customHelpSize' 	=> 'hidden',
		'columnName'    	=> 'value',
		'label'         	=> trans('admin.terms'),
		'placeholder'   	=> trans('admin.terms-and-conditions-placeholder'),
		'help'          	=> trans('help.brand|fields|terms'),
		'value' 			=> $contents
	])

	{{--Template name--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'name',
		'value'			=> 'terms',
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
		'value'			=> 'content',
	])

	{{--Status--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'status',
		'value'			=> 'active',
	])

@endsection