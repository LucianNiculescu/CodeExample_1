var body = $("body");

/**
 * Auto Scrolls the giving div used for example in the roles and permissions page
 * @param $scrollingDiv
 * @param initialScroll
 */
function autoScrollDiv($scrollingDiv, initialScroll) {
	if (initialScroll === undefined)
		initialScroll = 200;

	var scrollMargin = initialScroll / 2;

	$(window).scroll(function () {

		if ($(document).scrollTop() > 200) {
			$scrollingDiv
				.stop()
				.animate({"marginTop": (($(document).scrollTop() - scrollMargin))}, "slow");

			// Original solution which causes the div to be in the middle of screen
			// .animate({"marginTop": ($(window).scrollTop() )}, "slow" );
		}
		else {
			$scrollingDiv
				.stop()
				.animate({"marginTop": 0}, "slow");
		}
	});
}

/**
 * Filter function used in the roles and permissions page and can be used elsewhere
 * @param searchBox
 * @param searchable
 * @param hideable
 */
function filter(searchBox, searchable, hideable)
{
	// converting the class or id sent to a Dom element
	searchable = $(searchable);
	// The word you are searching for
	thisSearch = searchBox.val().toLowerCase();

	// Looping in the searchable list and check if it contains the value of the search or not.
	searchable.each(function(){
		// pick the current item and name it thisSearchable
		thisSearchable = $(this);
		// save it's content in thisContent
		thisContent = $(this).text().trim().toLowerCase();

		// if hideable is passed then check the parents for it
		if (hideable !== undefined)
		{
			thisSearchable	= thisSearchable.parents(hideable);
		}

		// If the search is part of thisContent then show it, else hide it
		if(thisContent.indexOf(thisSearch) > -1)
		{
			thisSearchable.show();
		}
		else
		{
			thisSearchable.hide();
		}
	});
}

/**
 * A generic error handling for all AJAX calls
 * @param data
 */
function alertError(data)
{
	console.log(data);
	// Hiding the loading div
	$(".loading_page").fadeOut("fast");
	swal({
		title: 	trans["error"],
		html: 	data,
		type: 	"error"});
}

/**
 * Toggle a Loading spinner for a given element
 * @param element
 * @param disabled
 * @param originalText is the text that used to be before the loading
 */
function toggleLoadingButton(element, disabled) {
	if(disabled){
		$(element).attr('data-title', element.html());
		$(element).html('<i class="fa fa-spinner fa-spin fa-fw"></i>&nbsp;Loading...');
	} else {
		$(element).html(element.attr('data-title'));
	}

	//Toggle disabled true/false
	$(element).prop('disabled', disabled);
}

/**
 * Adding a new navbar element inside the given navbar list, with the given ids
 * @param navBarId
 * @param tabContentId
 * @param newElementId
 * @param newElementTabText
 * @param closeButton
 * @param cookieName
 */
