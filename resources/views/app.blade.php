
<!doctype html>
<html lang="zxx">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
         <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
        <meta name="description" content="" />
        <meta name="keywords" content="" />
        <meta name="author" content="NIT" />
        @yield('title')
        <link rel="apple-touch-icon" href="" />
        <link rel="shortcut icon" type="image/x-icon" href="" />
        <!-- Vendors Min CSS -->
        <link rel="stylesheet" href="{{asset('admin/assets/css/vendors.min.css')}}">
        <!-- Style CSS -->
        <link rel="stylesheet" href="{{asset('admin/assets/css/style.css')}}">
        <!-- Responsive CSS -->
        <link rel="stylesheet" href="{{asset('admin/assets/css/responsive.css')}}">

         @stack('css')
    </head>

    <body>

        @yield('contents')

        <!-- Vendors Min JS -->
        <script src="{{asset('admin/assets/js/vendors.min.js')}}"></script>

        
        <!-- jvectormap Min JS -->
        <script src="{{asset('admin/assets/js/jvectormap-1.2.2.min.js')}}"></script>
        <!-- jvectormap World Mil JS -->
        <script src="{{asset('admin/assets/js/jvectormap-world-mill-en.js')}}"></script>
        <!-- Custom JS -->
        <script src="{{asset('admin/assets/js/custom.js')}}"></script>

         @stack('js')
    </body>
</html>