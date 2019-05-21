var widgetItemElems;

var widgetsContainer = $('.widgets-editor .grid');
var editWidgetsButton = $('.title-widget .edit-widgets');

// Initializing the Widgets grid with packery and all events
loadWidgets();

/**
 * loadWidgets is the main function that setup Packery
 * it is needed to be called after dragging widgets from in active to active and back
 **/
function loadWidgets(items) {
	if(items !== undefined)
	{
		widgetsContainer.empty().append(items);
	}

	// Setting Up Pakery
	widgetsContainer.packery({
		rowHeight: 250,
		columnHeight: 250,
		gutter: '.gutter-sizer'
	});

	widgetsContainer.imagesLoaded().progress(function () {
		widgetsContainer.packery();
	});

	widgetItemElems = $(widgetsContainer.packery('getItemElements'));

	widgetsContainer.packery('bindUIDraggableEvents', widgetItemElems);

	// this is where the csrf token is used to stop the token error - this targets the meta tag for the token
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});

	// make item elements draggable and will save to DB the widgets order
	widgetItemElems.draggable({
		handle: ".panel-draggable",
		containment: ".grid",
		scroll: false,
		cancel: '.panel-actions',
		//drag stop function
		stop: function (event, ui) {
			widgetsContainer.packery();
			saveWidgetsOrder();
		}
	});

	/**
	 * Remove widget, reload widget grid and run orderItems function to save order to db.
	 * Also appends an li item onto the inactive widget list with the name of the removed widget and makes it draggable.
	 * This is set with the data-item-id of the widget so if it is dragged back onto the grid the widget will be saved into
	 * the database
	 *
	 */
	widgetsContainer.on("click", ".remove-widget", function (e) {
		// Stops closing the widget several times
		e.stopImmediatePropagation();
		// gets the outer .grid item of button clicked
		var item = $(this).parents(".grid-item");

		// Removing any datatable in the widget to avoid problems in JS
		//item.find('.dataTables_wrapper').remove(); TODO: ???????????????????????????????????????????????????????????????

		item.find('.widget-inner').hide();

		//this removes the parent div (.grid-item) of the button clicked
		widgetsContainer.packery('remove', item).packery();

		// Save widgets in the server
		saveWidgetsOrder();

		var widgetId = item.data('item-id');

		// Removing the style that came from packery
		item.attr('style', '').removeClass('active untoggled toggeled').addClass('inactive');

		// Getting the outerHTML of the item . i.e. the html and the original tag
		var gridItem = $('<div>').append(item.clone()).html();

		// Injecting the widget inside the inactive widgets list and make it draggable
		var newInactiveWidget = $('<div class="panel inactive-widget ui-draggable ui-draggable-handle" style="z-index:90000;" ' +
			'data-item-id="'+widgetId+'" id="'+widgetId+'">' +
			'<li class="panel-heading inactive-drag" >' +
			gridItem+
			'</li></div>').draggable({ revert: true });

		// Removing the widget from the Grid
		item.remove();
		$(".inactive-widgets").append(newInactiveWidget);

		handleEmptyDashboard();

	});

	/**
	 * Remove widget, reload widget grid and run orderItems function to save order to db.
	 * Also appends an li item onto the inactive widget list with the name of the removed widget and makes it draggable.
	 * This is set with the data-item-id of the widget so if it is dragged back onto the grid the widget will be saved into
	 * the database
	 *
	 */
	widgetsContainer.on("click", ".toggle-prtg-settings", function (e) {
		// Stops closing the widget several times
		e.stopImmediatePropagation();
		togglePrtgSettingsContent();
	});

	/**
	 * Toggles the widget on button click
	 * When toggle-size button is clicked get the outer div of the link (the widget outer div) and toggle between the
	 * default and toggled classes as well as passing giving it the toggled class. The packery layout is then reloaded
	 * and the orderItems will reorder in the database
	 *
	 */
	widgetsContainer.on("click", ".toggle-size", function (event) {
		// Stops running several times
		event.stopImmediatePropagation();
		// gets the outer .grid item of button clicked
		var item = $(this).parents(".grid-item");

		// toggles between the two classes and toggled class
		$(item).toggleClass(item.data('default-class') + " " + item.data('toggle-class') + ' toggled');
		$(item).css('height', '');
		$(item).css('width', '');
		// resets the highchart
		resetHighChartsOnToggle(item);

		// get the current class when it is toggled and pass it to the DB and save for the current item
		widgetsContainer.packery('layout');

		// pass to be ordered and saved into the DB
		saveWidgetsOrder();
	});

	/**
	 * When edit widgets button is clicked, if the class active already exists run hideEditDashboard and
	 * deactivateWidgetMenu function. If no active class, run showEditDashboard and activateWidgetMenu functions
	 */
	$('.title-widget .edit-widgets.default').click(function (e) {
		e.stopImmediatePropagation();
		widgetsContainer.toggleClass('edit-mode').toggleClass('show-mode');
		// Edit Mode
		if ($(this).hasClass('active')) {
			showReportSettingsBar();
			hideEditDashboard();
			deactivateWidgetMenu();
		}
		else {
			hideReportSettingsBar();
			showEditDashboard();
			activateWidgetMenu();
		}

		makeInactiveWidgetItemsDraggable();
	});

}


/**
 * TODO: ?????
 */
function togglePrtgSettingsContent() {
	var prtgSettingsContainer= $('.grid-item.prtg .prtg-settings-content');
	if(prtgSettingsContainer.hasClass('hidden')) {
		prtgSettingsContainer.removeClass('hidden').addClass('show');
		prtgSettingsContainer.slideDown("slow");
	} else {
		prtgSettingsContainer.removeClass('show').addClass('hidden');
		prtgSettingsContainer.slideUp("slow");
	}
}


