@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Delivery Plan')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="flex-grow-1">
    

<!-- Start -->
<div class="card mb-30">
    <div class="card-header d-flex justify-content-between align-items-center">
         <h3>Delivery Plan</h3>
         <div class="dropdown">
             <a href="javascript:void(0)" id="PrintAction22" class="btn-custom yellow">
                 <i class="bx bx-printer"></i> Print
             </a>
         </div>
    </div>
    <div class="card-body">
        @include(adminTheme().'alerts')
        
        <div class="PrintAreaContact">
            <style>
                .profileTable {
                    border-collapse: collapse;
                    width:100%;
                }
                .profileTable tr th,.profileTable tr td{
                    padding:5px;
                    border: 1px solid #565656;
                }
            </style>
            <div class="">
                <div style="text-align: center;">
                    <img src="{{asset(general()->logo())}}"  style="max-height:60px;">
                    <h2>Delivery Plan</h2>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table profileTable">
                    <thead>
                        <tr>
                            <th style="min-width: 120px;width:120px">Order No</th>
                            <th style="min-width: 150px;">Details Spec</th>
                            <th style="min-width: 100px;width:100px;">Qty</th>
                            <th style="min-width: 120px;width:120px;">Delivery Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $i=>$item)
                        <tr>
                            <td>
                                {{$item->order->invoice}}
                            </td>
                            <td>
                                {{$item->description}}
                            </td>
                            <td>{{$item->quantity}}</td>
                            <td>{{Carbon\Carbon::parse($item->delivered_at)->format('d.m.Y')}}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>
</div>



@endsection 
@push('js') 

<script>
    $(document).ready(function(){
        $('#PrintAction22').on("click", function () {
            $('.PrintAreaContact').printThis({
              	importCSS: false,
              	loadCSS: "https://cdnjs.cloudflare.com/ajax/libs/bootstrap/4.6.2/css/bootstrap-grid.min.css",
            });
        });
    });
</script>

@endpush