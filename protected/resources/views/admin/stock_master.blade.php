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
<script type="text/javascript" src="{{ asset('assets/js/plugins/notifications/sweet_alert.min.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@push('js')
<script type="text/javascript" src="{{ asset('js/mixin.'.auth()->user()->language.'.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@section('header')
	@parent
	@section('header-title', __('trans.stock_master'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">
<div id="modal-detail" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-slate-800">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title">@{{ data.jan_code }}</h5>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.code')</span>
						<hr class="mt-10 mb-10">
						@{{ data.code }}</span>
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.brand')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.brand }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.size')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.size }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.type')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.type }}
					</div>
				</div>
				<div class="row" style="margin-top:50px;">
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.section')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.section }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.series')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.series }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.rim')</span>
						<hr class="mt-10 mb-10">
						@{{ data.rim }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.basic_price')</span>
						<hr class="mt-10 mb-10"> 
						<p class="text-right no-margin">@{{ currency(data.price ? data.price : 0) }}</p>
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn bg-slate-800" data-dismiss="modal">@lang('trans.close')</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-form" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form class="form-horizontal" :action="actionUrl" method="post" @submit.prevent="submitForm($event)">
				{{csrf_field()}}
				<input type="hidden" name="_method" value="PUT" v-if="editStatus">

				<div class="modal-header bg-slate-800">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title" v-if="!editStatus">@lang('trans.add_stock_master')</h5>
					<h5 class="modal-title" v-if="editStatus">@lang('trans.edit')</h5>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<div class="col-lg-12">
							<div class="row">
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.jan_code')</label>
									<input type="text" class="form-control" name="jan_code" :value="data.jan_code" autocomplete="off">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.code')</label>
									<input type="text" class="form-control" name="code" :value="data.code" autocomplete="off">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.brand')</label>
									<input type="text" class="form-control" name="brand" :value="data.brand" autocomplete="off">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.version')</label>
									<input type="text" class="form-control" name="version" :value="data.version" autocomplete="off">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.size')</label>
									<input type="text" class="form-control" name="size" :value="data.size" autocomplete="off">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.basic_price')</label>
									<input type="text" class="form-control" name="price" :value="data.price" autocomplete="off">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.type')</label>
									<input type="text" class="form-control" name="type" :value="data.type" autocomplete="off">
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

<!-- /basic modal -->
<div class="panel panel-default">
	<table class="table table-bordered table-striped table-hover table-xxs datatables example" api-source="{{ url('apis/stock_master') }}">
		<thead>
			<tr>
				<th><i class="icon-check"></i></th>
				<th class="text-center">@lang('trans.no')</th>
				<th class="text-center">@lang('trans.jan_code')</th>
				<th class="text-center">@lang('trans.type')</th>
				<th class="text-center">@lang('trans.brand')</th>
				<th class="text-center">@lang('trans.version')</th>
				<th class="text-center">@lang('trans.size')</th>
				<th class="text-center">@lang('trans.price')</th>
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
	var actionUrl = URL+'data/stock_master';
	var importUrl = URL+'excel/stock_master';
	var actionBtn = true;
	var order = [[1]];
	var btns = [
		`<a href="`+importUrl+`" class="btn btn-default mr-5"><i class="icon-upload position-left"></i>`+translate('import')+`</a>`
	];
	
	var columns = [
		{render: function (index, row, data) {
			return '<input type="checkbox" value="'+data.id+'" class="checkRow" onclick="controller.checkRow(event)">'
		}, orderable: false, width: '20px', class: 'text-center'},
		{data: 'DT_Row_Index', orderable: false, searchable: false, width: '20px', class: 'text-center'},
		// {render: function (index, row, data, meta) {
		// 	return `<a href="#" onclick="controller.selectData(event, `+meta.row+`)">`+data.jan_code+`</a>`;
		// }, class: 'text-center', orderable: false},
		{data: 'jan_code', class: 'text-center'},
		{data: 'type', class: 'text-center'},
		{data: 'brand', class: 'text-center'},
		{data: 'version', class: 'text-center', orderable: false},
		{data: 'size', class: 'text-center', orderable: false},
		{render: function (index, row, data) {
			return currency(data.price);
		}, class: 'text-right', orderable: false},
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
@endpush