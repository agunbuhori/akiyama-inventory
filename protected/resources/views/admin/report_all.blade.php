@extends('layouts.admin')

@push('css')
<style type="text/css">
.dataTables_paginate {
    float: right;
    text-align: right;
    margin: 20px -40px 0px 0px;
}
table tr.store:hover td {
	background-color: #CAF1F6;
	color: #000;
	/*font-weight: bold;*/
}
table tr.group:hover td {
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
{{-- <span>@{{store.code}}</span> --}}
@php
	$this_month = request()->has('month') ? request()->month : date('m');
	$last_month = $this_month - 1;

	if (request()->type == 'oli') {
	    $type = ['オイル'];
	} elseif (request()->type == 'battery') {
	    $type = ['バッテリー'];
	} elseif (request()->type == 'velg') {
	    $type = ['ホイール'];
	} elseif (request()->type == 'tire')  {
	    $type = ['タイヤ'];
	} else {
	    $type = ['オイル', 'バッテリー', 'ホイール', 'タイヤ'];
	}
@endphp

@foreach (App\Store::all() as $store)
<div id="last{{ $store->code }}" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title">@lang('trans.stock_last_month') @{{store.name}}</h5>
			</div>
			<div class="modal-body">
			 	<div class="col-md-12">
					<table class="table table-bordered table-striped table-xxs display">
						<thead>
							<tr>
								<th class="text-center"> @lang('trans.brand') </th>
								<th class="text-center"> @lang('trans.size') </th>
								<th class="text-center"> @lang('trans.amount') </th>
							</tr>
						</thead>
						@php
						    $datas = DB::table('stocks AS s')
						                ->select('s.jan_code', 's.type', 'stock_masters.size', 'stock_masters.brand')
						                ->addSelect(DB::raw("(SELECT SUM(amount) FROM stocks as i where s.jan_code = i.jan_code AND i.arrow = 'in' AND i.store_code = '".$store->code."' AND month(i.stock_datetime) <= '".$last_month."') AS stock_in") )
						                ->addSelect(DB::raw("(SELECT SUM(amount) FROM stocks as o where s.jan_code = o.jan_code AND o.arrow = 'out' AND o.store_code = '".$store->code."' AND month(o.stock_datetime) <= '".$last_month."') AS stock_out") )
						                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 's.jan_code')
						                ->whereMonth('s.stock_datetime', '<=', $last_month)
						                ->where('s.store_code', $store->code)
						                ->whereIn('s.type', $type)
						                ->groupBy('s.jan_code')
						                ->get();
						@endphp
						<tbody>
							@foreach ($datas as $data)
							<tr>
								<td>{{ $data->brand }}</td>
								<td>{{ $data->size }}</td>
								<td class="text-center">{{ $data->stock_in-$data->stock_out }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
			 	</div>	
			</div>
			<div class="modal-footer" style="margin-bottom: 15px;">
			</div>
		</div>
	</div>
</div>
<div id="in_this{{ $store->code }}" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title">@lang('trans.stock_in_this_month') @{{store.name}}</h5>
			</div>
			<div class="modal-body">
			 	<div class="col-md-12">
					<table class="table table-bordered table-striped table-xxs display">
						<thead>
							<tr>
								<th class="text-center"> @lang('trans.brand') </th>
								<th class="text-center"> @lang('trans.size') </th>
								<th class="text-center"> @lang('trans.amount') </th>
							</tr>
						</thead>
						@php
						    $datas = App\Stock::select('stock_masters.brand', 'stock_masters.size', DB::raw("SUM(stocks.amount) AS amount"))
						                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
						                ->whereMonth('stock_datetime', $this_month)
						                ->where('store_code', $store->code)
						                ->where('arrow', 'in')
						                ->whereIn('stocks.type', $type)
						                ->groupBy('stocks.jan_code')
						                ->get();

						@endphp
						<tbody>
							@foreach ($datas as $data)
							<tr>
								<td>{{ $data->brand }}</td>
								<td>{{ $data->size }}</td>
								<td class="text-center">{{ $data->amount }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
			 	</div>	
			</div>
			<div class="modal-footer" style="margin-bottom: 15px;">
			</div>
		</div>
	</div>
</div>
<div id="out_this{{ $store->code }}" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title">@lang('trans.stock_out_this_month')</h5>
			</div>
			<div class="modal-body">
			 	<div class="col-md-12">
					<table class="table table-bordered table-striped table-xxs display">
						<thead>
							<tr>
								<th class="text-center"> @lang('trans.brand') </th>
								<th class="text-center"> @lang('trans.size') </th>
								<th class="text-center"> @lang('trans.amount') </th>
							</tr>
						</thead>
						@php
							$datas = App\Stock::select('stock_masters.brand', 'stock_masters.size', DB::raw("SUM(stocks.amount) AS amount"))
						                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'stocks.jan_code')
						                ->whereMonth('stock_datetime', $this_month)
						                ->where('store_code', $store->code)
						                ->where('arrow', 'out')
						                ->whereIn('stocks.type', $type)
						                ->groupBy('stocks.jan_code')
						                ->get();

						@endphp
						<tbody>
							@foreach ($datas as $data)
							<tr>
								<td>{{ $data->brand }}</td>
								<td>{{ $data->size }}</td>
								<td class="text-center">{{ $data->amount }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
			 	</div>	
			</div>
			<div class="modal-footer" style="margin-bottom: 15px;">
			</div>
		</div>
	</div>
</div>
<div id="additional{{ $store->code }}" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title">@lang('trans.barang_titip')</h5>
			</div>
			<div class="modal-body">
			 	<div class="col-md-12">
					<table class="table table-bordered table-striped table-xxs display">
						<thead>
							<tr>
								<th class="text-center"> @lang('trans.brand') </th>
								<th class="text-center"> @lang('trans.size') </th>
								<th class="text-center"> @lang('trans.amount') </th>
							</tr>
						</thead>
						@php
						    $datas = App\BsStock::select(DB::raw("SUM(bs_stocks.amount) AS amount"), 'stock_masters.brand', 'stock_masters.size')
                                                ->leftJoin('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
                                                ->whereMonth('receipt_date', $this_month)
                                                ->where('client_code', $store->code_from_bs)
                                                ->groupBy('bs_stocks.jan_code')
                                                ->where('titip', '1')
                                                ->get();

						@endphp
						<tbody>
							@foreach ($datas as $data)
							<tr>
								<td>{{ $data->brand }}</td>
								<td>{{ $data->size }}</td>
								<td class="text-center">{{ $data->amount }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
			 	</div>	
			</div>
			<div class="modal-footer" style="margin-bottom: 15px;">
			</div>
		</div>
	</div>
</div>
@endforeach

<div class="row mb-20">
 	<div class="col-md-3">
		<table class="table table-bordered table-striped table-xxs">
			<thead>
				<tr class="bg-primary-600">
					<th colspan="2" class="text-center"> @lang('trans.last_month') </th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="background-color: #fff;"> @lang('trans.total_amount')</td>
					<td id="stock_bulan_lalu" class="text-right" style="background-color: #fff;"></td>
				</tr>
				<tr>
					<td style="background-color: #fff;"> @lang('trans.total_price')</td>
					<td id="harga_bulan_lalu" class="text-right" style="background-color: #fff;"></td>
				</tr>
			</tbody>
		</table>
 	</div>	
 	<div class="col-md-3">
		<table class="table table-bordered table-striped table-xxs">
			<thead>
				<tr class="bg-primary-600">
					<th colspan="2" class="text-center"> @lang('trans.in_this_month') </th>
				</tr>
			</thead>
			<tbody>
				<tr style="background-color: #fff;">
					<td > @lang('trans.total_amount')</td>
					<td id="stock_in_bulan_ini" class="text-right"></td>
				</tr>
				<tr style="background-color: #fff;">
					<td> @lang('trans.total_price')</td>
					<td id="harga_in_bulan_ini" class="text-right"></td>
				</tr>
			</tbody>
		</table>
 	</div>	
  	<div class="col-md-3">
 		<table class="table table-bordered table-striped table-xxs">
 			<thead>
 				<tr class="bg-primary-600">
 					<th colspan="2" class="text-center"> @lang('trans.out_this_month') </th>
 				</tr>
 			</thead>
 			<tbody>
 				<tr style="background-color: #fff;">
 					<td> @lang('trans.total_amount')</td>
 					<td id="stock_out_bulan_ini" class="text-right"></td>
 				</tr>
 				<tr style="background-color: #fff;">
 					<td> @lang('trans.total_price')</td>
 					<td id="harga_out_bulan_ini" class="text-right"></td>
 				</tr>
 			</tbody>
 		</table>
  	</div>	
   	<div class="col-md-3">
  		<table class="table table-bordered table-striped table-xxs">
  			<thead>
  				<tr class="bg-primary-600">
  					<th colspan="2" class="text-center"> @lang('trans.total_this_month') </th>
  				</tr>
  			</thead>
  			<tbody>
  				<tr style="background-color: #fff;">
  					<td> @lang('trans.total_amount')</td>
  					<td id="stock_bulan_ini" class="text-right"></td>
  				</tr>
  				<tr style="background-color: #fff;">
  					<td> @lang('trans.total_price')</td>
  					<td id="harga_bulan_ini" class="text-right"></td>
  				</tr>
  			</tbody>
  		</table>
   	</div>	
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<div class="row">
			<div class="col-md-6 col-sm-12">
				<div class="btn-group">
					<button {!! request()->type =='all' || request()->type == '' ? 'class="btn bg-slate-800 btn-type btn-xs"' : 'class="btn btn-default btn-type btn-xs"' !!} type="all">@lang('trans.all_type')</button>
					<button {!! request()->type =='tire' ? 'class="btn bg-slate-800 btn-type btn-xs"' : 'class="btn btn-default btn-type btn-xs"' !!} type="tire">@lang('trans.ban')</button>
					<button {!! request()->type =='battery' ? 'class="btn bg-slate-800 btn-type btn-xs"' : 'class="btn btn-default btn-type btn-xs"' !!} type="battery">@lang('trans.battery')</button>
					<button {!! request()->type =='velg' ? 'class="btn bg-slate-800 btn-type btn-xs"' : 'class="btn btn-default btn-type btn-xs"' !!} type="velg">@lang('trans.velg')</button>
					<button {!! request()->type =='oli' ? 'class="btn bg-slate-800 btn-type btn-xs"' : 'class="btn btn-default btn-type btn-xs"' !!} type="oli">@lang('trans.oli')</button>
				</div>
			</div>
			<div class="col-md-6 col-sm-12 text-right form-inline">
				<a href="{{ url('excel/export_report_all?type='.request()->type.'&month='.request()->month.'&year='.request()->year) }}" class="btn btn-default"><i class="icon-file-excel"></i></a>
				<select class="select2 btn-type" name="year" onchange="changeFilter(this)">
					@foreach (range(date('Y')-3, date('Y')+2) as $year)
					<option {!! $year == request('year') || (date('Y') == $year && ! request()->year) ? 'selected' : '' !!} value="{{ $year }}">{{ date_format2($year, 'Y', 'jp') }}</option>
					@endforeach
				</select>
				<select class="select2 btn-type" name="month" onchange="changeFilter(this)">
					@foreach (range(1, 12) as $month)
					<option {!! $month == request('month') || (date('m') == $month && ! request()->month) ? 'selected' : '' !!} value="{{ $month }}">{{ date_format2(sprintf('%02d', $month), 'm', 'jp') }}</option>
					@endforeach
				</select>
			</div>
		</div>
	</div>
	<div class="table-responsive">
		<table class="table table-bordered table-striped table-hover table-xxs">
			<thead>
				<tr>
					<th nowrap class="text-center bg-primary-600">@lang('trans.no')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.date')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.store_name')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.amount_stock_last_month')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.price_stock_last_month')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.amount_in_stock_this_month')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.price_stock_in_this_month')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.sell_amount')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.price_stock_out_this_month')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.barang_titip')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.price_barang_titip')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.amount_stock_this_month')</th>
					<th nowrap class="text-center bg-primary-600">@lang('trans.price_stock_this_month')</th>
				</tr>
			</thead>

			<tbody>
				@php
					$total_harga = 0;
					$harga_in_bulan_ini = 0;
					$harga_out_bulan_ini = 0;
					$harga_bulan_lalu = 0;
					$total_stock = 0;
					$stock_in_bulan_ini = 0;
					$stock_out_bulan_ini = 0;
					$stock_bulan_lalu = 0;

					$per_groups = [];

					foreach (App\StoreGroup::where('code', '!=', 2712)->get() as $group)
						$per_groups[$group->code] = [	'stock_last_month' => 0, 
														'stock_in_this_month' => 0,
														'stock_out_this_month' => 0,
														'stock_this_month' => 0,
														'price_last_month' => 0,
														'price_in_this_month' => 0,
														'price_out_this_month' => 0,
														'price_this_month' => 0,
														'additional' => 0,
														'price_additional' =>0
													];

				@endphp
				
				@foreach(App\StoreGroup::where('code', '!=', 2712)->get() as $group)
				@foreach ($stores as $index => $store)
				@php
				if ($store->store_group_code != $group->code)
					continue;
				@endphp
				<tr style="cursor: pointer;" class="store">
					<td class="text-center">{{ $loop->index+1 }}</td>
					<td nowrap class="text-center">{{ $store->date }}</td>
					<td nowrap>{{ $store->name }}</td>
					<td data-target="#last{{ $store->code }}" data-toggle="modal" class="text-center" @click="selectData( {{$store}} )">{{ number_format($store->stock_last_month) }}</td>
					<td data-target="#last{{ $store->code }}" data-toggle="modal" class="text-right info">{{ currency($store->price_last_month, 'jp') }}</td>
					<td data-target="#in_this{{ $store->code }}" data-toggle="modal" class="text-center">{{ number_format($store->stock_in_this_month) }}</td>
					<td data-target="#in_this{{ $store->code }}" data-toggle="modal" class="text-right info">{{ currency($store->price_stock_in_this_month, 'jp') }}</td>
					<td data-target="#out_this{{ $store->code }}" data-toggle="modal" class="text-center">{{ number_format($store->stock_out_this_month) }}</td>
					<td data-target="#out_this{{ $store->code }}" data-toggle="modal" class="text-right info">{{ currency($store->price_stock_out_this_month, 'jp') }}</td>
					<td data-target="#additional{{ $store->code }}" data-toggle="modal" class="text-center">{{ number_format($store->barang_titip_this_month) }}</td>
					<td data-target="#additional{{ $store->code }}" data-toggle="modal" class="text-right info">{{ currency($store->price_barang_titip_this_month, 'jp') }}</td>
					<td class="text-center">{{ number_format($store->total_stock) }}</td>
					<td class="text-right info">{{ currency($store->total_price_this_month, 'jp') }}</td>
					@php
						$total_harga += $store->total_price_this_month;
						$harga_in_bulan_ini += $store->price_stock_in_this_month;
						$harga_out_bulan_ini += $store->price_stock_out_this_month;
						$harga_bulan_lalu += $store->price_last_month;
						$total_stock += $store->total_stock;
						$stock_in_bulan_ini += $store->stock_in_this_month;
						$stock_out_bulan_ini += $store->stock_out_this_month;
						$stock_bulan_lalu += $store->stock_last_month;

						$per_groups[$store->store_group_code]['stock_last_month'] += $store->stock_last_month;
						$per_groups[$store->store_group_code]['stock_in_this_month'] += $store->stock_in_this_month;
						$per_groups[$store->store_group_code]['stock_out_this_month'] += $store->stock_out_this_month;
						$per_groups[$store->store_group_code]['stock_this_month'] += $store->total_stock;
						$per_groups[$store->store_group_code]['price_last_month'] += $store->price_last_month;
						$per_groups[$store->store_group_code]['price_in_this_month'] += $store->price_stock_in_this_month;
						$per_groups[$store->store_group_code]['price_out_this_month'] += $store->price_stock_out_this_month;
						$per_groups[$store->store_group_code]['price_this_month'] += $store->total_price_this_month;
						$per_groups[$store->store_group_code]['additional'] += $store->barang_titip_this_month;
						$per_groups[$store->store_group_code]['price_additional'] += $store->price_barang_titip_this_month;
					@endphp
				</tr>
				@endforeach
				<tr style="background-color: #29B6F6; color: #fff;" class="group"> 
					<td colspan="3" class="text-center">{{ $group->name }} @lang('trans.total')</td>
					<td class="text-center">{{ number_format($per_groups[$group->code]['stock_last_month']) }}</td>
					<td class="text-right">{{ currency($per_groups[$group->code]['price_last_month'], 'jp') }}</td>
					<td class="text-center">{{ number_format($per_groups[$group->code]['stock_in_this_month']) }}</td>
					<td class="text-right">{{ currency($per_groups[$group->code]['price_in_this_month'], 'jp') }}</td>
					<td class="text-center">{{ number_format($per_groups[$group->code]['stock_out_this_month']) }}</td>
					<td class="text-right">{{ currency($per_groups[$group->code]['price_out_this_month'], 'jp') }}</td>
					<td class="text-center">{{ number_format($per_groups[$group->code]['additional']) }}</td>
					<td class="text-right">{{ currency($per_groups[$group->code]['price_additional'], 'jp') }}</td>
					<td class="text-center">{{ number_format($per_groups[$group->code]['stock_this_month']) }}</td>
					<td class="text-right">{{ currency($per_groups[$group->code]['price_this_month'], 'jp') }}</td>
				</tr>
				@endforeach
			</tbody>	
		</table>
	</div>
