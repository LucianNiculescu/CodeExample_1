$(function() {
	/* Type Field Changes
	 * ----------------------------------------------------------------------------------
	 * Hide cost field based on type field value, and show Facebook model based on value
	 * TODO: Need to look at all JS on this page
	 */
	var typesToHideCost 	= ['facebook', 'twitter', 'linkedin', 'gha', 'google', 'quick_login', 'live', 'microsoft', 'voucher', 'voyat', 'whitelist'];
	var typeInput			= $('body.packages #type');
	var costInput			= $('body.packages #cost');
	var facebookContainer 	= $('body.packages #facebook_like_outer');
	var ghaContainer 		= $('body.packages #gha_outer');
	var voyatContainer 		= $('body.packages #voyat_outer');
	var tieredInputnw			= $('body.packages #gha-tiered-bandwidth');
	var tieredInput			= $('#tiered-bandwidth');

	// Watch for type input change
	typeInput.change(function() {
		var typeVal =  $( this ).val().toLowerCase();
		var typeData =  $( this ).data('name');

		// Toggle cost container based on type value (set cost input val to 0 on hide)
		var shouldHide = ( $.inArray(typeVal, typesToHideCost ) !== -1 || $.inArray(typeData, typesToHideCost ) !== -1 );

		(shouldHide === false
			? costInput.closest('.input-container').show('fast')
			: costInput.val("0").closest('.input-container').hide('fast'));

		// Toggle Facebook modal based on type value
		(typeVal.toLowerCase() === 'facebook'
			? facebookContainer.show('fast')
			: facebookContainer.hide('fast'));

		// Toggle Gha modal based on type value
		if($( this ).data('name') === 'gha' || typeVal.toLowerCase() === 'gha')
		{
			ghaContainer.show('fast');
			ghaContainer.find('#enrollment_code').prop('required', true);
		}
		else
		{
			ghaContainer.hide('fast');
			ghaContainer.find('#enrollment_code').prop('required', false);
		}

		if($( this ).data('name') === 'voyat' || typeVal.toLowerCase() === 'voyat')
		{
			voyatContainer.show('fast');
			voyatContainer.find('#key').prop('required', true);
		}
		else
		{
			voyatContainer.hide('fast');
			voyatContainer.find('#key').prop('required', false);
		}
	});

	// Only do tiered bandwidth if we are showing a package
	if (tieredInput.length > 0) {
		var tieredInputJs = tieredInput[0];
		// Toggle visibility of tiered sliders based on switch
		function showHideTieredSliders() {
			var shouldShow = $( tieredInput ).is(':checked');

			if (shouldShow) {
				$('.tiered-sliders').show();
			} else {
				$('.tiered-sliders').hide();
			}

		}

		// React to changing the Tiered Bandwidth switch
		tieredInputJs.addEventListener('change', showHideTieredSliders);

		// We need to show or hide the sliders when the page is first shown
		showHideTieredSliders()
	}

	// On Package Edit, type field is disabled textbox - trigger change to toggle relevant page changes
	if(typeInput.attr('type') === 'text')
		typeInput.trigger('change');

	/*
	 * DISABLED WHEN HOURS HAVE BEEN ADDED
	 * Duration field addon plural based on input value

	$('body.packages #duration').change(function() {
		var $this		= $( this );
		var durationVal = $this.val();
		var addon		= $this.parent().find('span');

		// Alter text in addon based on day figure
		(durationVal == 1
			? addon.html($this.data('addon-singular'))
			: addon.html($this.data('addon-plural')));
	}).trigger('change');
	 */

	/**
	 * Attribute tab plugin
	 */
	(function( $ ) {
		$.fn.attributeTabs = function() {

			var $this = $(this);
			var showSwitch = $this.find(':checkbox').first();
			var navTabs = $this.find('ul.nav-tabs').first();
			var tabContainer = $this.find('div.tab-container').first();
			var switchContainer = $this.find('div.switch-container').first();
			var tabContent = $this.find('div.tab-content').first();
			var heroUnit = tabContent.find('div.hero-unit').first();
			var content = tabContent.find('#content').first();
			var search = navTabs.find('#navSearch');

			// Toggle a list item and it's respective tab
			$this.on('attrtab.toggle', function(e, id) {
				search.trigger('attrtab.nav.search.clear');

				// Toggle list item and form
				navTabs.find('a[href="' + id + '"]').parent().toggle('fast');

				// Toggle hero unit and content when content is blank
				content.find( id )
					.toggle('fast', function() {
						var noOfItems   = content.find('.attribute-tab-content').length;
						var hiddenItems = content.find('.attribute-tab-content:not(:visible)').length;

						if(noOfItems === hiddenItems)
						{
							heroUnit.toggle();
							content.toggle();
						}
					});
			});

			// On Edit, show all with a value
			var shownFlag = false;
			navTabs.find('li > a').each(function (e) {
				var tabId 	 = $( this ).attr('href');
				var inputVal = $( tabId ).find('.form-control').val();

				if(inputVal !== undefined && inputVal !== "")
				{
					$this.trigger('attrtab.toggle', [ tabId ]);
					shownFlag = true;
				}
			});
			if(shownFlag)
				$this.trigger('attrtab.toggle.container');

			// When selecting an attribute, hide the nav-tabs li and show the form
			navTabs.find('li > a').click(function(e) {
				e.preventDefault();

				// Clear search
				search.trigger('attrtab.nav.search.clear');
				$this.trigger('attrtab.toggle', [ $( this ).attr('href') ]);
			});

			// When removing an attribute, show the nav-tabs li, hide the form and reset the values
			content.find('a.remove').click(function(e) {
				e.preventDefault();

				// Toggle and remove any value set
				var tabId = '#' + $( this ).data('id');
				$this.trigger('attrtab.toggle', [ tabId ]);
				content.find(tabId).find(':text').val('');
			});

			// Clear the search input and show all attributes when event triggered
			search.on('attrtab.nav.search.clear', function(e) {
				$( this ).val('');
				navTabs.find('li').removeClass('hidden-by-search');
			});

			// Limit the nav items shown when event triggered
			search.on('attrtab.nav.search.filter', function(e, val) {
				// navTabs.find("li[data-text*='"+ val + "']").addClass('hidden-by-search');
				navTabs.find("li.attribute-list-item").not("[data-text*='" + val + "']").addClass('hidden-by-search');
			});

			// Filter the list when using the nav search
			search.keyup(function() {
				var val = $( this ).val().toLowerCase();

				// Hide items based on value, or show all if no value supplied
				(val != ''
					? search.trigger('attrtab.nav.search.filter', [ val ])
					: search.trigger('attrtab.nav.search.clear'));
			});

			// Prevent enter key submitting any forms on search
			search.keydown(function(e) {
				if(e.keyCode == 13)
				{
					e.preventDefault();
					return false;
				}
			});
		};
	}(jQuery));

	// Initialise tabs plugin
	$('body.packages .attribute-tabs').attributeTabs();

	/**
	 * Package Update
	 * Present choice modal where there are active transactions for the package
	 * with the choice to update with edited package information
	 */
	var formMethod = $('body.packages input[name="_method"]').val();
	var translations = $('.embedded-translations');
	if(formMethod === 'PUT') {
		$('body.packages #crudForm').submit(function (e) {
			var $this = $(this);

			// Prevent submitting form if there is any validation issues
			if($this.validate().errorList.length != 0 )
				return false;

			var updateTransactionsInput = $('body.packages input[name="update_transactions"]');
			var gatewayIdInput = $('body.packages input[name="gateway_id"]');

			/**
			 * Get a translation embedded in the DOM
			 * @param key
			 */
			var trans = function (key) {
				return $('.embedded-translations .' + key).html();
			};

			// If we have transactions (available in an element on the blade),
			// display an alert to the user
			var transactionsCount = $('body.packages .transactions-count').html();
			if (transactionsCount > 0) {
				e.preventDefault();

				swal({
					title:  trans('update-transactions-title'),
					text: trans('update-transactions-text'),
					input: 'select',
					type: "warning",
					inputOptions: {
						'no': trans('update-package-not-transactions'),
						'yes': trans('update-package-with-transactions'),
						'replace': trans('update-package-by-replacing')
					},
					showCancelButton: true,
					confirmButtonText: trans('submit')
				}).then(function(selection) {
					// Insert selection into form
					updateTransactionsInput.val(selection);

					switch(selection) {
						// If the user wants to replace package, ask for extra confirmation
						case 'replace':
							swal({
								title: trans('are-you-sure'),
								text: trans('package-will-be-replaced'),
								type: 'warning',
								showCancelButton: true,
								confirmButtonColor: '#00adef',
								confirmButtonText: trans('submit')
							}).then(function () {
								// Submit
								$this.off('submit').submit();
							}).catch(swal.noop);
							break;
						// If the user wants to update transactions, check whether we have Gateways
						// for them to select
						case 'yes':
							// gatewaysList injected into body of form.blade.php
							switch(Object.keys(gatewaysList).length)
							{
								// If we have no Gateways, submit the form as if we're not updating
								case 0:
									updateTransactionsInput.val('no');
									$this.off('submit').submit();
									break;
								// If we have only one Gateway, insert that ID into the form and submit
								case 1:
									gatewayIdInput.val( Object.keys(gatewaysList)[0] );
									$this.off('submit').submit();
									break;
								// Show an alert for the user to select a Gateway which the transactions will be updated with
								default:
									swal({
										title:  trans('packages-select-gateway'),
										text: trans('packages-select-gateway-info'),
										type: 'warning',
										input: 'select',
										inputOptions: gatewaysList,
										showCancelButton: true,
										confirmButtonText: trans('submit')
									}).then(function(gatewayId) {
										$('body.packages input[name="gateway_id"]').val(gatewayId);
										$this.off('submit').submit();
									}).catch(swal.noop);
									break;
							}
							break;
						default:
							// Submit
							$this.off('submit').submit();
							break;
					}
				}).catch(swal.noop);
			}
		});
	}

	/**
	 * This function ties a slider and a text box together
	 * The slide is asRange and we use calls to its function
	 * to get and set the value of the slider and to respond
	 * to changes in the slider.
	 * @param slider_selector
	 * @param text_selector
	 */
	function sync_package_sliders(slider_selector, text_selector) {
		// We use a closure so that we can have a "semaphore" for each combination of slider and text box
		(function () {
			// This is essentially a semaphore to ensure we don't get locked in an endless cycle
			// where a change in one triggers a change in the other (which then changes the original etc)
			var syncing_sliders = false;

			// If the text box changes for some reason we need to update the slider
			function sync_from_text() {
				var oldValue = $(slider_selector).asRange('get');
				var newValue = $(text_selector).val();
				if (isNaN(newValue) || newValue < 0 || newValue > 999) {
					$(text_selector).val(oldValue);
				} else {
					// Do we really need to tell the slider to change its value?
					if ((! syncing_sliders) && (newValue != oldValue)) {
						syncing_sliders = true;
						$(slider_selector).asRange("set", newValue);
						var newValue = $(slider_selector).asRange('get');
						$(text_selector).val(newValue);
						syncing_sliders = false;
					}
				}
			}

			// The slider responds to typing in the text box.
			$(text_selector).keyup(function () {
				sync_from_text();
				// Updating the slider will lose focus so regain it.
				// We will only do this when typing.
				$(text_selector).focus();
			});

			// The slider responds to the spinners (arrows) in the text box.
			// We could be more sophisticated with mouseover/up/down
			// but we just react to the click event
			$(text_selector).click(function () {
				sync_from_text();
			});

			//The text box reflects changes in the slider
			$(slider_selector).on('asRange::change', function () {
				var newValue = $(slider_selector).asRange("get");
				if (! syncing_sliders) {
					syncing_sliders = true;
					$(text_selector).val(newValue);
					syncing_sliders = false;
				}
			});
		})();
	}
	// Sync the slider and text box for download bandwidth
	sync_package_sliders('#download', '#download-text');

	// Sync the slider and text box for upload bandwidth
	sync_package_sliders('#upload', '#upload-text');
});