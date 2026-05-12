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
            <form action="{{ route('admin.yarnBookingAction', ['update', $booking->booking_no ?? 0]) }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>PI Number</label>
                        <select class="form-control" name="pi_id" id="pi_select" data-url="{{ route('admin.yarnBookingAction', ['pi-select', $booking->id ?? 0]) }}" required>
                            <option value="">-- Select PI Number --</option>
                            @foreach($pis as $pi)
                                <option value="{{ $pi->id }}" {{ ($booking?->pi_id ?? '') == $pi->id ? 'selected' : '' }}>{{ $pi->pi_no }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label>Buyer Name</label>
                        <input type="text" readonly class="form-control buyer_name" value="{{ $booking->pi->buyer?->name ?? '' }}">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Creditor</label>
                        <input type="text" name="supplier" class="form-control" value="{{ $booking->supplier ?? '' }}" required placeholder="Supplier Name">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Attn. Name</label>
                        <input type="text" name="attn" class="form-control" value="{{ $booking->attn ?? '' }}" placeholder="Attention Name">
                    </div>

                    <div class="col-md-3 mb-3 d-none">
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
                <h5><b>Yarn Booking Items</b></h5>
                <div class="cardItems">
                    @include(adminTheme().'productions.yarn-booking.includes.items', ['items' => $items ?? []])
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

    // ----------------------------
    // Calculate total qty for an item
    // ----------------------------
    function calculateItemTotal($itemRow) {
        let total = 0;
        $itemRow.find('.yarn-qty').each(function() {
            total += parseFloat($(this).val()) || 0;
        });
        $itemRow.find('.total-qty').val(total);
    }

    // ----------------------------
    // On quantity change
    // ----------------------------
    $(document).on('input', '.yarn-qty', function() {
        let $yarnRow = $(this).closest('.yarnBody').closest('tr'); // outer item row
        calculateItemTotal($yarnRow);
    });

    // ----------------------------
    // Add new yarn row
    // ----------------------------
    $(document).on('click', '.addRow', function() {
        let $row = $(this).closest('.yarn-row');
        let $clone = $row.clone();

        // reset values
        $clone.find('select').prop('selectedIndex', 0);
        $clone.find('.yarn-qty').val(0);

        $row.closest('.yarnBody').append($clone);
    });

    // ----------------------------
    // Remove yarn row
    // ----------------------------
    $(document).on('click', '.removeRow', function() {
        let $tbody = $(this).closest('.yarnBody');
        if ($tbody.find('.yarn-row').length > 1) {
            $(this).closest('.yarn-row').remove();

            // recalc total for the remaining rows
            let $itemRow = $tbody.closest('tr');
            calculateItemTotal($itemRow);
        }
    });

    // ----------------------------
    // Initial total calculation on page load
    // ----------------------------
    $('.cardItems tr').each(function() {
        let $itemRow = $(this);
        if ($itemRow.find('.total-qty').length) {
            calculateItemTotal($itemRow);
        }
    });

});

</script>


<script>
document.addEventListener('focus', function(e) {
    // Focus event for any number input
    if (e.target.tagName === 'INPUT' && e.target.type === 'number') {
        if (e.target.value == 0) e.target.value = '';
    }
}, true); // use capture so focus triggers on delegation

document.addEventListener('blur', function(e) {
    // Blur event for any number input
    if (e.target.tagName === 'INPUT' && e.target.type === 'number') {
        if (e.target.value === '') e.target.value = 0;
    }
}, true);
</script>



@endpush


