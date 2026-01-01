@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('P.I Fabric Status') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>P.I Fabric Status</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.proformaInvoice') }}">Proforma Invoice</a></li>
            <li class="item">Fabric Status</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="w-100 text-right mb-1">
                <a href="{{ route('admin.fabricStatus', ['id' => $pi->id, 'print' => true]) }}" class="btn-custom primary">
                    <i class="fa-solid fa-print"></i> PRINT
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm text-center" style="font-size: 0.75rem; vertical-align: middle;">
                    <thead class="">
                        <tr>
                            <th rowspan="2">SL</th>
                            <th rowspan="2">BUYER</th>
                            <th rowspan="2">PI NO</th>
                            <th rowspan="2">FABRICATION</th>
                            <th rowspan="2">COLOR</th>
                            <th rowspan="2">YARN COUNT</th>
                            <th rowspan="2">YARN BOOKING</th>
                            <th rowspan="2">INHOUSE YARN</th>
                            <th rowspan="2">KNITTING FACTORY</th>
                            <th rowspan="2">TO YARN DELI KNITTING</th>
                            <th rowspan="2">GREY RECEIVE FOR DYEING</th>
                            <th rowspan="2">GREY RECEIVE BAL</th>
                            <th rowspan="2">KNITTING PROCE 55 LOSS (%)</th>
                            <th rowspan="2">DYEING FACTORY</th>
                            <th rowspan="2">GREY DELIVERY</th>
                            <th rowspan="2">GREY DELIVERY BAL</th>
                            <th rowspan="2">TOTAL DYEING</th>
                            <th rowspan="2">DYEING BALANCE</th>
                            <th colspan="2">DYEING TO FINISHED FAB I/H</th>
                            <th rowspan="2">DYE PROCE 55 LOSS (N)</th>
                            <th rowspan="2">REMARKS</th>
                        </tr>
                        <tr>
                            <th>GREY</th>
                            <th>FINISH</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            // ১. ইয়ার্ন কাউন্ট ও ফেব্রিকেশন প্রসেসিং
                            $uniqueYarnCounts = collect();
                            foreach($pi->yarnBookings as $yb) {
                                $countsArray = json_decode($yb->yarn_count, true);
                                if (is_array($countsArray)) {
                                    foreach ($countsArray as $item) {
                                        if (!empty($item['count'])) $uniqueYarnCounts->push(trim($item['count']));
                                    }
                                }
                            }
                            $yarnCountStr = $uniqueYarnCounts->unique()->implode(', ');
                            $fabrication = $pi->yarnBookings->pluck('fabric_type')->unique()->implode(', ');

                            // ২. ডাটা ক্যালকুলেশন (PI #25 এর জন্য)
                            $totalYarnReq = $pi->yarnBookings->sum('required_qty'); // ১০০০০
                            $totalYarnRecv = \App\Models\YarnReceive::where('pi_id', $pi->id)->sum('receive_qty'); // ৫০০০

                            $totalKnitReq = \App\Models\KnittingBooking::where('pi_id', $pi->id)->sum('booking_qty'); // ১০০০০
                            $totalGreyRecv = \App\Models\KnittingReceive::where('pi_id', $pi->id)->sum('weight'); // ৭০০০

                            // Knitting Loss
                            $knitLossPerc = $totalKnitReq > 0 ? (($totalKnitReq - $totalGreyRecv) / $totalKnitReq) * 100 : 0;

                            // ডাইং ও কালার লজিক
                            $dyeingReceives = \App\Models\DyeingReceive::where('pi_id', $pi->id)->get();
                            $dyeingColors = $dyeingReceives->pluck('color')->unique()->filter();
                            $totalDyeingFinishRecv = $dyeingReceives->sum('receive_qty'); // ফিনিশ ওজন (৬২০)

                            $totalDyeingReq = $pi->dyeingBookings->sum('required_qty'); // ৭০০
                            $dyeingBalance = $totalDyeingReq - $totalDyeingFinishRecv;
                        @endphp

                        <tr>
                            <td>1</td>
                            {{-- BUYER --}}
                            <td>{{ $pi->dyeingBookings->first()->buyer_name ?? ($pi->buyer->buyer_name ?? $pi->buyer_name) }}</td>

                            {{-- PI NO --}}
                            <td>{{ $pi->pi_no }}</td>

                            {{-- FABRICATION --}}
                            <td>{{ $fabrication ?: '--' }}</td>

                            {{-- COLOR --}}
                            <td class="text-left" style="line-height: 1.1;">
                                <span> {{ count($dyeingColors) }} COLORS</span>
                            </td>

                            {{-- YARN COUNT --}}
                            <td>{{ $yarnCountStr ?: '--' }}</td>

                            {{-- YARN BOOKING --}}
                            <td>{{ number_format($totalYarnReq, 2) }}</td>

                            {{-- INHOUSE YARN --}}
                            <td>{{ number_format($totalYarnRecv, 2) }}</td>

                            {{-- KNITTING FACTORY --}}
                            <td>{{ \App\Models\KnittingBooking::where('pi_id', $pi->id)->pluck('knitting_unit')->unique()->filter()->implode(', ') ?: '--' }}</td>

                            {{-- TO YARN DELI KNITTING (Knitting Booking) --}}
                            <td>{{ number_format($totalKnitReq, 2) }}</td>

                            {{-- GREY RECEIVE FOR DYEING --}}
                            <td>{{ number_format($totalGreyRecv, 2) }}</td>

                            {{-- GREY RECEIVE BAL (বুকিং - রিসিভ) --}}
                            <td>{{ number_format($totalKnitReq - $totalGreyRecv, 2) }}</td>

                            {{-- KNITTING PROCESS LOSS (%) --}}
                            <td>{{ number_format($knitLossPerc, 2) }}%</td>

                            {{-- DYEING FACTORY --}}
                            <td>{{ $pi->dyeingBookings->pluck('remarks')->unique()->filter()->implode(', ') ?: '--' }}</td>

                            {{-- GREY DELIVERY (নিটিং রিসিভকেই গ্রে ডেলিভারি ধরা হয়েছে) --}}
                            <td>{{ number_format($totalGreyRecv, 2) }}</td>

                            {{-- GREY DELIVERY BAL (ধরে নেওয়া হয়েছে ডেলিভারি ব্যালেন্স নেই) --}}
                            <td>0.00</td>

                            {{-- TOTAL DYEING (Required Qty) --}}
                            <td>{{ number_format($totalDyeingReq, 2) }}</td>

                            {{-- DYEING BALANCE (Required - Finish) --}}
                            <td>{{ number_format($dyeingBalance, 2) }}</td>

                            {{-- DYEING TO FINISHED FAB I/H (GREY Input) --}}
                            <td>{{ number_format($totalGreyRecv, 2) }}</td>

                            {{-- DYEING TO FINISHED FAB I/H (FINISH Output) --}}
                            <td>{{ number_format($totalDyeingFinishRecv, 2) }}</td>

                            {{-- DYE PROCESS LOSS (N) (Grey - Finish) --}}
                            <td>{{ number_format($totalGreyRecv - $totalDyeingFinishRecv, 2) }}</td>

                            {{-- REMARKS --}}
                            <td>{{ $pi->remarks ?: '--' }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="font-weight-bold bg-light">
                        <tr>
                            <td colspan="6" class="text-right">TOTAL</td>
                            <td>{{ number_format($totalYarnReq, 2) }}</td>
                            <td>{{ number_format($totalYarnRecv, 2) }}</td>
                            <td></td>
                            <td>{{ number_format($totalKnitReq, 2) }}</td>
                            <td>{{ number_format($totalGreyRecv, 2) }}</td>
                            <td>{{ number_format($totalKnitReq - $totalGreyRecv, 2) }}</td>
                            <td></td>
                            <td></td>
                            <td>{{ number_format($totalGreyRecv, 2) }}</td>
                            <td>0.00</td>
                            <td>{{ number_format($totalDyeingReq, 2) }}</td>
                            <td>{{ number_format($dyeingBalance, 2) }}</td>
                            <td>{{ number_format($totalGreyRecv, 2) }}</td>
                            <td>{{ number_format($totalDyeingFinishRecv, 2) }}</td>
                            <td>{{ number_format($totalGreyRecv - $totalDyeingFinishRecv, 2) }}</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@push('js')
@endpush
@push('css')
@endpush
