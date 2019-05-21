<div class="panel-body bg-guest-reports" id="demographicsBody">
	@include('admin.templates.system.loading', ['loadingStyle' => 'position: absolute; top: 90px;', 'spinnerClasses' => 'white'])
</div>

<script>
	var demographicsData = new Array();
	@if(isset($dashboardData))
		@if(session('admin.site.type') != 'site')

			demographicsData = [
			['{{number_format($dashboardData->sum('registered_users') ?? 0) }}', 'fa-users', '{{trans('admin.all-guests')}}'],
			['{{number_format($dashboardData->sum('reg_users_airpass') ?? 0)}}', 'fa-envelope-o', '{{trans('admin.email')}}'],
			['{{number_format($dashboardData->sum('reg_users_facebook') ?? 0)}}', 'fa-facebook-square', '{{trans('admin.facebook')}}'],
			['{{number_format($dashboardData->sum('reg_users_twitter') ?? 0)}}', 'fa-twitter', '{{trans('admin.twitter')}}'],
			['{{number_format($dashboardData->sum('reg_users_linkedin') ?? 0)}}', 'fa-linkedin', '{{trans('admin.linkedin')}}'],
			['{{number_format($dashboardData->sum('reg_users_google') ?? 0)}}', 'fa-google-plus', '{{trans('admin.google')}}']
			];
		@else
			demographicsData = [
				['{{number_format($dashboardData->registered_users ?? 0) }}', 'fa-users', '{{trans('admin.all-guests')}}'],
				['{{number_format($dashboardData->reg_users_airpass ?? 0)}}', 'fa-envelope-o', '{{trans('admin.email')}}'],
				['{{number_format($dashboardData->reg_users_facebook ?? 0)}}', 'fa-facebook-square', '{{trans('admin.facebook')}}'],
				['{{number_format($dashboardData->reg_users_twitter ?? 0)}}', 'fa-twitter', '{{trans('admin.twitter')}}'],
				['{{number_format($dashboardData->reg_users_linkedin ?? 0)}}', 'fa-linkedin', '{{trans('admin.linkedin')}}'],
				['{{number_format($dashboardData->reg_users_google ?? 0)}}', 'fa-google-plus', '{{trans('admin.google')}}']
			];
		@endif
	@endif

</script>