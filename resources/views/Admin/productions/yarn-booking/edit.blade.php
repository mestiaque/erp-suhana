

@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Yarn Booking Edit') }}</title>
@endsection


@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>Edit Yarn Booking</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.yarnBooking') }}">Yarn Bookings</a></li>
            <li class="item">Edit Yarn Booking</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')
            <form action="{{ route('admin.yarnBookingAction', ['update', $booking->id]) }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>Pi Number</label>
                        <select class="form-control" name="pi_no" id="pi_select" data-url="{{ route('admin.yarnBookingAction', ['pi-select', $booking->id]) }}" required>
                            <option value="">-- Select PI Number --</option>
                            @if(count($pis) > 0)
                                @foreach ($pis as $pi)
                                <option value="{{ $pi->pi_no }}" {{ $booking->pi_no == $pi->pi_no ? 'selected' : '' }}>
                                    {{ $pi->pi_no }}
                                </option>
                                @endforeach
                            @else
                                <option class="text-c" value="" disabled>No order found</option>
                            @endif
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label>Buyer Name</label>
                        <input type="text" readonly class="form-control buyer_name" placeholder="--" value="{{ $booking->buyer?->name ?? '' }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label>Status</label>
                        <select name="status" class="form-control" required>
                            <option value="pending" {{ $booking->status=='pending'?'selected':'' }}>Pending</option>
                            <option value="confirmed" {{ $booking->status=='confirmed'?'selected':'' }}>Confirmed</option>
                            <option value="approved" {{ $booking->status=='approved'?'selected':'' }}>Approved</option>
                            <option value="cancel" {{ $booking->status=='cancel'?'selected':'' }}>Cancel</option>
                        </select>
                    </div>
                </div>

                <br>
                <h5><b>Yarn Booking Items</b></h5>
                <div class="cardItems">
                    @include(adminTheme().'productions.yarn-booking.includes.items', ['items' => $booking->items ?? []])
                </div>


                <br>
                <button type="submit" class="btn btn-success"><i class="bx bx-check"></i> Update Yarn Booking</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {

    $(document).on('change', '#pi_select', function() {
        let pi_no = $(this).val();
        if (!pi_no) return;

        let url = $(this).data('url');

        $.get(url, { pi_no: pi_no }, function(res) {
            if (res.success) {
                $('.buyer_name').val(res.order.buyer_name)
                $('.cardItems').html(res.html);
                updateAllItems();
            } else {
                alert('PI not found');
                $('.cardItems').html('<tr><td colspan="4" class="text-center">No Items</td></tr>');
            }
        });
    });

});
</script>

@endpush



