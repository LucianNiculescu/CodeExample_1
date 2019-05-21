var body = $(body);

/**
 * Datatable view function
 */
body.on("click","body.roles-and-permissions .action_view, #roles-and-permissions-table td a:not('.action_edit')" , function(e){
	e.preventDefault();
	var thisLink 		= $(this);
	var url 			= thisLink.attr("href");
	var id 				= thisLink.data("id");
	var name 			= thisLink.data("name");

	thisLink.closest('table').find('tr').removeClass('active');
	thisLink.closest('tr').addClass('active');
	$('.role-details .contents').html('<i class="fa fa-spinner fa-spin fa-2x"  style="position:absolute; left:200px; top:50px"></i>');
	$.ajax({
		url : url,
		type: "get",
		data: {
			'id' 		: id ,
			'_method' 	: 'GET',
			'_token'	: $('input[name=_token]').val()
		}})
		.success (function(data){
			// Showing an over-lay div to avoid clicking on the screen while calling Ajax

			$('.role-details .contents').html(data);
			$('#permissionName').html(name);
			$('#showTable').dataTable({
				"order"     : [0, "asc"],
				"aoColumns"	: [
					null,
					null
				]
			});
		}).fail (function(data){
		alertError(data);
	});
});

/**
 * Changing a Category from on to off will switch off and disable all the create update and delete permissions checkboxes
 */
function changePermissionsStatus(thisCategory){
	var category 	= thisCategory.data("category");
	var isChecked 	= thisCategory.is(':checked');

	var permissionChechbox 	= thisCategory.parents(".permission-tab").find(".permission[data-permission^='"+category+"']");

	var permissionRow	= permissionChechbox.closest(".row");

	if(isChecked)
	{
		permissionRow.removeClass("disabled");
	}
	else
	{
		// Category switch is off, so create, update and delete should be disabled
		// By adding a disabled class to the whole row to disable also the label "for" functionality
		permissionRow.addClass("disabled");

		//And adding switched off class to the switchery span and remove the style from the children of the span which is the circle that goes right and left
		permissionChechbox.next().removeAttr("style").addClass("switched_off").children().removeAttr("style");

		// And also switched off
		permissionChechbox.prop("checked", false);

	}
}

/**
 * Calling the changePermissionsStatus when changing the category checkbox
 */
body.on("change", ".category", function(){
	changePermissionsStatus($(this));
});

/**
 *
 * Avoiding accordion to open when clicking on the switches by delegating the click action to $(".nav-tabs-vertical")

 body.on( "click", ".switchery",function(){
			return false;
		});
 */

/*		$('.tab-content').droppable({
 drop: function(){alert('dropped');}
 });*/


// roles/index.blade.php
$( document ).ready(function() {
	var defaultTab 			= $("#default-tab");
	var roleName 			= $(".role_name");

	var cancelButton 		= $(".cancel_button");
	var createButton 		= $(".create_button");
	var searchPermissions 	= $("#search_permissions");
	//var body = $("body");

	$(".roles-and-permissions #searchPermissionsCategories").keyup(function(){
		return filter($(this), ".permissions-categories.searchable");
	});

	$(".roles-and-permissions #searchRoleManage").keyup(function(){
		return filter($(this), ".role-manage.searchable");
	});

	$(".roles-and-permissions #searchWidgets").keyup(function(){
		return filter($(this), ".widgets.searchable");
	});

	/**
	 * running the changPermissionsStatus on all unchecked categories
	 */
	$(".category:not(:checked)").each(function(){
		changePermissionsStatus($(this));
	});

	// Making permissions auto scrolled
	autoScrollDiv($('body.roles-and-permissions div[id^="category_"]'));

	// Making widgets sortable
	$( ".roles-and-permissions .sortable" ).sortable({
			connectWith: ".roles-and-permissions .sortable",
			axis: "y",
			receive: function(e, ui) {

				if($(this).closest('.widgets-section').attr('id') === 'activeWidgets')
					ui.item.find('input').val("2");
				else if($(this).closest('.widgets-section').attr('id') === 'inactiveWidgets')
					ui.item.find('input').val("1");
				else
					ui.item.find('input').val("0");
			}
		}
	).disableSelection();

	// Resize the role details
	var roleDetails = $('body.roles-and-permissions .role-details .contents');
	var offSet = 236 + 30;
	resizeElement(roleDetails, offSet);
	$(window).on('resize', function () {
		resizeElement(roleDetails, offSet);
	});
});