@extends('layouts.admin')

@push('css')
@endpush
{{-- ================================================================================================================================= --}}
@push('sec-js')
<script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/moment/moment.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/d3/d3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/c3/c3.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@push('js')
<script type="text/javascript">
	var stores = {!! json_encode($storeNames) !!};
    var graph_bar = {!! json_encode(array_values($perType)) !!};
    {{-- var legends = {!! json_encode(array_pluck($perTypes, 'name')) !!}; --}}
    var graph_line = {!! json_encode($perAmount) !!};
    var graph_column = {!! json_encode($perGroup) !!};
    var graph_pie = {!! json_encode($perBrand) !!};
    var type = {!! json_encode($perTypes) !!};
</script>
<script type="text/javascript" src="{{ asset('js/graph.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@section('header')
	@parent
	@section('header-title', __('trans.dashboard'))
@endsection

@section('content')
<component id="dashboard">

<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default">
			@php	
			$stocks = App\BsStock::select('type')
									->join('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
									->groupBy('type')->get();

			foreach ($stocks as $stock) {
			    $stock->stock_in = App\BsStock::select(DB::raw("SUM(amount) as total"))
			    					->join('stock_masters', 'stock_masters.jan_code', '=', 'bs_stocks.jan_code')
			                        ->where('type', $stock->type)
			                        ->first()
			                        ->total;
			    $stock->stock_out = App\Stock::select(DB::raw("SUM(amount) as total"))
			                        ->where('arrow', 'out')
			                        ->where('type', $stock->type)
			                        ->first()
			                        ->total;
			}

			@endphp
			<div class="panel-heading">
				<h5 class="panel-title">@lang('trans.graph_bar')</h5>
				<div class="heading-elements">
					<ul class="icons-list">
						@foreach ($stocks as $index => $stock)
                		<li><img src="{{ asset('assets/images/') }}/{{ $stock->type }}.png" width="18px" style="margin-left: 20px; margin-right: 5px;"><span>{{ number_format($stock->stock_in - $stock->stock_out) }}</span></li>
                		@endforeach
                	</ul>
            	</div>
			</div>
			<div class="panel-body">
				<div class="chart-container"> 
					<center>
						<div class="chart has-fixed-height" id="stacked_bars" style="height: 518px;"></div>
					</center>
				</div>
			</div>
		</div>			
	</div>
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h5 class="panel-title">@lang('trans.graph_pie')</h5>
			</div>
			<div class="panel-body">
				<div class="chart-container">
					<center>
						<div class="chart has-fixed-height" id="basic_pie" style="height: 200px;"></div>
					</center>
				</div>
			</div>
		</div>		
	</div>
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h5 class="panel-title">@lang('trans.graph_column')</h5>
				<div class="heading-elements">
					<ul class="icons-list">
                		<li> @lang('trans.satuan_per_10000') </li>
                	</ul>
            	</div>
			</div>

			<div class="panel-body">
				<div class="chart-container">
					<center>
						<div class="chart has-fixed-height" id="stacked_columns" style="height: 200px;"></div>
					</center>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-12">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h6 class="panel-title text-semibold">@lang('trans.graph_line')</h6>
			</div>

			<div class="panel-body">
				<div class="chart-container">
					<div class="chart" id="c3-line-regions-chart"></div>
				</div>
			</div>
		</div>
	</div>
</div>


</component>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript" src="{{ asset('js/datatable.select2.js') }}"></script>
@endpush