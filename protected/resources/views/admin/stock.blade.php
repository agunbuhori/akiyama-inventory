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
<div id="modal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header bg-slate-800">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title">@lang('trans.detail_stock') @{{ data.store_name }}</h5>
			</div>

			<div class="modal-body">
				<div class="row">
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.stock_datetime')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.stock_datetime }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.jan_code')</span>
						<hr class="mt-10 mb-10">
						@{{ data.jan_code }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.type')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.type }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.amount')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.amount }}
					</div>
				</div>
				<div class="row" style="margin-top:50px;">
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
						<span class="text-muted">@lang('trans.price')</span>
						<hr class="mt-10 mb-10"> 
						¥@{{ data.price }}
					</div>
					<div class="col-md-3">
						<span class="text-muted">@lang('trans.receipts')</span>
						<hr class="mt-10 mb-10"> 
						@{{ data.receipt_number }}
					</div>
				</div>
			</div>

			<div class="modal-footer">
				<button type="button" class="btn bg-slate-800" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>

<div id="modal-form" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<form class="form-horizontal" :action="actionUrl" method="post" @submit.prevent="submitForm($event)" autocomplete="off">
				{{csrf_field()}}
				<input type="hidden" name="_method" value="PUT" v-if="editStatus">

				<div class="modal-header bg-slate-800">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title" v-if="!editStatus">@lang('trans.add_stock')</h5>
					<h5 class="modal-title" v-if="editStatus">@lang('trans.edit')</h5>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<div class="col-lg-12">
							<div class="row">
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.stock_datetime')</label>
									<input type="text" class="form-control pickadate-accessibility" name="stock_datetime" :value="data.stock_datetime" style="background-color: #fff;">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.jan_code')</label>
									<input type="text" class="form-control" name="jan_code" :value="data.jan_code" required="required">
								</div>
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.amount')</label>
									<input type="text" class="form-control" name="amount" :value="data.amount" required="required">
								</div>
								{{-- <div class="col-xs-3 mb-15">
									<label class="control-label">@lang('trans.arrow')</label> <br>
									<select class="form-control" name="arw">
										<option value="in" :selected="data.arrow =='in'">@lang('trans.filter_stock_in')</option>
										<option value="out" :selected="data.arrow =='out'">@lang('trans.filter_stock_out')</option>
									</select>
								</div> --}}
								<div class="col-xs-6 mb-15">
									<label class="control-label">@lang('trans.type')</label> <br>
									<select class="form-control" name="type">
										<option value="タイヤ" :selected="data.type =='タイヤ'">@lang('trans.ban')</option>
										<option value="バッテリー" :selected="data.type == 'バッテリー'">@lang('trans.battery')</option>
										<option value="ホイール" :selected="data.type == 'ホイール'">@lang('trans.velg')</option>
										<option value="オイル" :selected="data.type == 'オイル'">@lang('trans.oli')</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>

				<div class="modal-footer">
					<input type="hidden" name="id" :value="data.id">
					<button type="submit" class="btn bg-primary">@lang('trans.save')</button>
				</div>

			</form>
		</div>
	</div>
</div>

