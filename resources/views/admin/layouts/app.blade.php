<!DOCTYPE html>
 <html class="loading" >
   <!-- BEGIN: Head-->
   <head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
      <!-- CSRF Token -->
     <meta name="csrf-token" content="{{ csrf_token() }}">
     <meta http-equiv="X-UA-Compatible" content="IE=edge" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
     <meta name="description" content="" />
     <meta name="keywords" content="" />
     <meta name="author" content="NIT" />
     @yield('title')
     <link rel="apple-touch-icon" href="{{asset(general()->favicon())}}" />
     <link rel="shortcut icon" type="image/x-icon" href="{{asset(general()->favicon())}}" />

     <link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i%7COpen+Sans:300,300i,400,400i,600,600i,700,700i" rel="stylesheet" />
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
     <!-- BEGIN: Vendor CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset('admin/assets/css/vendors.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset('admin/assets/css/style.css')}}" />
     <!-- END: Vendor CSS-->
     <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css">
	 <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/1.2.2/css/buttons.dataTables.min.css">
     <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <!-- BEGIN: Theme CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset('admin/assets/css/responsive.css')}}" />

     <link rel="stylesheet" type="text/css" href="{{asset('admin/assets/css/custom.css')}}" />

     <!-- END: Custom CSS-->
     <meta http-equiv='cache-control' content='no-cache'>
     <meta http-equiv='expires' content='0'>
     <meta http-equiv='pragma' content='no-cache'>
     <style>
        .select2-container--default .select2-search--inline .select2-search__field {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            line-height: 35px;
        }

        .select2-container .select2-selection--single {
            height: 35px;
        }

         .card .card-header {
            border-bottom: 1px solid #e3ebf3;
            padding-bottom: 15px;
        }
        .checkbox {
            display: inline-block;
        }

        .refActionBtn {
            width: 40px;
            text-align: center;
        }

        .metisMenu .mm-collapse:not(.mm-show) {
            display: none;
        }

        .refActionBtn span {
            padding: 8px 10px;
            display: block;
            background: #dddada;
            cursor: pointer;
        }

        .searchRef {
            position: relative;
        }

        .reffSearchResult {
            position: absolute;
            width: 100%;
            background: white;
            border: 1px solid #ced4da;
            border-top: 0;
            height: 200px;
            overflow: auto;
            display: none;
            z-index: 9;
        }

        .reffSearchResult ul li {
            list-style: none;
            padding: 5px;
            border-bottom: 1px solid #e3d9d9;
        }
        table.table thead {
          background: #7c7c7c;
          color: #fff !important;
        }

       .badge-warning
        {
            color: white;
            background-color:#d9a50c;
        }

        table.table a { color: #000; }
        .badge-warning { color: #000; background-color: #d9a50c4d; }
        .badge-success { color: #035415; background-color: #17e64642; }
        .badge-secondary { color: #fff; background-color: #6c757d; }
        .badge-danger { color: #fff; background-color: #dc3545; }

     </style>

     @stack('css')
   </head>
   <!-- END: Head-->

   <!-- BEGIN: Body-->
   <body>


    @include('admin.layouts.sidebar')

     <!-- BEGIN: Content-->
     <div class="main-content d-flex flex-column">
        @include('admin.layouts.header')
        @yield('contents')
        @include('admin.layouts.footer')
     </div>
     <!-- END: Content-->

     <!-- BEGIN: Vendor JS-->
     <script src="{{asset('admin/assets/js/vendors.min.js')}}"></script>
     <!-- BEGIN Vendor JS-->

     <script src="{{asset('admin/assets/js/jvectormap-1.2.2.min.js')}}"></script>
     <script src="{{asset('admin/assets/js/jvectormap-world-mill-en.js')}}"></script>

    <script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/2.5.0/jszip.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/bpampuch/pdfmake@0.1.18/build/pdfmake.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/bpampuch/pdfmake@0.1.18/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.2.2/js/buttons.print.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/printThis/1.15.0/printThis.min.js" integrity="sha512-d5Jr3NflEZmFDdFHZtxeJtBzk0eB+kkRXWFQqEc1EKmolXjHm2IKCA7kTvXBNjIYzjXfD5XzIjaaErpkZHCkBg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
     <script src="{{asset('admin/assets/js/inword.js')}}"></script>
     <script src="{{asset('admin/assets/js/custom.js')}}"></script>
     <!-- END: Page JS-->


     <script>
      $(document).ready(function(){


        $(document).on('click', '.searchRef .input-group input:not([readonly])', function() {
            $(this).siblings(".reffSearchResult").remove();
            $(this).closest('.searchRef').find(".reffSearchResult").show();
        });



        $(document).on('keyup', '.searchRef .input-group input', function() {

            var that =$(this);
            var search =$(this).val();
            var url ="";
            var total =10;
            if(url){
                $.ajax({
                  url:url,
                  dataType: 'json',
                  cache: false,
                  data: {'reff_search':search},
                  success : function(data){

                    total = data.total;

                    if (total == 0) {
                        that.closest('.searchRef').find(".refActionBtn").empty().append('<span class="add" style="background: #13bb37;color: white;"><i class="bx bx-plus"></i></span>');
                    } else {
                        that.closest('.searchRef').find(".refActionBtn").empty().append('<span><i class="bx bx-search"></i></span>');
                    }

                    $('.reffSearchResult').empty().append(data.view);

                  },error: function () {
                      alert('error');

                    }
                });
            }

        });

        $(document).on('click', '.searchRef .reffSearchResult ul li .reffAdd', function() {
            var title =$(this).data('name');
            $(this).closest('.searchRef').find(".input-group input").prop('readonly', true).val(title);
            $(this).closest('.searchRef').find(".refActionBtn").empty().append('<span class="remove" style="background: #ff6a6a;color: white;"><i class="bx bx-x"></i></span>');
            $(this).closest('.searchRef').find(".reffSearchResult").hide();
        });

        $(document).on('click', '.searchRef .refActionBtn .add', function() {
            $(this).closest('.searchRef').find(".input-group input").prop('readonly', true);
            $(this).closest('.searchRef').find(".refActionBtn").empty().append('<span class="remove" style="background: #ff6a6a;color: white;"><i class="bx bx-x"></i></span>');
            $(this).closest('.searchRef').find(".reffSearchResult").hide();
        });

        $(document).on('click', '.searchRef .refActionBtn .remove', function() {
            $(this).closest('.searchRef').find(".input-group input").prop('readonly', false);
            $(this).closest('.searchRef').find(".refActionBtn").empty().append('<span><i class="bx bx-search"></i></span>');
        });

        $(document).on('click', function(event) {
            if (!$(event.target).closest('.searchRef').length) {
                $('.reffSearchResult').hide();
            }
        });

        $('#PrintAction').on("click", function () {
            $('.PrintAreaContact').printThis();
          });

        $('#PrintAction2').on("click", function () {
            $('.PrintAreaContact2').printThis();
          });

         $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });

            $(document).on('click','.mediaDelete',function(e){
              e.preventDefault();
              var url =$(this).attr('href');
              if(confirm("Are you sure you want to delete this?")){
                $.ajax({
                  url : url,
                  type:'GET',
                  cache: false,
                  contentType: false,
                  dataType: 'json',
                  beforeSend: function()
                  {

                  },
                  complete: function()
                  {

                  },
                  }).done(function (data) {

                     location.reload(true);

                  }).fail(function () {
                      alert('fail');
                  });

              }else{
                  return false;
              }

            });

            $(document).on('click','.reloadPage',function(){

                location.reload();
                return true;

            });
          // When a file is selected, update the image preview
            $(document).on('change','.account-upload', function (e) {

                var input = e.target;
                var profiImage ='.'+$(this).data('imageshow');
                if (input.files && input.files[0]) {
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        $(profiImage).attr('src', e.target.result);
                    };

                    reader.readAsDataURL(input.files[0]);
                }
            });
          $(document).on('click','.showPassword',function(){
                $(this).toggleClass('active-show');
                if ($(this).hasClass('active-show')) {
                    $('input.password').prop('type','text');
                    $(this).empty().append('<i class="bx bx-show"></i>');
                } else {
                    $('input.password').prop('type','password');
                    $(this).empty().append('<i class="bx bx-hide"></i>');
                }
            });


          $("#division").on("change", function(){
                var id = $(this).val();
                  if(id==''){
                   $('#district').empty().append('<option value="">Select District</option>');
                   $('#city').empty().append('<option value="">Select Thana</option>');
                  }
                  var url ='{{url('geo/filter')}}' + '/'+id;
                  $.get(url,function(data){
                    $('#district').empty().append(data.geoData);
                    $('#city').empty().append('<option value="">Select Thana</option>');
                  });
            });

            $("#district").on("change", function(){
                var id = $(this).val();
                  if(id==''){
                   $('#city').empty().append('<option value="">Select Thana</option>');
                  }
                  var url ='{{url('geo/filter')}}' + '/'+id;
                  $.get(url,function(data){
                    $('#city').empty().append(data.geoData);
                  });
            });

      });
    </script>

    <script type="text/javascript">
      ///Check Box Select With Count show

          $(function() {
            $('.checkCounter').text('0');
            var generallen = $("input[name='checkid[]']:checked").length;
            if (generallen > 0) {
              $(".checkCounter").text('(' + generallen + ')');
            } else {
              $(".checkCounter").text(' ');
            }

          })

          function updateCounter() {
            var len = $("input[name='checkid[]']:checked").length;
            if (len > 0) {
              $(".checkCounter").text('(' + len + ')');
            } else {
              $(".checkCounter").text(' ');
            }
          }

          $("input:checkbox").on("change", function() {
            updateCounter();
          });


        $(document).ready(function(){
          $('#checkall').click(function() {
              var checked = $(this).prop('checked');
              $('input:checkbox').prop('checked', checked);
              updateCounter();
            });
        });

        ///Check Box Select With Count show
      </script>


      <script>
        document.addEventListener('focus', function(e) {
            const target = e.target;

            // Only act on number inputs that are not disabled or readonly
            if (
                target.tagName === 'INPUT' &&
                target.type === 'number' &&
                !target.disabled &&
                !target.readOnly
            ) {
                // Clear if the current value is exactly 0
                if (target.value === '0' || target.value === 0) {
                    target.value = '';
                }
            }
        }, true); // capture phase so it works on dynamically added inputs


        document.addEventListener('blur', function(e) {
            const target = e.target;

            // Only act on number inputs that are not disabled or readonly
            if (
                target.tagName === 'INPUT' &&
                target.type === 'number' &&
                !target.disabled &&
                !target.readOnly
            ) {
                // Restore 0 if the input is empty
                if (target.value === '') {
                    target.value = 0;
                }
            }
        }, true); // use capture so it works on dynamically added inputs
      </script>

      @stack('js')
   </body>
   <!-- END: Body-->
 </html>
