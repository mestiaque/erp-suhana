@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Dyeing Receive') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>{{ $action == 'create' ? 'Add' : 'Edit' }} Dyeing Receive</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.dyeingReceive') }}">Dyeing Receives</a></li>
            <li class="item">{{ $action == 'create' ? 'Add' : 'Edit' }} Receive</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')

            {{-- ফর্ম অ্যাকশন রুট --}}
            {{-- <form action="{{ route('admin.dyeingReceiveAction', [$action, $receive->id ?? 0]) }}" method="POST"> --}}

            <form action="{{ route('admin.dyeingReceiveAction', [$action, $receive->receive_no ?? 0]) }}" method="POST">
                @csrf
                {{-- hidden input এখন আর দরকার নেই কারণ আমরা URL থেকেই $action পাচ্ছি, তবুও নিরাপত্তার জন্য রাখতে পারেন --}}
                <input type="hidden" name="action_type" value="{{ $action }}">


                @csrf
                <input type="hidden" name="action" value="{{ $action }}">
                <input type="hidden" name="receive_id" value="{{ $receive->id ?? '' }}">
                <input type="hidden" name="pi_id" id="pi_id_hidden" value="{{ $receive->pi_id ?? '' }}">

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>PI Number</label>
                        {{-- এখানে 'create' এর বদলে 'store' চেক করুন অথবা $receive চেক করুন --}}
                        @if($action == 'store')
                            <select class="form-control" name="pi_id" id="pi_select_receive" data-url="{{ route('admin.dyeingReceiveAction', 'pi-select') }}" required>
                                <option value="">-- Select PI Number --</option>
                                @foreach($pis as $pi)
                                    <option value="{{ $pi->id }}">{{ $pi->pi_no }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text" class="form-control" value="{{ $receive->pi->pi_no ?? '' }}" readonly>
                            <input type="hidden" name="pi_id" value="{{ $receive->pi_id ?? '' }}">
                        @endif
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Booking Number</label>
                        <input type="text" name="booking_no" id="booking_no_display" class="form-control" value="{{ $receive?->getBookingNo() ?? '' }}" readonly placeholder="Auto-filled from PI">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Buyer Name</label>
                        <input type="text" name="buyer_name" readonly class="form-control buyer_name" value="{{ $receive->pi->buyer_name ?? '' }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Challan No</label>
                        <input type="text" name="challan_no" class="form-control" value="{{ $receive->challan_no ?? '' }}" placeholder="Enter Challan Number">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Receive Date</label>
                        <input type="date" name="receive_date" class="form-control" value="{{ $receive->receive_date ?? date('Y-m-d') }}" required>
                    </div>
                </div>

                <br>
                <h5><b>Dyeing Receive Items</b></h5>
                <div class="cardItems">
                    {{-- বুকিং সিলেক্ট করলে এখানে আইটেম লোড হবে (ajax) অথবা এডিট মোডে আইটেম থাকবে --}}
                    @if($action == 'update' || !empty($items))
                        @include(adminTheme().'productions.dyeing-receive.includes.items', ['items' => $items ?? []])
                    @else
                        <div class="alert alert-secondary text-center">Please select a booking number to load items.</div>
                    @endif
                </div>

                <br>
                <button type="submit" class="btn btn-success">
                    <i class="bx bx-check"></i> {{ $action == 'create' ? 'Save Receive' : 'Update Receive' }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    $('#pi_select_receive').change(function() {
        let pi_id = $(this).val();
        if (!pi_id) return;

        let url = $(this).data('url');
        $.get(url, { pi_id: pi_id }, function(res) {
            if(res.success){
                $('.buyer_name').val(res.buyer);
                $('#booking_no_display').val(res.booking_no); // বুকিং নম্বর বসাবে
                $('.cardItems').html(res.html); // আইটেম টেবিল লোড করবে
            } else {
                alert('No Dyeing Booking found for this PI');
                $('.cardItems').html('<div class="alert alert-danger text-center">No items found.</div>');
            }
        });
    })
});
</script>
@endpush
