@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Dyeing Booking Edit') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Dyeing Booking</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.dyeingBooking') }}">Dyeing Bookings</a></li>
            <li class="item">Edit Dyeing Booking</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')
            <form action="{{ route('admin.dyeingBookingAction', ['update', $booking->booking_no ?? 0]) }}" method="POST">
                @csrf
                <input type="hidden" name="action" value="{{ $action}}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>PI Number</label>
                        <select class="form-control" name="pi_id" id="pi_select" data-url="{{ route('admin.dyeingBookingAction', ['pi-select', $booking->id ?? 0]) }}" required>
                            <option value="">-- Select PI Number --</option>
                            @foreach($pis as $pi)
                                <option value="{{ $pi->id }}" {{ ($booking?->pi_id ?? '') == $pi->id ? 'selected' : '' }}>{{ $pi->pi_no }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Buyer Name</label>
                        <input type="text" name="buyer_name" readonly class="form-control buyer_name" value="{{ $booking->pi->buyer?->name ?? '' }}">
                    </div>

                    {{-- <div class="col-md-3 mb-3">
                        <label>Supplier</label>
                        <input type="text" name="supplier" class="form-control" value="{{ $booking->supplier ?? '' }}" required>
                    </div> --}}

                    <div class="col-md-3 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="pending" {{ $booking?->status=='pending'?'selected':'' }}>Pending</option>
                            <option value="confirmed" {{ $booking?->status=='confirmed'?'selected':'' }}>Confirmed</option>
                            <option value="approved" {{ $booking?->status=='approved'?'selected':'' }}>Approved</option>
                            <option value="cancel" {{ $booking?->status=='cancel'?'selected':'' }}>Cancel</option>
                        </select>
                    </div>
                </div>

                <br>
                <h5><b>Dyeing Booking Items</b></h5>
                <div class="cardItems">
                    @include(adminTheme().'productions.dyeing-booking.includes.items', ['items' => $items ?? []])
                </div>

                <br>
                <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update Dyeing Booking</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')

<script>

$(document).ready(function() {

    // ----------------------------
    // PI Number change
    // ----------------------------
    $('#pi_select').change(function() {
        let pi_no = $(this).val();
        if (!pi_no) return;

        let url = $(this).data('url');
        $.get(url, { pi_no: pi_no }, function(res) {
            if(res.success){
                $('.buyer_name').val(res.order.buyer_name);
                $('.cardItems').html(res.html);
            } else {
                alert('PI not found');
                $('.cardItems').html('<tr><td colspan="7" class="text-center">No items found</td></tr>');
            }
        });
    });



});

</script>


<script>


</script>



@endpush


