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
<script type="text/javascript" src="{{ asset('assets/js/plugins/tables/datatables/extensions/fixed_columns.min.js') }}"></script>
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
<script type="text/javascript" src="{{ asset('assets/js/pages/picker_date.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@section('header')
	@parent
	@section('header-title', __('trans.stock'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller" v-cloak>

<div id="modal-reset" class="modal fade" style="margin-top: 100px;">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form class="form-horizontal" action="{{ url('data/reset') }}" method="post" autocomplete="off">
			{{csrf_field()}}
				<div class="modal-body">
					<div class="row">
						<div class="coll-md-12" style="text-align: center; margin-top: 10px;">
							<i class="icon icon-warning2 icon-3x" style="color:#F44336;"> </i>
						</div>
						<div class="coll-md-12" style="text-align: center; margin-top: 20px;">
							<h5> Are you sure ? </h5>
						</div>
						<div class="coll-md-12" style="text-align: center; margin-top: 0px;">
							<span> You will not be able to recover this imaginary file! </span>
						</div>
						<div class="coll-md-12" style="text-align: center; margin-top: 20px;">
							<button class="btn btn-default" data-dismiss="modal">@lang('trans.cancel')</button>
							<button type="submit" class="btn btn-danger">@lang('trans.reset')</button>
						</div>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<div class="row" style="margin-top: -70px; margin-bottom: 20px;">
	<div class="clo-md-12">
		<div class="col-md-6">
		</div>
	  	<div class="col-md-3">
	 		<table class="table table-bordered table-striped table-xxs">
	 			<thead>
	 				<tr class="bg-primary-600">
	 					<th colspan="2" class="text-center"> @lang('trans.total_stock') </th>
	 				</tr>
	 			</thead>
	 			<tbody>
	 				<tr style="background-color: #fff;">
	 					<td> @lang('trans.total_amount')</td>
	 					<td id="total_amount" class="text-right"></td>
	 				</tr>
	 				<tr style="background-color: #fff;">
	 					<td> @lang('trans.total_price')</td>
	 					<td id="total_price" class="text-right"></td>
	 				</tr>
	 			</tbody>
	 		</table>
	  	</div>	
	   	<div class="col-md-3">
	  		<table class="table table-bordered table-striped table-xxs">
	  			<thead>
	  				<tr class="bg-primary-600">
	  					<th colspan="2" class="text-center"> @lang('trans.close_book') </th>
	  				</tr>
	  			</thead>
	  			<tbody>
	  				<tr style="background-color: #fff;">
	  					<td> @lang('trans.total_amount')</td>
	  					<td id="total_close_book" class="text-right"></td>
	  				</tr>
	  				<tr style="background-color: #fff;">
	  					<td> @lang('trans.total_price')</td>
	  					<td id="total_price_close_book" class="text-right"></td>
	  				</tr>
	  			</tbody>
	  		</table>
	   	</div>	
	</div>
</div>

<div class="row">
	<div class="col-md-12"> 

		@if (session()->has('message'))
		<div class="alert alert-success">
			@lang('trans.upload_success')
		</div>
		@elseif (session()->has('message_error') || $errors->count())
		<div class="alert alert-danger">
			@lang('trans.file_not_valid')
		</div>
		@endif
		<div class="panel panel-default">
			<div class="panel-body">
				<div class="row">
					<form action="{{ url('excel/export_close_book') }}" method="POST">
						{{ csrf_field() }}

						<div class="col-md-6 form-inline">
							@if (auth()->user()->role === 1)
							<select class="form-control select-store" name="store">
								@foreach (\App\Store::all() as $store)
								<option value="{{ $store->code }}">
									{{ $store->code }} {{ $store->name }}
								</option>
								@endforeach
							</select>
							<button type="button" data-target="#modal-reset" data-toggle="modal" class="btn btn-danger"><i class="icon icon-database-refresh position-left"></i>@lang('trans.reset')</button>
							@else
								<input type="hidden" name="store" value="{{ auth()->user()->store_code }}">
								<a href="{{ url('excel/close_book') }}" class="btn btn-default btn-xs ml-5"><i class="icon-upload position-left"></i>@lang('trans.import')</a>
							@endif
						</div>

						<div class="col-md-6">
							<div class="{!! auth()->user()->role === 1 ? 'text-right' : 'text-right' !!}">
								<button type="submit" class="btn btn-default"><i class="icon icon-file-excel"></i></button>

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
					</form>
				</div>
			</div>

			<table class="table table-bordered table-striped table-hover table-xxs datatables example">
				<thead>
					<tr>
						<th class="text-center">@lang('trans.no')</th>
						<th class="text-center">@lang('trans.jan_code')</th>
						<th class="text-center">@lang('trans.brand')</th>
						<th class="text-center">@lang('trans.version')</th>
						<th class="text-center">@lang('trans.size')</th>
						<th class="text-center">@lang('trans.type')</th>
						{{-- <th class="text-center">@lang('trans.amount')</th> --}}
						{{-- <th class="text-center">@lang('trans.stock_out')</th> --}}
						<th class="text-center">@lang('trans.price')</th>
						<th class="text-center">@lang('trans.total_amount')</th>
						<th class="text-center">@lang('trans.close_book')</th>
						{{-- <th class="text-center">@lang('trans.price_close_book')</th> --}}
						<th class="text-center">@lang('trans.difference')</th>
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
	var date = $('select[name=year]').val()+'-'+$('select[name=month]').val()+'-'+$('select[name=day]').val();
	var store = $('select[name=store]').val();
	var actionUrl = URL+'close_book_api';
	var exportUrl = URL+'excel/export_close_book';
	var actionBtn = false;
	var order = [[2, "asc"]];

	var columns = [
		{data: 'DT_Row_Index', orderable: false, searchable: false, width: '20px', class: 'text-center'},
		{data: 'jan_code', class: 'text-center', searchable: true},
		{data: 'brand', class: 'text-center', orderable: true, searchable: true},
		{data: 'version', class: 'text-center', orderable: false, searchable: true},
		{data: 'size', class: 'text-center', orderable: false, searchable: true},
		{data: 'type', class: 'text-center', searchable: true},
		{data: 'basic_price', class: 'text-center', orderable: false, searchable: false},
		// {data: 'amount', class: 'text-center', orderable: false, searchable: false},
		// {data: 'stock_out', class: 'text-center', orderable: false, searchable: false},
		{data: 'total', class: 'text-center', orderable: false, searchable: false},
		// {data: 'close_book', class: 'text-center', orderable: false, searchable: false},
		{data: 'close_book', class: 'text-center', orderable: false, searchable: false},
		{data: 'difference', class: 'text-center', orderable: false, searchable: false},
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

		$('.select-nosearch').select2({
			width: 'auto',
			minimumResultsForSearch: Infinity
		});

		$('.select-store').select2({
			width: '300px'
		});

		$('select[name=day]').next('.select2-container').hide();

		$('select[name=store]').on('change', function () {
			store = $(this).val();
			controller.table.ajax.url(URL+'close_book_api?&date='+date+'&store='+store).load();
		});

		$('.select-nosearch').on('change', function() {
			date = $('select[name=year]').val()+'-'+$('select[name=month]').val()+'-'+$('select[name=day]').val();
			controller.table.ajax.url(URL+'close_book_api?&date='+date+'&store='+store).load();
		});

		controller.table.on('xhr', function (response) {
			$('#total_amount').html(controller.table.ajax.json().totalAmount);
			$('#total_price').html(controller.table.ajax.json().totalPrice);
			$('#total_close_book').html(controller.table.ajax.json().totalCloseBook);
			$('#total_price_close_book').html(controller.table.ajax.json().totalPriceCloseBook);
		})
	});
</script>
@endpush