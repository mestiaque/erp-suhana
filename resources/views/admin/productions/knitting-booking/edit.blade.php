@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($booking ? 'Edit Knitting Booking' : 'New Knitting Booking') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>{{ $booking ? 'Edit' : 'New' }} Knitting Booking</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.knittingBooking') }}">Knitting Bookings</a></li>
            <li class="item">{{ $booking ? 'Edit' : 'Create' }}</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')

            {{-- Action URL: Update হলে knit_booking_no পাস হবে --}}
            <form action="{{ route('admin.knittingBookingAction', ['update', $booking->booking_no ?? 0]) }}" method="POST">
                @csrf

                {{-- এডিট মোডের জন্য হিডেন ফিল্ড --}}
                @if($booking)
                    <input type="hidden" name="booking_no" value="{{ $booking->booking_no }}">
                @endif

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Select PI Number</label>
                        <select class="form-control" name="pi_id" id="pi_select"
                                data-url="{{ route('admin.knittingBookingAction', 'pi-select') }}" required>
                            <option value="">-- Select PI --</option>
                            @foreach($pis as $pi)
                                <option value="{{ $pi->id }}" {{ ($booking && $booking->pi_id == $pi->id) ? 'selected' : '' }}>
                                    {{ $pi->pi_no }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Buyer Name</label>
                        <input type="text" id="buyer_name" class="form-control bg-light"
                               value="{{ $booking?->pi?->buyer_name ?? '' }}" readonly>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Knitting Unit/Factory</label>
                        <input type="text" name="knitting_unit" class="form-control"
                               value="{{ $booking->knitting_unit ?? '' }}" placeholder="Enter Factory Name">
                    </div>

                    <div class="col-md-3 mb-3 d-none">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="pending" {{ ($booking && $booking->status == 'pending') ? 'selected' : '' }}>Pending</option>
                            <option value="confirmed" {{ ($booking && $booking->status == 'confirmed') ? 'selected' : '' }}>Confirmed</option>
                            <option value="approved" {{ ($booking && $booking->status == 'approved') ? 'selected' : '' }}>Approved</option>
                        </select>
                    </div>
                </div>

                <br>
                <h5><b>Knitting Specifications & Target</b></h5>
                <div class="table-responsive">
                    <div id="knitting_items_area">
                        {{-- AJAX অথবা এডিট মোডে এখানে items.blade.php লোড হবে --}}
                        @include(adminTheme().'productions.knitting-booking.includes.items', ['items' => $items])
                    </div>
                </div>

                <br>
                <div class="text-">
                    <button type="submit" class="btn btn-success">
                        <i class="bx bx-check"></i> {{ $booking ? 'Update' : 'Save' }} Knitting Booking
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // PI সিলেক্ট করলে অটোমেটিক আইটেম লোড করা
    $('#pi_select').on('change', function() {
        let pi_id = $(this).val();
        let url = $(this).data('url');

        if (!pi_id) {
            $('#knitting_items_area').html('');
            $('#buyer_name').val('');
            return;
        }

        $('#knitting_items_area').html('<div class="text-center py-3">Loading Items...</div>');

        $.ajax({
            url: url,
            type: 'GET',
            data: { pi_id: pi_id },
            success: function(res) {
                if (res.success) {
                    $('#knitting_items_area').html(res.html);
                    $('#buyer_name').val(res.buyer_name);
                } else {
                    alert(res.message);
                    $('#knitting_items_area').html('<div class="text-center text-danger">No Yarn Booking found for this PI</div>');
                }
            },
            error: function() {
                alert('Error fetching data from server');
            }
        });
    });
});
</script>
@endpush