function addPrtgNavbarTab(navBarId, tabContentId, newElementId, newElementTabText, closeButton, cookieName) {
	var navbar = $("#" + navBarId);
	var li = $("<li></li>");
	var a  = $("<a></a>");
	var tabcontent = $('#' + tabContentId);
	var div = $("<div></div>");
	var addedDiv = $("<div></div>");
	var newElementTabId = newElementId+'_tab';


	//First create the anchor and append it to the navbar
	li.attr('role', 'presentation').attr('id', newElementTabId + '_tab_id');

	//Add a border-bottom to the elements of the first row
	if($("#" + navBarId + " li").length > 4)
		$("#" + navBarId + " li:lt(5)").attr('class', 'underlined');
	else
		$("#" + navBarId + " li:lt(5)").removeClass('underlined');

	addPrtgSettingsCloseButton()

	a.attr('href', '#' + newElementTabId)
		.attr('aria-controls', newElementTabId)
		.attr('role', 'tab')
		.attr('data-toggle', 'tab')
		.attr('aria-expanded', 'false')
		.attr('title', newElementId)
		.text(newElementTabText);
	li.append(a);

	//Just in case we need a close button
	if(closeButton === true) {
		if(typeof cookieName === 'string'){
			var closeTabButton = $('<div class="navbar-close" onclick="closeAndSwitchPrtgNavbarTab('+'\''+newElementId  +'_tab_tab_id\', \'' + newElementId +'_tab'+ '\','+'\''+ cookieName +'\''+')"><i class="fa fa-close"></i></div>');
		} else {
			var closeTabButton = $('<div class="navbar-close" onclick="closeAndSwitchPrtgNavbarTab('+'\''+newElementId  +'_tab_tab_id\', \'' + newElementId +'_tab'+ '\''+')"><i class="fa fa-close"></i></div>');
		}
		li.append(closeTabButton);
	}

	li.appendTo(navbar);

	//Second, create the div element that will be showed
	div.attr('role', 'tabpanel').attr('class', 'tab-pane').attr('id', newElementTabId);
	addedDiv.attr('id', newElementId);
	div.append(addedDiv);
	tabcontent.append(div);
}

/**
 * Closing an existing navbar Tab and content
 * Switching from the closed tab to the first one
 * @param navbarTabId
 * @param navbarContentId
 * @param cookie
 */
function closeAndSwitchPrtgNavbarTab(navbarTabId, navbarContentId, cookie) {
	var navbar = $("#" + navbarTabId);
	var navbarContent = $("#" + navbarContentId);
	var navbarTabParent = navbar.parent();
	var navbarContentParent = navbarContent.parent();
	//Find the first active element from the navbar + content and remove them
	navbar.remove();
	navbarContent.remove();

	//Check if there are other tabs that can be active
	if(navbarContentParent.find('.tab-pane').length) {
		//Check if the selected tab is active or trigger the first one
		if(navbar.hasClass('active')) {
			// Switch (add active class to them) to the first tab
			navbarContentParent.find('.tab-pane').removeClass('active').first().addClass('active');
			navbarTabParent.find('li').removeClass('active').first().addClass('active');
		}
	} else {
		//Switch to the PRTG Settings Container
		togglePrtgSettingsContent();
		//Remove the close button of the prtg settings container
		$('.grid-item.prtg .prtg-settings-content .toggle-prtg-settings').remove();
	}

	if(typeof cookie === 'string') {
		var cookie 	= getCookie(cookie);
		if (typeof cookie === 'string' && !($.cookie(cookie) === null)) {

			// Calling Ajax to remove the selected cookie
			$.ajax({
				url: "/json/widgets/prtg/0/get-report-data",
				type: "get",
				data: {
					id: 		navbarTabId,
					method:		'\\App\\Admin\\Widgets\\Prtg::deletePrtgSensorFromCookies'
				},
				success: function (data) {
				},
				error: function (data) {
				}
			});
		}
	}
}

/**
 * When logging out all ajax calls should be aborted to avoid errors
*/
body.on("click", ".logout", function(e){
	e.preventDefault();
	window.ajaxEnabled = false;
	$.xhrPool.abortAll();
	window.location.replace("/logout");
});

/**
 * Datatable active/deactivate function
 */
