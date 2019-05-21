<?php
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/
// For testing purposes
//Route::get('phpinfo', function(){return phpinfo();});
//Route::get('test-pms', function() { echo "testing"; });
/**
 * Not logged in
 * Web Middleware holds the session, cookies and the csrf token information
 */
Route::group(['middleware' => ['web']], function ()
{
	Route::post('feedback/send-email', 	'\App\Feedback\Controller@sendEmail' )->name('sendFeedbackEmail');							// Sending Feedback email to applications@airangel.com
	Route::get('reset-guest-password', 	'\App\Admin\Modules\Passwords\Controller@resetGuestPassword')->name('resetGuestPassword');	// This is the link which will be sent to a guest to reset his password
	Route::get('error/{code}/{errorCode}','\App\Exceptions\Errors\Controller@index')->name('error');								// Error Page

	//Password
	Route::group(['prefix' => 'password', 'as' => 'password.'], function() {
		Route::get('forgot', '\App\Admin\Modules\Passwords\Controller@forgot')->name('forgotPassword');
		Route::post('reset', '\App\Admin\Modules\Passwords\Controller@reset')->name('resetPassword');
		Route::get('change', '\App\Admin\Modules\Passwords\Controller@change')->name('changePassword');
		Route::post('save',  '\App\Admin\Modules\Passwords\Controller@save')->name('savePassword');
	});

	//Login
	Route::get('login',  '\App\Admin\Modules\Users\Controller@getLogin')->name('login'); 	// GET Login will show the login form
	Route::post('login', '\App\Admin\Modules\Users\Controller@postLogin')->name('login'); 	// POST Login will action the form after submit
	Route::get('logout', '\App\Admin\Modules\Users\Controller@logout')->name('logout');		// Logout route will logout the Admin user and empty the session

	Route::resource('api/{version}/{type}/{id}/{join?}/', '\App\API\Controller');
	Route::resource('languages', '\App\Admin\Modules\Languages\Controller');
	Route::post('search',		'\App\Admin\Search\Controller@postSearch' );
	Route::get('search',		'\App\Admin\Search\Controller@getSearch' );
});

/**
 * Logged in
 * Middleware web + auth will make sure you store the session variables
 * Plus it won't allow you to use these routes unless you are logged in
 * Prefixed routes means they start with /prefix_value/
 * auth = logged in
 * access = our permissions
 */
Route::group(['middleware' => ['web', 'auth', 'locale']], function ()
{
	Route::get('help/{page}', 						'\App\Admin\HelpPages\Controller@help' ); 								// On Page help
	Route::resource('help', 						'\App\Admin\Modules\Help\Controller'); 									// Main help
	Route::get('estate', 							'\App\Admin\Modules\Sites\Controller@showEstateView')->name('estate'); 	// Estate page
	Route::get('ap-list-widget',					'\App\Admin\Modules\Hardware\Controller@showApListWidget')->name('ap-list-widget'); 	// ap-list-widget page
	Route::get('site-list-widget', 					'\App\Admin\Modules\Sites\Controller@showEstateView')->name('site-list-widget'); 	// site-list-widget page
	Route::get('dashboard/{siteId?}/{redirect?}', 	['uses' => '\App\Admin\Modules\Sites\Controller@dashboard', 'middleware' => 'set_logged_in_site'])->name('dashboard'); 	// Dashboard
	Route::post('json/save-widgets', 				'\App\Admin\Widgets\Controller@saveWidgets');							// Saving Widgets order
	Route::get('filemanager-config', 				'\App\Admin\ResponsiveFilemanager\Controller@index');					// Getting responsive file manager config folders
	Route::get('datatable/manage/sites', 			'\App\Admin\Modules\Sites\Controller@getSitesDatatable')->name('datatable.system.sites');

	Route::resource('manage/sites', 				'\App\Admin\Modules\Sites\Controller', ['only' => ['index', 'store', 'update', 'destroy']] );
});

