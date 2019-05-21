<div class="panel-body bg-guest-reports" id="device">

	@if($dashboardData != null)
		<div class="data-per-user-container padding-10">
			<div class="day-icon center-text white">
				<i class="fa fa-tablet white center-text font-size-70" aria-hidden="true"></i>
			</div>
			<div class="day-name center-text white font-size-40">
				{{round($dashboardData->most_popular_platform_percent)}}%
			</div>
			<div class="day-text center-text white small-line-height">
				{{trans('admin.' . $dashboardData->most_popular_platform)}}
			</div>
		</div>
	@else
		<div class="text-center">
			<h2 class="white">{{trans('admin.no-device-data-available-title')}}</h2>
			<p class="white">{{trans('admin.no-device-data-available')}}</p>
		</div>
	@endif
</div>