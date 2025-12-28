@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($receive ? 'Edit Knitting Receive' : 'New Knitting Receive') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>{{ $receive ? 'Edit' : 'New' }} Knitting Receive</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.knittingReceive') }}">Knitting Receives</a></li>
            <li class="item">Edit</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')

            <form action="{{ route('admin.knittingReceiveAction', ['update', $receive->receive_no ?? 0]) }}" method="POST">
                @csrf
                @if($receive)
                    <input type="hidden" name="receive_no" value="{{ $receive->receive_no }}">
                @endif

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Select PI Number</label>
                        <select class="form-control" name="pi_id" id="pi_receive_select"
                                data-url="{{ route('admin.knittingReceiveAction', 'knit-booking-select') }}" required>
                            <option value="">-- Select PI --</option>
                            @foreach($pis as $pi)
                                <option value="{{ $pi->id }}" {{ ($receive && $receive->pi_id == $pi->id) ? 'selected' : '' }}>
                                    {{ $pi->pi_no }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Receive Date</label>
                        <input type="date" name="receive_date" class="form-control" value="{{ $receive->receive_date ?? date('Y-m-d') }}" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Chalan No</label>
                        <input type="text" name="chalan_no" class="form-control" value="{{ $receive->chalan_no ?? '' }}" placeholder="Enter Chalan No" required>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Knitting Booking No</label>
                        <input type="text" name="" id="knit_booking_no_display" class="form-control bg-light" value="{{ $receive->knit_booking_no ?? '' }}" readonly>
                        <input type="hidden" name="knit_booking_no" id="knit_booking_no_input" value="{{ $receive->knit_booking_no ?? '' }}">
                    </div>
                </div>

                <br>
                <h5><b>Fabric Receive Items</b></h5>
                <div id="receive_items_area">
                    @include(adminTheme().'productions.knitting-receive.includes.items', ['items' => $items])
                </div>

                <br>
                <div class="text-right">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="bx bx-check"></i> {{ $receive ? 'Update' : 'Save' }} Receive Record
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
    $('#pi_receive_select').on('change', function() {
        let pi_id = $(this).val();
        let url = $(this).data('url');

        if (!pi_id) {
            $('#receive_items_area').html('');
            return;
        }

        $('#receive_items_area').html('<div class="text-center py-3">Loading items...</div>');

        $.ajax({
            url: url,
            type: 'GET',
            data: { pi_id: pi_id },
            success: function(res) {
                if (res.success) {
                    $('#receive_items_area').html(res.html);
                    $('#knit_booking_no_display').val(res.knit_booking_no_show);
                    $('#knit_booking_no_input').val(res.knit_booking_no);
                } else {
                    alert(res.message);
                    $('#receive_items_area').html('');
                }
            }
        });
    });
});
</script>
@endpush
