// https://datatables.net/examples/plug-ins/dom_sort.html
		$.fn.dataTable.ext.order['dom-text'] = function  ( settings, col )
		{
			return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
				return $('input', td).val();
			} );
		}
		 
		/* Create an array with the values of all the input boxes in a column, parsed as numbers */
		$.fn.dataTable.ext.order['dom-text-numeric'] = function  ( settings, col )
		{
			return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
				return $('input', td).val() * 1;
			} );
		}
		
		$.fn.dataTable.ext.order['dom-text-numeric2'] = function  ( settings, col )
		{
			return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
				return $('input.qty', td).val() * 1;
			} );
		}
		
		/* Create an array with the values of all the select options in a column */
		$.fn.dataTable.ext.order['dom-select'] = function  ( settings, col )
		{
			return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
				return $('select', td).val();
			} );
		}
		 
		/* Create an array with the values of all the checkboxes in a column */
		$.fn.dataTable.ext.order['dom-checkbox'] = function  ( settings, col )
		{
			return this.api().column( col, {order:'index'} ).nodes().map( function ( td, i ) {
				return $('input', td).prop('checked') ? '1' : '0';
			} );
		}