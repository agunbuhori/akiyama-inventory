@extends('layouts.admin')

@push('css')
<style type="text/css">
.dataTables_filter input {
    width: 300px;
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
	@section('header-title', __('trans.shift'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">
<div id="modal-form" class="modal fade">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form class="form-horizontal" :action="actionUrl" method="post" @submit="submitForm($event)">
			{{csrf_field()}}
			<input type="hidden" name="_method" value="PUT" v-if="editStatus">
			<div class="modal-header bg-slate-800">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h5 class="modal-title" v-if="editStatus">@lang('trans.update_status')</h5>
			</div>

			<div class="modal-body">
				<div class="form-group">
					<div class="col-lg-12">
						<div class="row">
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.from_store')</span>
							</div> 
							<div class="col-md-8 mb-10">
								 @{{ data.from_store }}
							</div> 
							<hr width="95%"> 
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.to_store')</span>
							</div> 
							<div class="col-md-8 mb-10">
								 @{{ data.to_store }}
							</div> 
							<hr width="95%"> 
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.brand')</span>
							</div> 
							<div class="col-md-8 mb-10">
								 @{{ data.brand }}
							</div> 
							<hr width="95%"> 
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.size')</span>
							</div> 
							<div class="col-md-8 mb-10">
								 @{{ data.size }}
							</div> 
							<hr width="95%"> 
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.amount')</span>
							</div> 
							<div class="col-md-8 mb-10">
								 @{{ data.amount }}
							</div> 
							<hr width="95%"> 
							<div class="col-md-4">
								<span class="text-muted">@lang('trans.status')</span>
							</div> 
							<div class="col-md-8 mb-10">
								<select name="status" class="form-control select3" :value="data.status">
									<option value="pending">@lang('trans.pending')</option> 
									<option value="acc" selected="true">@lang('trans.acc')</option> 
									<option value="fail">@lang('trans.fail')</option>
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
<div class="panel panel-default">
	<div class="panel-heading">
		<div class="row">
			<div class="col-md-3 col-sm-12">
				<select class="form-control select-store" name="store">
					@foreach (App\Store::all() as $store)
					<option value="{{ $store->code }}">{{ $store->code }} {{ $store->name }}</option>
					@endforeach
				</select>
			</div>
			<div class="col-md-9 col-sm-12 text-right form-inline">
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
	<table class="table table-bordered table-striped table-hover table-xxs datatable-responsive datatables">
		<thead>
			<tr>
				<th><i class="icon-check"></i></th>
				<th class="text-center">@lang('trans.no')</th>
				<th class="text-center">@lang('trans.receipt_date')</th>
				<th class="text-center">@lang('trans.from_store')</th>
				<th class="text-center">@lang('trans.to_store')</th>
				<th class="text-center">@lang('trans.brand')</th>
				<th class="text-center">@lang('trans.size')</th>
				<th class="text-center">@lang('trans.type')</th>
				<th class="text-center">@lang('trans.amount')</th>
				<th class="text-center">@lang('trans.status')</th>
				{{-- <th class="text-center">@lang('trans.option')</th> --}}
			</tr>
		</thead>
	</table>
</div>
</component>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript">
	var actionUrl = URL+'data/shift';
	var exportUrl = URL+'excel/shift';
	var actionBtn = false;
	var order = [[1]];
	
	var filter = $('select[name=filter]').val();
	var date = $('select[name=year]').val()+'-'+$('select[name=month]').val()+'-'+$('select[name=day]').val();
	var store = $('select[name=store]').val();

	var btns = ['<a href="'+exportUrl+'" type="button" class="btn btn-default mr-5"><i class="icon-file-excel"></i></a>'];
	var columns = [
		{render: function (index, row, data) {
			return '<input type="checkbox" value="'+data.id+'" class="checkRow" onclick="controller.checkRow(event)">'
		}, orderable: false, width: '20px', class: 'text-center', searchable: false},
		{data: 'DT_Row_Index', width: '20px', class:'text-center', orderable: false, searchable: false},
		{render: function (index, row, data){
			return moment(data.created_at).format(dateFormat)
		}, class: 'text-center', width: '130px'},
		{data: 'to_store', class: 'text-left', orderable: false},
		{data: 'from_store', class: 'text-left', orderable: false},
		{data: 'brand', class: 'text-center', orderable: false, searchable: false},
		{data: 'size', class: 'text-center', orderable: false, searchable: false},
		{data: 'type', class: 'text-center', orderable: false, searchable: false},
		{data: 'amount', class: 'text-center'},
		{render: function(index, row, data) {
			if (data.status == 'pending')
				class_name = 'bg-warning';
			else if (data.status == 'acc')
				class_name = 'bg-success';
			else if (data.status == 'done')
				class_name = 'bg-primary';
			return '<span class="label '+class_name+'">'+translate(data.status)+'</span>';
		}, class: 'text-center', orderable: false},
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

		$('select[name=store]').on('change', function () {
			store = $(this).val();
			controller.table.ajax.url(URL+'data/shift?auth=central&store='+store+'&filter='+filter+'&date='+date).load();
		});

		$('select[name=filter]').on('change', function () {
			filter = $(this).val();
			controller.table.ajax.url(URL+'data/shift?auth=central&store='+store+'&filter='+filter+'&date='+date).load();

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
			controller.table.ajax.url(URL+'data/shift?auth=central&store='+store+'&filter='+filter+'&date='+date).load();
		});
	});

</script>
@endpush