body.on("click",".action_status" , function(e){
	e.preventDefault();

	var thisLink 		= $(this);
	var name			= thisLink.data("name");
	var currentStatus	= thisLink.data("status");
	var sweetText 		= "";

	if (currentStatus == "inactive")
	{
		sweetText = trans["to-activate"] +" '" + name +"'?";
	}
	else
	{
		sweetText = trans["to-deactivate"] +" '" + name +"'?";
	}

	// Sweet Alert call to confirm
	swal({
		title				: trans["are-you-sure"],
		text				: sweetText,
		type				: "warning",
		showCancelButton	: true,
		confirmButtonColor	: "#00adef" ,
		confirmButtonText	: trans["yes-please"],
		cancelButtonText	: trans["cancel"]
	}).then(function(){
		// Call back function if user press yes please


		//Ajax URL
		var url 			= thisLink.attr("href");
		var id 				= thisLink.data("id");
		//child is the icon with the toggle font awesome
		var child 			= thisLink.find(">:first-child");
		var newStatus 		= "";
		var currentIcon		= "";
		var newIcon 		= "";
		var currentTextColor= "";
		var newTextColor 	= "";
		var newTitle		= "";

		// Toggle the status
		if (currentStatus == "inactive"){

			currentIcon		= "fa-toggle-off";
			currentTextColor= "text-danger";
			// New status details for icon, color and text
			newStatus 		= "active";
			newIcon 		= "fa-toggle-on";
			newTextColor 	= "text-success";
			newTitle		= trans["click-to-deactivate"] +" '"+name+"'";

		}else{

			currentIcon		= "fa-toggle-on";
			currentTextColor= "text-success";
			// New status details for icon, color and text
			newStatus 		= "inactive";
			newIcon 		= "fa-toggle-off";
			newTextColor 	= "text-danger";
			newTitle		= trans["click-to-activate"] +" '"+name+"'";
		}

		// Showing an over-lay div to avoid clicking on the screen while calling Ajax
		$(".loading_page").fadeIn("fast");

		//console.log("URL: " + url);
		// Calling Ajax to toggle the status
		$.ajax({
			url : url,
			type: "post",
			data: {
				'id' 		: id ,
				'status' 	: newStatus,
				'new-status' 	: newStatus,
				'_method' 	: 'PUT',
				'_token'	: $('input[name=_token]').val()
			}})
			.success (function(data){
				//console.log("DATA: " + data);
				if(data ==1){

					thisLink.data("status",newStatus);
					thisLink.attr("title", newTitle);
					child.removeClass(currentIcon);
					child.removeClass(currentTextColor);
					child.addClass(newIcon);
					child.addClass(newTextColor);

					// Hiding the overlay div
					$(".loading_page").fadeOut("fast");

					if(newStatus == 'active'){
						newStatus = trans["active"];
						thisLink.removeClass('inactive').addClass('active');
					}
					else{
						newStatus = trans["inactive"];
						thisLink.removeClass('active').addClass('inactive');
					}

					swal({
						title:	trans["done"],
						text: 	"'" + name + "' = " + newStatus + " !",
						type:	"success"
					});
				}
				else
				{
					alertError(data);
				}
			}).fail (function(data){
				alertError(data);
		});
	}, function (dismiss) {

	});
});

/**
 * Datatable delete function
 */
body.on("click",".action_delete" , function(e)
{
	e.preventDefault();
	var thisLink 		= $(this);
	var name			= thisLink.data("name");
	// Sweet Alert call to confirm
	swal({  	title				: trans["are-you-sure"],
		text				: trans["to-delete"] + " '" + name + "'?",
		type				: "warning",
		showCancelButton	: true,
		confirmButtonColor	: "#00adef" ,
		confirmButtonText	: trans["yes-please"],
		cancelButtonText	: trans["cancel"]
	}).then(function() {
		// Call back function if user press yes please

		// Ajax URL
		var url 			= thisLink.attr("href");
		var id 				= thisLink.data("id");
		// Showing an overlary div to avoid clicking while calling the function
		$(".loading_page").fadeIn("fast");
		// Calling Ajax to toggle the status
		//console.log("URL: " + url);
		$.ajax({
			url : url,
			type: "post",
			data: {
				'id' 		: id ,
				'name' 		: name ,
				'_method' 	: 'DELETE',
				'_token'	: $('input[name=_token]').val()
			}}).success (function(data){
			//console.log("DATA: " + data);
			if (data == 1)
			{
				// Hiding the row of the deleted site
				if($('body.manage.portals').length)
					window.location.replace("/manage/portals");
				else if($('body.manage.adjets').length)
					window.location.replace("/manage/injectionjets");
				else if($('body.manage.pms').length)
					window.location.replace("/manage/pms");
				else
					thisLink.parent().parent().hide();

				// Hiding the loading div
				$(".loading_page").fadeOut("slow");
				swal({
					title:	trans["done"],
					text: "'" + name + "' " + trans["is-deleted"],
					type: "success"});
			}
			else
			{
				alertError(data);
			}
		}).fail(function(data){
			alertError(data);
		});
	}, function (dismiss) {

	});
});

