
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<link rel="icon" href="{{ asset('assets/images/icon.png') }}">

	<title>@lang('trans.authentication')</title>

	<!-- Global stylesheets -->
	<link href="https://fonts.googleapis.com/css?family=Roboto:400,300,100,500,700,900" rel="stylesheet" type="text/css">
	<link href="{{ asset('assets/css/icons/icomoon/styles.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('assets/css/bootstrap.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('assets/css/core.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('assets/css/components.css') }}" rel="stylesheet" type="text/css">
	<link href="{{ asset('assets/css/colors.css') }}" rel="stylesheet" type="text/css">
	@stack('css')
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/nicescroll.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/drilldown.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/axios.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/vue.js') }}"></script>
	<script type="text/javascript" src="{{ url('phpjs') }}"></script>
	@stack('sec-js')
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script type="text/javascript">
		const URL = "{{ url('/') }}/",
		CURRENT = "{{ request()->url() }}",
		TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

		function logoutSession(event) {
			event.preventDefault();
			axios.post(URL+'logout');
			setTimeout(function () {
				location.reload();
			}, 300);
		}
	</script>
	<script type="text/javascript">
		function clearNotif(id) {
			window.location = URL+'data/shift/'+id;
		}
	</script>
	<script type="text/javascript" src="{{ asset('assets/js/core/app.js') }}"></script>
	@stack('js')
	<!-- /theme JS files -->

</head>

