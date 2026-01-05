@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Floor Planning List') }}</title>
@endsection

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Floor Planning List</h3>
             <div class="dropdown d-flex">
             </div>
        </div>

        <div class="card-body">
            @include(adminTheme().'alerts')
            <form action="{{ route('admin.floorPlanning') }}">
                <div class="row mb-2">
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="date" name="startDate" value="{{ request()->startDate ? Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') : '' }}" class="form-control" />
                            <input type="date" name="endDate" value="{{ request()->endDate ? Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') : '' }}" class="form-control" />
                        </div>
                    </div>
                    <div class="col-md-6 mb-1">
                        <div class="input-group">
                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Search Style No, Buyer, Merchandiser" class="form-control" />
                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>PI</th>
                            <th>Styles</th>
                            <th>Total Qty</th>
                            <th>Created By / Date</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($masterPlans as $i => $plan)
                        <tr>
                            <td>{{ $masterPlans->firstItem() + $i }}</td>

                            {{-- Styles as comma-separated --}}
                            <td>
                                @php
                                    $piNos = $plan->productions->pluck('pi_no')->unique()->toArray();
                                @endphp
                                {{ implode(', ', $piNos) }}
                            </td>
                            <td>
                                @php
                                    $styleNos = $plan->productions->pluck('style_no')->unique()->toArray();
                                @endphp
                                {{ implode(', ', $styleNos) }}
                            </td>

                            {{-- Total Qty --}}
                            <td>
                                @php
                                    $totalQty = $plan->productions->sum(fn($p) => $p->style?->total_qty ?? 0);
                                @endphp
                                {{ number_format($totalQty) }}
                            </td>

                            {{-- Created By / Date --}}
                            <td>
                                {{ $plan->creator?->name ?? '--' }}
                                <br>
                                {{ $plan->created_at->format('d.m.Y H:i') }}
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($plan->status=='pending')
                                    <span class="badge badge-warning">Pending</span>
                                @elseif($plan->status=='confirmed')
                                    <span class="badge badge-info">Confirmed</span>
                                @elseif($plan->status=='approved')
                                    <span class="badge badge-success">Approved</span>
                                @elseif($plan->status=='cancelled')
                                    <span class="badge badge-danger">Cancelled</span>
                                @else
                                    <span class="badge badge-secondary">{{ ucfirst($plan->status) }}</span>
                                @endif
                            </td>
                            <td>
                                @if(can('floor_planning.view') || can('floor_planning.edit'))
                                    @can('floor_planning.view')
                                        <a href="{{ route('admin.floorPlanningAction',['view',$plan->id]) }}" class="btn-custom yellow mr-1" >
                                            <i class="fa fa-eye"></i>
                                        </a>
                                    @endcan
                                        @can('floor_planning.edit')
                                            <a href="{{ route('admin.floorPlanningAction',['edit',$plan->id]) }}" class="btn-custom success mr-1"><i class="bx bx-edit"></i></a>
                                        @endcan
                                @else
                                --
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted">No Planning Found</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $masterPlans->links('pagination') }}
            </div>
        </div>
    </div>
</div>
@include(adminTheme().'productions.planning.view')
@endsection
