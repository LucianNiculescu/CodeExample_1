
{{--Role Information--}}
<div class="role-info">
	<div class="col-md-6">

		{{--Name--}}
		@include('admin.templates.system.input-fields.basic.input',
		[
			'tabindex'      => 1,
			'autofocus'     => 'autofocus',
			'columnName'    => 'role',
			'validation'    => 'required maxlength="32"',
			'label'         => trans('admin.role-name'),
			'placeholder'   => trans('admin.role-name-placeholder'),
			'help'          => trans('help.roles|fields|role-name'),
		 ])


	</div>

	@if(isset($sites) or isset($site))
		<div class="col-md-6">

			{{--Sites--}}
			@if(isset($sites))
				@include('admin.templates.system.input-fields.basic.select',
				[
					'tabindex'      => 2,
					'columnName'    => 'site_id',
					'list'          => $sites,
					'default'       => session('admin.site.loggedin') ?? session('admin.user.site'),
					'validation'    => 'required' ,
					'label'         => trans('admin.site'),
					'placeholder'   => trans('admin.site-placeholder'),
					'help'          => trans('help.roles|fields|site'),
				])
			@elseif(isset($site))
				@include('admin.templates.system.input-fields.basic.input',
				[
					'tabindex'      => 2,
					'columnName'    => 'site_name',
					'value'			=>	$site,
					'validation'    => 'readonly' ,
					'label'         => trans('admin.site'),
					'help'          => trans('help.roles|fields|site'),
				])
			@endif
		</div>
	@endif

	<div class="col-md-6">
		{{-- Description --}}
		@include('admin.templates.system.input-fields.basic.textarea',[
			'tabindex'      	=> 3,
			'columnName'    	=> 'description',
			'label'         	=> trans('admin.description'),
			'placeholder'   	=> trans('admin.description-placeholder'),
			'help'          	=> trans('help.roles|fields|description'),
		])


	</div>

</div>
{{-- hidden Status--}}
@include('admin.templates.system.input-fields.basic.hidden',
[
	'columnName'    => 'status',
])
