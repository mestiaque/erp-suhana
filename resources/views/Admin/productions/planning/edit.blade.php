@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Production Planning Edit') }}</title>
@endsection

@push('css')
<style>
    .search-result-box{position:absolute;z-index:9;width:100%;background:#fff;border:1px solid #ddd;display:none;}
    .search-result-box li{padding:6px 10px;cursor:pointer;}
    .searchlist ul {list-style:none;margin:0;padding:0;}
    .searchlist ul li{border-top:1px solid #dbd6d6;padding:5px 10px;cursor:pointer;}
    .searchlist ul li:hover{background:#f2f2f2;}
    .searchGrid {position:relative;}
    .itemSearch {height:200px;overflow:auto;position:absolute;width:100%;background:white;border:1px solid #dfdfdf;border-top:0;display:none;}
    .table-striped tr th{padding:3px;}
    .table-striped tr td{padding:3px;}
    .lineCheck {
        border: 1px solid #bebebe;
        padding: 5px 10px;
        border-radius: 3px;
        margin: 0;
        cursor: pointer;
        margin: 3px 1px;
    }
</style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Planning</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.samples') }}">Planning</a></li>
            <li class="item">Edit Planning</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.productionPlanningAction', ['update', $plan->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-header">
                                <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">1.Cutting Section</span></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;">Starting Date</th>
                                            <td style="padding:1px;">
                                                <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->cutting_start?Carbon\Carbon::parse($plan->cutting_start)->format('Y-m-d\TH:i'):''}}" data-name="cutting_start">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Ending Date</th>
                                            <td style="padding:1px;">
                                                <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->cutting_end?Carbon\Carbon::parse($plan->cutting_end)->format('Y-m-d\TH:i'):''}}" data-name="cutting_end">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-header">
                                <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">2.Sewing Section</span></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;">Starting Date</th>
                                            <td style="padding:1px;">
                                                <input type="datetime-local" class="form-control form-control-sm updateDate sewingStarDate" value="{{$plan->cutting_end?Carbon\Carbon::parse($plan->cutting_end)->format('Y-m-d\TH:i'):''}}" data-name="sewing_start">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Ending Date</th>
                                            <td style="padding:1px;">
                                                <input type="datetime-local" readonly="" class="form-control form-control-sm updateDate" value="{{$plan->sewing_end?Carbon\Carbon::parse($plan->sewing_end)->format('Y-m-d\TH:i'):''}}" data-name="sewing_end">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-header">
                                <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">3.Packing Section</span></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;">Starting Date</th>
                                            <td style="padding:1px;">
                                                <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->packing_start?Carbon\Carbon::parse($plan->packing_start)->format('Y-m-d\TH:i'):''}}" data-name="packing_start">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Ending Date</th>
                                            <td style="padding:1px;">
                                                <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->packing_end?Carbon\Carbon::parse($plan->packing_end)->format('Y-m-d\TH:i'):''}}" data-name="packing_end">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-header">
                                <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">4.Shipment Section</span></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;">Starting Date</th>
                                            <td style="padding:1px;">
                                                <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->shippment_start?Carbon\Carbon::parse($plan->shippment_start)->format('Y-m-d\TH:i'):''}}" data-name="shippment_start">
                                            </td>
                                        </tr>
                                        <tr>
                                            <th style="padding:5px;">Ending Date</th>
                                            <td style="padding:1px;">
                                                <input type="datetime-local" class="form-control form-control-sm updateDate" value="{{$plan->shippment_end?Carbon\Carbon::parse($plan->shippment_end)->format('Y-m-d\TH:i'):''}}" data-name="shippment_end">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="card shadow-sm mb-3 flex-fill">
                            <div class="card-header">
                                <h3><span style="background: #4CAF50;color: white;padding: 5px 10px;border-radius: 5px;">5. Sewing Production Planning </span></h3>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-bordered">
                                        <tr>
                                            <th style="padding:5px;min-width:250px;width:250px;">Style No</th>
                                            <th style="padding:5px;min-width:400px;">Floor/Line</th>
                                            <th style="padding:5px;min-width:250px;width:250px;">Output </th>
                                            <th style="padding:5px;min-width:250px;width:250px;">
                                                Setup
                                            </th>
                                        </tr>
                                        <tr>
                                            <td style="padding:5px;">
                                                <select class="form-control form-control-sm mb-2 styleSelect">
                                                    <option value="">Select</option>
                                                    @foreach(App\Models\OrderDetails::orderBy('id', 'desc')->where('status','pending')->get() as $style)
                                                    <option value="{{$style->style_no}}" data-buyer="{{$style->buyer_name}}"  data-merchandiser="{{$style->merchant_name}}"  data-qty="{{$style->total_qty}}" >{{$style->style_no}}</option>
                                                    @endforeach
                                                </select>
                                                <p>
                                                    Order Qty :<b class="styleQty"></b> <br>
                                                    Buyer :<b class="styleBuyer"></b> <br>
                                                    Merchandiser :<b class="styleMerchant"></b> <br>
                                                </p>
                                            </td>
                                            <td>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        @php
                                                        $attributes = App\Models\Attribute::where('type', 4)
                                                            ->where('status', 'active')
                                                            ->get()
                                                            ->groupBy('name');
                                                        @endphp

                                                        @foreach($attributes as $name => $items)
                                                            <b>{{ $name }}</b>
                                                            <br>

                                                            @foreach($items as $line)
                                                                <label class="lineCheck">
                                                                    <input type="checkbox" name="floor[]" value="{{ $line->slug }}">
                                                                    Line - <b>{{ $line->slug }} / </b> C/H: {{ $line->capacity }}
                                                                </label>
                                                            @endforeach

                                                            <hr>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <p>
                                                    P. Start:<b class="startDate"></b> <br>
                                                    Total Hours:<b class="totalTime"></b> <br>
                                                    Hourly Target :<b class="hourTarget"></b> <br>
                                                    Per Day/Hours :<b class="totalHour" data-hour="10">10h</b> <br>
                                                    P. End:<b class="EndOfDate"></b> <br>
                                                </p>
                                            </td>
                                            <td>
                                                <label>Lose Time (In Minite)</label>
                                                <input type="text" class="form-control form-control-sm extraTime" placeholder="Lose Hour (In Minite)">
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                  
                </div>

                <!-- <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update Sample</button> -->
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    $(document).ready(function () {
        
        $('.updateDate').change(function(){
            var url ="{{ route('admin.productionPlanningAction', ['date-update', $plan->id]) }}";
            var dataName =$(this).data('name');
            var dataValue =$(this).val();

            $.ajax({
                url: url,
                data: {'dataName': dataName,'dataValue':dataValue},
                dataType: 'json',
                success: function(res){
                    //success
                },
                error: function(){
                    alert('Error updating item.');
                }
            });

        });


        // On select style
        $(document).on("change", ".styleSelect", function () {
            if (!$(".sewingStarDate").val()) {
                alert("Please select Start Date & Time first.");
                $(this).val("");
                return;
            }

            let qty = Number($(this).find(":selected").data("qty"));
            let buyer = $(this).find(":selected").data("buyer");
            let merch = $(this).find(":selected").data("merchandiser");

            $(".styleQty").text(qty.toLocaleString() + " pcs");
            $(".styleBuyer").text(buyer);
            $(".styleMerchant").text(merch);

            calculateProduction();
        });

        $(document).on("change", "input[name='floor[]']", calculateProduction);
        $(document).on("input", ".extraTime", calculateProduction);
        $(document).on("change", ".sewingStarDate", calculateProduction);

        function calculateProduction() {

            let startDate = $(".sewingStarDate").val();
            let qty = Number($(".styleSelect option:selected").data("qty"));
            if (!startDate || !qty) return;

            let start = new Date(startDate);

            $(".startDate").text(formatDate(start));

            // Calculate total capacity
            let totalCapacity = 0;
            $("input[name='floor[]']:checked").each(function () {
                let cap = Number($(this).closest(".lineCheck").text().match(/C\/H:\s*(\d+)/)[1]);
                totalCapacity += cap;
            });

            if (totalCapacity === 0) return;

            $(".hourTarget").text(totalCapacity + " pcs");

            // Total minutes needed
            let totalMinutes = Math.round((qty / totalCapacity) * 60);

            // Add lose time
            let loseTime = Number($(".extraTime").val()) || 0;
            totalMinutes += loseTime;

            // Set daily working limits
            const workStart1 = 10;      // 10:00
            const workEnd1   = 13;      // 1:00 PM
            const workStart2 = 14;      // 2:00 PM
            const workEnd2   = 21;      // 9:00 PM
            const dailyWorkMinutes = (3 * 60) + (7 * 60); // 600 minutes

            $(".totalHour").text("10h");
            $(".totalTime").text(Math.floor(totalMinutes / 60) + "h - " + (totalMinutes % 60) + "m");

            // Calculate END DATE + TIME
            let end = new Date(start);

            while (totalMinutes > 0) {
                let hour = end.getHours();

                if (hour >= workStart1 && hour < workEnd1) {
                    end.setMinutes(end.getMinutes() + 1);
                    totalMinutes--;
                }
                else if (hour >= workStart2 && hour < workEnd2) {
                    end.setMinutes(end.getMinutes() + 1);
                    totalMinutes--;
                }
                else {
                    // Move to next working slot
                    if (hour < workStart1) {
                        end.setHours(workStart1, 0, 0);
                    } else if (hour < workStart2) {
                        end.setHours(workStart2, 0, 0);
                    } else {
                        // End of day → go next day 10AM
                        end.setDate(end.getDate() + 1);
                        end.setHours(workStart1, 0, 0);
                    }
                }
            }

            $(".EndOfDate").text(formatDate(end));
        }

        // Helper function to format date
        function formatDate(d) {
            let day = String(d.getDate()).padStart(2, '0');
            let month = String(d.getMonth() + 1).padStart(2, '0');
            let year = d.getFullYear();

            let hours = d.getHours();
            let minutes = String(d.getMinutes()).padStart(2, '0');

            // Convert to AM/PM
            let ampm = hours >= 12 ? "PM" : "AM";
            let hour12 = hours % 12;
            if (hour12 === 0) hour12 = 12;

            hour12 = String(hour12).padStart(2, '0');

            // final format: 05/12/2025, 10.00 AM
            return `${day}/${month}/${year}, ${hour12}.${minutes} ${ampm}`;
        }


    });

</script>
@endpush
