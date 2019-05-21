$(document).ready( function () {
	var body = $("body");

	var divHieght = 0;
	var brandDivs = $("body.brand .brand-div");

	$.each(brandDivs, function()
	{
		if ($(this).height() > divHieght)
			divHieght = $(this).height();
	});

	brandDivs.height(divHieght);
});

/**
 * Brand Reset
 */
body.on("click", ".action_reset", function (e) {
	e.preventDefault();
	var thisLink = $(this);
	var name = thisLink.data("name");
	var portal = thisLink.data("portal");

	// Sweet Alert call to confirm
	swal({
		title: trans["are-you-sure"],
		text: trans["to-reset"] + " '" + name + " - " + portal +"'?",
		type: "question",
		showCancelButton: true,
		confirmButtonColor: "#00adef",
		confirmButtonText: trans["yes-please"]
	}).then(
		// Call back function if user press yes please
		function () {
			// Ajax URL
			var url = thisLink.attr("href");
			var id = thisLink.data("id");
			// Showing an overlary div to avoid clicking while calling the function
			$(".loading_page").fadeIn("fast");
			// Calling Ajax to toggle the status
			//console.log("URL: " + url);
			$.ajax({
				url: url,
				method: "POST",
				data: {
					id: id,
					action: "delete",
					_method: "POST",
					_token: $('input[name=_token]').val()
				}
			}).success(function (data) {
				//console.log("DATA: " + data);
				if (data == 1) {
					//thisLink.parent().parent().hide();

					thisLink.prev().prev().removeClass('fa-check text-sucess').addClass('fa-warning text-warning');
					thisLink.remove();

					// Hiding the loading div
					$(".loading_page").fadeOut("fast");

					swal({
						title: trans["done"],
						text: "'" + name + " - " + portal + "' " + trans["is-back-to-default"],
						type: "success"
					});
				}
				else {
					alertError(data);
				}
			}).fail(function (data) {
				alertError(data);
			});
		}).catch(swal.noop);

	return false;
});
