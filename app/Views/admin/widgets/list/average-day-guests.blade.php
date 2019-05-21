<div class="panel-body bg-guest-reports" id="avgDayUsers">

	@if($dashboardData != null)
		<div class="avg-day-users-container padding-10">
			<div class="day-icon center-text white">
				<i class="fa fa-users white center-text font-size-70" aria-hidden="true"></i>
			</div>
			<div class="day-name center-text white font-size-40">
				@if( is_null($dashboardData->registered_users) || $dashboardData->registered_users == 0 )
					0
				@else
					{{ number_format($dashboardData->registered_users / $dashboardData->active_days, 0) }}
				@endif
			</div>
			<div class="day-text center-text white small-line-height">
				{{trans('admin.avg-day-guests')}}
			</div>
		</div>
	@else
		<div class="text-center">
			<h2 class="white">{{trans('admin.no-avg-day-guests-available-title')}}</h2>
			<p class="white">{{trans('admin.no-avg-day-guests-available')}}</p>
		</div>
	@endif
</div>