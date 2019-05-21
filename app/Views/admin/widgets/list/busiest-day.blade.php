<div class="panel-body bg-guest-reports" id="busiestDay">
	{{--{{dd($dashboardData)}}--}}
	@if($dashboardData != null)
		<div class="busiest-day-container padding-10">
			<div class="day-icon center-text white">
				<i class="fa fa-calendar white center-text font-size-70" aria-hidden="true"></i>
			</div>
			<div class="day-name center-text white font-size-40">

					{{$dashboardData->busiest_active_connections_day}}

			</div>
			<div class="day-text center-text white small-line-height">
				{{trans('admin.busiest-day-by-number-of-guests')}}
			</div>
		</div>
	@else
		<div class="text-center">
			<h2 class="white">{{trans('admin.no-busiest-day-data-for-this-site-title')}}</h2>
			<p class="white">{{trans('admin.no-busiest-day-data-for-this-site')}}</p>
		</div>
	@endif
</div>