<!DOCTYPE html>
<html lang="en">
<!-- [Head] start -->

<head>
  <title>@yield('title') | WebFlare ERP</title>
  <!-- [Meta] -->
  <meta charset="utf-8">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="description"
    content="Mantis is made using Bootstrap 5 design framework. Download the free admin template & use it for your project.">
  <meta name="keywords"
    content="Mantis, Dashboard UI Kit, Bootstrap 5, Admin Template, Admin Dashboard, CRM, CMS, Bootstrap Admin Template">
  <meta name="author" content="CodedThemes">

  <!-- [Favicon] icon -->
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Public+Sans:wght@300;400;500;600;700&display=swap"
    id="main-font-link">
  <!-- [Bootstrap Icons] https://icons.getbootstrap.com/ -->
  <link rel="stylesheet" href="{{ asset('assets/css/plugins/dataTables.bootstrap5.min.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/css/plugins/buttons.bootstrap5.min.css') }}">
  <!-- Swal2 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.min.css">
  <!-- Slim Select -->
  <script src="https://cdn.jsdelivr.net/npm/slim-select@latest/dist/slimselect.js"></script>
  <link href="https://cdn.jsdelivr.net/npm/slim-select@latest/dist/slimselect.css" rel="stylesheet">
  
  <!-- [Tabler Icons] https://tablericons.com -->
  <link rel="stylesheet" href="{{ asset('assets/fonts/tabler-icons.min.css') }}">
  <!-- [Feather Icons] https://feathericons.com -->
  <link rel="stylesheet" href="{{ asset('assets/fonts/feather.css') }}">
  <!-- [Font Awesome Icons] https://fontawesome.com/icons -->
  <link rel="stylesheet" href="{{ asset('assets/fonts/fontawesome.css') }}">
  <!-- [Material Icons] https://fonts.google.com/icons -->
  <link rel="stylesheet" href="{{ asset('assets/fonts/material.css') }}">
  <!-- [Template CSS Files] -->
  <link rel="stylesheet" href="{{ asset('assets/css/style.css') }}" id="main-style-link">
  <link rel="stylesheet" href="{{ asset('assets/css/style-preset.css') }}">
  @stack('styles')

</head>
<!-- [Head] end -->

<body>
  @include('admin.layouts.sidebar')
  @include('admin.layouts.header')
  @yield('content')
  @include('admin.layouts.footer')


  <script src="{{ asset('assets/js/plugins/apexcharts.min.js') }}"></script>
  <script src="{{ asset('assets/js/pages/dashboard-default.js') }}"></script>
  <script src="{{ asset('assets/js/admin/helper.js') }}"></script>
  <!-- Required Js -->
  <script src="{{ asset('assets/js/plugins/popper.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/simplebar.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/bootstrap.min.js') }}"></script>
  <script src="{{ asset('assets/js/fonts/custom-font.js') }}"></script>
  <script src="{{ asset('assets/js/pcoded.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/feather.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
  <script src="{{ asset('assets/js/plugins/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/dataTables.bootstrap5.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/buttons.colVis.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/buttons.print.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/pdfmake.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/jszip.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/dataTables.buttons.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/vfs_fonts.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/buttons.html5.min.js') }}"></script>
  <script src="{{ asset('assets/js/plugins/buttons.bootstrap5.min.js') }}"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.26.25/dist/sweetalert2.all.min.js"></script>

  <script>layout_change('light');</script>

  <script>change_box_container('false');</script>

  <script>layout_rtl_change('false');</script>

  <script>preset_change("preset-1");</script>

  <script>font_change("Public-Sans");</script>

  @stack('scripts')

</body>

</html>