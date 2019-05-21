<div id="csvSettings" class="bg-settings-reports panel-body">
	<div class="settings-widget-container">
		<div class="period-buttons" data-toggle="buttons">
			<div class="btn btn-info period-btn col-xs-6 @if(in_array(\Request::route()->getName(), ['reports.technology-reports', 'reports.financial-reports'])) disabled @endif">
				<input type="radio" name="report-period" id="last-24-hours" autocomplete="off">
				<div class="day-icon margin-top-10 margin-bottom-5">
					<i class="fa fa-clock-o center-text font-size-30" aria-hidden="true"></i>
				</div>
				{{trans('admin.24-hours')}}
			</div>
			<div class="btn btn-info period-btn  col-xs-6 @if(!isset($titleClass)) active @endif">
				<input type="radio" name="report-period" id="last-week" autocomplete="off" @if(!isset($titleClass)) checked @endif>
				<div class="day-icon center-text margin-top-10  margin-bottom-5">
					<i class="fa fa-calendar font-size-30" aria-hidden="true"></i>
				</div>
				{{trans('admin.week')}}
			</div>
			<div class="btn btn-info period-btn col-xs-6">
				<input type="radio" name="report-period" id="last-month" autocomplete="off">
				<div class="day-icon center-text margin-top-10  margin-bottom-5">
					<i class="fa fa-calendar font-size-30" aria-hidden="true"></i>
				</div>
				{{trans('admin.month')}}
			</div>
			<div class="btn btn-info period-btn col-xs-6">
				<input type="radio" name="report-period" id="last-year" autocomplete="off">
				<div class="day-icon center-text margin-top-10  margin-bottom-5">
					<i class="fa fa-calendar font-size-30" aria-hidden="true"></i>
				</div>
				{{trans('admin.year')}}
			</div>

			<div class="periods  col-xs-12">
				<div class="datepicker-container">
					<div class="datepicker"></div>
					<input type="hidden" name="period" id="period" value="last-week">
					<input type="hidden" name="period-from" id="period-from">
					<input type="hidden" name="period-to" id="period-to">
				</div>
			</div>

			<div class="btn btn-info custom-period period-btn text-center col-xs-offset-3 col-xs-6">
				<input type="radio" name="report-period" id="custom-period" autocomplete="off">

				{{trans('admin.go')}}
			</div>
		</div>
	</div>
</div>
<style>
/*	.grid-item * {background: black; !important;}*/

</style>

@push('footer-js')
<script>
	var locale = "{{\App::getLocale()}}";
</script>
@endpush








