@extends('layouts.admin')

@push('css')
@endpush
{{-- ================================================================================================================================= --}}
@push('sec-js')
<script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/moment/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/daterangepicker.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/anytime.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/pickadate/picker.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/pickadate/picker.date.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/pickadate/picker.time.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/pickers/pickadate/legacy.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@push('js')
<script type="text/javascript" src="{{ asset('js/mixin.'.auth()->user()->language.'.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@section('header')
	@parent
	@section('header-title', __('trans.shift'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">
<div id="modal-form" class="modal fade">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			
			<div class="modal-header bg-slate-800">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title" v-if="editStatus">@lang('trans.update_status')</h5>
			</div>

			<div class="modal-body">
				<div class="form-group">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.to_store')</span>
							</div>
							<div class="col-md-8 mb-10">
								@{{ data.to_store }}
							</div>
							<hr width="95%"> 
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.brand')</span>
							</div>
							<div class="col-md-8 mb-10">
								@{{ data.brand }}
							</div>
							<hr width="95%"> 
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.type')</span>
							</div>
							<div class="col-md-8 mb-10">
								@{{ data.type }}
							</div>
							<hr width="95%"> 
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.size')</span>
							</div>
							<div class="col-md-8 mb-10">
								@{{ data.size }}
							</div>
							<hr width="95%">
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.amount')</span>
							</div>
							<div class="col-md-8 mb-10">
								@{{ data.amount }}
							</div>
							<hr width="95%">
						</div>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<form class="col-md-6 form-inline" :action="actionUrl" method="post" @submit="submitForm($event)">
				{{csrf_field()}}
					<input type="hidden" name="_method" value="PUT" v-if="editStatus">
					<input type="hidden" name="id" :value="data.id">
					<input type="hidden" name="status" value="fail">
					<button type="submit" class="btn bg-danger pull-left">@lang('trans.fail')</button>
				</form>
				<form class="col-md-6 form-inline" :action="actionUrl" method="post" @submit="submitForm($event)">
				{{csrf_field()}}
					<input type="hidden" name="_method" value="PUT" v-if="editStatus">
					<input type="hidden" name="id" :value="data.id">
					<input type="hidden" name="jan_code" :value="data.jan_code">
					<input type="hidden" name="code" :value="data.code">
					<input type="hidden" name="amount" :value="data.amount">
					<input type="hidden" name="status" value="done">
					<button type="submit" class="btn bg-primary">@lang('trans.done')</button>
				</form>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<table class="table table-bordered table-striped table-hover table-xxs datatable-responsive datatables">
		<thead>
			<tr>
				<th><i class="icon-check"></i></th>
				<th class="text-center">@lang('trans.no')</th>
				<th class="text-center">@lang('trans.receipt_date')</th>
				<th class="text-center">@lang('trans.to_store')</th>
				<th class="text-center">@lang('trans.brand')</th>
				<th class="text-center">@lang('trans.size')</th>
				<th class="text-center">@lang('trans.type')</th>
				<th class="text-center">@lang('trans.amount')</th>
				<th class="text-center">@lang('trans.status')</th>
			</tr>
		</thead>
	</table>
</div>
</div>

</component>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript">
	var actionUrl = URL+'data/shift_dipinjam';
	var importUrl = URL+'excel/bs_stock';
	var actionBtn = false;
	var order = [[1]];
	
	var columns = [
		{render: function (index, row, data) {
			return '<input type="checkbox" value="'+data.id+'" class="checkRow" onclick="controller.checkRow(event)">'
		}, orderable: false, width: '20px', class: 'text-center'},
		{data: 'DT_Row_Index', width: '20px', class:'text-center', orderable: false},
		{render: function (index, row, data){
			return moment(data.created_at).format(dateFormat)
		}, class: 'text-center', width: '130px'},
		{data: 'from_store', class: 'text-left', orderable: false},
		{data: 'brand', class: 'text-center', orderable: false},
		{data: 'size', class: 'text-center', orderable: false},
		{data: 'type', class: 'text-center', orderable: false},
		{data: 'amount', class: 'text-center'},
		{render: function(index, row, data, meta) {
			if (data.status == 'done')
				return '<i class="icon-check"></i>';
			else if(data.status == 'fail')
				return '<span class="label bg-danger">'+translate(data.status)+'</span>';
			else
				return `<a href="#" onclick="controller.editData(event, `+meta.row+`)">
							`+translate('confirmation')+`
						</a>`;
		}, class: 'text-center', orderable: false}
	];

</script>
<script type="text/javascript" src="{{ asset('js/data.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/datatable.select2.js') }}"></script>
<script type="text/javascript"></script>
@endpush