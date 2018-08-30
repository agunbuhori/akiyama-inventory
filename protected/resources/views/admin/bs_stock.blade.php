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
@section('header-title', __('trans.bs_stock'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">
	<div id="modal-detail" class="modal fade">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header bg-slate-800">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title">@{{ data.name }} : @{{ data.receipt_number }}</h5>
				</div>

				<div class="modal-body">
					<div class="row">
						<div class="col-md-3">
							<span class="text-muted">@lang('trans.receipt_date')</span>
							<hr class="mt-10 mb-10">
						@{{ moment(data.receipt_date).format(dateFormat) }}</span>
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.version')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.version }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.stock_code')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.stock_code }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.jan_code')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.jan_code }}
					</div>
				</div>
				<div class="row" style="margin-top:50px;">
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.stock_name')</span>
						<hr class="mt-10 mb-10">
						@{{ data.stock_name }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.amount')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.amount }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.sell_price')</span>
						<hr class="mt-10 mb-10"> 
						<p class="text-right no-margin">¥@{{ formatPrice(data.sell_price) }}</p>
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.basic_price')</span>
						<hr class="mt-10 mb-10"> 
						<p class="text-right no-margin">¥@{{ formatPrice(data.basic_price) }}</p>
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
			<form class="form-horizontal" :action="actionUrl" method="post" @submit="submitForm($event)">
				{{csrf_field()}}
				<input type="hidden" name="_method" value="PUT" v-if="editStatus">

				<div class="modal-header bg-slate-800">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title" v-if="!editStatus">@lang('trans.add_bs_stock')</h5>
					<h5 class="modal-title" v-if="editStatus">@lang('trans.edit_bs_stock')</h5>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<div class="col-lg-12">
							<div class="row">
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.company')</label>
									<input type="text" class="form-control" name="company_code" :value="data.company_code">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.receipt_date')</label>
									<input type="text" class="form-control pickadate-accessibility" name="receipt_date" style="background-color: #fff;" required="required" :value="data.receipt_date">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.receipt_number')</label>
									<input type="text" class="form-control" name="receipt_number" :value="data.receipt_number">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.stock_code')</label>
									<input type="text" class="form-control" name="stock_code" :value="data.stock_code">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.jan_code')</label>
									<input type="text" class="form-control" name="jan_code" :value="data.jan_code">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.stock_name')</label>
									<input type="text" class="form-control" name="stock_name" :value="data.stock_name">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.amount')</label>
									<input type="text" class="form-control" name="amount" :value="data.amount">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.sell_price')</label>
									<input type="text" class="form-control" name="sell_price" :value="data.sell_price">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.basic_price')</label>
									<input type="text" class="form-control" name="basic_price" :value="data.basic_price">
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

