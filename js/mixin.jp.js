const mixin = {
	created() {
		$.extend( $.fn.dataTable.defaults, {
			autoWidth: false,
			dom: '<"datatable-header"fl><"datatable-scroll"t><"datatable-footer"ip>',
			language: {
				"sEmptyTable":     "データがありません。",
				"sInfo":           " _TOTAL_ 件中 _START_ から _END_ まで表示",
				"searchPlaceholder": "キーワードを入力",
				"sInfoEmpty":      " 0 件中 0 から 0 まで表示",
				"sInfoFiltered":   "（全 _MAX_ 件より抽出）",
				"sInfoPostFix":    "",
				"sInfoThousands":  ",",
				"sLengthMenu":     "_MENU_ 件表示",
				"sLoadingRecords": "読み込み中...",
				"sProcessing":     "処理中...",
				"sSearch":         "",
				"sZeroRecords":    "一致するレコードがありません",
				"oPaginate": {
					"sFirst":    "先頭",
					"sLast":     "最終",
					"sNext":     "次へ",
					"sPrevious": "前へ"
				},
				"oAria": {
					"sSortAscending":  ": 列を昇順に並べ替えるにはアクティブにする",
					"sSortDescending": ": 列を降順に並べ替えるにはアクティブにする"
				}
			},
			drawCallback: function () {
				$(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').addClass('dropup');
			},
			preDrawCallback: function() {
				$(this).find('tbody tr').slice(-3).find('.dropdown, .btn-group').removeClass('dropup');
			}
		});
	}
}