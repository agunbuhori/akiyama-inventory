var controller = new Vue({
	el: '#controller',
	mixins: [mixin],
	data: {
		datas: [],
		data: {},
		actionUrl: actionUrl,
		editStatus: false,
		table: null
	},
	mounted: function () {
		this.datatable();
	},
	methods: {
		datatable() {
			const _this = this;
			_this.table = $('.datatables').DataTable({
				order: order,
				scrollX: true,
				serverSide: true,
				processing: true,
				pageLength: 25,
				bScrollInfinite: true,
		        bScrollCollapse: true,
				scrollY: '500px',
				fixedHeader: {
				            header: true,
				            
				},
				responsive: {
				            details: {
				                type: 'column'
				            }
				        },
				ajax: {
					url: _this.actionUrl,
					type: 'get',
					beforeSend: function () {
						$('.panel').block({ 
						message: '<i class="icon-spinner4 spinner"></i>',
							timeout: 200,
							overlayCSS: {
								backgroundColor: '#fff',
								opacity: 0.8,
								cursor: 'wait'
							},
							css: {
								border: 0,
								padding: 0,
								backgroundColor: 'transparent'
							}
						});
					},
				},
				columns: columns
			}).on('xhr', function () {
				_this.datas = _this.table.ajax.json().data;
				$('.dataTables_length select').select2({
					minimumResultsForSearch: Infinity,
					width: 'auto'
				});
			});

			if (actionBtn)
				$('.dataTables_length').prepend(`
					<button type="button" class="btn btn-default mr-5" onclick="controller.addData(event)"><i class="icon-plus3"></i></button>
					<button type="button" class="btn btn-default btn-delete disabled mr-5" disabled="true" onclick="controller.deleteDatas()"><i class="icon-trash"></i></button>
				`);

			if (typeof btns != "undefined")
				for (var i = 0; i < btns.length; i++)
					$('.dataTables_length').prepend(btns[i]);

		},
		addData() {
			event.preventDefault();
			this.editStatus = false;
			this.data = {};
			$('#modal-form').modal('show');
		},
		submitForm(event) {
			event.preventDefault();
			const _this = this;
			var actionUrl = ! this.editStatus ? this.actionUrl : this.actionUrl+'/'+this.data.id;
			axios.post(actionUrl, new FormData($(event.target)[0])).then(response => {
				$('#modal-form').modal('hide');
				_this.table.ajax.reload();
			});
		},
		selectData(event, index) {
			event.preventDefault();
			$('#modal-detail').modal('show');
			this.data = this.datas[index];
		},
		editData(event, index) {
			this.editStatus = true;
			this.data = this.datas[index];

			event.preventDefault();
			$('#modal-form').modal('show');
		},
		editPass(event, index) {
			this.editStatus = true;
			this.data = this.datas[index];

			event.preventDefault();
			$('#modal-pass').modal('show');
		},
		submitFormPass(event) {
			event.preventDefault();
			const _this = this;
			var actionUrl = ! this.editStatus ? this.actionUrl : this.actionUrl+'/'+this.data.id;
			axios.post(actionUrl, new FormData($(event.target)[0])).then(response => {
				$('#modal-pass').modal('hide');
				_this.table.ajax.reload(null, false);
			});
		},
		deleteData(event, id) {
			event.preventDefault();
			
			if (confirm(translate("are_you_sure"))) {
				$(event.target).parents('tr').remove();
				axios.post(this.actionUrl+'/'+id, {_method: 'DELETE'}).then(response => {
					alert(translate('data_has_been_removed'));
				});
			}
		},
		checkRow(event) {
			if ($(event.target).is(':checked'))
				$(event.target).parents('tr').addClass('active');
			else
				$(event.target).parents('tr').removeClass('active');

			if ($('.checkRow:checked').length > 0)
				$('.btn-delete').removeClass('disabled').removeAttr('disabled');
			else
				$('.btn-delete').addClass('disabled').attr('disabled', 'true');
		},
		deleteDatas() {
			var values = [];
			const _this = this;	
			$.each($('.checkRow:checked'), function () {
				values.push($(this).val());
				$(this).parents('tr').remove();
			});

			if (values && confirm(translate("are_you_sure")))
				axios.post(this.actionUrl+'/'+values[0], {_method: 'DELETE', datas: values}).then(response => {
					alert(translate('data_has_been_removed'));
				});
		},
		formatPrice(value) {
	        let val = (value/1).toFixed(0).replace(',', ',')
	        return val.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",")
		}
	}
});