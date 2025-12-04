@extends(adminTheme().'layouts.app')

@section('title')
<title>Sample View</title>
@endsection

@push('css')

@endpush

@section('contents')

<div class="flex-grow-1">
    <div class="breadcrumb-area">
        <h1>View Sample</h1>
        <ol class="breadcrumb">
            <li class="item"><a href="{{ route('admin.dashboard') }}"><i class="bx bx-home-alt"></i></a></li>
            <li class="item"><a href="{{ route('admin.samples') }}">Samples</a></li>
            <li class="item">View Sample</li>
        </ol>
    </div>
    <div class="card mb-30">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <table class="table table-bordered table-striped">
                        <tr><th>Order No :</th><td>{{ $order->id }}</td></tr>
                        <tr><th style="width:10rem">Style :</th><td>{{ $order->style ?? '--' }}</td></tr>
                        <tr><th>Type :</th><td>{{ $order->type ?? '--' }}</td></tr>
                        <tr style="width:10rem"><th>Merchent Name :</th><td>{{ $order?->merchant_name ?? '--' }}</td></tr>
                        <tr>
                            <th>Status :</th>
                            <td>
                                @php
                                    $status = $order->status ?? '--';

                                    $badge = [
                                        'temp'      => 'badge badge-secondary',
                                        'pending'   => 'badge badge-warning',
                                        'confirmed' => 'badge badge-info',
                                        'completed' => 'badge badge-success',
                                        'cancel'    => 'badge badge-danger',
                                    ];

                                    $class = $badge[$status] ?? 'badge badge-dark';
                                @endphp

                                <span class="{{ $class }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="table table-bordered table-striped">
                        <tr><th>Created By :</th><td>{{ $order?->createdUser?->name ?? '--' }}</td></tr>
                        <tr><th>Created Date :</th><td>{{ $order->created_at->format('d.m.Y')  ?? '--' }}</td></tr>
                        <tr><th>Received Date :</th><td>{{ $order?->received_at?->format('d.m.Y') ?? '--' }}</td></tr>
                        <tr><th>Delivery Date :</th><td>{{  $order?->delivery_at?->format('d.m.Y')  ?? '--' }}</td></tr>
                        <tr><th>Currency :</th><td>{{  $order?->currency  ?? '--' }}</td></tr>
                    </table>
                </div>
                <div class="col-md-4">
                    <table class="table table-bordered table-striped">
                        <tr style="width:10rem"><th>Buyer Name :</th><td>{{ $order?->style }}</td></tr>
                        <tr><th>Buyer Country :</th><td>{{ $order?->buyer?->country ?? '--' }}</td></tr>
                        <tr><th>Buyer Email :</th><td>{{  $order?->buyer?->email  ?? '--' }}</td></tr>
                        <tr><th>Buyer Mobile :</th><td>{{  $order?->buyer?->mobile  ?? '--' }}</td></tr>
                        <tr><th>Buyer Address :</th><td>{{  $order?->buyer?->address_line1  ?? '--' }}</td></tr>
                    </table>
                </div>
            </div>
            <h5 class="mb-2">Sample Items</h5>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th width="50">#</th>
                            <th>Composition</th>
                            <th>GSM</th>
                            <th>Color</th>
                            <th>Size</th>
                            <th>Quantity</th>
                            <th>Comments</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($order->items as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->composition }}</td>
                            <td>{{ $item->gsm }}</td>
                            <td>{{ $item->color }}</td>
                            <td>{{ $item->size }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ $item->comments }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No items found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div style="" class="mt-4">
                <h5 style=""><u>Payment Terms:</u></h5>
                <div style="border: 1px solid #80808045; padding: 1rem 2rem;">
                    @if($order->payment_terms)
                    {!! $order->payment_terms !!}
                    @else
                    <p class="m-0 w-100 text-center"><i>No terms found</i></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
