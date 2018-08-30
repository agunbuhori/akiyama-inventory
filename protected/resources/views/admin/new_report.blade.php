@extends('layouts.admin')

@push('css')
<style type="text/css">
.dataTables_filter input {
    width: 300px;
 }
 table.example tr:hover td {
 	background-color: #CAF1F6;
 	color: #000;
 	/*font-weight: bold;*/
 }
</style>
@endpush
{{-- ================================================================================================================================= --}}
@push('sec-js')
<script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@push('js')
<script type="text/javascript" src="{{ asset('js/mixin.'.auth()->user()->language.'.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@section('header')
	@parent
	@section('header-title', __('trans.report_per_store'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">

<div class="row mb-20">
 	<div class="col-md-3">
		<table class="table table-bordered table-striped table-xxs">
			<thead>
				<tr>
					<th colspan="2" class="text-center bg-primary-600"> @lang('trans.last_month') </th>
				</tr>
			</thead>
			<tbody>
				<tr style="background-color: #fff;">
					<td> @lang('trans.total_amount')</td>
					<td id="total_last_month" class="text-right"></td>
				</tr>
				<tr style="background-color: #fff;">
					<td> @lang('trans.total_price')</td>
					<td id="price_last_month" class="text-right"></td>
				</tr>
			</tbody>
		</table>
 	</div>	
 	<div class="col-md-3">
		<table class="table table-bordered table-striped table-xxs">
			<thead>
				<tr>
					<th colspan="2" class="text-center bg-primary-600"> @lang('trans.in_this_month') </th>
				</tr>
			</thead>
			<tbody>
				<tr style="background-color: #fff;">
					<td> @lang('trans.total_amount')</td>
					<td id="in_this_month" class="text-right"></td>
				</tr>
				<tr style="background-color: #fff;">
					<td> @lang('trans.total_price')</td>
					<td id="price_in_this_month" class="text-right"></td>
				</tr>
			</tbody>
		</table>
 	</div>	
  	<div class="col-md-3">
 		<table class="table table-bordered table-striped table-xxs">
 			<thead>
 				<tr>
 					<th colspan="2" class="text-center bg-primary-600"> @lang('trans.out_this_month') </th>
 				</tr>
 			</thead>
 			<tbody>
 				<tr style="background-color: #fff;">
 					<td> @lang('trans.total_amount')</td>
 					<td id="out_this_month" class="text-right"></td>
 				</tr>
 				<tr style="background-color: #fff;">
 					<td> @lang('trans.total_price')</td>
 					<td id="price_out_this_month" class="text-right"></td>
 				</tr>
 			</tbody>
 		</table>
  	</div>	
   	<div class="col-md-3">
  		<table class="table table-bordered table-striped table-xxs">
  			<thead>
  				<tr>
  					<th colspan="2" class="text-center bg-primary-600"> @lang('trans.total_this_month') </th>
  				</tr>
  			</thead>
  			<tbody>
  				<tr style="background-color: #fff;">
  					<td> @lang('trans.total_amount')</td>
  					<td id="total_this_month" class="text-right"></td>
  				</tr>
  				<tr style="background-color: #fff;">
  					<td> @lang('trans.total_price')</td>
  					<td id="price_this_month" class="text-right"></td>
  				</tr>
  			</tbody>
  		</table>
   	</div>	
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<div class="row">
			<form action="{{ url('excel/export_report') }}" method="POST">
				{{ csrf_field() }}
				<div class="col-md-6 col-sm-12">
					<select class="form-control select-store" name="store">
						@foreach (App\Store::all() as $store)
						<option value="{{ $store->code }}">{{ $store->code }} {{ $store->name }}</option>
						@endforeach
					</select>
					<div class="btn-group">
						<button type="button" class="btn bg-slate-800 btn-type btn-xs" typee="all">@lang('trans.all_type')</button>
						<button type="button" class="btn btn-default btn-type btn-xs" typee="tire">@lang('trans.ban')</button>
						<button type="button" class="btn btn-default btn-type btn-xs" typee="battery">@lang('trans.battery')</button>
						<button type="button" class="btn btn-default btn-type btn-xs" typee="velg">@lang('trans.velg')</button>
						<button type="button" class="btn btn-default btn-type btn-xs" typee="oli">@lang('trans.oli')</button>
					</div>
				</div>
				<div class="col-md-6 col-sm-12 text-right form-inline">
					<button type="submit" class="btn btn-default"><i class="icon icon-file-excel"></i></button>

					<select class="form-control select2" name="filter">
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
			</form>
		</div>
	</div>
	<table class="table table-bordered table-striped table-hover table-xxs datatable-responsive datatables example">
		<thead>
			<tr>
				<th class="text-center">@lang('trans.no')</th>
				<th class="text-center">@lang('trans.date')</th>
				<th class="text-center">@lang('trans.type')</th>
				<th class="text-center">@lang('trans.brand')</th>
				<th class="text-center">@lang('trans.size')</th>
				<th class="text-center">@lang('trans.stock_last_month')</th>
				<th class="text-center">@lang('trans.stock_in')</th>
				<th class="text-center">@lang('trans.barang_titip')</th>
				{{-- <th class="text-center">@lang('trans.stock_in')</th> --}}
				<th class="text-center">@lang('trans.basic_price')</th>
				<th class="text-center">@lang('trans.stock_out')</th>
				<th class="text-center">@lang('trans.sell_price')</th>
				<th class="text-center">@lang('trans.stock_this_month')</th>
				<th class="text-center">@lang('trans.close_book')</th>
				<th class="text-center">@lang('trans.total_price_this_month')</th>
			</tr>
		</thead>
	</table>
</div>
</component>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript">
	var filter = $('select[name=filter]').val();
	var date = $('select[name=year]').val()+'-'+$('select[name=month]').val()+'-'+$('select[name=day]').val();
	var store = $('select[name=store]').val();

	var actionUrl = URL+'new_report_api';
	var importUrl = URL+'excel/bs_stock';
	var exportUrl = URL+'excel/export_report';
	var actionBtn = false;

	// var btns = ['<a href="'+exportUrl+'" type="button" class="btn btn-default mr-5"><i class="icon-file-excel"></i></a>'];
	var order = [[1, "desc"]];
	
	var columns = [
		{orderable: false, data: 'DT_Row_Index', class: 'text-center', searchable: false},
		{orderable: false, data: 'receipt_date', class: 'text-center', orderable: true},
		{orderable: false, data: 'type', class: 'text-center', orderable: true},
		{orderable: false, data: 'brand', orderable: true},
		{orderable: false, data: 'size', class: 'text-center'},
		{orderable: false, data: 'stock_last_month', class: 'text-center'},
		{orderable: false, data: 'stock_in_this_month', class: 'text-center'},
		{orderable: false, data: 'barang_titip_this_month', class: 'text-center'},
		// {orderable: false, data: 'stock_in_store', orderable: false, class: 'text-center'},
		{render: function (index, row, data) { 
			return currency(data.basic_price);
		}, class: 'text-right', orderable: false},
		{orderable: false, data: 'stock_out_this_month', orderable: false, class: 'text-center'},
		{render: function (index, row, data) {
			return currency(data.sell_price);
		}, class: 'text-right', orderable: false},
		{orderable: false, data: 'stock_this_month', orderable: false, class: 'text-center'},
		{render: function (index, row, data) {
			return 0;
		}, class: 'text-center', orderable: false},
		{render: function (index, row, data) {
			return currency(data.total_price_this_month);
		}, class: 'text-right', orderable: false},
	];


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

		$('.select3').select2({
			minimumResultsForSearch: Infinity
		});

		$('.select-nosearch').select2({
			width: 'auto',
			minimumResultsForSearch: Infinity
		});

		$('.select-store').select2({
			width: '300px'
		});

		$('select[name=day]').next('.select2-container').hide();

		$('.btn-type').on('click', function () {
			type = $(this).attr('typee');
			$('.btn-type.bg-slate-800').removeClass('bg-slate-800').addClass('btn-default').removeAttr('disabled');
			$(this).removeClass('btn-default').addClass('bg-slate-800');
			controller.table.ajax.url(URL+'new_report_api?store='+store+'&filter='+filter+'&date='+date+'&type='+type).load();
		});

		$('select[name=store]').on('change', function () {
			store = $(this).val();
			controller.table.ajax.url(URL+'new_report_api?store='+store+'&filter='+filter+'&date='+date).load();
		});

		$('select[name=filter]').on('change', function () {
			filter = $(this).val();
			controller.table.ajax.url(URL+'new_report_api?store='+store+'&filter='+filter+'&date='+date).load();

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
			controller.table.ajax.url(URL+'new_report_api?store='+store+'&filter='+filter+'&date='+date).load();
		});

		controller.table.on('xhr', function() {
			$('#total_last_month').html(controller.table.ajax.json().total_last_month);
			$('#total_this_month').html(controller.table.ajax.json().total_this_month);
			$('#price_last_month').html(currency(controller.table.ajax.json().price_last_month));
			$('#price_this_month').html(currency(controller.table.ajax.json().price_this_month));
			$('#in_this_month').html(controller.table.ajax.json().in_this_month);
			$('#out_this_month').html(controller.table.ajax.json().out_this_month);
			$('#price_out_this_month').html(currency(controller.table.ajax.json().price_out_this_month));
			$('#price_in_this_month').html(currency(controller.table.ajax.json().price_in_this_month));
		});
	});

</script>
@endpush