/**
 * Latency Dropdown Small Widget Size
 * When a link is clicked it removes the active class from the current one and adds the caret back in
 */
$('.widget-sub-menu li').on('click', function(){
	var $item = $(this).parents(".grid-item");
	//console.log($item);
	$($item).find('.dropdown-menu li.active').removeClass('active');
	$($item).find('.widget-dropdown-display').html("<i class='gateway-icon fa fa-wifi'></i><span class='gateway-name'>"  + $(this).text() + '</span><span class="caret select-gateway"></span>');
});


$(".elastic-search").on("click",function(e) {
	if($(this).attr("disabled") == "disabled")
		return false;

	e.preventDefault(); // cancel the link itself
	$('.from').val($(this).data('from'));
	$('.search_type').val($(this).data('type'));
	$('#advanced-search-form').submit();
});


// Use to add any chosen to a select
$('.chosen-select').chosen({
	width: "100%"
});

// DOM has loaded
$(document).ready( function () {

	// Making view section auto scrolled
	autoScrollDiv($('.view-section'), 150);

	// If we use the datatable class on a table we change it to a datatable
	$('.datatable').dataTable();

	// Use to add Tagging (tokenfield) to any input
	$('.tagging').tokenfield({
		inputType : 'text',
		createTokensOnBlur : true
	});

/********************************** All Validations ***********************************************/
	$.validator.addMethod("customAdjets", function(value, element) {
		if($("body.sites input[name='adjets']").is(":checked"))
			return (value !== '' && parseFloat(value) > 0 );
		return true;
	}, trans["adjets-validation"]);

	$(".validate-me-diff").validate({
		rules: {
			// password: "required",
			confirm_password: {
				equalTo: "#password"
			}
		}
	});

	// Use to add jquery validation to a form
	$(".validate-me").validate({
		ignore: ".validate-ignore"
	});

	$.validator.addMethod("mac-address", function(value, element) {
		value = value.toUpperCase();
		// Check the element hasn't had the mask applied yet
		if(!jQuery(element).data('dirty'))
		{
			jQuery(element).data('dirty', 'true');
			return true;
		} else {
			if(this.optional(element) || /^([0-9A-F]{2}[:-]){5}([0-9A-F]{2})$/.test(value))
			{
				// If mac validation is passed then assign its value to the uppercase to be saved as uppercase
				jQuery(element).val(value);
				return true;
			}
			else
			{
				return false;
			}
		}

	}, trans['mac-address-invalid']);

/********************************** END All Validations ***********************************************/

	// search function - uncomment this when the search class is built
	//new UISearch( document.getElementById( 'sb-search' ) );
	// hides the widget guide text as default - this can then be shown when the button is clicked to edit widget area
	$('.drag-guide').hide();

/*************************************** USER PROFILE **************************************************/

	/**
	 * Opens the user profile container
	 * Slides down the user profile container, adds an overlay for entire page, adds a class and pushes breadcrumbs
	 */
	function openProfileContainer() {
		$(".user-profile-container").slideDown('fast');
		$(".system-edit-overlay").fadeToggle("fast", "linear").addClass("profile-opened");
		$(".system-header-outer").toggleClass("profile-border");
		$(".breadcrumbs").toggleClass("profile-border-top");
	}

	/**
	 * Closes the user profile container
	 * Slides up the user profile container, removes the overlay, removes the class and returns the breadcrumbs to their place
	 * @returns {boolean}
	 */
	function closeProfileContainer() {
		$('.user-profile-container').slideUp('fast');
		$('.system-edit-overlay').fadeToggle("fast", "linear").removeClass("profile-opened");
		$('.breadcrumbs.fluid-container').removeClass('profile-border-top');
		return false;
	}

	//on click of the X, it closes the user profile container
	$('.user-profile-container .profile-cancel').click(function(e) {
		return closeProfileContainer();
	});

	if ($(".user-profile-container").length ) {
		//Checks if the navbar has the user profile container opened and closes it
		$(".navbar-avatar").click(function () {
			if($('.system-edit-overlay').hasClass('profile-opened'))
				closeProfileContainer();
			else
				openProfileContainer();
		});
	}

	//Register a "click" event (as $().click() is not working because we add the 'profile-opened' class dynamically) on system overlay to close the user profile container
	$(document).on('click', ".system-edit-overlay.profile-opened", function() {
		closeProfileContainer();
	});

/*************************************** END USER PROFILE **************************************************/

	//Default options for Toastr notification system
	toastr.options = {
		"closeButton": true,
		"debug": false,
		"newestOnTop": true,
		"progressBar": false,
		"positionClass": "toast-bottom-right",
		"preventDuplicates": false,
		"onclick": null,
		"showDuration": "300",
		"hideDuration": "1000",
		"timeOut": "0",
		"extendedTimeOut": "0",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut",
		"tapToDismiss": false
	};

	/* Autofocus Datatables search input (only when there are no widgets, tested by checking for a .table-no-focus */
	if( $('.table-no-focus').length < 1 )
		$.extend( true, $.fn.dataTable.defaults, {
			"initComplete": function(settings, json) {
				$('div.dataTables_filter input').focus();
			}
		} );

	// Autofocus Form Input with first tab index
	$("input[tabindex='1']:first").focus();

	// Feedback section
	Feedback({
		h2cPath:'/portal/js/html2canvas.min.js',
		url: '/feedback/send-email',
		label: ''
	});
	$('.feedback-btn.feedback-bottom-right').html('<i class="fa fa-bullhorn" aria-hidden="true"></i>').attr('title', 'Send feedback');
});





