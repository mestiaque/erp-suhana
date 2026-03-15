@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle($receive ? 'Edit Yarn Receive' : 'New Yarn Receive') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>{{ $receive ? 'Edit' : 'New' }} Yarn Receive</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.yarnReceive') }}">Yarn Receives</a></li>
            <li class="item">{{ $receive ? 'Edit' : 'Create' }}</li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">
            @include(adminTheme().'alerts')

            {{-- Action URL: Update হলে receive_no পাস হবে, নতুবা 0 --}}
            <form action="{{ route('admin.yarnReceiveAction', ['update', $receive->receive_no ?? 0]) }}" method="POST">
                @csrf
                {{-- এডিট মোডের জন্য হিডেন রিসিভ নম্বর --}}
                <input type="hidden" name="receive_no" value="{{ $receive->receive_no ?? '' }}">
                {{-- AJAX থেকে প্রাপ্ত বুকিং নম্বর রাখার জন্য --}}
                <input type="hidden" name="booking_no" id="hidden_booking_no" value="{{ $receive->booking_no ?? '' }}">

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Select PI Number</label>
                        <select class="form-control" name="pi_id" id="pi_receive_select"
                                data-url="{{ route('admin.yarnReceiveAction', 'pi-select') }}" required>
                            <option value="">-- Select PI --</option>
                            @foreach($pis as $pi)
                                <option value="{{ $pi->id }}" {{ (isset($receive) && $items->isNotEmpty() && $items->first()?->pi_id == $pi->id) ? 'selected' : '' }}>
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
                        <label>Creditor</label>
                        <input type="text" name="supplier" id="supplier_name" class="form-control" value="{{ $receive->supplier ?? '' }}" readonly>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label>Chalan No</label>
                        <input type="text" name="chalan_no" class="form-control" value="{{ $receive->chalan_no ?? '' }}" placeholder="Enter Chalan Number">
                    </div>
                </div>

                {{-- <div class="row">
                    <div class="col-md-3 mb-3">
                        <label>Buyer Name</label>
                        <input type="text" id="buyer_name" class="form-control" value="{{ $receive->booking->buyer_name ?? '' }}" readonly>
                    </div>
                </div> --}}

                <br>
                <h5><b>Yarn Receive Items</b></h5>

                <div class="table-responsive mt-3" id="receive_items_body">
                    @include(adminTheme().'productions.yarn-receive.includes.items', ['items' => $items])
                </div>

                <br>
                <button type="submit" class="btn btn-success d-nonex">
                    <i class="bx bx-check"></i> {{ $receive ? 'Update' : 'Save' }} Yarn Receive
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function() {
    // PI সিলেক্ট করলে ডাটা লোড করার লজিক
    $('#pi_receive_select').on('change', function() {
        let pi_id = $(this).val();
        let url = $(this).data('url');

        if (!pi_id) {
            $('#receive_items_body').html('<tr><td colspan="5" class="text-center">Please select a PI to load items</td></tr>');
            return;
        }

        // লোডার দেখানো (ঐচ্ছিক)
        $('#receive_items_body').html('<tr><td colspan="5" class="text-center">Loading items...</td></tr>');

        $.ajax({
            url: url,
            type: 'GET',
            data: { pi_id: pi_id },
            success: function(res) {
                if (res.success) {
                    $('#receive_items_body').html(res.html);
                    $('#supplier_name').val(res.supplier);
                    $('#buyer_name').val(res.buyer_name);
                    $('#hidden_booking_no').val(res.booking_no);
                    $('button[type="submit"]').removeClass('d-none');
                } else {
                    alert(res.message || 'Error loading data');
                    $('#receive_items_body').html('<tr><td colspan="5" class="text-center text-danger">No booking found for this PI</td></tr>');
                }
            },
            error: function() {
                alert('Server error occurred');
            }
        });
    });

    $(document).on('input', '.yarn-recv', function() {
        let $subTable = $(this).closest('.yarnBody'); // সাব টেবিলের বডি ধরলাম
        let total = 0;

        // ওই সাব-টেবিলের সব yarn-recv যোগ করা
        $subTable.find('.yarn-recv').each(function() {
            let val = parseFloat($(this).val()) || 0;
            total += val;
        });

        // মেইন টেবিলের row খুঁজে total-qty তে ভ্যালু বসানো
        // $(this).closest('tr').parent().closest('tr') ব্যবহার করে মেইন রো তে যাওয়া যায়
        $(this).closest('table').closest('tr').find('.total-qty').val(total.toFixed(2));
    });
});
</script>
@endpush
