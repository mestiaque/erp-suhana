<!DOCTYPE html>
<html lang="en-US">
    <head>
        
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta http-equiv="x-ua-compatible" content="ie=edge" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{csrf_token()}}" />
        @yield('title')
        <!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="" />
        @yield('SEO')

        <!-- Vendors Min CSS -->
         <link rel="stylesheet" href="{{asset('admin/assets/css/vendors.min.css')}}" />
         <!-- Style CSS -->
         <link rel="stylesheet" href="{{asset('admin/assets/css/style.css')}}" />
         <!-- Responsive CSS -->
         <link rel="stylesheet" href="{{asset('admin/assets/css/responsive.css')}}" />
        
        @stack('css')
    </head>
    
    <body>
        
        <!--Main Content Section Start-->
        @yield('contents')
        <!--Main Content Section End-->

        <!-- Vendors Min JS -->
         <script src="{{asset('admin/assets/js/vendors.min.js')}}"></script>
         <!-- Custom JS -->
         <script src="{{asset('admin/assets/js/custom.js')}}"></script>

        <script>
            $(document).ready(function(){

            $("#division").on("change", function(){
                var id = $(this).val();
                  if(id==''){
                   $('#district').empty().append('<option value="">No District</option>');
                   $('#city').empty().append('<option value="">No City</option>');
                  }
                  var url ='{{url('geo/filter')}}' + '/'+id;
                  $.get(url,function(data){
                    $('#district').empty().append(data.geoData);
                    $('#city').empty().append('<option value="">No City</option>');
                  });   
            });

            $("#district").on("change", function(){
                var id = $(this).val();
                  if(id==''){
                   $('#city').empty().append('<option value="">No City</option>');
                  }
                  var url ='{{url('geo/filter')}}' + '/'+id;
                  $.get(url,function(data){
                    $('#city').empty().append(data.geoData);  
                  });   
            });
            
        });
        </script>
        
        @stack('js')
        
        
    </body>
</html>
