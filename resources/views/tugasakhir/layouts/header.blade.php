<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Sentir, Responsive admin and dashboard UI kits template">
    <meta name="keywords" content="admin,bootstrap,template,responsive admin,dashboard template,web apps template">
    <meta name="author" content="Ari Rusmanto, Isoh Design Studio, Warung Themes">
    <meta content="{{ csrf_token() }}" name="csrf-token">
    <title>SIMPRODI | Tugas Akhir @yield('tambahan')</title>


    <!-- BOOTSTRAP CSS (REQUIRED ALL PAGE)-->
    <link href="{{ asset('master/assets/css/bootstrap.min.css') }}" rel="stylesheet">

    <!-- PLUGINS CSS -->
    <link href="{{ asset('master/assets/plugins/weather-icon/css/weather-icons.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/prettify/prettify.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/magnific-popup/magnific-popup.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/owl-carousel/owl.carousel.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/owl-carousel/owl.theme.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/owl-carousel/owl.transitions.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/chosen/chosen.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/icheck/skins/all.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/datepicker/datepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/timepicker/bootstrap-timepicker.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/validator/bootstrapValidator.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/summernote/summernote.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/markdown/bootstrap-markdown.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/datatable/css/bootstrap.datatable.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/morris-chart/morris.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/c3-chart/c3.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/slider/slider.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/salvattore/salvattore.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/toastr/toastr.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/fullcalendar/fullcalendar/fullcalendar.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/plugins/fullcalendar/fullcalendar/fullcalendar.print.css') }}" rel="stylesheet"
        media="print">

    <!-- MAIN CSS (REQUIRED ALL PAGE)-->
    <link href="{{ asset('master/assets/plugins/font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('master/assets/css/style-responsive.css') }}" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap4-toggle@3.6.1/css/bootstrap4-toggle.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">



    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
