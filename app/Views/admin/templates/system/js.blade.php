@if(isset($includeMapJs) && $includeMapJs)
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.key') }}"></script>
<script src="/admin/templates/system/vendor/googlemaps-libs/richmarker.js"></script>
@endif

<script src="{{ elixir('admin/templates/system/js/foot-script.js') }}"></script>

<!-- switchery switch colour - if no colour overrides, the default colours are used -->
@if($template != null || $template != '')
	<script src="/admin/templates/{{$template}}/js/switchery.js"></script>
@endif

@stack('footer-js')

<script>
	var locale = "{{\App::getLocale()}}";
	// Set defaults for all client side datatables.
	$.extend(  true, $.fn.dataTable.defaults, {
		"oLanguage"		: {
			"oPaginate"	:	{
				"sFirst"	:    "{{trans('admin.datatable-paginate-first')}}" ,
				"sPrevious"	:    "{{trans('admin.datatable-paginate-previous')}}" ,
				"sNext"		:    "{{trans('admin.datatable-paginate-next')}}" ,
				"sLast"		:    "{{trans('admin.datatable-paginate-last')}}"
			},
			"sInfo"			:    "{{trans('admin.datatable-info')}}" ,
			"sInfoEmpty"	:    "{{trans('admin.datatable-infoEmpty')}}" ,
			"sSearch"		:    "{{trans('admin.datatable-search')}}" ,
			"sEmptyTable"	:    "{{trans('admin.datatable-emptyTable')}}" ,
			"sLengthMenu"	:    "{{trans('admin.datatable-lengthMenu').' _MENU_ '.trans('admin.datatable-infoPostFix')}}"
	}});


	// Keep only the translation vars in this file and use with e.g. trans['are-you-sure']
	var trans = {
		'active'								: '{{trans('admin.active')}}',
		'adjets-validation'						: '{{trans('admin.adjets-validation')}}',
		'apply-template'						: '{{trans('admin.apply-template')}}',
		'are-you-sure' 							: '{{trans('admin.are-you-sure')}}', //'Are you sure?'
		'blocked'	 							: '{{trans('admin.blocked')}}',
		'block-device'							: '{{trans('admin.block-device')}}',
		'cancel'								: '{{trans('admin.cancel')}}',
		'click-to-deactivate' 					: '{{trans('admin.click-to-deactivate')}}',
		'click-to-activate' 					: '{{trans('admin.click-to-activate')}}',
		'clear-test-data' 						: '{{trans('admin.clear-test-data')}}',
		'done'									: '{{trans('admin.done')}}',
		'ok'									: '{{trans('admin.ok')}}',
		'info'									: '{{trans('admin.info')}}',
		'is-deleted' 							: '{{trans('admin.is-deleted')}}',
		'is-signed-in' 							: '{{trans('admin.is-signed-in')}}',
		'is-signed-out' 						: '{{trans('admin.is-signed-out')}}',
		'is-rebooted' 							: '{{trans('admin.is-rebooted')}}',
		'error'									: '{{trans('admin.error')}}',
		'error-saving'							: '{{trans('admin.error-saving')}}',
		'enabled'								: '{{trans('admin.enabled')}}',
		'disabled'								: '{{trans('admin.disabled')}}',
		'inactive'								: '{{trans('admin.inactive')}}',
		'pick-a-package'						: '{{trans('admin.pick-a-package')}}',
		'portal-template-overwrite-warning'		: '{{trans('admin.portal-template-overwrite-warning')}}',
		'portal-preview-save-warning'			: '{{trans('admin.portal-preview-save-warning')}}',
		'block-reason-placeholder'				: '{{trans('admin.block-reason-placeholder')}}',
		'reason-is-long'						: '{{trans('admin.reason-is-long')}}',
		'reason-is-required'					: '{{trans('admin.reason-is-required')}}',
		'reason-placeholder'					: '{{trans('admin.reason-placeholder')}}',
		'is-back-to-default'					: '{{trans('admin.is-back-to-default')}}',
		'server-error'							: '{{trans('admin.server-error')}}',
		'something-wrong'						: '{{trans('admin.something-wrong')}}',
		'time-out' 								: '{{trans('admin.time-out')}}',
		'to-delete' 							: '{{trans('admin.to-delete')}}',
		'to-activate' 							: '{{trans('admin.to-activate')}}',
		'to-upgrade' 							: '{{trans('admin.to-upgrade')}}',
		'to-enable-aaa' 						: '{{trans('admin.to-enable-aaa')}}',
		'enable-aaa' 							: '{{trans('admin.enable-aaa')}}',
		'aaa-enabled' 							: '{{trans('admin.aaa-enabled')}}',
		'aaa-disabled' 							: '{{trans('admin.aaa-disabled')}}',
		'disable-aaa' 							: '{{trans('admin.disable-aaa')}}',
		'to-disable-aaa' 						: '{{trans('admin.to-disable-aaa')}}',
		'to-deactivate' 						: '{{trans('admin.to-deactivate')}}',
		'to-block'								: '{{trans('admin.to-block')}}',
		'to-unblock'							: '{{trans('admin.to-unblock')}}',
		'to-reset'								: '{{trans('admin.to-reset')}}',
		'to-reboot'								: '{{trans('admin.to-reboot')}}',
		'reboot'								: '{{trans('admin.reboot')}}',
		'terms-and-conditions'					: '{{trans('admin.terms-and-conditions')}}',
		'to-clear-test-data'					: '{{trans('admin.to-clear-test-data')}}',
		'test-data-cleared'						: '{{trans('admin.test-data-cleared')}}',
		'to-send-reset-email'					: '{{trans('admin.to-send-reset-email')}}',
		'to-signin'								: '{{trans('admin.to-signin')}}',
		'to-signout'							: '{{trans('admin.to-signout')}}',
		'reset-email-sent'						: '{{trans('admin.reset-email-sent')}}',
		'unblock-device'						: '{{trans('admin.unblock-device')}}',
		'unblocked'								: '{{trans('admin.unblocked')}}',
		'yes-please' 							: '{{trans('admin.yes-please')}}',
		'mac-address-invalid' 					: '{{trans('admin.mac-address-invalid')}}',
		'add-sms-creds-to-site-attributes'		: '{{trans('admin.add-sms-creds-to-site-attributes')}}',
		'upgraded'								: '{{trans('admin.upgraded')}}',
		'prtg-sensors-succes'					: '{{trans('admin.prtg-sensors-succes')}}',
		'prtg-sensors-fail'						: '{{trans('admin.prtg-sensors-fail')}}',
		'prtg-sensors-delete-success'			: '{{trans('admin.prtg-sensors-delete-success')}}',
		'prtg-sensors-delete-fail'				: '{{trans('admin.prtg-sensors-delete-fail')}}'

	};

	// Keep only the translation vars in this file and use with e.g. trans['are-you-sure']
	var widgetTrans = {
		'current'							: '{{trans('admin.current')}}',
		'download'							: '{{trans('admin.download')}}',
		'previous'							: '{{trans('admin.previous')}}',
		'guests'							: '{{trans('admin.guests')}}',
		'no-data-found'						: '{{trans('admin.no-data-found')}}',
		'no-map-title' 						: '{{trans('admin.no-map-title')}}',
		'no-map-text' 						: '{{trans('admin.no-map-text')}}',
		'chart-timeout-description' 		: '{{trans('admin.chart-timeout-description')}}',
		'chart-timeout' 					: '{{trans('admin.chart-timeout')}}',
		'dwell-time'						: '{{trans('admin.dwell-time')}}',
		'dwell-time-description'			: '{{trans('admin.dwell-time-description')}}',
		'last-24-hours'						: '{{trans('admin.last-24-hours')}}',
		'last-2-days'						: '{{trans('admin.last-2-days')}}',
		'last-week'							: '{{trans('admin.last-week')}}',
		'last-month'						: '{{trans('admin.last-month')}}',
		'last-year'							: '{{trans('admin.last-year')}}',
		'prtg-last-24-hours'				: '{{trans('admin.last-24-hours')}}',
		'prtg-last-2-days'					: '{{trans('admin.last-2-days')}}',
		'prtg-last-week'					: '{{trans('admin.last-week')}}',
		'prtg-last-month'					: '{{trans('admin.last-month')}}',
		'prtg-last-year'					: '{{trans('admin.last-year')}}',
		'gateway-connection-error'			: '{{trans('error.gateway-connection')}}',
		'hours'								: '{{trans('admin.hours')}}',
		'custom-period'						: '{{trans('admin.custom')}}',
		'from'								: '{{trans('admin.from')}}',
		'to'								: '{{trans('admin.to')}}',
		'gender-info'						: '{{trans('admin.gender-info')}}',
		'login-types-info'					: '{{trans('admin.login-types-info')}}',
		'accumulated-guests-info'			: '{{trans('admin.accumulated-guests-info')}}',
		'new-guests-info'					: '{{trans('admin.new-guests-info')}}',
		'registered-users-info'				: '{{trans('admin.registered-users-info')}}',
		'data-transferred'					: '{{trans('admin.data-transferred')}}',
		'net-income'						: '{{trans('admin.net-income')}}',
		'income'							: '{{trans('admin.income')}}',
		'packages'							: '{{trans('admin.packages')}}',
		'revenue'							: '{{trans('admin.revenue')}}',
		'sales'								: '{{trans('admin.sales')}}',
		'time-in-hours'						: '{{trans('admin.time-in-hours')}}',
		'upload'							: '{{trans('admin.upload')}}'
	};

	var currencySymbol = '{!! \App\Helpers\CurrencyHelper::getCurrencySymbol() !!}';

</script>

<!-- NEEDED LAST FOR THE THEME TO WORK PROPERLY -->
<script>
	(function (document, window, $) {
		'use strict';
		var Site = window.Site;
		$(document).ready(function () {
			Site.run();
		});
	})(document, window, jQuery);
</script>

