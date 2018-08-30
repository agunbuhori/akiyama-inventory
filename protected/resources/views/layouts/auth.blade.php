
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
	<!-- /global stylesheets -->

	<!-- Core JS files -->
	<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/pace.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/core/libraries/jquery.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/core/libraries/bootstrap.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/loaders/blockui.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/nicescroll.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/ui/drilldown.js') }}"></script>
	<script type="text/javascript" src="{{ asset('assets/js/plugins/forms/styling/uniform.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/axios.min.js') }}"></script>
	<script type="text/javascript" src="{{ asset('js/vue.js') }}"></script>
	<!-- /core JS files -->

	<!-- Theme JS files -->
	<script type="text/javascript" src="{{ asset('assets/js/core/app.js') }}"></script>
	<script type="text/javascript">
		const URL = "{{ url('/') }}/",
		CURRENT = "{{ request()->url() }}",
		TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

		$(function () {
			$(".styled, .multiselect-container input").uniform({
				radioClass: 'choice'
			});
		});
	</script>
	<!-- /theme JS files -->

</head>

<body class="login-container">

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
	</div>
	<!-- /main navbar -->


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
	<div class="footer text-muted text-center">
		&copy; 2018 <a href="#">アキヤマグループ倉庫管理</a> by <a href="http://sumroch.com" target="_blank">Sumroch</a>
	</div>
	<!-- /footer -->
	@stack('vue')
</body>
</html>