</div>
</component>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript">
	var controller = new Vue ({
		el: '#controller',
		data: {
			store: {},
		},
		mounted: function() {

		},
		methods: {
			selectData(store) {
				this.store = store;
			}
		}
	});
</script>
<script type="text/javascript">
	var total_harga = {{ $total_harga }};
	var harga_in_bulan_ini = {{ $harga_in_bulan_ini }};
	var harga_out_bulan_ini = {{ $harga_out_bulan_ini }};
	var harga_bulan_lalu = {{ $harga_bulan_lalu }};
	var total_stock = {{ $total_stock }};
	var stock_in_bulan_ini = {{ $stock_in_bulan_ini }};
	var stock_out_bulan_ini = {{ $stock_out_bulan_ini }};
	var stock_bulan_lalu = {{ $stock_bulan_lalu }};
	var per_groups = {!! json_encode($per_groups) !!}
	$(function () {
		$('#harga_bulan_ini').html(currency(total_harga, 'jp'));
		$('#harga_in_bulan_ini').html(currency(harga_in_bulan_ini, 'jp'));
		$('#harga_out_bulan_ini').html(currency(harga_out_bulan_ini, 'jp'));
		$('#harga_bulan_lalu').html(currency(harga_bulan_lalu, 'jp'));
		$('#stock_bulan_ini').html(total_stock.toLocaleString());
		$('#stock_in_bulan_ini').html(stock_in_bulan_ini.toLocaleString());
		$('#stock_out_bulan_ini').html(stock_out_bulan_ini.toLocaleString());
		$('#stock_bulan_lalu').html(stock_bulan_lalu.toLocaleString());
	})

	$('.btn-type').on('click', function () {
		type = $(this).attr('type');
		month = $('[name=month]').val();
		year = $('[name=year]').val();
		$('.btn-type.bg-slate-800').removeClass('bg-slate-800').addClass('btn-default').removeAttr('disabled');
		$(this).removeClass('btn-default').addClass('bg-slate-800').attr('disabled', 'disabled');
		window.location.href = URL+'report_all?type='+type+'&month='+month+'&year='+year;
	});

	$(function() {
		$('.select2').select2({
			width: 'auto',
			minimumResultsForSearch: Infinity,
		});
	});

	function changeFilter(that) {
		type = 'all';
		window.location.href = URL+'report_all?type='+type+'&month='+$('[name=month]').val()+'&year='+$('[name=year]').val();
	}

	$(document).ready(function() {
	    $('table.display').DataTable( {
            "scrollCollapse": true,
            "searching": false,
             "bLengthChange": false,
             "bFilter": true,
             "bInfo": false,
             "bAutoWidth": false,
             "columnDefs": [
                 { "orderable": false, "targets": 0 },
                 { "orderable": false, "targets": 1 }
              ],
              "language":  {
	              	"sEmptyTable":"-",
                	"paginate": {
                         "first": "",
                         "last": "",
                         "next": "",
                         "previous": "",
                    }
                },
	    });
	});
</script>
@endpush