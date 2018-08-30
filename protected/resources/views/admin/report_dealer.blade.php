@extends('layouts.admin')

@push('css')
<style type="text/css">
table tr.example:hover td {
 	background-color: #CAF1F6;
 	color: #000;
 	/*font-weight: bold;*/
 }
</style>
@endpush
{{-- ================================================================================================================================= --}}
@push('sec-js')
{{-- <script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/datatables.min.js') }}"></script> --}}
{{-- <script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/extensions/fixed_columns.min.js') }}"></script> --}}
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/selects/select2.min.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@push('js')
<script type="text/javascript" src="{{ asset('js/mixin.'.auth()->user()->language.'.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@section('header')
	@parent
	@section('header-title', __('trans.report_dealer'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">
{{-- <div class="row" style="margin-top: -65px; margin-bottom: 25px;">
	<div class="col-md-12 text-right">
		<select class="select-nosearch" name="year">
			@foreach (range(date('Y')-2, date('Y')+1) as $year)
			<option {!! $year == date('Y') ? 'selected' : '' !!} value="{{ $year }}">{{ date_format2($year, 'Y', 'jp') }}</option>
			@endforeach
		</select>
	</div>
</div> --}}
<div class="row mb-20">
	@php 
		$total_amount = 0; 
		$total_price = 0; 
	@endphp
	@foreach($groups as $group)
 	<div class="col-md-3">
		<table class="table table-bordered table-striped table-xxs">
			<thead>
				<tr>
					<th colspan="2" class="text-center"> {{ $group->name }} </th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="background-color: #fff;"> @lang('trans.total_amount')</td>
					<td class="text-right" style="background-color: #fff;">{{ number_format($group->amount) }}</td>
				</tr>
				<tr>
					<td style="background-color: #fff;"> @lang('trans.total_price')</td>
					<td class="text-right" style="background-color: #fff;">¥{{ number_format($group->price) }}</td>
				</tr>
			</tbody>
		</table>
 	</div>	
 	@php 
 		$total_amount = $total_amount + $group->amount; 
 		$total_price = $total_price + $group->price; 
 	@endphp
 	@endforeach
 	<div class="col-md-3">
		<table class="table table-bordered table-striped table-xxs">
			<thead>
				<tr class="bg-primary-600">
					<th colspan="2" class="text-center"> @lang('trans.total_all_group') </th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td style="background-color: #E0F7FA;"> @lang('trans.total_amount')</td>
					<td class="text-right" style="background-color: #E0F7FA;">{{ number_format($total_amount) }}</td>
				</tr>
				<tr>
					<td style="background-color: #E0F7FA;"> @lang('trans.total_price')</td>
					<td class="text-right" style="background-color: #E0F7FA;">¥{{ number_format($total_price) }}</td>
				</tr>
			</tbody>
		</table>
 	</div>	
</div>
<div class="panel panel-default">
	<div class="panel-body">
		<div class="row">
			<div class="col-md-3">
				<h4 class="no-margin">@lang('trans.report_dealer')</h4>
			</div>
			<div class="col-md-9 text-right form-inline">
				<a href="{{ url('excel/export_report_dealer') }}" class="btn btn-default"><i class="icon-file-excel"></i></a>
				
			</div>
		</div>
	</div>	
	<div class="table-responsive">
		<table class="table datatable-fixed-left table-bordered table-striped table-hover table-xxs datatable-responsive datatables">
			<thead>
				<tr>
					<td nowrap rowspan="2" width="150px" bgcolor="#1E88E5" color="#fff">@lang('trans.store') </td>
					@foreach (range(1, 12) as $month)
					<th class="text-center" colspan="2">{{ $month }}月</th>
					@endforeach
				</tr>
				<tr>
					@foreach (range(1, 12) as $month)
						@foreach (range (1, 2) as $number)
							@if ($number == 1)
							<th nowrap="" class="text-center">@lang('trans.total_amount')</th>
							@else
							<th nowrap="" class="text-center">@lang('trans.price_out_dealer')</th>
							@endif
						@endforeach
					@endforeach
				</tr>
			</thead>
			@foreach (App\Store::all() as $index => $store)
			<tr class="example">
				<td nowrap bgcolor="#E0F7FA"> {{ $store->name }} </td>
				@foreach (range(1, 12) as $month)
					@foreach (range (1, 2) as $number)
						@if ($number == 1)
							@if ($stores[$index]->code == $store->code)
							<td class="text-center">{{ $stores[$index]->total_amount[$month-1] }}</th>
							@else
							<td class="text-center"></th>
							@endif
						@else
							<td class="text-center">¥{{ number_format($stores[$index]->total_price[$month-1]) }}</th>
						@endif
					@endforeach
				@endforeach
			</tr>
			{{-- @if ($stores[$index]->code == 108 || $stores[$index]->code == 209 || $stores[$index]->code == 303 || $stores[$index]->code == 888)
				<tr style="background-color: #29B6F6; color: #fff;" class="group"> 
					<td class="text-center"> @lang('trans.total')</td>
					@foreach (range(1, 12) as $month)
						@foreach (range (1, 2) as $number)
							@if ($number == 1)
								<td class="text-center">0</th>
							@else
								<td class="text-center">¥0</th>
							@endif
						@endforeach
					@endforeach
				</tr>
			@endif --}}
			@endforeach
		</table>
	</div>
</div>
</component>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript" src="{{ asset('js/datatable.select2.js') }}"></script>
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
	});

</script>
@endpush