@extends('layouts.admin')

@push('css')
@endpush
{{-- ================================================================================================================================= --}}
@push('sec-js')
<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput/plugins/purify.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput/plugins/sortable.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/uploaders/fileinput/fileinput.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/switchery.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/switch.min.js') }}"></script>
@endpush
{{-- ================================================================================================================================= --}}
@push('js')
@endpush
{{-- ================================================================================================================================= --}}
@section('header')
@endsection
{{-- ================================================================================================================================= --}}
@section('content')
<div class="col-md-6 col-md-offset-3">
	
	<form class="panel panel-default" method="post" action="{{ url('excel/stock') }}" enctype="multipart/form-data">
		{{ csrf_field() }}
		<div class="panel-heading text-center">
			<i class="icon-cube icon-3x"></i>
			<h4 class="panel-title text-center">@lang('trans.import_stock')</h4>
		</div>

		<div class="panel-body">
			@if (session('amount'))
			<div class="alert alert-success">
				<span class="text-bold">@lang('trans.success')! </span><span id="total-row">{{ session('amount') }}</span> @lang('trans.data_has_been_added')
			</div>
			@endif
			
			@if ($errors->count())
			<div class="alert alert-danger">
				<ul>
					@foreach ($errors->all() as $error)
					<li>{{ $error }}</li>
					@endforeach
				</ul>
				<hr>
				@lang('trans.follow_this_document_format') : <a href="{{ asset('formats/master_format.xlsx') }}" class="text-bold"><i class="icon-download4 position-left ml-10"></i> @lang('trans.download_here')</a>
			</div>
			@endif

			<div class="form-group">
				<label class="radio-inline">
					<input type="radio" name="arrow" value="out" class="styled" checked="checked">
					@lang('trans.stock_out')
				</label>
				<label class="radio-inline">
					<input type="radio" name="arrow" value="return" class="styled">
					@lang('trans.return')
				</label>
			</div>

			<input id="fileItem" type="file" class="file-input" name="csv" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
		</div>

		<div class="panel-footer p-20">
			<a href="{{ url()->previous() }}" class="btn btn-default"><i class="icon-circle-left2 position-left"></i> @lang('trans.back')</a>
			<div class="pull-right">
				<button class="btn btn-success" onclick="return confirm('こちら '+document.getElementById('fileItem').files[0].name+' でよろしいですか。');"><i class="icon-upload position-left"></i> @lang('trans.upload')</button>
			</div>
		</div>
	</form>
</div>
@endsection
{{-- ================================================================================================================================= --}}
@push('vue')
<script type="text/javascript">

	$(function () {
		    // Buttons inside zoom modal
		    var previewZoomButtonClasses = {
		    	toggleheader: 'btn btn-default btn-icon btn-xs btn-header-toggle',
		    	fullscreen: 'btn btn-default btn-icon btn-xs',
		    	borderless: 'btn btn-default btn-icon btn-xs',
		    	close: 'btn btn-default btn-icon btn-xs'
		    };

		    // Icons inside zoom modal classes
		    var previewZoomButtonIcons = {
		    	prev: '<i class="icon-arrow-left32"></i>',
		    	next: '<i class="icon-arrow-right32"></i>',
		    	toggleheader: '<i class="icon-menu-open"></i>',
		    	fullscreen: '<i class="icon-screen-full"></i>',
		    	borderless: '<i class="icon-alignment-unalign"></i>',
		    	close: '<i class="icon-cross3"></i>'
		    };

		    // File actions
		    var fileActionSettings = {
		    	showZoom: false,
		    	showUpload: false,
		    	zoomClass: 'btn btn-link btn-xs btn-icon',
		    	zoomIcon: '<i class="icon-zoomin3"></i>',
		    	dragClass: 'btn btn-link btn-xs btn-icon',
		    	dragIcon: '<i class="icon-three-bars"></i>',
		    	removeClass: 'btn btn-link btn-icon btn-xs',
		    	removeIcon: '<i class="icon-trash"></i>',
		    	indicatorNew: '<i class="icon-file-plus text-slate"></i>',
		    	indicatorSuccess: '<i class="icon-checkmark3 file-icon-large text-success"></i>',
		    	indicatorError: '<i class="icon-cross2 text-danger"></i>',
		    	indicatorLoading: '<i class="icon-spinner2 spinner text-muted"></i>'
		    };

		    $('.file-input').fileinput({
		    	browseLabel: translate('browse'),
		    	removeLabel: translate('remove'),
		    	showUpload: false,
		    	showPreview: false,
		    	browseIcon: '<i class="icon-file-plus"></i>',
		    	uploadIcon: '<i class="icon-file-upload2"></i>',
		    	removeIcon: '<i class="icon-cross3"></i>',
		    	initialCaption: "No file selected",
		    	previewZoomButtonClasses: previewZoomButtonClasses,
		    	previewZoomButtonIcons: previewZoomButtonIcons,
		    	fileActionSettings: fileActionSettings,

		    }).change(function () {
		    	$('.alert-danger').hide();
		    	$('.alert-success').hide();
		    });

		    $(".styled, .multiselect-container input").uniform({
		           radioClass: 'choice'
		    });
		});

	function uploadFile(event) {
		event.preventDefault();
		var action = $(event.target).attr('action');
		var data = new FormData($(event.target)[0]);

		$('.panel-body').block({ 
			message: '<i class="icon-spinner4 spinner"></i>',
			timeout: 2000,
			overlayCSS: {
				backgroundColor: '#fff',
				opacity: 0.8,
				cursor: 'wait'
			},
			css: {
				border: 0,
				padding: 0,
				backgroundColor: 'transparent'
			}
		});

		axios.post(action, data).then(response => {
			$('#total-row').html(response.data);
			$('.alert-success').fadeIn();
			$('.alert-danger').fadeOut();
			$('.fileinput-remove').trigger('click');
		}).catch(error => {
			$('.alert-success').fadeOut();
			$('.alert-danger').fadeIn();
		});

	}
</script>
@endpush