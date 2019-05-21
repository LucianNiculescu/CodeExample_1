<div id="reports-settings" class="btn-toolbar margin-bottom-30" role="toolbar">
	<div class="btn-group btn-group-justified {{ $reportTypeCss }}" data-toggle="buttons" role="group" aria-label="Period settings">
		<div class="btn-group" role="group" aria-label="Period settings">
			<div class="btn btn-info period-btn @if(in_array(\Request::route()->getName(), ['reports.technology-reports', 'reports.financial-reports'])) disabled @endif">
				<div class="dont-touch" title="{{ trans('help.reports|no-suitable-data') }}">
				</div>
				<input type="radio" name="report-period" id="last-24-hours" autocomplete="off">
				<div class="day-icon center-text margin-top-10 margin-bottom-5">
					<span class="fa-stack">
						<i class="fa fa-clock-o fa-2x" aria-hidden="true"></i>
					</span>
					{{trans('admin.24-hours')}}
				</div>
			</div>
		</div>
		<div class="btn-group" role="group" aria-label="Period settings">
			<div class="btn btn-info period-btn @if(!isset($titleClass)) active @endif">
				<input type="radio" name="report-period" id="last-week" autocomplete="off" @if(!isset($titleClass)) checked @endif>
				<div class="day-icon center-text margin-top-10  margin-bottom-5">
					<span class="fa-stack">
						<i class="fa fa-calendar-o fa-2x" aria-hidden="true"></i>
						<strong class="fa-stack-1x calendar-text" aria-hidden="true">7</strong>
					</span>
					{{trans('admin.week')}}
				</div>
			</div>
		</div>
		<div class="btn-group" role="group" aria-label="Period settings">
			<div class="btn btn-info period-btn">
				<input type="radio" name="report-period" id="last-month" autocomplete="off">
				<div class="day-icon center-text margin-top-10  margin-bottom-5">
					<span class="fa-stack">
						<i class="fa fa-calendar-o fa-2x" aria-hidden="true"></i>
						<strong class="fa-stack-1x calendar-text" aria-hidden="true">31</strong>
					</span>
					{{trans('admin.month')}}
				</div>
			</div>
		</div>
		<div class="btn-group" role="group" aria-label="Period settings">
			<div class="btn btn-info period-btn">
				<input type="radio" name="report-period" id="last-year" autocomplete="off">
				<div class="day-icon center-text margin-top-10  margin-bottom-5">
					<span class="fa-stack">
						<i class="fa fa-calendar-o fa-2x" aria-hidden="true"></i>
						<strong class="fa-stack-1x calendar-text small" aria-hidden="true">365</strong>
					</span>
					{{trans('admin.year')}}
				</div>
			</div>
		</div>

		<div class="btn-group" role="group" aria-label="Period settings">
			<div id="custom-report-period" class="btn btn-info period-btn">
				<input type="radio" name="report-period" id="custom-period" autocomplete="off">
				<div class="day-icon center-text margin-top-10  margin-bottom-5">
					<span class="fa-stack">
						<i class="fa fa-calendar-o fa-2x" aria-hidden="true"></i>
						<strong class="fa-stack-1x calendar-text" aria-hidden="true">??</strong>
					</span>
					{{trans('admin.custom-dates')}}
				</div>
			</div>
		</div>

	</div>
	<div class="datepicker-toggle">
		<div class="periods col-sm-12">
			<div class="datepicker-container">
				<div class="datepicker margin-bottom-10"></div>
				<input type="hidden" name="period" id="period" value="last-week">
				<input type="hidden" name="period-from" id="period-from">
				<input type="hidden" name="period-to" id="period-to">
			</div>
			<div id="use-custom-period" class="btn btn-success custom-period text-center col-sm-offset-4 col-sm-4 margin-top-5 margin-bottom-5">
				{{trans('admin.go')}}
			</div>
		</div>
	</div>

</div>
