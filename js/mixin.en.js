const mixin = {
	created() {
		$.extend( $.fn.dataTable.defaults, {
		    autoWidth: false,
            // responsive: true,
		    dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
		    language: {
		        search: '',
		        searchPlaceholder: 'Type to filter...',
		        lengthMenu: '<span>Show:</span> _MENU_',
		        paginate: { 'first': 'First', 'last': 'Last', 'next': '&rarr;', 'previous': '&larr;' }
		    },
		    drawCallback: function () {
		        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
		    },
		    preDrawCallback: function() {
		        $(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
		    },
		});
	}
}