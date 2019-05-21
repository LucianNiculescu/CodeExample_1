<div class="panel-body bg-guest-reports" id="avgTimeOnline">
	@if($dashboardData != null)
		<div class="average-time-online-container padding-10">
			<div class="day-icon center-text white">
				<i class="fa fa-clock-o white center-text font-size-70" aria-hidden="true"></i>
			</div>
			<div class="day-name center-text white font-size-40">

				@if( isset( $dashboardData->sum_session_time ) && $dashboardData->sum_session_time > 0 )

					<?php
						if(is_null($dashboardData->users_seen) || $dashboardData->users_seen == 0)
							$usageInfoAverageTime = 0;
						else
							$usageInfoAverageTime = intval(( $dashboardData->sum_session_time / $dashboardData->users_seen) / 60);
					?>
					@if( $usageInfoAverageTime < 200 )
						{{ $usageInfoAverageTime . ' ' .trans('admin.mins') }}
					@elseif( $usageInfoAverageTime < 2880 )
						{{ intval($usageInfoAverageTime / 60) . ' ' .trans('admin.hours') }}
					@else
						{{ intval($usageInfoAverageTime / 1440) . ' ' .trans('admin.days') }}
					@endif

				@else
					0 {{ trans('admin.seconds') }}
				@endif
			</div>
			<div class="day-text center-text white small-line-height">
				{{trans('admin.average-time-online')}}
			</div>
		</div>
	@else
		<div class="text-center">
			<h2 class="white">{{trans('admin.no-average-time-online-data-title')}}</h2>
			<p class="white">{{trans('admin.no-average-time-online-data')}}</p>
		</div>
	@endif
</div>