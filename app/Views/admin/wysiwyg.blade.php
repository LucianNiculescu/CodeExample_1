{{--

TinyMCE settings
----------------
Must be included for any WYSIWYG
To use ad one of the classes portal-wysiwyg, full-wysiwyg, small-wysiwyg or read-only-wysiwyg to a text area

--}}
<script src="/tinymce/tinymce.min.js?cachbuster={{ csrf_token() }}"></script>
<script>

	// Full WYSIWYG add the class full-wysiwyg to a textarea
	tinymce.init({
		selector: "textarea.full-wysiwyg",
		theme: "modern",
		height : "600",
		language: LANG,
		forced_root_block : "",
		valid_elements: "*[*]",
		extended_valid_elements: "*[*]",
		valid_children: "+body[style],+body[class],+body[link]",
		menubar:false,
		statusbar: true,
		relative_urls:false,
		external_filemanager_path:"/filemanager/",
		filemanager_title:"Filemanager" ,
		external_plugins: { "responsivefilemanager" : "/filemanager/tinymce/plugins/responsivefilemanager/plugin.min.js"},
		plugins: [
			"advlist autolink lists link image charmap preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars code ",
			"insertdatetime media nonbreaking save  directionality codemirror",

			"template textcolor paste textcolor "
		],
		toolbar: "bold italic forecolor backcolor | fontsizeselect | numlist bullist outdent indent alignleft | link | code",
		codemirror: {
			indentOnInit: true, // Whether or not to indent code on init.
			fullscreen: false,   // Default setting is false
			path: 'codemirror-5.22.0', // Path to CodeMirror distribution
			config: {           // CodeMirror config object
				//mode: 'application/x-httpd-php',
				lineNumbers: true
			},
			width: 800,         // Default value is 800
			height: 600,        // Default value is 550
			jsFiles: [          // Additional JS files to load
				//'mode/clike/clike.js',
				//'mode/php/php.js'
			]
		}

	});

	// Full WYSIWYG add the class full-wysiwyg to a textarea
	tinymce.init({
		selector: "textarea.portal-wysiwyg",
		theme: "modern",
		height : "600",
		language: LANG,
		forced_root_block : "",
		content_css: "/portal/css/style.css",
		valid_elements: "*[*]",
		extended_valid_elements: "*[*]",
		valid_children: "+body[style],+body[class],+body[link]",
		menubar:false,
		statusbar: true,
		relative_urls:false,
		external_filemanager_path:"/filemanager/",
		filemanager_title:"Filemanager" ,
		external_plugins: { "responsivefilemanager" : "/filemanager/tinymce/plugins/responsivefilemanager/plugin.min.js"},
		plugins: [
			"advlist autolink lists link image charmap preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars code ",
			"insertdatetime media nonbreaking save  directionality responsivefilemanager codemirror",

			"template textcolor paste textcolor "
		],

		setup: function(editor) {
			editor.addButton('custom-code', {
				type: 'menubutton',
				text: '{{trans('admin.custom-code')}}',
				icon: false,
				menu: [{
					text: '{{trans('admin.companyname')}}',
					onclick: function() {
						editor.insertContent('%%COMPANYNAME%%');
					}
				}, {
					text: '{{trans('admin.welcome')}}',
					onclick: function() {
						editor.insertContent('%%WELCOME%%');
					}
				},{
					text: '{{trans('admin.buttons')}}',
					onclick: function() {
						editor.insertContent('%%BUTTONS%%');
					}
				},{
					text: '{{trans('admin.social-bottons')}}',
					onclick: function() {
						editor.insertContent('%%SOCIAL_BUTTONS%%');
					}
				},{
					text: '{{trans('admin.email-login-form')}}',
					onclick: function() {
						editor.insertContent('%%EMAIL_LOGIN_FORM%%');
					}
				},{
					text: '{{trans('admin.email-registration-form')}}',
					onclick: function() {
						editor.insertContent('%%EMAIL_REGISTRATION_FORM%%');
					}
				},{
					text: '{{trans('admin.gha-login-form')}}',
					onclick: function() {
						editor.insertContent('%%GHA_LOGIN_FORM%%');
					}
				},{
					text: '{{trans('admin.gha-registration-form')}}',
					onclick: function() {
						editor.insertContent('%%GHA_REGISTRATION_FORM%%');
					}
				},{
					text: '{{trans('admin.voucher-login-form')}}',
					onclick: function() {
						editor.insertContent('%%VOUCHER_LOGIN_FORM%%');
					}
				},{
					text: '{{trans('admin.facebook-button')}}',
					onclick: function() {
						editor.insertContent('%%FACEBOOK_BUTTON%%');
					}
				},{
					text: '{{trans('admin.google-button')}}',
					onclick: function() {
						editor.insertContent('%%GOOGLE_BUTTON%%');
					}
				},{
					text: '{{trans('admin.linkedin-button')}}',
					onclick: function() {
						editor.insertContent('%%LINKEDIN_BUTTON%%');
					}
				},{
					text: '{{trans('admin.live-button')}}',
					onclick: function() {
						editor.insertContent('%%LIVE_BUTTON%%');
					}
				},{
					text: '{{trans('admin.twitter-button')}}',
					onclick: function() {
						editor.insertContent('%%TWITTER_BUTTON%%');
					}
				}]
			});
		},

		toolbar: "insertfile responsivefilemanager | bold italic forecolor backcolor | fontsizeselect | numlist bullist outdent indent alignleft | link | code custom-code",
		codemirror: {
			indentOnInit: true, // Whether or not to indent code on init.
			fullscreen: false,   // Default setting is false
			path: 'codemirror-5.22.0', // Path to CodeMirror distribution
			config: {           // CodeMirror config object
				//mode: 'application/x-httpd-php',
				lineNumbers: true
			},
			width: 800,         // Default value is 800
			height: 600,        // Default value is 550
			jsFiles: [          // Additional JS files to load
				//'mode/clike/clike.js',
				//'mode/php/php.js'
			]
		}
		/*style_formats:[
		 {
		 title: "Headers",
		 items: [
		 {title: "Header 1",format: "h1"},
		 {title: "Header 2",format: "h2"},
		 {title: "Header 3",format: "h3"},
		 {title: "Header 4",format: "h4"},
		 {title: "Header 5",format: "h5"},
		 {title: "Header 6",format: "h6"}
		 ]
		 },

		 {
		 title: "Blocks",
		 items: [
		 {title: "Paragraph",format: "p"},
		 {title: "Blockquote",format: "blockquote"},
		 {title: "Div",format: "div"},
		 {title: "Pre",format: "pre"}
		 ]
		 }
		 ],
		 */
	});

	// Create the WYSIWYG for the welcome-back display on the portal - applied to a textarea having class welcome-back-wysiwyg
	tinymce.init({
		selector: "textarea.welcome-back-wysiwyg",
		theme: "modern",
		height : "600",
		language: LANG,
		forced_root_block : "",
		content_css: "/portal/css/style.css",
		valid_elements: "*[*]",
		extended_valid_elements: "*[*]",
		valid_children: "+body[style],+body[class],+body[link]",
		menubar:false,
		statusbar: true,
		relative_urls:false,
		external_filemanager_path:"/filemanager/",
		filemanager_title:"Filemanager" ,
		external_plugins: { "responsivefilemanager" : "/filemanager/tinymce/plugins/responsivefilemanager/plugin.min.js"},
		plugins: [
			"advlist autolink lists link image charmap preview hr anchor pagebreak",
			"searchreplace wordcount visualblocks visualchars code ",
			"insertdatetime media nonbreaking save  directionality responsivefilemanager codemirror",

			"template textcolor paste textcolor "
		],

		setup: function(editor) {
			editor.addButton('custom-code', {
				type: 'menubutton',
				text: '{{trans('admin.custom-code')}}',
				icon: false,
				menu: [{
					text: '{{trans('admin.companyname')}}',
					onclick: function() {
						editor.insertContent('%%COMPANYNAME%%');
					}
				}, {
					text: '{{trans('admin.welcome')}}',
					onclick: function() {
						editor.insertContent('%%WELCOME%%');
					}
				},{
					text: '{{trans('admin.buttons')}}',
					onclick: function() {
						editor.insertContent('%%BUTTONS%%');
					}
				},{
					text: '{{trans('admin.social-bottons')}}',
					onclick: function() {
						editor.insertContent('%%SOCIAL_BUTTONS%%');
					}
				},{
					text: '{{trans('admin.email-login-form')}}',
					onclick: function() {
						editor.insertContent('%%EMAIL_LOGIN_FORM%%');
					}
				},{
					text: '{{trans('admin.email-registration-form')}}',
					onclick: function() {
						editor.insertContent('%%EMAIL_REGISTRATION_FORM%%');
					}
				},{
					text: '{{trans('admin.gha-login-form')}}',
					onclick: function() {
						editor.insertContent('%%GHA_LOGIN_FORM%%');
					}
				},{
					text: '{{trans('admin.gha-registration-form')}}',
					onclick: function() {
						editor.insertContent('%%GHA_REGISTRATION_FORM%%');
					}
				},{
					text: '{{trans('admin.voucher-login-form')}}',
					onclick: function() {
						editor.insertContent('%%VOUCHER_LOGIN_FORM%%');
					}
				},{
					text: '{{trans('admin.facebook-button')}}',
					onclick: function() {
						editor.insertContent('%%FACEBOOK_BUTTON%%');
					}
				},{
					text: '{{trans('admin.google-button')}}',
					onclick: function() {
						editor.insertContent('%%GOOGLE_BUTTON%%');
					}
				},{
					text: '{{trans('admin.linkedin-button')}}',
					onclick: function() {
						editor.insertContent('%%LINKEDIN_BUTTON%%');
					}
				},{
					text: '{{trans('admin.live-button')}}',
					onclick: function() {
						editor.insertContent('%%LIVE_BUTTON%%');
					}
				},{
					text: '{{trans('admin.twitter-button')}}',
					onclick: function() {
						editor.insertContent('%%TWITTER_BUTTON%%');
					}
				}]
			});
		},

		toolbar: "insertfile responsivefilemanager | bold italic forecolor backcolor | fontsizeselect | numlist bullist outdent indent alignleft | link | code custom-code",
		codemirror: {
			indentOnInit: true, // Whether or not to indent code on init.
			fullscreen: false,   // Default setting is false
			path: 'codemirror-5.22.0', // Path to CodeMirror distribution
			config: {           // CodeMirror config object
				//mode: 'application/x-httpd-php',
				lineNumbers: true
			},
			width: 800,         // Default value is 800
			height: 600,        // Default value is 550
			jsFiles: [          // Additional JS files to load
				//'mode/clike/clike.js',
				//'mode/php/php.js'
			]
		}
		/*style_formats:[
		 {
		 title: "Headers",
		 items: [
		 {title: "Header 1",format: "h1"},
		 {title: "Header 2",format: "h2"},
		 {title: "Header 3",format: "h3"},
		 {title: "Header 4",format: "h4"},
		 {title: "Header 5",format: "h5"},
		 {title: "Header 6",format: "h6"}
		 ]
		 },

		 {
		 title: "Blocks",
		 items: [
		 {title: "Paragraph",format: "p"},
		 {title: "Blockquote",format: "blockquote"},
		 {title: "Div",format: "div"},
		 {title: "Pre",format: "pre"}
		 ]
		 }
		 ],
		 */
	});

	// Small WYSIWYG add the class small-wysiwyg to a textarea
	tinymce.init({
		selector: "textarea.small-wysiwyg",
		theme: "modern",
		height : "185",

		language: LANG,
		forced_root_block : "",
		menubar:false,
		statusbar: true,
		relative_urls:false,
		plugins: "code codemirror",
		toolbar: " bold italic | style_formats| fontsizeselect | numlist bullist indent alignleft alignright | code",
		codemirror: {
			indentOnInit: true, // Whether or not to indent code on init.
			fullscreen: false,   // Default setting is false
			path: 'codemirror-5.22.0', // Path to CodeMirror distribution
			config: {           // CodeMirror config object
				//mode: 'application/x-httpd-php',
				lineNumbers: true
			},
			width: 800,         // Default value is 800
			height: 600,        // Default value is 550
			jsFiles: [          // Additional JS files to load
				//'mode/clike/clike.js',
				//'mode/php/php.js'
			]
		}

	});

	// Read only WYSIWYG add the class read-only-wysiwyg to a textarea
	tinyMCE.init({
		selector: "textarea.read-only-wysiwyg",
		mode : "textareas",
		height : "185",

		theme : "modern",
		readonly: true,
		toolbar: false,
		menubar: false,
		statusbar: false
	});
</script>