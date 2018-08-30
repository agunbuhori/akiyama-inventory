@extends('layouts.admin')

@push('css')
<style type="text/css">
.dataTables_paginate {
    float: right;
    text-align: right;
    margin: 10px -20px 0px 0px;
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
<script type="text/javascript" src="{{asset('assets/js/plugins/visualization/echarts/echarts.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@push('js')
<script type="text/javascript" src="{{ asset('js/mixin.'.auth()->user()->language.'.js') }}"></script>
<script type="text/javascript">
	var graph_bar_store = {!! json_encode($graph_bar_store) !!};
	var graph_pie_store = {!! json_encode($graph_pie_store) !!};
    var graph_column_store = {!! json_encode($graph_column_store) !!};
</script>
<script type="text/javascript" src="{{ asset('js/graph_store.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@section('header')
	@parent
	@section('header-title', __('trans.dashboard'))
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<component id="controller">
<div id="modal-form" class="modal fade">
	<div class="modal-dialog modal-sm">
		<div class="modal-content">
			<form class="form-horizontal" action="{{ url('data/shift') }}" method="post" autocomplete="off">
				{{csrf_field()}}
				<div class="modal-header bg-slate-800">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title">@lang('trans.add_request')</h5>
				</div>
				<input type="hidden" name="id" :value="data.id">
				<input type="hidden" name="jan_code" :value="data.jan_code">
				<input type="hidden" name="to_store" :value="data.store_code">
				<div class="modal-body">
					<div class="form-group">
						<div class="col-lg-12">
							<div class="row">
								<div class="col-md-4">
									<span class="text-muted">@lang('trans.to_store')</span>
								</div>
								<div class="col-md-8 mb-10">
									@{{ data.name }}
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
									<span class="text-muted">@lang('trans.type')</span>
								</div>
								<div class="col-md-8 mb-10">
									@{{ data.type }}
								</div>
								<hr width="95%"> 
								<div class="col-md-4">
									<span class="text-muted">@lang('trans.size')</span>
								</div>
								<div class="col-md-8 mb-10">
									@{{ data.size }}
								</div>
								<hr width="95%">
							</div>
							<div class="row">
								<div class="col-xs-12 mb-15">
									<label class="control-label">@lang('trans.amount')</label>
									<input type="text" class="form-control" name="amount" required="">
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
 
<form action="{{ url('dashboard') }}" method="get" autocomplete="off">
	<div class="input-group content-group col-md-6">
		<div class="has-feedback has-feedback-left">
			<input type="text" name="search" class="form-control input-xlg" placeholder="キーワードを入力" value="{{ request()->search }}">
			<div class="form-control-feedback">
				<i class="icon-search4 text-muted text-size-base"></i>
			</div>
		</div>
		<div class="input-group-btn">
			<button type="submit" class="btn btn-primary btn-xlg">@lang('trans.search')</button>
		</div>
	</div>
</form>

@if (request()->has('search') && request()->search != '')
	@if($stocks->isEmpty())
		<alert class="alert alert-danger text-bold col-md-6"><i class="icon-warning position-left"></i>@lang('trans.stocks_is_empty') </alert>
	@else
	<div class="row" style="margin-bottom: 20px;"> 
		<div class="col-xs-12">
			<div class="panel panel-default">
				<table class="table table-bordered table-striped table-hover table-xxs">
					<thead>
						<tr>
							<th class="text-center" width="40px">@lang('trans.no')</th>
							<th class="text-center">@lang('trans.store')</th>
							<th class="text-center">@lang('trans.brand')</th>
							<th class="text-center">@lang('trans.version')</th>
							<th class="text-center">@lang('trans.type')</th>
							<th class="text-center">@lang('trans.size')</th>
							<th class="text-center">@lang('trans.amount')</th>
							<th class="text-center">@lang('trans.option')</th>
						</tr>
					</thead>
					<tbody>
						@foreach ($stocks as $index => $data)
						<tr>
							<td class="text-center">{{ $index+1 }}</td>
							<td>{{ $data->name }}</td>
							<td>{{ $data->brand }}</td>
							<td>{{ $data->version }}</td>
							<td class="text-center">{{ $data->type }}</td>
							<td>{{ $data->size }}</td>
							<td class="text-center">{{ $data->amount }}</td>
							<td class="text-center"><a href="" @click="addData(event, {{ $data }} )" data-target="#modal-form" data-toggle="modal">@lang('trans.request')</a></td>
						</tr>
						@endforeach
					</tbody>
				</table>
			</div>
			<div class="pull-right"> {!! $stocks->appends($_GET)->links() !!} </div>
		</div> 
	</div>
	@endif
@else
<div class="row">
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h5 class="panel-title">@lang('trans.graph_bar')</h5>
			</div>
			<div class="panel-body">
				<div class="chart-container">
					<div class="chart has-fixed-height" id="basic_bars" style="height: 200px;"></div>
				</div>
			</div>
		</div>
	</div>
	<div class="col-md-6">
		<div class="panel panel-default" style="height: 300px;">
			<div class="btn-group" style="margin:10px;">
				<button {!! request()->type =='tire' || request()->type == '' ? 'class="btn bg-slate-800 btn-type btn-xs"' : 'class="btn btn-default btn-type btn-xs"' !!} type="tire">@lang('trans.ban')</button>
				<button {!! request()->type =='battery' ? 'class="btn bg-slate-800 btn-type btn-xs"' : 'class="btn btn-default btn-type btn-xs"' !!} type="battery">@lang('trans.battery')</button>
				<button {!! request()->type =='velg' ? 'class="btn bg-slate-800 btn-type btn-xs"' : 'class="btn btn-default btn-type btn-xs"' !!} type="velg">@lang('trans.velg')</button>
				<button {!! request()->type =='oli' ? 'class="btn bg-slate-800 btn-type btn-xs"' : 'class="btn btn-default btn-type btn-xs"' !!} type="oli">@lang('trans.oli')</button>
			</div>
			<table class="table table-bordered table-striped table-hover table-xxs display" style="border-bottom:1px solid #ddd">
				<thead>
					<tr>
						<th class="text-center" width="10px">@lang('trans.no')</th>
						<th class="text-center">@lang('trans.brand')</th>
						<th class="text-center">@lang('trans.size')</th>
						<th class="text-center">@lang('trans.stock_this_month')</th>
					</tr>
				</thead>
				<tbody>
					@foreach ($datas as $key => $stock)
					<tr>
						<td class="text-center">{{ $key+1 }}</td>
						<td class="text-center">{{ $stock->brand }}</td>
						<td>{{ $stock->size }}</td>
						<td class="text-center">{{ $stock->total }}</td>
					</tr>
					@endforeach
				</tbody>
			</table>
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
	<div class="col-md-6">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h5 class="panel-title">@lang('trans.graph_pie')</h5>
			</div>
			<div class="panel-body">
				<div class="chart-container">
					<div class="chart has-fixed-height" id="basic_pie" style="height: 200px;"></div>
				</div>
			</div>
		</div>		
	</div>
</div>
@endif

</component>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript">
	var controller = new Vue({
		el : '#controller',
		data : {
			data : {},
		},
		mounted: function() {

		},
		methods: {
			addData(event, data) {
				this.data = data;
			}
		}
	});

	$('.btn-type').on('click', function () {
		type = $(this).attr('type');
		$('.btn-type.bg-slate-800').removeClass('bg-slate-800').addClass('btn-default').removeAttr('disabled');
		$(this).removeClass('btn-default').addClass('bg-slate-800').attr('disabled', 'disabled');
		window.location.href = URL+'dashboard?type='+type;
		// controller.table.ajax.url(URL+'dashboard?type='+type).load();
	});

	$(document).ready(function() {
	    $('table.display').DataTable( {
            "scrollCollapse": true,
            "searching": false,
             "bLengthChange": false,
             "bFilter": true,
             "bInfo": false,
             "bAutoWidth": false,
             "pageLength": 5,
             "columnDefs": [
                 { "orderable": false, "targets": 0 },
                 { "orderable": false, "targets": 1 },
                 { "orderable": false, "targets": 2 },
                 { "orderable": false, "targets": 3 }
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
<script type="text/javascript" src="{{ asset('js/datatable.select2.js') }}"></script>
@endpush