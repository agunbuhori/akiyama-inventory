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
	@section('header-title', __('trans.translate'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">
<div id="modal-form" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form class="form-horizontal" :action="actionUrl" method="post" @submit="submitForm($event)" autocomplete="off">
			{{csrf_field()}}
			<input type="hidden" name="_method" value="PUT" v-if="editStatus">

			<div class="modal-header bg-slate-800">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title" v-if="!editStatus">@lang('trans.translate')</h5>
				<h5 class="modal-title" v-if="editStatus">@lang('trans.translate')</h5>
			</div>

			<div class="modal-body">
				<div class="form-group">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.key')</label>
								<input type="text" class="form-control" name="key" :value="data.key">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.english')</label>
								<input type="text" class="form-control" name="english" :value="data.english">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.indonesia')</label>
								<input type="text" class="form-control" name="indonesia" :value="data.indonesia">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.japan')</label>
								<input type="text" class="form-control" name="japanese" :value="data.japanese">
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
<div class="panel panel-default">
	<table class="table table-bordered table-striped table-hover table-xxs datatable-responsive datatables example">
		<thead>
			<tr>
				<th><i class="icon-check"></i></th>
				<th class="text-center">@lang('trans.no')</th>
				<th class="text-center">@lang('trans.key')</th>
				<th class="text-center">@lang('trans.english')</th>
				<th class="text-center">@lang('trans.indonesia')</th>
				<th class="text-center">@lang('trans.japan')</th>
				@if (auth()->user()->role === 1)
				<th class="text-center">@lang('trans.option')</th>
				@endif
			</tr>
		</thead>
	</table>
</div>
</component>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript">
	var actionUrl = URL+'data/translate';
	var importUrl = URL+'excel/bs_stock';
	var actionBtn = true;
	var order = [[1]];

	var columns = [
		{render: function (index, row, data) {
			return '<input type="checkbox" value="'+data.id+'" class="checkRow" onclick="controller.checkRow(event)">'
		}, orderable: false, width: '20px', class: 'text-center'},
		{data: 'DT_Row_Index', width: '20px', class:'text-center', orderable: false, searchable: false},
		{data: 'key', class: 'text-left', orderable: false},
		{data: 'english', class: 'text-left', orderable: false},
		{data: 'indonesia', class: 'text-left', orderable: false},
		{data: 'japanese', class: 'text-left', orderable: false},
	];

	// If authentocation is central
	if (isCentral())
		columns.push({render: function(index, row, data, meta) {
			return `<a href="#" onclick="controller.editData(event, `+meta.row+`)">
						<i class="icon-pencil mr-10"></i>
					</a>
					<a href="#" onclick="controller.deleteData(event, `+data.id+`)">
						<i class="icon-trash text-danger"></i>
					</a>`;
		}, class: 'text-center', orderable: false, width: '85px'});

</script>
<script type="text/javascript" src="{{ asset('js/data.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/datatable.select2.js') }}"></script>
@endpush