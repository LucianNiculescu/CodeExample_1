<h3 class="margin-0">
	{{trans('admin.look-and-feel')}}
	<small>
		@include('admin.templates.system.input-fields.help', ['help' => trans('help.brand|fields|look-and-feel')])
	</small>
</h3>


<form autocomplete="off" class="form-horizontal form_blade validate-me" method="post" action="/manage/brand/look-and-feel/{{ $siteId }}/edit" id="form" enctype="multipart/form-data">
	{!! csrf_field() !!}

	@can('access', 'manage.brand.look-and-feel-edit')

		{{-- Logo upload --}}
		@include('admin.templates.system.input-fields.image-uploader', [
			'label'			=> trans('admin.logo'),
			'columnName'	=> 'logo-file',
			'extension'		=> '.png',
			'default'		=> config('app.defaultLogo'),
			'preview'		=> $lookAndFeel['logoFileName'],
		])

		{{-- Background file upload --}}
		@include('admin.templates.system.input-fields.image-uploader', [
			'label'			=> trans('admin.background'),
			'columnName'	=> 'background-file',
			'extraClass'	=> '',
			'extension'		=> '.jpg',
			'default'		=> config('app.defaultBackground'),
			'preview'		=> $lookAndFeel['backgroundFileName']
		])

		{{-- Background (main) color--}}
		@include('admin.templates.system.input-fields.basic.input', [
			'disableLabelFloat' => true,
			'customFullSize' 	=> 'col-lg-4 col-md-4 col-sm-12 col-xs-12',
			'customLabelSize' 	=> 'col-lg-12 col-md-12 col-sm-12 col-xs-12',
			'customInputSize' 	=> 'col-lg-12 col-md-12 col-sm-12 col-xs-12',
			'tabindex' 			=> 1,
			'columnName' 		=> 'background_color',
			'value' 			=> $lookAndFeel['backgroundColor'],
			'label' 			=> trans('admin.background_color'),
			'placeholder' 		=> trans('admin.background_color-placeholder'),
			'extraClass'    	=> 'asColorpicker colorInputUi-input',
			'validation'    	=> 'data-plugin=asColorPicker data-mode=simple '
		])

		{{-- Border (secoundry) color --}}
		@include('admin.templates.system.input-fields.basic.input', [
			'disableLabelFloat' => true,
			'customFullSize' 	=> 'col-lg-4 col-md-4 col-sm-12 col-xs-12',
			'customLabelSize' 	=> 'col-lg-12 col-md-12 col-sm-12 col-xs-12',
			'customInputSize' 	=> 'col-lg-12 col-md-12 col-sm-12 col-xs-12',
			'tabindex' 			=> 2,
			'columnName'    	=> 'border_color',
			'value'		    	=> $lookAndFeel['borderColor'],
			'label'         	=> trans('admin.border_color'),
			'placeholder'   	=> trans('admin.border_color-placeholder'),
			'extraClass'    	=> 'asColorpicker colorInputUi-input',
			'validation'    	=> 'data-plugin=asColorPicker data-mode=simple '
		])

		{{-- Extra color --}}
		@include('admin.templates.system.input-fields.basic.input', [
			'disableLabelFloat' => true,
			'customFullSize' 	=> 'col-lg-4 col-md-4 col-sm-12 col-xs-12',
			'customLabelSize' 	=> 'col-lg-12 col-md-12 col-sm-12 col-xs-12',
			'customInputSize' 	=> 'col-lg-12 col-md-12 col-sm-12 col-xs-12',
			'tabindex' 			=> 3,
			'columnName'    	=> 'extra_color',
			'value' 			=> $lookAndFeel['extraColor'],
			'label' 			=> trans('admin.extra_color'),
			'placeholder' 		=> trans('admin.extra_color-placeholder'),
			'extraClass' 		=> 'asColorpicker colorInputUi-input',
			'validation' 		=> 'data-plugin=asColorPicker data-mode=simple '
		])
	@else
		{{-- Logo upload --}}
		@include('admin.templates.system.input-fields.image-uploader', [
			'label' 			=> trans('admin.logo'),
			'columnName' 		=> 'logo-file',
			'extension' 		=> '.png',
			'default' 			=> config('app.defaultLogo'),
			'preview' 			=> $lookAndFeel['logoFileName'],
			'readonly'  		=> true
		])

		{{-- Background file upload --}}
		@include('admin.templates.system.input-fields.image-uploader', [
			'disableLabelFloat' =>true,
			'customFullSize' 	=> 'col-xs-12  col-sm-12 col-md-12 image-uploader',
			'customLabelSize' 	=> 'col-xs-12  col-sm-12 col-md-12 image-uploader-label',
			'customInputSize' 	=> 'col-xs-12  col-sm-12 col-md-12',
			'label'				=> trans('admin.background'),
			'columnName'		=> 'background-file',
			'extraClass'		=> '',
			'extension'			=> '.jpg',
			'default'			=> config('app.defaultBackground'),
			'preview'			=> $lookAndFeel['backgroundFileName'],
			'readonly' 			=> true
		])

		{{-- Background (main) color--}}
		@include('admin.templates.system.input-fields.basic.input', [
			'disableLabelFloat' => true,
			'customFullSize' 	=> 'col-md-4',
			'customLabelSize' 	=> 'col-md-12',
			'customInputSize' 	=> 'col-md-12',
			'tabindex' 			=> 1,
			'columnName' 		=> 'background_color',
			'value' 			=> $lookAndFeel['backgroundColor'],
			'label' 			=> trans('admin.background_color'),
			'placeholder' 		=> trans('admin.background_color-placeholder'),
			'type'				=> 'color',
			'disabledColor' 	=> true
		])

		{{-- Border (secoundry) color --}}
		@include('admin.templates.system.input-fields.basic.input', [
			'disableLabelFloat' => true,
			'customFullSize' 	=> 'col-md-4',
			'customLabelSize' 	=> 'col-md-12',
			'customInputSize' 	=> 'col-md-12',
			'tabindex' 			=> 2,
			'columnName' 		=> 'border_color',
			'value' 			=> $lookAndFeel['borderColor'],
			'label' 			=> trans('admin.border_color'),
			'placeholder' 		=> trans('admin.border_color-placeholder'),
			'type' 				=> 'color',
			'disabledColor' 	=> true
		])

		{{-- Extra color --}}
		@include('admin.templates.system.input-fields.basic.input', [
			'disableLabelFloat' => true,
			'customFullSize' 	=> 'col-md-4',
			'customLabelSize' 	=> 'col-md-12',
			'customInputSize' 	=> 'col-md-12',
			'tabindex' 			=> 3,
			'columnName' 		=> 'extra_color',
			'value'		    => $lookAndFeel['extraColor'],
			'label'         => trans('admin.extra_color'),
			'placeholder'   => trans('admin.extra_color-placeholder'),
			'type'			=> 'color',
			'disabledColor' => true
		])
	@endcan

	@can('access', 'manage.brand.look-and-feel-edit')
		<div class="buttons col-xs-12 col-sm-12 col-md-12 margin-top-0 padding-bottom-20">
			<a tabindex="4" href="/manage/brand" type="button" class="btn btn-default pull-right " id="reset-btn" title="{{trans('admin.cancel')}}">
				<i class="fa fa-ban padding-right-5"></i>
				{{trans('admin.cancel')}}
			</a>

			<button type="submit" class="btn btn-info pull-right margin-right-5 " id="save-btn" title="{{trans('admin.save')}}" >
				<i class="fa fa-save padding-right-5"></i>
				{{trans('admin.save')}}
			</button>
		</div>
	@endcan
</form>