/**
 * TODO: ?????
 */
function handleEmptyDashboard()
{
	var inactiveWidgets = $("#inactive-widget-list .grid-item.inactive");
	var activeWidgets = $(".grid .grid-item").not(".inactive");

	if(inactiveWidgets.length > 0)
		$("#noInactiveWidgets").hide();
	else
		$("#noInactiveWidgets").show();

	if(activeWidgets.length > 0)
		$("#noActiveWidgets").hide();
	else
		$("#noActiveWidgets").show();
}

/**
 * Resize the highcharts within the widgets
 * NOTE: this is for charts within a tabbed content panel inside a widget
 * @param  $item
 */
function resetHighChartsOnToggle($item)
{
	var chartContainer = $item.find('.chart-container');
	chartContainer.each(function (i, itemElem) {
		var chart = $(itemElem).highcharts();
		// checks if chart exists to avoid error
		if(chart){
			//null value sets the width to the outer container
			chart.setSize(null,null, false);
		}
	});
}

/**
 * Adding inactive widget to the Widgets Grid
 **/
function processDroppedWidget(event, ui)
{
	var draggedItem = $(ui.draggable);

	if (ui.draggable.hasClass('inactive-widget')) {

		$(ui.draggable).fadeOut('fast', function () {
			$(this).remove();
		});
		var gridItem = draggedItem.find('.grid-item').removeClass('inactive');
		gridItem.find('.widget-inner').show();
		widgetsContainer.append(gridItem);
		widgetsContainer.packery( 'addItems', gridItem );

		saveWidgetsOrder();
		handleEmptyDashboard();
		loadWidgets(widgetsContainer.packery('getItemElements'));

		// Initializing the specific widget Javascript
		gridItem.trigger('initWidget');

	}
}


/**
 * Give the .inactive widget items draggable capabilities
 */
function makeInactiveWidgetItemsDraggable()
{
	$(".inactive-widget").find('.widget-inner').hide();
	$(".inactive-widget").draggable({
		revert: true,
		stop: function (event, ui) {
			widgetsContainer.packery();
			handleEmptyDashboard();
		}
	});
}

/**
 * Creates the drop zone, checks the dropped item has .inactive-widget class. If the dropped item has inactive-widget
 * class remove the dropped item from inactive list, loader the loading page and pass the dropped item into makeWidgetActive
 * function
 */
$('.drop-zone').droppable({
	drop: processDroppedWidget
});



/**
 * Gets all of the currently active widgets, loops through them, checks if they have a toggles class. Passes the
 * toggleStatus, widgetId and widget order through for each widget into an AJAX call to save them to the database
 */
function saveWidgetsOrder() {
	//gets a list of item elements
	var itemElems = widgetsContainer.packery('getItemElements');

	//set empty array
	var widgetList = [];
	// loops through the packery items
	$(itemElems).each(function (i, itemElem)
	{
		// targets the data-item-id and puts it into a variable
		var elemId = $(itemElem).attr("data-item-id");
		// makes sure it doesn't get empty packery items such as guttering
		if (elemId)
		{
			var togglestatus;
			if ($(itemElem).hasClass('toggled'))
				togglestatus = "toggled";
			else
				togglestatus = "untoggled";

			// Adding status and id and order to widgetList
			widgetList.push({
				"status": togglestatus,
				"widget_id": elemId,
				"order": i
			});
		}
	});

	// Saving Widgets order using AJAX call
	$.ajax(
		{
			url: "/json/save-widgets",
			type: "post",
			data: {
				widgetList: widgetList,
				user_id: USER_ID,
				site_id: SITE_ID,
				route: ROUTE
			},
			success: function (data) {

			},
			error: function (data) {

			}
		});

	// custom event that can be triggered from anywhere to do anything
	$('body').trigger('saveWidgetsOrder:finish');
}

/**
 * hides the edit widget screen
 */
function hideEditDashboard(){

	$('.site-menubar').attr('style','z-index:1100;');
	$('.drag-guide').hide();
	$('.dashboard-description').show();
	$(".system-edit-overlay").fadeOut('slow');
	$('.title').attr('style','color:#424242;');
	$('.drop-zone').attr('style','position:initial;');
	$('.form_actions').attr('style','z-index:1000');
	handleEmptyDashboard();
}

/**
 * shows the edit widget screen
 */
function showEditDashboard(){

	$('.site-menubar').attr('style','z-index:4400;background:none!important;');
	$('#main-menu').attr('style','z-index:100');
	$('.site-navbar').attr('style','z-index:4500');
	$('.title-widget').attr('style','z-index:4400');
	$('.form_actions').attr('style','top:-17px');
	$('.title').attr('style','color:#fff;');
	$('.drag-guide').show().fadeIn('slow');
	$('.dashboard-description').hide();
	$('.drop-zone').attr('style','position:relative;z-index:4200');
	$('.site-footer').attr('style','z-index:4500');
	$('.system-edit-overlay').hide().fadeIn('slow');
	handleEmptyDashboard();
}

/**
 * Activate the widget menu
 */
function activateWidgetMenu() {
	editWidgetsButton.addClass('active').removeClass('default');
	$('#main-menu').hide();
	$('#inactive-widget-list').fadeIn('fast');
}

/**
 *  Deactivate the widget menu
 */
function deactivateWidgetMenu() {
	editWidgetsButton.addClass('default').removeClass('active');

	$('#inactive-widget-list').fadeOut('fast');
	$('#main-menu').fadeIn('fast');
}