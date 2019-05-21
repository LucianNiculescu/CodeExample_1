<div class="panel-body bg-guest-reports" id="loyalty">

    @if($dashboardData != null)
        <div class="loyalty-container padding-top-20">
            <div class="day-icon center-text white">
                <i class="fa fa-heart white center-text font-size-70" aria-hidden="true"></i>
            </div>
            <div class="day-name center-text white font-size-40" style="width:50%;float:left;">

                @if( isset($dashboardData->users_new) && $dashboardData->users_new > 0 )
                    {{round(number_format(($dashboardData->users_new / ($dashboardData->users_new + $dashboardData->users_returning)) * 100, 2))}}%
                @else
                    100%
                @endif

                <p style="font-size:18px;" class="small-line-height">{{trans('admin.new')}}</p>
            </div>
            <div class="day-name center-text white font-size-40" style="width:50%;float:left;">

				@if( isset($dashboardData->users_returning) && $dashboardData->users_returning > 0 )
					{{round(number_format(($dashboardData->users_returning / ($dashboardData->users_new + $dashboardData->users_returning)) * 100, 2))}}%
				@else
					100%
				@endif

                <p style="font-size:18px;" class="small-line-height">{{trans('admin.returning')}}</p>
            </div>
        </div>
    @else
        <div class="text-center">
            <h2 class="white">{{trans('admin.no-loyalty-stats-available-title')}}</h2>
            <p class="white">{{trans('admin.no-loyalty-stats-available')}}</p>
        </div>
    @endif
</div>
