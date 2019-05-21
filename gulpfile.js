// this stops the gulp notify error
process.env.DISABLE_NOTIFIER = true;
var elixir = require('laravel-elixir');
var es6 = require('es6-promise').polyfill();

// del is needed to delete the files before readding them
var del = require('del');
// extends elixir delete so we can use mix.delete
elixir.extend('delete', function (path) {
	new elixir.Task('delete', function () {
		del(path);
	});
});

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir.config.assetsPath = 'app/Views/admin/templates/system';

elixir(function(mix) {

	var templates = [
		"airangel",
		"default",
		"vital",
		"system"
	];

	// Delete templates
	mix.delete([
		'public/build',
		'public/admin/templates/airangel',
		'public/admin/templates/vital',
		'public/admin/templates/default',
		'public/admin/templates/system'
	]);

	// Merges all of the css files in the order shown for the CSS on the login page
	mix.styles([
			'vendor/bootstrap-sweetalert/sweetalert2.css',
			'css/plugins/bootstrap.css',
			'css/plugins/bootstrap-extend.css',
			'css/guest/guest.css',
			'css/user/site.css'
		],
		'public/admin/templates/system/css/guest.css', // To
		'./app/Views/admin/templates/system' // From
	);

	// Merges all of the JS files in the order shown for the JS on the login page
	mix.scripts([
			'vendor/modernizr/modernizr.js',
			'vendor/breakpoints/breakpoints.js',
			'vendor/bootstrap-sweetalert/sweetalert2.js',
			'vendor/jquery/jquery-1.12.1.js',
			'js/jquery-validate/jquery.validate.js',
			'js/jquery-validate/localization.js',
	/*		'js/jquery-validate/localization/messages_ar.js',
			 'js/jquery-validate/localization/messages_de.js',
			 'js/jquery-validate/localization/messages_es.js',
			 'js/jquery-validate/localization/messages_fr.js',*/
			'js/additional-methods.min.js'
		],
		'public/admin/templates/system/js/guest.js', // To
		'./app/Views/admin/templates/system' // From
	);

	// Merges the JS files used in the footer on the login page
	mix.scripts([
			'vendor/bootstrap/bootstrap.js',
			'vendor/animsition/animsition.js',
			'js/core.js',
			'js/site.js',
			'js/components/animsition.js',
			'js/components/jquery-placeholder.js',
			'js/components/material.js'
		],
		'public/admin/templates/system/js/guest-foot.js', // To
		'./app/Views/admin/templates/system' // From
	);

	// Merges all of the css files in the order shown
	mix.styles([
		'css/plugins/bootstrap.css',
		'css/plugins/bootstrap-extend.css',
		'vendor/animsition/animsition.css',
		'vendor/asscrollable/asScrollable.css',
		'vendor/ascolorpicker/asColorPicker.css',
		'css/plugins/toastr-2.1.3.css',
		//'vendor/asspinner/asSpinner.css',
		'vendor/asrange/asRange.css',
		'vendor/switchery/switchery.min.css',
		'vendor/slidepanel/slidePanel.css',
		'vendor/filament-tablesaw/tablesaw.css',
		// 'vendor/bootstrap-datepicker/bootstrap-datepicker.css',
		'css/user/search/component.css',
		//'css/plugins/jquery-ui.css',
		'vendor/jquery-ui/jquery-ui-1.12.1.css',
		'css/plugins/bootstrap-tokenfield.css',
		//'vendor/webui-popover/webui-popover.css',
		'css/plugins/jquery.dataTables.css',
		'css/plugins/chosen.css',
		'css/plugins/chosen-bootstrap.css',
		'css/plugins/jasny-bootstrap.css',
		'vendor/bootstrap-sweetalert/sweetalert2.min.css',
		'css/widgets.css',
		'css/modules/portals.css',
		'css/modules/gateways.css',
		'css/modules/forms.css',
		'css/modules/messages.css',
		'css/modules/online-now.css',
		'css/modules/pms.css',
		'css/modules/roles.css',
		'css/modules/brand.css',
		'css/modules/guests.css',
		'css/modules/csv-reports.css',
		'css/modules/packages.css',
		'css/modules/adjets.css',
		'css/modules/sites.css',
		'css/modules/vouchers.css',
		'css/modules/walled-garden.css',
		'css/modules/help.css',
		'css/user/site.css',
		'css/user/system.css',
		// Feedback
		'css/feedback.css'
	],
		'public/admin/templates/system/css/', // To
		'./app/Views/admin/templates/system' // From
	);

	// Merges the JS files used in the header
	mix.scripts([
		'vendor/jquery/jquery-1.12.1.js',
		'js/jquery-validate/jquery.validate.js',
		'js/jquery-validate/localization.js',
/*		'js/jquery-validate/localization/messages_ar.js',
		'js/jquery-validate/localization/messages_de.js',
		'js/jquery-validate/localization/messages_es.js',
		'js/jquery-validate/localization/messages_fr.js',*/
		'js/additional-methods.min.js', // TODO: what is this? find out and get un minified version
		'vendor/modernizr/modernizr.js',
		'js/search/modernizr.custom.js',
		'vendor/breakpoints/breakpoints.js',
		'js/toastr-2.1.3.js', //TODO: Find the unminified version as now we get a 404 error from http://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.js
		'js/components/datepicker.js',
		'js/map.js',
		//'js/vue.min.js',
		//'js/vue.js',
		'js/components/datepicker.js',
		'js/highcharts/highcharts.js',
		'js/highcharts/highcharts-3d.js',
		'js/highcharts/highcharts-more.js',
		'js/highcharts/modules/data.js',
		'js/highcharts/modules/exporting.js'
	],
		'public/admin/templates/system/js/head-scripts.js', // To
		'./app/Views/admin/templates/system' // From
	);
//*/

	// Merges the JS files used in the footer
	mix.scripts([
		'vendor/bootstrap/bootstrap.js', // UI Framework
		'vendor/animsition/animsition.js', // Better animation for jQuery
		//'vendor/asscroll/jquery-asScroll.js', // A jquery plugin that generate a styleable scrollbar.
		//'vendor/asspinner/jquery-asSpinner.js', // Spinner for numbers in a form
		'vendor/asscrollable/jquery.asScrollable.all.js', // A jquery plugin that make a block element scrollable.
		'vendor/ashoverscroll/jquery-asHoverScroll.js', // A jquery plugin helps scroll the list that is larger than its container.
		'vendor/asrange/jquery-asRange.js', // A jquery plugin to convert input into range slider.
		'vendor/switchery/switchery.min.js', // iOS 7 style switches
		'vendor/slidepanel/jquery-slidePanel.js',
		//'js/jquery-ui.js', // Shouldn't this be in the vendor folder?
		'vendor/jquery-ui/jquery-ui-1.12.1.js', // Shouldn't this be in the vendor folder?
		//'vendor/jquery-ui/jquery-ui-touch-punch.js',
		'vendor/filament-tablesaw/tablesaw-init.js',
		//'vendor/webui-popover/jquery.webui-popover.min.js',
		'vendor/ascolor/jquery-asColor.js',
		'vendor/ascolorpicker/jquery-asColorPicker.js',
		//'vendor/bootstrap-datepicker/bootstrap-datepicker.js',
		'vendor/googlemaps-libs/markerclusterer.js', // Google maps - Created a nicer Icon and clusters close markers
		'vendor/googlemaps-libs/spiderfier.js', // Google maps - Click on an icon to spider leg the info
		'js/jquery.dataTables.js',
		'js/packery.min.js',
		'js/imagesloaded.pkgd.min.js',
		'js/search/classie.js', // Any use? http://callmenick.com/post/add-remove-and-check-classes
		'js/search/uisearch.js',
		'js/core.js',
		'js/site.js',
		'js/widgets-editor.js',
		'js/widgets.js',
		'js/init-widgets.js',
		'js/sections/menu.js',
		'js/sections/menubar.js',
		'js/sections/gridmenu.js',
		'js/components/asscrollable.js',
		'js/components/asrange.js',
		'js/components/ascolorpicker.js',
		//'js/components/asSpinner.js',
		'js/components/animsition.js',
		'js/components/slidepanel.js',
		'js/components/switchery.js',
		//'js/components/bootstrap-datepicker.js',
		'js/components/input-group-file.js',
		'js/jquery.cookie.js',
		'js/jasny-bootstrap.js',
		//'js/components/webui-popover.js',
		'js/chosen.jquery.js',
		'js/components/jquery.formatter.js',
		'js/components/formatter-js.js',
		'vendor/bootbox/bootbox.js',
		// Feedback
		'js/feedback.js',
		'js/system.js',
		'js/bootstrap-tokenfield.js',
		'js/md5.js',
		'vendor/bootstrap-sweetalert/sweetalert2.js',
		// Modules
		'js/modules/translations.js',
		'js/modules/portals.js',
		'js/modules/gateways.js',
		'js/modules/forms.js',
		'js/modules/brand.js',
		'js/modules/roles.js',
		'js/modules/vouchers.js',
		'js/modules/guests.js',
		'js/modules/messages.js',
		'js/modules/online-now.js',
		'js/modules/packages.js',
		'js/modules/csv-reports.js',
		'js/modules/site.js',
		'js/modules/adjets.js',
		'js/modules/walled-garden.js',
		'js/modules/pms.js'

	],
		'public/admin/templates/system/js/foot-script.js', // To
		'./app/Views/admin/templates/system' // From
	);

	// TODO: search the filesystem for templates rather than using the hard coded array
	// Loops through the array and copies the assets for each template
	for (var i = 0; i <  templates.length; i++) {

		mix.copy([
			'app/Views/admin/templates/'+templates[i]+'/css/'
		], 'public/admin/templates/'+templates[i]+'/css/');

		mix.copy([
			'app/Views/admin/templates/'+templates[i]+'/js/'
		], 'public/admin/templates/'+templates[i]+'/js/');

		mix.copy('app/Views/admin/templates/'+templates[i]+'/img', 'public/admin/templates/'+templates[i]+'/img')
			.version('public/admin/templates/'+templates[i]+'/img');
	}

	// copies the images from the colour picker vendor into the build folder to stop the 404 error
	mix.copy([
		'app/Views/admin/templates/system/vendor/ascolorpicker/images/'
	], 'public/build/admin/templates/system/css/images/');

	mix.copy([
		'app/Views/admin/templates/system/vendor/jquery-ui/images/'
	], 'public/build/admin/templates/system/css/images/');

	// copies the images from the colour picker vendor into the public folder to stop the 404 error
	mix.copy([
		'app/Views/admin/templates/system/vendor/ascolorpicker/images/'
	], 'public/admin/templates/system/css/images/');

	mix.copy([
		'app/Views/admin/templates/system/images/'
	], 'public/admin/templates/system/images/');

	// copies the portal templates backgrounds
	mix.copy([
		'resources/portal-templates/backgrounds/'
	], 'public/admin/templates/system/images/portal-templates/backgrounds/');

	// copies the portal templates images
	mix.copy([
		'resources/portal-templates/templates/'
	], 'public/admin/templates/system/images/portal-templates/templates/');

	// copies the images from the chosen vendor into the build folder to stop the 404 error
	mix.copy([
		'app/Views/admin/templates/system/css/chosen-sprite.png'
	], 'public/build/admin/templates/system/css/');

	// copies the images from the chosen vendor into the public folder to stop the 404 error
	mix.copy([
		'app/Views/admin/templates/system/css/chosen-sprite.png'
	], 'public/admin/templates/system/css/');

	// copies the font files from the views file system into public file system
	mix.copy([
		'app/Views/admin/templates/system/fonts/'
	], 'public/admin/templates/system/fonts/');

	// copies the vendor files from the views file system into public file system
	mix.copy([
		'app/Views/admin/templates/system/vendor/'
	], 'public/admin/templates/system/vendor/');

	// mix.copy([
	// 	'app/Views/admin/templates/system/js/highcharts/'
	// ], 'public/admin/templates/system/js/highcharts/');

	// versions the js and css files
	mix.version([
		'public/admin/templates/system/js/head-scripts.js',
		'public/admin/templates/system/js/foot-script.js',
		'public/admin/templates/system/css/all.css',
		'public/admin/templates/system/js/guest-foot.js',
		'public/admin/templates/system/js/guest.js',
		'public/admin/templates/system/css/guest.css'
	]);
});