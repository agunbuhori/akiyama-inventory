@extends('layouts.admin')

@push('css')
<style type="text/css">
.dataTables_filter input {
    width: 300px;
 }
table.example tr:hover td {
 	background-color: #CAF1F6;
 	color: #000;
 }
</style>
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
	@section('header-title', __('trans.unregister'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">
<div id="modal-form" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form class="form-horizontal" action="{{ url('data/unregister') }}" method="post" @submit.prevent="submitForm($event)">
				{{csrf_field()}}
				<input type="hidden" name="_method" value="PUT" v-if="editStatus">

				<div class="modal-header bg-slate-800">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title" v-if="!editStatus">@lang('trans.add_stock_master')</h5>
					<h5 class="modal-title" v-if="editStatus">@lang('trans.add_jan_code')</h5>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<div class="col-lg-12">
							<div class="row">
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.jan_code')</label>
									<input type="text" class="form-control" :value="data.jan_code" autocomplete="off" disabled="">
									<input type="hidden" name="jan_code" :value="data.jan_code">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.type')</label>
									<input type="text" class="form-control" autocomplete="off" name="type">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.code')</label>
									<input type="text" class="form-control" name="code" autocomplete="off">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.brand')</label>
									<input type="text" class="form-control" name="brand" autocomplete="off" required="">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.size')</label>
									<input type="text" class="form-control" name="size" autocomplete="off" required="">
								</div>
								<div class="col-xs-6 mb-15">
									<div class="row">
										<div class="col-md-4">
											<label class="control-label">@lang('trans.section')</label>
										</div>
										<div class="col-md-4">
											<label class="control-label">@lang('trans.series')</label>
										</div>
										<div class="col-md-4">
											<label class="control-label">@lang('trans.rim')</label>
										</div>
									</div>
									<div class="row">
										<div class="col-md-4">
											<input type="text" class="form-control" name="section" autocomplete="off">
										</div>
										<div class="col-md-4">
											<input type="text" class="form-control" name="series" autocomplete="off">
										</div>
										<div class="col-md-4">
											<input type="text" class="form-control" name="rim" autocomplete="off">
										</div>
									</div>
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.basic_price')</label>
									<input type="text" class="form-control" name="price" autocomplete="off" required="">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.version')</label>
									<input type="text" class="form-control" name="version" autocomplete="off">
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<button type="submit" class="btn bg-primary">@lang('trans.save')</button>
				</div>
				
			</form>
		</div>
	</div>
</div>
<div class="row">
	<div class="col-md-6 col-md-offset-3">
		<div class="panel panel-default">
			<table class="table table-bordered table-striped table-hover table-xxs datatable-responsive datatables example">
				<thead>
					<tr>
						{{-- <th><i class="icon-check"></i></th> --}}
						<th class="text-center">@lang('trans.no')</th>
						<th class="text-center">@lang('trans.store')</th>
						<th class="text-center">@lang('trans.jan_code')</th>
						<th class="text-center">@lang('trans.amount')</th>
						<th class="text-center">@lang('trans.option')</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
</component>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript">
	var actionUrl = URL+'data/unregister';
	var importUrl = URL+'excel/bs_stock';
	var exportUrl = URL+'excel/unregister';
	var order = [[0]];
	var actionBtn = false;
	
	var btns = ['<a href="'+exportUrl+'" type="button" class="btn btn-default mr-5"><i class="icon-file-excel"></i></a>'];

	var columns = [
		{data: 'DT_Row_Index', width: '20px', class:'text-center', orderable: false, searchable: false},
		{data: 'store_name', class: 'text-left', searchable: false},
		{data: 'jan_code', class: 'text-center', orderable: false, searchable: true},
		{data: 'amounts', class: 'text-center', orderable: false, searchable: false},
		{render: function (index, row, data, meta) { 
			return `<a href="#" onclick="controller.editData(event, `+meta.row+`)">
						<i class="icon-pencil mr-10"></i>
					</a>
					<a href="{{ url('delete_unregister') }}`+`/`+data.jan_code+`" onclick="return confirm('削除?')">
						<i class="icon-trash text-danger mr-10"></i>
					</a>`;
		}, class: 'text-center', orderable: false},
	];

</script>
<script type="text/javascript" src="{{ asset('js/data.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/datatable.select2.js') }}"></script>
@endpush