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
	@section('header-title', __('trans.companies'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">
<div id="modal-form" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form class="form-horizontal" :action="actionUrl" method="post" @submit="submitForm($event)">
			{{csrf_field()}}
			<input type="hidden" name="_method" value="PUT" v-if="editStatus">

			<div class="modal-header bg-slate-800">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title" v-if="!editStatus">@lang('trans.add_company')</h5>
				<h5 class="modal-title" v-if="editStatus">@lang('trans.edit_company')</h5>
			</div>

			<div class="modal-body">
				<div class="form-group">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.company_code')</label>
								<input type="text" class="form-control" name="code" :value="data.code">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.company_name')</label>
								<input type="text" class="form-control" name="name" :value="data.name">
							</div>
							<div class="col-xs-6 mb-15">
								<label class="control-label">@lang('trans.contact_name')</label>
								<input type="text" class="form-control" name="contact_name" :value="data.contact_name">
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
	<table class="table table-bordered table-striped table-hover table-xxs datatable-responsive datatables">
		<thead>
			<tr>
				<th><i class="icon-check"></i></th>
				<th class="text-center">@lang('trans.no')</th>
				<th class="text-center">@lang('trans.company_name')</th>
				<th class="text-center">@lang('trans.contact_name')</th>
				<th class="text-center">@lang('trans.email')</th>
				<th class="text-center">@lang('trans.contact')</th>
				<th class="text-center">@lang('trans.address')</th>
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
	var actionUrl = URL+'data/company';
	var importUrl = URL+'excel/bs_stock';
	var actionBtn = true;
	
	var columns = [
		{render: function (index, row, data) {
			return '<input type="checkbox" value="'+data.id+'" class="checkRow" onclick="controller.checkRow(event)">'
		}, orderable: false, width: '20px', class: 'text-center'},
		{data: 'DT_Row_Index', width: '20px', class:'text-center', orderable: false, searchable: false},
		{data: 'name', class: 'text-left', orderable: false},
		{data: 'contact_name', class: 'text-center', orderable: false},
		{data: 'email', class: 'text-center'},
		{data: 'contact', class: 'text-center'},
		{data: 'address', class: 'text-center'},
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