<?php
	// TODO: Move to composer
	$profile_pic_size = 100;
	$profil_pic_url = "https://www.gravatar.com/avatar/" . md5(strtolower(trim($email))) . "?d=" . urlencode($default) . "&s=" . $profile_pic_size;
?>
<form class="validate-me-diff" action="/manage-profile" method="post" id="profile">
	{!! csrf_field() !!}
	<div class="user-profile-container">
		<div class="container">

			<div class="row">

				<div class="col-md-12 padding-top-20">
					<button class="btn pull-right profile-cancel" title="{{trans('admin.close')}}">
						<i class="fa fa-close"></i>
					</button>
				</div>

				<div class="col-md-4 col-sm-12 col-xs-12 padding-top-20">
					<img src="{{ $profil_pic_url }}" alt="{{ session('admin.user.username') }}" class="profile-avatar">
					<h2 class="profile-username">
						{{ session('admin.user.username') }}<br>
						<small>{{ trans('admin.role') }} {{ session('admin.user.role') }}</small>
					</h2>
				</div>

				<div class="col-md-4 col-sm-12 col-xs-12 padding-top-20">
					{{--Password--}}
					@include('admin.templates.system.input-fields.basic.input',
					[
						'tabindex' 		=> 101,
						'type'          => 'password',
						'columnName'    => 'profile_password',
						'label'         => trans('admin.password'),
						'placeholder'   => trans('admin.password-placeholder'),
						'help'          => trans('admin.password-help'),
						'value'			=> ''
					])

					{{--Repeat Password--}}
					@include('admin.templates.system.input-fields.basic.input',
					[
						'tabindex' 			=> 102,
						'type'          	=> 'password',
						'columnName'    	=> 'profile_pass',
						'validation'    	=> 'equalTo=#profile_password',
						'label'         	=> trans('admin.repeat-password'),
						'placeholder'   	=> trans('admin.repeat-password-placeholder'),
						'help'          	=> trans('admin.repeat-password-help'),
						'value'				=> '',
					])
				</div>

				<div class="col-md-4 col-sm-12 col-xs-12 padding-top-20">

					{{--Languages--}}
					@include('admin.templates.system.input-fields.basic.select',
					[
						'tabindex' 		=> 103,
						'columnName'    => 'profile_language',
						'list'			=> \App\Models\Airconnect\Translation::getLanguages('admin'),
						'default'		=> session('admin.user.language'),
						'label'         => trans('admin.language'),
						'placeholder'   => trans('admin.language-placeholder'),
						'help'          => trans('admin.language-help'),
						'value'			=> session('admin.user.language')
					])

					{{--TimeZone--}}
					@include('admin.templates.system.input-fields.basic.select-with-groups',
					[
						'tabindex' 		=> 104,
						'columnName'    => 'profile_timezone',
						'list'			=> \App\Helpers\DateTime::getTimeZones(),
						'value'			=> session('admin.user.timezone'),
						'label'         => trans('admin.timezone'),
						'placeholder'   => trans('admin.timezone-placeholder'),
						'help'          => trans('admin.timezone-help'),
					])
				</div>
			</div>

			<div class="row margin-bottom-20">

				<div class="col-md-12 col-sm-12 col-xs-12">
					<button type="submit" class="btn pull-right" title="{{trans('admin.save')}}" form="profile" value="submit">
						<i class="fa fa-save padding-right-5"></i>
						{{trans('admin.save')}}
					</button>
				</div>

			</div>

		</div>
	</div>

</form>