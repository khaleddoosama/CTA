<!DOCTYPE html>
<html lang="en" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="keywords" content="HTML5 Template" />
    <meta name="description" content="Webmin - Bootstrap 4 & Angular 5 Admin Dashboard Template" />
    <meta name="author" content="potenzaglobalsolutions.com" />
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <title>@yield('title')</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('asset/images/favicon.ico') }}" />
    <!-- Font -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css?family=Poppins:200,300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900">

    <!-- css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('asset/css/style.css') }}" />

</head>

<body>

    <div class="wrapper">

        <!--================================= preloader -->

        <div id="pre-loader">
            <img src="{{ asset('asset/images/pre-loader/loader-01.svg') }}" alt="">
        </div>

        <!--================================= preloader -->


        <!--================================= header start-->

        @include('layouts.navbar')
        <!--================================= header End-->

        <!--================================= Main content -->

        <div class="container-fluid">
            <div class="row">
                <!-- Left Sidebar start-->
                @include('layouts.sidebar')

                <!-- Left Sidebar End-->

                <!--================================ wrapper -->

                <div class="content-wrapper">
                    <div class="page-title">
                        <div class="row">
                            <div class="col-sm-6">
                                <h4 class="mb-0">@yield('title') </h4>
                            </div>
                            <div class="col-sm-6">
                                <ol class="float-left pt-0 pr-0 breadcrumb float-sm-right">
                                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"
                                            class="default-color">الرئيسيه</a></li>
                                    <li class="breadcrumb-item active">@yield('title')</li>

                                </ol>
                            </div>
                        </div>
                    </div>
                    @yield('content')
                    <!--================================= footer -->

                    @include('layouts.footer')
                </div><!-- main content wrapper end-->
            </div>
        </div>
    </div>

    <!--================================= footer -->



    <!--================================= jquery -->

    <!-- jquery -->
    {{-- <script src="js/jquery-3.3.1.min.js"></script> --}}
    <script src="{{ asset('asset/js/jquery-3.3.1.min.js') }}"></script>

    <!-- plugins-jquery -->
    <script src="{{ asset('asset/js/plugins-jquery.js') }}"></script>
    <!-- plugin_path -->
    <script>
        var plugin_path = 'js/';
    </script>

    <!-- chart -->
    <script src="{{ asset('asset/js/chart-init.js') }}"></script>
    <!-- calendar -->
    <script src="{{ asset('asset/js/calendar.init.js') }}"></script>
    <!-- charts sparkline -->
    <script src="{{ asset('asset/js/sparkline.init.js') }}"></script>
    <!-- charts morris -->
    <script src="{{ asset('asset/js/morris.init.js') }}"></script>
    <!-- datepicker -->
    <script src="{{ asset('asset/js/datepicker.js') }}"></script>
    <!-- sweetalert2 -->
    <script src="{{ asset('asset/js/sweetalert2.js') }}"></script>
    <!-- toastr -->
    <script src="{{ asset('asset/js/toastr.js') }}"></script>
    <!-- validation -->
    <script src="{{ asset('asset/js/validation.js') }}"></script>
    <!-- lobilist -->
    <script src="{{ asset('asset/js/lobilist.js') }}"></script>
    <!-- custom -->
    <script src="{{ asset('asset/js/custom.js') }}"></script>
</body>

</html>