<body>
	<div id="modal-change-password" class="modal fade">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<form class="form-horizontal" action="{{ url('data/user/'.auth()->user()->id) }}" method="post" autocomplete="off">
				{{csrf_field()}}
				<input type="hidden" name="_method" value="PUT">
				
				<div class="modal-header bg-slate-800">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title" v-if="editStatus">@lang('trans.change_password')</h5>
				</div>

				<div class="modal-body">
					<div class="form-group">
						<div class="col-lg-12">
							<div class="row">
								<div class="col-xs-12 mb-15">
									<label class="control-label">@lang('trans.your_password')</label>
									<input type="password" class="form-control" name="your_password" required="">
								</div>
								<hr style="margin-top: 90px; width: 100%;">
								<div class="col-xs-12 mb-15">
									<label class="control-label">@lang('trans.new_password')</label>
									<input type="password" class="form-control" name="new_password" required="">
								</div>
								<div class="col-xs-12 mb-15">
									<label class="control-label">@lang('trans.confirm_password')</label>
									<input type="password" class="form-control" name="password" required="">
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

	<!-- Main navbar -->
	<div class="navbar navbar-inverse" style="background-color: #0E52A5; border-bottom: 6px solid #DA0013;">
		<div class="navbar-header" style="width: 400px;">
			<a class="navbar-brand" href="{{ url('/') }}" style="width: 400px;">
			    <img src="{{ asset('assets/images/akiyama.png') }}" alt="" style="width: 35px; height: 35px; margin-top: -8px; float: left; margin-right: 10px;">
			    <h4 style="margin: 0px; margin-top: -3px" class="text-bold">アキヤマ石油産業㈱</h4>    
			</a>

			<ul class="nav navbar-nav pull-right visible-xs-block">
				<li><a data-toggle="collapse" data-target="#navbar-mobile"><i class="icon-tree5"></i></a></li>
			</ul>
		</div>

		<div class="navbar-collapse collapse" id="navbar-mobile">
			@if (auth()->check())
			<ul class="nav navbar-nav navbar-right">
				<li class="dropdown language-switch">
					<a class="dropdown-toggle" data-toggle="dropdown">
						@if(auth()->user()->language == 'en') 
							<img src="{{ asset('assets/images/flags/gb.png') }}" class="position-left" alt=""> English <span class="caret"></span>
						@else 
							<img src="{{ asset('assets/images/flags/jp.png') }}" class="position-left" alt=""> 日本語 <span class="caret"></span>
						@endif
					</a>

					<ul class="dropdown-menu">
						<li {!! auth()->user()->language === 'jp' ? 'class="active"' : '' !!}><a href="{{ url('language_jp') }}" class="espana"><img src="{{ asset('assets/images/flags/jp.png') }}" alt=""> 日本語</a></li>
						<li {!! auth()->user()->language === 'en' ? 'class="active"' : '' !!}><a href="{{ url('language_en') }}" class="english"><img src="{{ asset('assets/images/flags/gb.png') }}" alt=""> English</a></li>
					</ul>
				</li>

				<li class="dropdown dropdown-user">
					<a class="dropdown-toggle" data-toggle="dropdown">
						{{-- <img src="{{ asset('assets/images/placeholder.jpg') }}" alt=""> --}}
						<span>{{ auth()->user()->fullname }}</span>
						<i class="caret"></i>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						<li><a href="#" onclick="logoutSession(event)"><i class="icon-switch2"></i>@lang('trans.logout')</a></li>
					</ul>
				</li>
			</ul>
			@endif
		</div>
	</div>
	<!-- /main navbar -->

	<!-- Second navbar -->
	<div class="navbar navbar-default" id="navbar-second">
		<ul class="nav navbar-nav no-border visible-xs-block">
			<li><a class="text-center collapsed" data-toggle="collapse" data-target="#navbar-second-toggle"><i class="icon-menu7"></i></a></li>
		</ul>
		@if (auth()->check())
		<div class="navbar-collapse collapse" id="navbar-second-toggle">
			<ul class="nav navbar-nav">
				
				<li {!! request()->is('dashboard') || request()->is('home') || request()->is('/') ? 'class="active"' : '' !!}><a href="{{ url('dashboard') }}"><i class="icon-display4 position-left"></i> @lang('trans.dashboard')</a></li>

				@if (auth()->user()->role == 1)
				<li {!! request()->is('bs_stock') ? 'class="active"' : '' !!}><a href="{{ url('bs_stock') }}"><i class="icon-stack3 position-left"></i> @lang('trans.bs_stock')</a></li>
				<li {!! request()->is('shift') ? 'class="active"' : '' !!}><a href="{{ url('shift') }}"><i class="icon-file-text position-left"></i> @lang('trans.shift')</a></li>
				@endif

				<li {!! request()->is('stock') || request()->is('close_book') ? 'class="active"' : '' !!}>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="icon-cube position-left"></i>@lang('trans.stock')<span class="caret"></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li {!! request()->is('stock') ? 'class="active"' : '' !!}><a href="{{ url('stock') }}"><i class="icon-drawer-out"></i>@lang('trans.stock_out')</a></li>
						<li {!! request()->is('close_book') ? 'class="active"' : '' !!}><a href="{{ url('close_book') }}"><i class="icon-drawer3"></i>@lang('trans.close_book')</a></li>
					</ul>
				</li>

				@if (auth()->user()->role == 2)
				<li {!! request()->is('shift') || request()->is('shift_dipinjam') ? 'class="active"' : '' !!}>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="icon-file-empty position-left"></i>@lang('trans.shift')<span class="caret"></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li {!! request()->is('shift') ? 'class="active"' : '' !!}><a href="{{ url('shift') }}"><i class="icon-file-plus"></i>@lang('trans.shift')</a></li>
						<li {!! request()->is('shift_dipinjam') ? 'class="active"' : '' !!}><a href="{{ url('shift_dipinjam') }}"><i class="icon-file-check"></i>@lang('trans.shift_dipinjam')</a></li>
					</ul>
				</li>
				@endif

				@if (auth()->user()->role == 1)
				<li {!! request()->is('new_report_store') || request()->is('new_report_all') || request()->is('report_dealer') ? 'class="active"' : '' !!}>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="icon-book3 position-left"></i>@lang('trans.report')<span class="caret"></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li {!! request()->is('new_report_store') ? 'class="active"' : '' !!}><a href="{{ url('new_report_store') }}"><i class="icon-file-empty"></i>@lang('trans.per_store')</a></li>
						<li {!! request()->is('new_report_all') ? 'class="active"' : '' !!}><a href="{{ url('new_report_all') }}"><i class=" icon-files-empty"></i>@lang('trans.all')</a></li>
						<li {!! request()->is('report_dealer') ? 'class="active"' : '' !!}><a href="{{ url('report_dealer') }}"><i class=" icon-file-text2"></i>@lang('trans.report_dealer')</a></li>
					</ul>
				</li>
				@endif

				@if (auth()->user()->role == 1)
				<li {!! request()->is('store') || request()->is('store_group') || request()->is('stock_master') || request()->is('unregister') ? 'class="active"' : '' !!}>
					<a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
						<i class="icon-database position-left"></i>@lang('trans.master_data')<span class="caret"></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li {!! request()->is('stock_master') ? 'class="active"' : '' !!}><a href="{{ url('stock_master') }}"><i class="icon-cube3 position-left"></i> @lang('trans.stock_master')</a></li>
						<li {!! request()->is('store_group') ? 'class="active"' : '' !!}><a href="{{ url('store_group') }}"><i class=" icon-make-group"></i>@lang('trans.store_group')</a></li>
						<li {!! request()->is('store') ? 'class="active"' : '' !!}><a href="{{ url('store') }}"><i class="icon-store"></i>@lang('trans.store')</a></li>
						<li {!! request()->is('unregister') ? 'class="active"' : '' !!}><a href="{{ url('unregister') }}"><i class="icon-barcode2"></i>@lang('trans.unregister')</a></li>
					</ul>
				</li>
				@endif
				
			</ul>

			<ul class="nav navbar-nav navbar-right">
				
				@php
					if (auth()->user()->role == 2) {
						$notifications = App\Shift::select('shifts.*', 'from_store.name as from_store', 'to_store.name AS to_store', 'stock_masters.brand', 'stock_masters.type', 'stock_masters.size')
							                        ->join('stores AS from_store', 'from_store.code', '=', 'shifts.from_store')
							                        ->join('stores AS to_store', 'to_store.code', '=', 'shifts.to_store')
							                        ->join('stock_masters', 'stock_masters.jan_code', '=', 'shifts.jan_code')
							                        ->where('shifts.to_store', auth()->user()->store_code)
							                        ->where('status', 'pending')
							                        ->get();
					} else {
						$notifications = App\Shift::select('shifts.*', 'from_store.name as from_store', 'to_store.name AS to_store', 'stock_masters.brand', 'stock_masters.type', 'stock_masters.size')
							                        ->join('stores AS from_store', 'from_store.code', '=', 'shifts.from_store')
							                        ->join('stores AS to_store', 'to_store.code', '=', 'shifts.to_store')
							                        ->join('stock_masters', 'stock_masters.jan_code', '=', 'shifts.jan_code')
							                        ->where('status', 'done')
							                        ->where('read', 0)
							                        ->get();

					}
					
					$results = App\Shift::select('shifts.*', 'from_store.name as from_store', 'to_store.name AS to_store', 'stock_masters.brand', 'stock_masters.type', 'stock_masters.size')
						                        ->join('stores AS from_store', 'from_store.code', '=', 'shifts.from_store')
						                        ->join('stores AS to_store', 'to_store.code', '=', 'shifts.to_store')
						                        ->join('stock_masters', 'stock_masters.jan_code', '=', 'shifts.jan_code')
						                        ->where('shifts.from_store', auth()->user()->store_code)
						                        ->whereIn('status', ['fail', 'done'])
						                        ->where('read_store', 0)
						                        ->get();

				@endphp 

				<li class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-bubbles4"></i>
						@if ($notifications->count() + $results->count() != 0) <span class="badge bg-warning-400">{{ $notifications->count() + $results->count() }}</span> @endif
					</a>
					<div class="dropdown-menu dropdown-content width-350">
						@if ($notifications->isEmpty() && $results->isEmpty())
						<div class="dropdown-content-heading">
							@lang('trans.no_notification')
						</div>
						@else
						<div class="dropdown-content-heading">
							@lang('trans.notifications')
						</div>
						<ul class="media-list dropdown-content-body">
							@foreach ($notifications as $notification)
							<li class="media">
								<div class="media-body">
									<span class="media-heading text-primary">
										@if (auth()->user()->role == 2)
											<a href="{{ url('shift_dipinjam') }}"><i class="icon-arrow-right7 mr-10 ml-10 position-left"></i><span class="text-semibold">{{ $notification->from_store }}</span> </a>
										@else
											<a href="{{ url('shift') }}">{{ $notification->to_store }}<i class="icon-arrow-right7 mr-10 ml-10 position-left"></i><span class="text-semibold">{{ $notification->from_store }}</span> </a>
										@endif
										@if (auth()->user()->role == 1)
										<span class="badge bg-warning-400 pull-right ml-10" style="cursor: pointer;" onclick="clearNotif( {{ $notification->id }} )"><i class="icon-x"></i></span>
										@endif
										<span class="media-annotation pull-right">{{ date('Y年m月d日', strtotime($notification->created_at)) }}</span>
									</span>
									<span class="text-muted">
										{{ $notification->brand }} {{ $notification->size }} ({{ $notification->amount }})
									</span>
								</div>
							</li>
							@endforeach
							@foreach ($results as $result)
							<li class="media">
								<div class="media-body">
									<span class="media-heading text-primary">
										<span {!! $result->status == 'done' ? 'class="label bg-primary"' : 'class="label bg-danger"' !!}>{{ $result->status }}</span>
										<a href="{{ url('shift') }}">{{ $result->to_store }}<i class="icon-arrow-right7 mr-10 ml-10 position-left"></i><span class="text-semibold">{{ $result->from_store }}</span> </a>
										<span class="badge bg-warning-400 pull-right ml-10" style="cursor: pointer;" onclick="clearNotif( {{ $result->id }} )"><i class="icon-x"></i></span>
										{{-- <span class="media-annotation pull-right">{{ date('Y年m月d日', strtotime($result->updated_at)) }}</span> --}}
									</span>
									<span class="text-muted">
										{{ $result->brand }} {{ $result->size }} ({{ $result->amount }})
									</span>
								</div>
							</li>
							@endforeach
						</ul>
						@endif
					</div>
				</li>

				@if (auth()->user()->role == 1)
				<li {!! request()->is('translate') || request()->is('history') ? 'class="active"' : '' !!} class="dropdown">
					<a href="#" class="dropdown-toggle" data-toggle="dropdown">
						<i class="icon-cog3"></i>
						<span class="visible-xs-inline-block position-right">Share</span>
						<span class="caret"></span>
					</a>

					<ul class="dropdown-menu dropdown-menu-right">
						<li {!! request()->is('translate') ? 'class="active"' : '' !!}><a href="{{ url('translate') }}"><i class="icon-sphere"></i> @lang('trans.language')</a></li>
						{{-- <li {!! request()->is('history') ? 'class="active"' : '' !!}><a href="{{ url('history') }}"><i class="icon-history"></i> @lang('trans.history')</a></li> --}}
						<li><a href="#" data-target="#modal-change-password" data-toggle="modal"><i class="icon-lock2"></i> @lang('trans.change_password')</a></li>
						{{-- <li><a href="#" data-target="#modal-reset" data-toggle="modal"><i class="icon-trash"></i> @lang('trans.reset_data')</a></li> --}}
					</ul>
				</li>
				@endif
			</ul>
		</div>
		@endif
	</div>
	<!-- /second navbar -->

	@section('header')
	<!-- Page header -->
	<div class="page-header">
		<div class="page-header-content">
			<div class="page-title">
				<h4><a href="{{ url()->previous() }}"><i class="icon-arrow-left52 position-left"></i></a> <span class="text-semibold">@yield('header-title')</span></h4>
			</div>
		</div>
	</div>
	<!-- /page header -->
	@show

	<!-- Page container -->
	<div class="page-container">
		<!-- Page content -->
		<div class="page-content">
			<!-- Main content -->
			<div class="content-wrapper">
				@yield('content')
			</div>
			<!-- /main content -->
		</div>
		<!-- /page content -->
	</div>
	<!-- /page container -->


	<!-- Footer -->
	<div class="footer text-muted">
		&copy; 2018 <a href="#">アキヤマグループ倉庫管理</a> by <a href="http://sumroch.com" target="_blank">Sumroch</a>
	</div>
	<!-- /footer -->
	@stack('vue')
</body>
</html>