<div class="row" style="margin-top: -70px;">
	<div class="col-md-6 pull-left" id="span" style="margin-top: 55px;">
	</div>

	<div class="col-md-3 pull-right">
		<div class="panel panel-body panel-body-accent p-10">
			<div class="media no-margin">
				<div class="media-left media-middle">
					<i class="icon-cart-add2 icon-3x text-slate-800"></i>
				</div>
				<div class="media-body text-right">
					<h3 class="no-margin text-semibold" id="total_price"></h3>
					<span class="text-uppercase text-size-mini text-muted">@lang('trans.total_price')</span>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-3 pull-right">
		<div class="panel panel-body panel-body-accent p-10">
			<div class="media no-margin">
				<div class="media-left media-middle">
					<i class="icon-coins icon-3x text-slate-800"></i>
				</div>

				<div class="media-body text-right">
					<h3 class="no-margin text-semibold" id="total_amount"></h3>
					<span class="text-uppercase text-size-mini text-muted">@lang('trans.total_amount')</span>
				</div>
			</div>
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
					<form action="{{ url('excel/export_stock') }}" method="POST">
						{{ csrf_field() }}
						@if (auth()->user()->role === 1)
						<div class="col-md-6 form-inline">
							<select class="form-control select-store" name="store">
								@foreach (\App\Store::all() as $store)
								<option value="{{ $store->code }}">
									{{ $store->code }} {{ $store->name }}
								</option>
								@endforeach
							</select>

							<div class="btn-group">
								<button type="button" class="btn bg-slate-800 btn-stockreturn btn-xs" stockreturn="0"><i class="icon-redo position-left"></i>@lang('trans.stock_out')</button>
								<button type="button" class="btn btn-default btn-stockreturn btn-xs" stockreturn="1"><i class="icon-undo position-left"></i>@lang('trans.return')</button>
							</div>

						</div>
						@endif

						@if (auth()->user()->role === 2)
						<div class="col-md-6">

						{{-- <form class="col-md-6 text-left form-inline" method="post" action="{{ url('data/stock') }}" enctype="multipart/form-data">
							{{<div class="btn-group">
								<button type="button" class="btn bg-slate-800 btn-arrow btn-xs" arrow="in"><i class="icon-arrow-left8 position-left"></i>@lang('trans.filter_stock_in')</button>
								<button type="button" class="btn btn-default btn-arrow btn-xs" arrow="out"><i class="icon-arrow-right8 position-left"></i>@lang('trans.filter_stock_out')</button>
							</div> --}}
							<input type="hidden" name="store" value="{{ auth()->user()->store_code }}">
							<div class="btn-group">
								<button type="button" class="btn bg-slate-800 btn-stockreturn btn-xs" stockreturn="0"><i class="icon-redo position-left"></i>@lang('trans.stock_out')</button>
								<button type="button" class="btn btn-default btn-stockreturn btn-xs" stockreturn="1"><i class="icon-undo position-left"></i>@lang('trans.return')</button>
							</div>
							<a href="{{ url('excel/stock') }}" class="btn btn-default btn-xs ml-5"><i class="icon-upload position-left"></i>@lang('trans.import')</a>
						{{-- </form> --}}
						</div>
						@endif

						<div class="col-md-6">
							<div class="{!! auth()->user()->role === 1 ? 'text-right' : 'text-right' !!}">

								<button type="submit" class="btn btn-default"><i class="icon icon-file-excel"></i></button>
								
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
					</form>
				</div>
			</div>

			<table class="table table-bordered table-striped table-hover table-xxs datatables example">
				<thead>
					<tr>
						@if (auth()->user()->role === 1)
						@endif
						<th class="text-center" width="20px"><i class="icon-check"></i></th>
						<th class="text-center">@lang('trans.no')</th>
						<th class="text-center">@lang('trans.date')</th>
						<th class="text-center">@lang('trans.brand')</th>
						<th class="text-center">@lang('trans.version')</th>
						<th class="text-center">@lang('trans.size')</th>
						<th class="text-center">@lang('trans.type')</th>
						<th class="text-center">@lang('trans.amount')</th>
						<th class="text-center">@lang('trans.total_sales')</th>
						@if (auth()->user()->role != 1) 
						<th class="text-center">@lang('trans.option')</th>
						@endif
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
	var stockreturn = $('.btn-stockreturn.bg-slate-800').attr('stockreturn');
	var filter = $('select[name=filter]').val();
	var date = $('select[name=year]').val()+'-'+$('select[name=month]').val()+'-'+$('select[name=day]').val();
	var store = $('select[name=store]').val();
	var actionUrl = URL+'data/stock';
	var exportUrl = URL+'excel/export_stock';
	var actionBtn = ! isCentral();
	var order = [[2, "asc"]];

	// var btns = ['<a href="'+exportUrl+'" type="button" class="btn btn-default mr-5"><i class="icon-file-excel"></i></a>'];

	var columns = [
		{render: function (index, row, data) {
			return '<input type="checkbox" value="'+data.id+'" class="checkRow" onclick="controller.checkRow(event)">'
		}, orderable: false, width: '20px', class: 'text-center'},
		{data: 'DT_Row_Index', orderable: false, searchable: false, width: '20px', class: 'text-center'},
		{data: 'stock_datetime', class: 'text-center', searchable: false},
		{data: 'brand', class: 'text-center', orderable: true, searchable: true},
		{data: 'version', class: 'text-center', orderable: false, searchable: true},
		{data: 'size', class: 'text-center', orderable: false, searchable: true},
		{data: 'type', class: 'text-center', searchable: true},
		{data: 'amount', class: 'text-center', orderable: false, searchable: false},
		{data: 'price', class: 'text-right', orderable: false, searchable: false},
	];

	// If authentocation is central
	if (!isCentral())
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

		$('select[name=day]').next('.select2-container').hide();

		$('select[name=store]').on('change', function () {
			store = $(this).val();
			controller.table.ajax.url(URL+'data/stock?store='+store+'&arrow=out&filter='+filter+'&date='+date+'&stockreturn='+stockreturn).load();
		});

		$('select[name=arrow]').on('change', function () {
			arrow = $(this).val();
			controller.table.ajax.url(URL+'data/stock?store='+store+'&arrow=out&filter='+filter+'&date='+date+'&stockreturn='+stockreturn).load();
		});

		$('.btn-stockreturn').on('click', function () {
			stockreturn = $(this).attr('stockreturn');
			$('.btn-stockreturn.bg-slate-800').removeClass('bg-slate-800').addClass('btn-default').removeAttr('disabled');
			$(this).removeClass('btn-default').addClass('bg-slate-800').attr('disabled', 'disabled');
			controller.table.ajax.url(URL+'data/stock?store='+store+'&arrow=out&filter='+filter+'&date='+date+'&stockreturn='+stockreturn).load();
		});

		$('select[name=filter]').on('change', function () {
			filter = $(this).val();
			controller.table.ajax.url(URL+'data/stock?store='+store+'&arrow=out&filter='+filter+'&date='+date+'&stockreturn='+stockreturn).load();

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
			controller.table.ajax.url(URL+'data/stock?store='+store+'&arrow=out&filter='+filter+'&date='+date+'&stockreturn='+stockreturn).load();
		});

		controller.table.on('xhr', function (response) {
			$('#total_amount').html(controller.table.ajax.json().totalAmount);
			$('#total_price').html(controller.table.ajax.json().totalPrice);

			$('#span').empty();
			loop = controller.table.ajax.json().count;
			dates = controller.table.ajax.json().dates;

			for (i = 0; i < loop; i++) {
			    $('#span').append("<span class='label ml-5 bg-primary-800' style='padding: 5px;' id='text'>"+dates[i]+"</span>");
			}
			for (i = 0; i < 5 - loop; i++) {
			    $('#span').append("<span class='label ml-5 bg-grey-300' style='padding: 5px;' id='text'>00</span>");
			}
		})
	});
</script>
@endpush