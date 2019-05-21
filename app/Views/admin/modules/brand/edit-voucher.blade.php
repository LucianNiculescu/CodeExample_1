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
		'label'         	=> trans('admin.voucher'),
		'placeholder'   	=> trans('admin.voucher-placeholder'),
		'help'          	=> trans('help.brand|fields|voucher'),
		'value' 			=> $contents
	])

	{{--Template name--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'name',
		'value'			=> $voucherType,
	])

	{{--Language--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'language',
		'value'			=> $language,
	])

	{{--Portal--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'portal',
		'value'			=> $portalId,
	])

	{{--Site--}}
	@include('admin.templates.system.input-fields.basic.hidden',
	[
		'columnName'    => 'site',
		'value'			=> $siteId,
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