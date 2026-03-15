@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Dyeing Receive') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">

    <div class="breadcrumb-area">
        <h1>{{ $action == 'store' ? 'Add' : 'Edit' }} Dyeing Receive</h1>
        <ol class="breadcrumb">
            <li class="item">
                <a href="{{ route('admin.dashboard') }}">
                    <i class="bx bx-home-alt"></i>
                </a>
            </li>
            <li class="item">
                <a href="{{ route('admin.dyeingReceive') }}">Dyeing Receives</a>
            </li>
            <li class="item">
                {{ $action == 'store' ? 'Add' : 'Edit' }} Receive
            </li>
        </ol>
    </div>

    <div class="card mb-30">
        <div class="card-body">

            @include(adminTheme().'alerts')

            <form method="POST"
                action="{{ route(
                    'admin.dyeingReceiveAction',
                    [$action == 'store' ? 'store' : 'update', $receive?->receive_no ?? 0]
                ) }}">
                @csrf

                {{-- hidden fields --}}
                <input type="hidden" name="pi_id" id="pi_id_hidden" value="{{ $receive?->pi_id ?? '' }}">
                <input type="hidden" name="booking_no" id="booking_no_hidden" value="{{ $receive?->booking_no ?? '' }}">

                <div class="row">

                    {{-- PI Number --}}
                    <div class="col-md-3 mb-3">
                        <label>PI Number</label>

                        @if($action == 'store')
                            <select class="form-control"
                                    id="pi_select_receive"
                                    data-url="{{ route('admin.dyeingReceiveAction','pi-select') }}"
                                    required>
                                <option value="">-- Select PI Number --</option>
                                @foreach($pis as $pi)
                                    <option value="{{ $pi->id }}">{{ $pi->pi_no }}</option>
                                @endforeach
                            </select>
                        @else
                            <input type="text"
                                   class="form-control"
                                   value="{{ $receive?->pi?->pi_no ?? '' }}"
                                   readonly>
                        @endif
                    </div>

                    {{-- Booking Number --}}
                    <div class="col-md-3 mb-3">
                        <label>Booking Number</label>
                        <input type="text"
                               id="booking_no_display"
                               class="form-control"
                               value="{{ $receive?->getBookingNo() ?? '' }}"
                               readonly>
                    </div>

                    {{-- Buyer --}}
                    <div class="col-md-3 mb-3">
                        <label>Buyer Name</label>
                        <input type="text"
                               class="form-control buyer_name"
                               value="{{ $receive?->pi?->buyer?->name ?? '' }}"
                               readonly>
                    </div>

                    {{-- Challan --}}
                    <div class="col-md-3 mb-3">
                        <label>Challan No</label>
                        <input type="text"
                               name="challan_no"
                               class="form-control"
                               value="{{ $receive?->challan_no ?? '' }}">
                    </div>

                    {{-- Receive Date --}}
                    <div class="col-md-3 mb-3">
                        <label>Receive Date</label>
                        <input type="date"
                               name="receive_date"
                               class="form-control"
                               value="{{ $receive?->receive_date ?? date('Y-m-d') }}"
                               required>
                    </div>
                </div>

                <hr>

                <h5><b>Dyeing Receive Items</b></h5>

                <div class="cardItems">
                    @if($action == 'update')
                        @include(
                            adminTheme().'productions.dyeing-receive.includes.items',
                            ['items' => $items, 'action' => 'update', 'receive' => $receive]
                        )
                    @else
                        <div class="alert alert-secondary text-center">
                            Please select a PI number to load items.
                        </div>
                    @endif
                </div>

                <br>

                <button type="submit" class="btn btn-success">
                    <i class="bx bx-check"></i>
                    {{ $action == 'store' ? 'Save Receive' : 'Update Receive' }}
                </button>

            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
$(document).ready(function () {

    $('#pi_select_receive').change(function () {

        let pi_id = $(this).val();
        if (!pi_id) return;

        $('#pi_id_hidden').val(pi_id);

        let url = $(this).data('url');

        $.get(url, { pi_id: pi_id }, function (res) {

            if (res.success) {
                $('.buyer_name').val(res.buyer);
                $('#booking_no_display').val(res.booking_no_show);
                $('#booking_no_hidden').val(res.booking_no);
                $('.cardItems').html(res.html);
            } else {
                $('.cardItems').html(
                    '<div class="alert alert-danger text-center">No items found</div>'
                );
            }
        });
    });

});
</script>
@endpush
