
<script>

	/**
	 * Creating a loading data div and append it to .dataTables_wrapper
	 * TODO: not sure this can be moved due to the translations
	 */
	function showLoadingDiv(table)
	{
		// if loading_datatable is not created create it on the fly ONLY ONCE
		if (! $( ".loading_datatable" ).length ) {
			var tableWrapper = $(".dataTables_wrapper");

			// Table is sent as an argument
			if(table !== undefined)
				tableWrapper = table.closest(".dataTables_wrapper");

			var loadingDiv 	= "<div class=\"loading_datatable\">";
			loadingDiv 		+= "<h3 class=\"loading_title\">{{trans('admin.datatable-loadingRecords')}}</h3>";
			loadingDiv 		+= "<p><i class=\"fa fa-spinner fa-spin\"></i></p>";
			loadingDiv 		+= "<p class=\"loading_text\">{{trans('admin.getting-data-text')}}</p>";
			loadingDiv 		+= "</div>";

			tableWrapper.append(loadingDiv);
		}

		$(".loading_datatable").show();
	}

	/**
	 * Hiding the .loading_datatable div
	 */
	function hideLoadingDiv(table)
	{
		// Table is sent as an argument
		if(table !== undefined)
			tableWrapper = table.closest(".dataTables_wrapper").find(".loading_datatable").fadeOut(300);
		else
		$(".loading_datatable").fadeOut(300);
	}

	/**
	 * Setting up a delay on the searchbox to avoid hitting the DB every keypress
	 */
	function setupSearchDelay(oSettings)
	{
		var filterTimer;
		// Only this datatable, not all on page
		//$( ".dataTables_filter input")
		$('#' + oSettings.sTableId).parent().find( ".dataTables_filter input")
		.unbind()
		.bind("keyup", function(e){

			var thisVal = $( this ).val();

			// Cancelling any previous delayed search
			clearTimeout(filterTimer);

			// If the user hits enter
			if ( e.keyCode == 13 ){
				// searching the datatable
				//oTable.fnFilter( thisVal );
				oSettings.oInstance.fnFilter( thisVal );
			}else{
				//filterTimer = setTimeout(function(){ oTable.fnFilter( thisVal ); },600);
				filterTimer = setTimeout(function(){ oSettings.oInstance.fnFilter( thisVal ); },600);
			}
		});

		if($('.widgets-editor').length === 0)
			$('#' + oSettings.sTableId).parent().find( ".dataTables_filter input").focus();
	}


</script>
