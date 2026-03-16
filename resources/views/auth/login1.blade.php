<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="Sentir, Responsive admin and dashboard UI kits template">
		<meta name="keywords" content="admin,bootstrap,template,responsive admin,dashboard template,web apps template">
		<meta name="author" content="Ari Rusmanto, Isoh Design Studio, Warung Themes">
		<title>Login | SIMPRODI - Tugas Akhir</title>
 
		<!-- BOOTSTRAP CSS (REQUIRED ALL PAGE)-->
		<link href="{{ asset('master/assets/css/bootstrap.min.css')}}" rel="stylesheet">
		
		<!-- PLUGINS CSS -->
		<link href="{{ asset('master/assets/plugins/weather-icon/css/weather-icons.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/prettify/prettify.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/magnific-popup/magnific-popup.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/owl-carousel/owl.carousel.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/owl-carousel/owl.theme.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/owl-carousel/owl.transitions.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/chosen/chosen.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/icheck/skins/all.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/datepicker/datepicker.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/timepicker/bootstrap-timepicker.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/validator/bootstrapValidator.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/summernote/summernote.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/markdown/bootstrap-markdown.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/datatable/css/bootstrap.datatable.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/morris-chart/morris.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/c3-chart/c3.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/slider/slider.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/salvattore/salvattore.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/toastr/toastr.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/fullcalendar/fullcalendar/fullcalendar.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/plugins/fullcalendar/fullcalendar/fullcalendar.print.css')}}" rel="stylesheet" media="print">

        <!-- MAIN CSS (REQUIRED ALL PAGE)-->
        <link href="{{ asset('master/assets/plugins/font-awesome/css/font-awesome.min.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/css/style.css')}}" rel="stylesheet">
        <link href="{{ asset('master/assets/css/style-responsive.css')}}" rel="stylesheet">
 
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
		<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
		<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
		<![endif]-->
	</head>
 
	<body class="login tooltips">
	
		
		
		
		<!--
		===========================================================
		BEGIN PAGE
		===========================================================
		-->
		<div class="login-header text-center">
			<img src="{{ asset('master/assets/img/logo_primary.png')}}" class="logo" alt="Logo">
		</div>
		<div class="login-wrapper">
			
			<form method="POST" action="{{ route('login') }}" aria-label="{{ __('Login') }}">
                        @csrf
				<div class="form-group has-feedback lg left-feedback no-label">
				  <input type="text" class="form-control no-border input-lg rounded" placeholder="Enter username" name="email" autofocus>
				  <span class="fa fa-user form-control-feedback"></span>
				</div>
				<div class="form-group has-feedback lg left-feedback no-label">
				  <input type="password" class="form-control no-border input-lg rounded" placeholder="Enter password" name="password">
				  <span class="fa fa-unlock-alt form-control-feedback"></span>
				</div>
				<div class="form-group">
				  <div class="checkbox">
					<label>
					  <input type="checkbox" class="i-yellow-flat"> Remember me
					</label>
				  </div>
				</div>
				<div class="form-group">
					<button type="submit" class="btn btn-warning btn-lg btn-perspective btn-block">LOGIN</button>
				</div>
			</form>
			<p class="text-center"><strong><a href="{{ route('password.request') }}">Forgot your password?</a></strong></p>
			<p class="text-center">or</p>
			<p class="text-center"><strong><a href="{{ route('register') }}" >Create new account</a></strong></p>
		</div><!-- /.login-wrapper -->
		<!--
		===========================================================
		END PAGE
		===========================================================
		-->
		
		<!--
		===========================================================
		Placed at the end of the document so the pages load faster
		===========================================================
		-->
		<!-- MAIN JAVASRCIPT (REQUIRED ALL PAGE)-->
		<script src="{{ asset('master/assets/js/jquery.min.js')}}"></script>
        <script src="{{ asset('master/assets/js/bootstrap.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/retina/retina.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/nicescroll/jquery.nicescroll.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/slimscroll/jquery.slimscroll.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/backstretch/jquery.backstretch.min.js')}}"></script>

        <!-- PLUGINS -->
        <script src="{{ asset('master/assets/plugins/skycons/skycons.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/prettify/prettify.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/magnific-popup/jquery.magnific-popup.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/owl-carousel/owl.carousel.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/chosen/chosen.jquery.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/icheck/icheck.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/datepicker/bootstrap-datepicker.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/timepicker/bootstrap-timepicker.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/mask/jquery.mask.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/validator/bootstrapValidator.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/datatable/js/jquery.dataTables.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/datatable/js/bootstrap.datatable.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/summernote/summernote.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/markdown/markdown.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/markdown/to-markdown.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/markdown/bootstrap-markdown.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/slider/bootstrap-slider.js')}}"></script>

        <script src="{{ asset('master/assets/plugins/toastr/toastr.js')}}"></script>
		
		<!-- FULL CALENDAR JS -->
        <script src="{{ asset('master/assets/plugins/fullcalendar/lib/jquery-ui.custom.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/fullcalendar/fullcalendar/fullcalendar.min.js')}}"></script>
        <script src="{{ asset('master/assets/js/full-calendar.js')}}"></script>

        <!-- EASY PIE CHART JS -->
        <script src="{{ asset('master/assets/plugins/easypie-chart/easypiechart.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/easypie-chart/jquery.easypiechart.min.js')}}"></script>

        <!-- KNOB JS -->
        <!--[if IE]>
        <script type="text/javascript" src="{{ asset('master/assets/plugins/jquery-knob/excanvas.js')}}"></script>
        <![endif]-->
        <script src="{{ asset('master/assets/plugins/jquery-knob/jquery.knob.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/jquery-knob/knob.js')}}"></script>

        <!-- FLOT CHART JS -->
        <script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.tooltip.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.resize.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.selection.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.stack.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/flot-chart/jquery.flot.time.js')}}"></script>

        <!-- MORRIS JS -->
        <script src="{{ asset('master/assets/plugins/morris-chart/raphael.min.js')}}"></script>
        <script src="{{ asset('master/assets/plugins/morris-chart/morris.min.js')}}"></script>

        <!-- C3 JS -->
        <script src="{{ asset('master/assets/plugins/c3-chart/d3.v3.min.js')}}" charset="utf-8"></script>
        <script src="{{ asset('master/assets/plugins/c3-chart/c3.min.js')}}"></script>

        <!-- MAIN APPS JS -->
        <script src="{{ asset('master/assets/js/apps.js')}}"></script>

        </body>
        </html>