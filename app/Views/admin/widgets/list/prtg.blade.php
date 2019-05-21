@if(empty($prtgSiteAttributes))
	<div class="panel-body bg-guest-reports prtg-error">
		<div class="text-center">
			<h2 class="white">{!! trans('admin.please-set-up-prtg') !!}</h2>
		</div>
	</div>
@else
	@include('admin.templates.system.loading', ['loadingStyle' => 'position: absolute; top: 90px;'])
	<div id="prtgSettingsWidget" class="bg-settings-reports panel-body prtg-settings-content @if(count(\Request::cookie('prtgWidget_'.session('admin.site.loggedin').'_'.session('admin.user.id'))['sensor_ids']) > 0) hidden @endif">
		<h3 class="margin-0">
			{{trans('admin.settings')}}
		</h3>
		<div class="settings-widget-container">
			<div class="period-buttons" data-toggle="buttons">
				<div class="row">
					<div class="col-md-4">
						<div class="btn btn-info period-btn col-xs-6">
							<input type="radio" name="prtg-report-period" id="prtg-last-24-hours" default="last-24-hours" autocomplete="off">
							<div class="day-icon margin-top-10 margin-bottom-5">
								<i class="fa fa-clock-o center-text font-size-30" aria-hidden="true"></i>
							</div>
							{{trans('admin.24-hours')}}
						</div>
						<div class="btn btn-info period-btn  col-xs-6 @if(!isset($titleClass)) active @endif">
							<input type="radio" name="prtg-report-period" id="prtg-last-2-days" default="last-2-days" autocomplete="off" @if(!isset($titleClass)) checked @endif>
							<div class="day-icon center-text margin-top-10  margin-bottom-5">
								<i class="fa fa-calendar font-size-30" aria-hidden="true"></i>
							</div>
							{{trans('admin.2-days')}}
						</div>
						<div class="btn btn-info period-btn col-xs-6">
							<input type="radio" name="prtg-report-period" id="prtg-last-week" default="last-week" autocomplete="off">
							<div class="day-icon center-text margin-top-10  margin-bottom-5">
								<i class="fa fa-calendar font-size-30" aria-hidden="true"></i>
							</div>
							{{trans('admin.week')}}
						</div>
						<div class="btn btn-info period-btn col-xs-6">
							<input type="radio" name="prtg-report-period" id="prtg-last-month" default="last-month" autocomplete="off">
							<div class="day-icon center-text margin-top-10  margin-bottom-5">
								<i class="fa fa-calendar font-size-30" aria-hidden="true"></i>
							</div>
							{{trans('admin.month')}}
						</div>

						<div class="periods  col-xs-12">
							<div class="datepicker-container">
								<div class="datepicker"></div>
								<input type="hidden" name="prtg-period" id="prtg-period" value="last-2-days">
								<input type="hidden" name="prtg-period-from" id="prtg-period-from">
								<input type="hidden" name="prtg-period-to" id="prtg-period-to">
							</div>
						</div>
					</div>
					<div class="col-md-6 prtg-dropdown-settings col-md-offset-2">
						@include('admin.templates.system.input-fields.basic.select', [
							'tabindex' 		=> 1000,
							'columnName'    => 'prtg_avg',
							'list'			=> $prtgAvgList,
							'value'         => 86400,
							'label'         => trans('admin.average'),
							'placeholder'   => trans('admin.average-placeholder'),
							'class'    		=> 'prtg-avg'
						])

						@include('admin.templates.system.input-fields.basic.select', [
							'tabindex' 		=> 1001,
							'columnName'    => 'prtg_group',
							'list'			=> $prtgGroups,
							'value'         => '',
							'label'         => trans('admin.group'),
							'placeholder'   => trans('admin.group-placeholder'),
							'class'    		=> 'prtg-group'
						])

						@include('admin.templates.system.input-fields.basic.select', [
							'tabindex' 		=> 1100,
							'columnName'    => 'prtg_name',
							'list'			=> [],
							'validation'    => 'disabled',
							'value'         => '',
							'label'         => trans('admin.name'),
							'placeholder'   => trans('admin.name-placeholder'),
							'class'    		=> 'prtg-name',
						])

						@include('admin.templates.system.input-fields.basic.select', [
							'tabindex' 		=> 1200,
							'columnName'    => 'prtg_type',
							'list'			=> [],
							'validation'    => 'disabled',
							'value'         => '',
							'label'         => trans('admin.type'),
							'placeholder'   => trans('admin.type-placeholder'),
							'class'    		=> 'prtg-type',
						])
						<div class="btn btn-info prtg-custom-period period-btn text-center col-xs-offset-3 col-xs-6 ">
							<input type="radio" name="prtg-report-period" id="prtg_init_widget" autocomplete="off" default="custom-period" data-flag="1">

							{{trans('admin.go')}}
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="widget-content">
		<!-- Nav tabs -->
		<ul class="nav nav-tabs prtg-nav" role="tablist" id="prtg_navbar">
		</ul>

		<div class="tab-content" id="prtg_tab_content">
		</div>
	</div>
@endif

@push('footer-js')
	<script>
		var locale = "{{\App::getLocale()}}";
	</script>
@endpush




