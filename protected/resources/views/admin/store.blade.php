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
	@section('header-title', __('trans.store'))
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
				<h5 class="modal-title" v-if="!editStatus">@lang('trans.add_store')</h5>
				<h5 class="modal-title" v-if="editStatus">@lang('trans.edit_store')</h5>
			</div>

			<div class="modal-body">
				<div class="form-group">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.store_code')</label>
								<input type="text" class="form-control" name="store_code" :value="data.code">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.store_code_from_bs')</label>
								<input type="text" class="form-control" name="code_from_bs" :value="data.code_from_bs">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.store_name')</label>
								<input type="text" class="form-control" name="name" :value="data.name">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.email')</label>
								<input type="text" class="form-control" name="email" :value="data.email">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.contact')</label>
								<input type="text" class="form-control" name="contact" :value="data.contact">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.address')</label>
								<input type="text" class="form-control" name="address" :value="data.address">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.user_id')</label>
								<input type="text" class="form-control" name="user_id" :value="data.username">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.store_group')</label>
								<select name="store_group_code" class="form-control select3">
								@foreach (App\StoreGroup::all() as $group)
									<option :selected="data.store_group_code == '{{ $group->code }}'" value="{{ $group->code }}">{{ $group->name }}</option> 
								@endforeach
								</select>
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
<div id="modal-pass" class="modal fade">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form class="form-horizontal" :action="actionUrl" method="post" @submit="submitFormPass($event)" autocomplete="off">
			{{csrf_field()}}
			<input type="hidden" name="_method" value="PUT" v-if="editStatus">

			<div class="modal-header bg-slate-800">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title" v-if="editStatus">@lang('trans.edit_password')</h5>
			</div>

			<div class="modal-body">
				<div class="form-group">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-xs-12 mb-15">
								<label class="control-label">@lang('trans.new_password')</label>
								<input type="text" class="form-control" name="password">
								<input type="hidden" class="form-control" name="store_code" :value="data.code">
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
				<th class="text-center">@lang('trans.store_code')</th>
				{{-- <th class="text-center">@lang('trans.store_code_from_bs')</th> --}}
				<th class="text-center">@lang('trans.name')</th>
				<th class="text-center">@lang('trans.username')</th>
				<th class="text-center">@lang('trans.email')</th>
				<th class="text-center">@lang('trans.contact')</th>
				<th class="text-center">@lang('trans.address')</th>
				<th class="text-center">@lang('trans.change_password')</th>
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
	var actionUrl = URL+'data/store';
	var importUrl = URL+'excel/bs_stock';
	var actionBtn = true;
	var order = [[1]];

	var columns = [
		{render: function (index, row, data) {
			return '<input type="checkbox" value="'+data.id+'" class="checkRow" onclick="controller.checkRow(event)">'
		}, orderable: false, width: '20px', class: 'text-center'},
		{data: 'DT_Row_Index', width: '20px', class:'text-center', orderable: false, searchable: false},
		{data: 'code', class: 'text-center', orderable: false},
		// {data: 'code_from_bs', class: 'text-center', orderable: false},
		{data: 'name', class: 'text-left', orderable: false},
		{data: 'username', class: 'text-left', orderable: false},
		{data: 'email', class: 'text-left', orderable: false},
		{data: 'contact', class: 'text-center', orderable: false},
		{data: 'address', class: 'text-left', orderable: false},
		{render: function (index, row, data, meta) {
			return '<a href="" onclick="controller.editPass(event, '+meta.row+')"> @lang('trans.change_password') </a>'
		}, orderable: false, width: '20px', class: 'text-center'}
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