<div class="panel-body bg-guest-reports" id="avgDataPerGuest">

	@if($dashboardData != null)
		<div class="data-per-user-container padding-10">
			<div class="day-icon center-text white">
				<i class="fa fa-user white center-text font-size-70" aria-hidden="true"></i>
			</div>
			<div class="day-name center-text white font-size-40">

				@if( isset( $dashboardData->users_seen ) && $dashboardData->users_seen > 0 )
					<?php /*$result = ($dashboardData->sum_upload_total + $dashboardData->sum_download_total) / $dashboardData->users_seen;*/ ?>
					{{round(number_format(($dashboardData->sum_upload_total + $dashboardData->sum_download_total) / $dashboardData->users_seen, 2))}} mb
				@else
					0 mb
				@endif

			</div>
			<div class="day-text center-text white small-line-height">
				{{trans('admin.avg-data-per-guest')}}
			</div>
		</div>
	@else
		<div class="text-center">
			<h2 class="white">{{trans('admin.no-avg-data-per-guest-available-title')}}</h2>
			<p class="white">{{trans('admin.no-avg-data-per-guest-available')}}</p>
		</div>
	@endif
</div>