<div class="row" style="margin-top: -20px; margin-bottom: 10px;">
	<div class="col-md-12 text-left" id="span"> 
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-6">
				<div class="btn-group">
					<button type="button" class="btn bg-slate-800 btn-additional btn-xs" additional="0"><i class="icon-stack3 position-left"></i>@lang('trans.filter_reguler_stock')</button>
					<button type="button" class="btn btn-default btn-additional btn-xs" additional="1"><i class="icon-stack2 position-left"></i>@lang('trans.filter_additional_stock')</button>
					<a href="{{ url('excel/bs_stock') }}" class="btn btn-xs btn-default ml-10"><i class="icon-upload position-left"></i>@lang('trans.import')</a>
				</div>
			</div>

			<div class="col-md-6 form-inline text-right">
				<select class="form-control select2" name="filter">
					<option value="per_year" data-icon="calendar3">@lang('trans.per_year')</option>
					<option value="per_month" selected="selected" data-icon="calendar2">@lang('trans.per_month')</option>
					<option value="per_day" data-icon="calendar52">@lang('trans.per_day')</option>
				</select>

				<select class="select-nosearch" name="year">
					@foreach (range(2016, date('Y')+1) as $year)
					<option {!! $year == date('Y') ? 'selected' : '' !!} value="{{ $year }}">{{ date_format2($year, 'Y', 'jp') }}</option>
					@endforeach
				</select>

				<select class="select-nosearch" name="month">
					@foreach (range(1, 12) as $month)
					<option {!! $month == date('m') ? 'selected' : '' !!} value="{{ $month }}">{{ date_format2($month, 'm', 'jp') }}</option>
					@endforeach
				</select>

				<select class="select-nosearch" name="day">
					@foreach (range(1, 31) as $day)
					<option {!! $day == date('d') ? 'selected' : '' !!} value="{{ $day }}">{{ date_format2($day, 'd', 'jp') }}</option>
					@endforeach
				</select>
			</div>
		</div>
	</div>

	<table class="table table-bordered table-striped table-hover table-xxs datatable-responsive datatables example">
		<thead>
			<tr>
				<th><i class="icon-check"></i></th>
				<th class="text-center">@lang('trans.no')</th>
				<th class="text-center">@lang('trans.receipt_date')</th>
				<th class="text-center">@lang('trans.jan_code')</th>
				<th class="text-center">@lang('trans.receipt_number')</th>
				<th class="text-center">@lang('trans.stock_name')</th>
				<th class="text-center">@lang('trans.amount')</th>
				<th class="text-center">@lang('trans.basic_price')</th>
				<th class="text-center">@lang('trans.total_sales')</th>
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

	var additional = $('.btn-additional.bg-slate-800').attr('additional');
	var filter = $('select[name=filter]').val();
	var date = $('select[name=year]').val()+'-'+$('select[name=month]').val()+'-'+$('select[name=day]').val();

	var actionUrl = URL+'data/bs_stock';
	// var importUrl = URL+'excel/bs_stock';
	var actionBtn = true;
	var order = [[2, "asc"]];
	// var loop = 0;
	
	// var btns = [
	// `<a href="`+importUrl+`" class="btn btn-default mr-5"><i class="icon-upload"></i></a>`
	// ];
	
	var columns = [
		{render: function (index, row, data) {
			return '<input type="checkbox" value="'+data.id+'" class="checkRow" onclick="controller.checkRow(event)">'
		}, orderable: false, width: '20px', class: 'text-center'},
		{data: 'DT_Row_Index', width: '20px', class:'text-center', orderable: false, searchable: false},
		{render: function (index, row, data){
			return moment(data.receipt_date).format(dateFormat)
		}, class: 'text-center'},
		{data: 'jan_code', class: 'text-center', orderable: false, searchable: true},
		{render: function (index, row, data, meta) {
			return `<a href="#" onclick="controller.selectData(event, `+meta.row+`)">`+data.receipt_number+`</a>`;
		}, class: 'text-center', orderable: false},
		{data: 'size', class: 'text-center', orderable: false, searchable: false},
		{data: 'amount', class: 'text-center', orderable: false, searchable: false},
		{render: function (index, row, data) {
			return currency(data.basic_price);
		}, class: 'text-right', orderable: false},
		{render: function (index, row, data) {
			return currency(data.sell_price);
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
	<script type="text/javascript">
		$(function () {
			function iconFormat(icon) {
				var originalOption = icon.element;
				if (!icon.id) { return icon.text; }
				var $icon = "<i class='icon-" + $(icon.element).data('icon') + "'></i>" + icon.text;

				return $icon;
			}

			$('.select2').select2({
				width: 'auto',
				templateResult: iconFormat,
				minimumResultsForSearch: Infinity,
				templateSelection: iconFormat,
				escapeMarkup: function(m) { return m; }
			});

			$('.select-nosearch').select2({
				width: 'auto',
				minimumResultsForSearch: Infinity
			});

			$('.select-store').select2({
				width: '300px'
			});

			$('.btn-additional').on('click', function () {
				additional = $(this).attr('additional');
				$('.btn-additional.bg-slate-800').removeClass('bg-slate-800').addClass('btn-default').removeAttr('disabled');
				$(this).removeClass('btn-default').addClass('bg-slate-800').attr('disabled', 'disabled');

				controller.table.ajax.url(URL+'data/bs_stock?filter='+filter+'&date='+date+'&additional='+additional).load();
			});

			$('select[name=day]').next('.select2-container').hide();

			$('select[name=filter]').on('change', function () {
				filter = $(this).val();
				controller.table.ajax.url(URL+'data/bs_stock?filter='+filter+'&date='+date+'&additional='+additional).load();

				switch (filter) {
					case 'per_day':
					$('select[name=month]').next('.select2-container').show();
					$('select[name=day]').next('.select2-container').show();
					break;
					case 'per_month':
					$('select[name=month]').next('.select2-container').show();
					$('select[name=day]').next('.select2-container').hide();
					break;
					case 'per_year':
					$('select[name=month]').next('.select2-container').hide();
					$('select[name=day]').next('.select2-container').hide();
					break;
				}
			});

			$('.select-nosearch').on('change', function() {
				date = $('select[name=year]').val()+'-'+$('select[name=month]').val()+'-'+$('select[name=day]').val();
				controller.table.ajax.url(URL+'data/bs_stock?filter='+filter+'&date='+date+'&additional='+additional).load();
			});

			controller.table.on('xhr', function() {
				$('#span').empty();
				loop = controller.table.ajax.json().count;
				dates = controller.table.ajax.json().dates;

				for (i = 0; i < loop; i++) {
				    $('#span').append("<span class='label ml-5 bg-primary-800' style='padding: 5px;' id='text'>"+dates[i]+"</span>");
				}
				for (i = 0; i < 5 - loop; i++) {
				    $('#span').append("<span class='label ml-5 bg-grey-300' style='padding: 5px;' id='text'>00</span>");
				}
			});

		});
	</script>
	@endpush