/**
 * Notification system using Toastr (at the moment)
 * @param type
 * @param message
 */
function notification(type, message) {
	toastr[type](message);
}

// form_template.blade.php
// Editing a new role removes the empty class otherwise the label will float over the role input field
$("input#role").change(function () {
	$(this).removeClass("empty");
});

/**
 * Highlighting the searchresult area
 * @param search
 */
function highlightSearch(search)
{
	$(".search-results").find('.highlightable').each( function ( index ) {
		var foundStringAt = -1;
		var data = $(this).html();

		var newData = '';
		var part = '';
		search = search.toLowerCase();
		foundStringAt = data.toLowerCase().indexOf(search);
		// if it is found
		if( foundStringAt !== -1)
		{
			// Loop and highlight each time you find it
			while( foundStringAt !== -1)
			{
				// Part is the data until the search including the search word
				part = data.slice(0, foundStringAt + search.length);
				// data has the rest of the data after slicing the part from it
				data = data.slice(foundStringAt + search.length);

				// Putting the new value together and surrounding bold tag
				newData += [part.slice(0, foundStringAt), '<b>', part.slice(foundStringAt, foundStringAt + search.length), '</b>'].join('');

				// Finding the search word again and loop if it is found
				foundStringAt = data.toLowerCase().indexOf(search);

			}

			// Adding the rest of the data to newData
			newData += data;
			$(this).html(newData);
		}
		else
			$(this).html(data);
	});
}


/**
 * Resize an element to the size of the browser window with a minus offset
 * @param element
 * @param offSet
 */
function resizeElement(element, offSet)
{
	var windowHeight = $( window ).height();
	element.css('max-height', (windowHeight-offSet) + 'px');
}