/**
 * Logged in with permission
 * Access Middleware checks if the user logged in or not Plus checks his permission from the url
 * i.e. To access url like roles/create you should have roles.create permissions under your role
 * If there is a number (ex:0) in the route, the access middleware will ignore it and anything after it except the edit
 * auth = logged in
 * access = our permissions
 */
Route::group(['middleware' => ['web','access', 'locale']], function () {
	Route::get('online-now/{mac?}', 					'\App\Admin\Modules\OnlineNow\Controller@index' )->name('online-now');
	Route::get('translations/download', 				'\App\Admin\Modules\Translations\Controller@downloadCSV');
	Route::get('manage/portals/{id}/portal-preview', 	'\App\Admin\Modules\Portals\Controller@portalPreview');
	Route::post('manage-profile', 						'\App\Admin\Profile\Controller@saveProfileInfo')->name('saveProfileInfo');

	Route::resource('messages', 						'\App\Admin\Modules\Messages\Controller');
	Route::resource('gateways', 						'\App\Admin\Modules\Gateways\Controller' );
	Route::resource('roles-and-permissions',			'\App\Admin\Modules\Roles\Controller');
	Route::resource('injectionjet-templates', 			'\App\Admin\Modules\Adjets\Templates\Controller');

	Route::resource('sites', 							'\App\Admin\Modules\Sites\Controller');
	Route::resource('users', 							'\App\Admin\Modules\Users\Controller' );
	Route::resource('translations', 					'\App\Admin\Modules\Translations\Controller');
	Route::delete('widgets/ap-list/{serial}',			'\App\Admin\Modules\Hardware\Controller@deleteHhe');

	// Manage
	Route::group(['prefix' => 'manage', 'as' => 'manage.'], function ()
	{
		Route::post('sites/{id}/prtg',				'\App\Admin\Modules\Sites\Controller@setUpPrtg');
		Route::resource('sites', 					'\App\Admin\Modules\Sites\Controller' , ['except' => ['index', 'store', 'update', 'destroy']]); // At the moment will forward to estate route
		Route::resource('guests', 					'\App\Admin\Modules\Guests\Controller' );
		//Route::resource('sites', 			'\App\Admin\Modules\Sites\Controller' , ['except' => ['index', 'store', 'update', 'destroy']]); // At the moment will forward to estate route
		//Route::resource('guests', 			'\App\Admin\Modules\Guests\Controller' );

		// Require a site to be logged in the session
		Route::group(['middleware' => 'require_site'], function() {

			Route::get('injectionjets/create/{templateId}', 	'\App\Admin\Modules\AdJets\Controller@createAdjet')->name('injectionjets.create');
			Route::resource('injectionjets', 					'\App\Admin\Modules\AdJets\Controller');
			Route::resource('blacklist', 						'\App\Admin\Modules\Blacklist\Controller', ['except' => 'create'] );
			Route::get('forms/:question/{id}', 					'\App\Admin\Modules\Forms\Controller@getQuestion');
			Route::resource('forms', 							'\App\Admin\Modules\Forms\Controller' );
			Route::resource('locations', 						'\App\Admin\Modules\Locations\Controller' );
			Route::resource('notes', 							'\App\Admin\Modules\Notes\Controller' );
			Route::resource('packages', 						'\App\Admin\Modules\Packages\Controller', ['except' => 'show'] );
			Route::resource('portals', 							'\App\Admin\Modules\Portals\Controller' );
			Route::post('portals/:clear-test-data', 			'\App\Admin\Modules\Portals\Controller@clearTestData');
			Route::resource('pms', 								'\App\Admin\Modules\Pms\Controller' );
			Route::resource('guest-whitelist',					'\App\Admin\Modules\GuestWhitelist\Controller' );

			// Guests
			Route::group(['prefix' => 'guests', 'as' => 'guests.'], function ()
			{
				Route::get('transactions/{id}/edit', 	'\App\Admin\Modules\Transactions\Controller@edit' );
				Route::put('transactions/{id}/edit', 	'\App\Admin\Modules\Transactions\Controller@update' );
				Route::resource('transactions', 		'\App\Admin\Modules\Transactions\Controller' );
			});

			// Vouchers
			Route::group(['prefix' => 'vouchers', 'as' => 'vouchers.'], function ()
			{
				Route::get('batch/{batchNo}/edit', 	'\App\Admin\Modules\Vouchers\Controller@batchEdit' )->name('batch.edit');
				Route::put('batch/{batchNo}', 		'\App\Admin\Modules\Vouchers\Controller@batchUpdate' )->name('batch.update');
				Route::delete('batch/{batchNo}', 	'\App\Admin\Modules\Vouchers\Controller@batchDelete' )->name('batch.delete');
			});
			Route::resource('vouchers',			'\App\Admin\Modules\Vouchers\Controller' );
		});



		// manage/brand
		Route::group(['prefix' => 'brand', 'middleware' => 'require_site', 'as' => 'brand.'], function ()
		{
			Route::get('/', 							'\App\Admin\Modules\Brand\Controller@index' )->name('index');
			Route::post('look-and-feel/{siteId}/edit', 	'\App\Admin\Modules\Brand\Controller@saveLookAndFeel' );

			// manage/brand/emails
			Route::group(['prefix' => 'emails', 'as' => 'emails.'], function ()
			{
				Route::get('{portalId}/{emailTemplateName}/edit',	'\App\Admin\Modules\Brand\Controller@editEmailTemplate' )->name('edit');
				Route::get('{portalId}/{emailTemplateName}', 		'\App\Admin\Modules\Brand\Controller@viewEmailTemplate' );
				Route::post('edit', 								'\App\Admin\Modules\Brand\Controller@emails' )->name('edit');
			});

			// manage/brand/terms
			Route::group(['prefix' => 'terms', 'as' => 'terms.'], function ()
			{
				Route::get('{portalId}/edit', 	'\App\Admin\Modules\Brand\Controller@editTerms' )->name('edit');
				Route::get('{portalId}', 		'\App\Admin\Modules\Brand\Controller@viewTerms' );
				Route::post('edit', 			'\App\Admin\Modules\Brand\Controller@terms' )->name('edit');
			});

			// manage/brand/site-terms
			Route::group(['prefix' => 'site-terms', 'as' => 'site.terms.'], function ()
			{
				Route::get('{siteId}/{lang}/edit', 	'\App\Admin\Modules\Brand\Controller@editSiteTerms' )->name('edit');
				Route::get('{siteId}/{lang}', 		'\App\Admin\Modules\Brand\Controller@viewSiteTerms' );
				Route::post('edit', 				'\App\Admin\Modules\Brand\Controller@siteTerms' )->name('edit');
			});

			// manage/brand/vouchers
			Route::group(['prefix' => 'vouchers', 'as' => 'vouchers.'], function ()
			{
				Route::get('{siteId}/{langKey}/{voucherType}/edit/', 	'\App\Admin\Modules\Brand\Controller@editVoucher' )->name('edit');
				Route::get('{siteId}/{langKey}/{voucherType}', 			'\App\Admin\Modules\Brand\Controller@viewVoucher' )->name('view');
				Route::post('edit', 									'\App\Admin\Modules\Brand\Controller@voucher' )->name('edit');
			});
		});
	});

	// Datatables
	Route::group( [ 'prefix' => 'datatable', 'as' => 'datatable.' ], function()
	{
		Route::get('gateways', 						'\App\Admin\Modules\Gateways\Controller@getGatewaysDatatable')->name('gateways');
		Route::get('networking/gateways', 			'\App\Admin\Modules\Gateways\Controller@getSystemGatewaysDatatable')->name('system.gateways');
		Route::get('networking/hardware', 			'\App\Admin\Modules\Hardware\Controller@getHardwareDatatable')->name('hardware');
		Route::get('sites', 						'\App\Admin\Modules\Sites\Controller@getAllSitesDatatable')->name('sites');
		Route::get('translations', 					'\App\Admin\Modules\Translations\Controller@getTranslationsDatatable')->name('translations');
		Route::get('users', 						'\App\Admin\Modules\Users\Controller@getUsersDatatable')->name('users');
		Route::get('system/users', 					'\App\Admin\Modules\Users\Controller@getSystemUsersDatatable')->name('system.users');
		Route::get('messages', 						'\App\Admin\Modules\Messages\Controller@getMessagesDatatable')->name('messages');
		Route::get('system/messages', 				'\App\Admin\Modules\Messages\Controller@getSystemMessagesDatatable')->name('system.messages');
		Route::get('manage/packages', 				'\App\Admin\Modules\Packages\Controller@getPackagesDatatable')->name('packages');
		Route::get('manage/guests', 				'\App\Admin\Modules\Guests\Controller@getManageGuestsDatatable')->name('guests');
		Route::get('online-now/{mac?}',				'\App\Admin\Modules\OnlineNow\Controller@getOnlineNowDatatable')->name('online-now');
		Route::get('manage/vouchers/all', 			'\App\Admin\Modules\Vouchers\Controller@getAllVouchersDatatable')->name('vouchers.all');
		Route::get('manage/vouchers/batch', 		'\App\Admin\Modules\Vouchers\Controller@getBatchVouchersDatatable')->name('vouchers.batch');
		Route::get('manage/vouchers/single', 		'\App\Admin\Modules\Vouchers\Controller@getSingleVouchersDatatable')->name('vouchers.single');
	});

	// JSON
	Route::group(['prefix' => 'json', 'as' => 'json.'], function() {

		Route::get('widgets/messages/{siteId?}/{messageNo?}', 		'\App\Admin\Widgets\Controller@messages');
		Route::post('online-now/{siteId}/sign-out-guest',			'\App\Admin\Modules\OnlineNow\Controller@signOutGuest' );
		Route::post('online-now/{siteId}/sign-in-guest',			'\App\Admin\Modules\OnlineNow\Controller@signInGuest' );

		Route::group(['prefix' => 'networking', 'as' => 'networking.'], function() {
			Route::get('walled-garden/{mac?}', 						'\App\Admin\Modules\WalledGarden\Controller@getWalledGardenByMac');
			Route::delete('walled-garden/{mac?}/{ruleId}/delete', 	'\App\Admin\Modules\WalledGarden\Controller@destroy');
		});

		// json/manage
		Route::group(['prefix' => 'manage', 'as' => 'manage.'], function() {
			Route::post('guests/{guestId}/assign-package',	'\App\Admin\Modules\Guests\Controller@assignPackage' );
			Route::post('guests/{guestId}/block-device',	'\App\Admin\Modules\Guests\Controller@blockDevice' );
			Route::post('guests/{guestId}/reset-password',	'\App\Admin\Modules\Guests\Controller@resetPassword' );
			Route::post('sites/prtg',						'\App\Admin\Modules\Sites\Controller@setUpPrtg');

			Route::get('vouchers/{siteId}/get_guests_by_codes/{codes}/{join?}/', 		'\App\Admin\Modules\Vouchers\Controller@getGuestsByCodes');
			Route::get('vouchers/{packageId}/get_human_readable_by_package/{join?}/',	'\App\Admin\Modules\Packages\Controller@getHumanReadableByPackage');
			Route::get('vouchers/{packageId}/get_gateways_by_package/{join?}/',			'\App\Admin\Modules\Gateways\Controller@getGatewaysByPackage');
			Route::get('guests/{packageId}/get_gateways_by_package/{join?}/', 			'\App\Admin\Modules\Gateways\Controller@getGatewaysByPackage');
			Route::get('pms/:/get_upms_config/', 										'\App\Admin\Modules\Pms\Controller@getUpmsConfig');
			Route::get('pms/:/set_upms_config/', 										'\App\Admin\Modules\Pms\Controller@setUpmsConfig');
			Route::get('pms/:/validate_room/', 											'\App\Admin\Modules\Pms\Controller@validateRoom');
			Route::get('pms/:/charge_room/', 											'\App\Admin\Modules\Pms\Controller@chargeRoom');

		});

		// json/widgets
		Route::group(['prefix' => 'widgets', 'as' => 'widgets.'], function() {
			Route::post('gateway-control/0/reboot-gateway',									'\App\Admin\Modules\Gateways\Controller@rebootGateway' );
			Route::post('gateway-control/0/aaa-gateway',									'\App\Admin\Modules\Gateways\Controller@aaaGateway' );

			Route::get('{widget?}/0/get-report-data', 										'\App\Admin\Modules\Reports\Controller@getReportData');
			Route::get('map/0/get-map-data', 												'\App\Admin\Widgets\Controller@getMapData');
			Route::get('demographics/0/get-demographics-data', 								'\App\Admin\Widgets\Controller@getDemographicsData');
			Route::get('wan-throughput/0/get_wan_throughput_chart_data/{mac?}/{siteId?}', 	'\App\Admin\Widgets\Controller@getWanThroughPutChartData');
			Route::get('{type}/0/charts/{mac?}/{siteId?}/{startDate?}/{endDate?}/{hourly?}','\App\Admin\Json\Charts\Controller@chart');
		});
	});

	// Reports
	Route::group(['prefix' => 'reports', 'as' => 'reports.'], function ()
	{
		Route::get('csv', 			'\App\Admin\Modules\Reports\Controller@CsvReports')->name('csv-reports');
		Route::post('csv', 			'\App\Admin\Modules\Reports\Controller@generateCsv');
		Route::get('guest', 		'\App\Admin\Modules\Reports\Controller@guestDashboard' )->name('guest-reports');
		Route::get('financial', 	'\App\Admin\Modules\Reports\Controller@financialDashboard' )->name('financial-reports');
		Route::get('technology',	'\App\Admin\Modules\Reports\Controller@technologyDashboard' )->name('technology-reports');
	});

	// Networking
	Route::group(['prefix' => 'networking', 'as' => 'networking.'], function ()
	{
		Route::resource('gateways', 		'\App\Admin\Modules\Gateways\Controller' );
		Route::resource('hardware', 		'\App\Admin\Modules\Hardware\Controller' );
		Route::resource('walled-garden', 	'\App\Admin\Modules\WalledGarden\Controller' );
		Route::resource('whitelist', 		'\App\Admin\Modules\Whitelist\Controller', ['except' => 'create'] );
	});

	// System
	Route::group(['prefix' => 'system', 'as' => 'system.'], function ()
	{
		Route::resource('users', 					'\App\Admin\Modules\Users\Controller' );
		Route::resource('messages',					'\App\Admin\Modules\Messages\Controller' );
		Route::resource('roles-and-permissions',	'\App\Admin\Modules\Roles\Controller');
	});

	// Migrations
	Route::group(['prefix' => 'migrations', 'as' => 'migrations.'], function ()
	{
		Route::get('migrate', 	'\App\Admin\DevTools\Migrations\Controller@migrate')->name('migrate');
		Route::get('rollback',	'\App\Admin\DevTools\Migrations\Controller@rollback')->name('rollback');
		Route::get('/', 		'\App\Admin\DevTools\Migrations\Controller@index' );
	});

	// Cache
	Route::group(['prefix' => 'cache', 'as' => 'cache.'], function ()
	{
		Route::get('/', 			'\App\Admin\DevTools\Cache\Controller@index' )->name('index');
		Route::get('all', 			'\App\Admin\DevTools\Cache\Controller@clearAll' )->name('cache-all');
		Route::get('permissions', 	'\App\Admin\DevTools\Cache\Controller@clearPermissions' )->name('permissions');
		Route::get('translations', 	'\App\Admin\DevTools\Cache\Controller@clearTranslations' );
		Route::get('vendors', 		'\App\Admin\DevTools\Cache\Controller@clearVendors' );
	});

	// Services
	Route::group(['prefix' => 'services', 'as' => 'services.'], function ()
	{
		Route::get('/', '\App\Admin\DevTools\Services\Controller@index' );
		Route::get('call/{url}/{config?}/{method?}/', '\App\Admin\DevTools\Services\Controller@call' )->name('call');
	});

});
