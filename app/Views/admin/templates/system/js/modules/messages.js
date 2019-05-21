/**
 * Called from the header callback for a datatable,
 * it uses the filterList to build a select box for a column
 * with an onchange which passes the select option as an AJAX call
 * @param thisTable
 * @param thead
 * @param fullSource
 * @param filterList
 */
function buildDatatableDropdowns(thisTable, thead, fullSource, filterList)
{
	/**
	 * Build select list on the messages list
	 * @param filter
	 */
	function buildDropDown (filter)
	{
		var th = $(thead).find('th').eq(filter.columnNumber);
		var select = th.find('select');

		// Only build select if it does not exist
		if ( select.length === 0) {
			th.append(':<select class="dropdown-filter chosen"></select>');

			select = th.find('select');

			//Stop the click on the dropdown also causing the sort to happen for sortable headers
			select.click(function( event ) {
				event.stopPropagation();
			});

			// Filter the data from the server based on the selected value
			// To make this work properly for multiple columns we would need to either build the AJAX string using all filters
			// or reset the other filters to a default so it is clear the filter is unused.
			select.on( 'change', function () {
				thisTable.fnSettings().sAjaxSource = fullSource+"AA_filter_" + filter.columnName+"=" + select.val();
				thisTable.fnDraw();
			});

			// Add an option for of the filter values
			filter.filterValues.forEach( function(filterValue) {

				// Mark an option as selected if it matches the initial value
				if ((filter.initialValue !== undefined) && (filter.initialValue === filterValue.value))
					select.append( "<option selected = 'selected' value=" + filterValue.value + ">" + filterValue.text + "</option>" );
				else
					select.append( "<option value=" + filterValue.value + ">" + filterValue.text + "</option>" );
			});
		}
	}

	// Build the select box for each column and set the onChange action
	filterList.forEach(function(filter) {
		buildDropDown(filter );
	});
}