@extends(adminTheme().'layouts.app')
@section('title')
    <title>{{ websiteTitle('Shifts List') }}</title>
@endsection

@push('css')
<style type="text/css"></style>
@endpush

@section('contents')
<div class="flex-grow-1">
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Shifts List</h3>
            <div class="dropdown">
                <a href="{{ route('admin.shiftsAction', ['form']) }}" class="btn-custom primary" style="padding:5px 15px;">
                    <i class="bx bx-plus"></i> Add Shift
                </a>
                <a href="{{ route('admin.shifts') }}" class="btn-custom yellow">
                    <i class="bx bx-rotate-left"></i>
                </a>
            </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')

            {{-- Search Accordion --}}
            <div class="accordion-box">
                <div class="accordion">
                    <div class="accordion-item">
                        <a class="accordion-title" href="javascript:void(0)">
                            <i class="bx bx-filter-alt"></i> Search click Here..
                        </a>
                        <div class="accordion-content" style="border:1px solid #e1000a;border-top:0;">
                            <form action="{{ route('admin.shifts') }}" method="GET">
                                <div class="row">
                                    <div class="col-md-12 mb-0">
                                        <div class="input-group">
                                            <input type="text" name="search" value="{{ request()->search ?? '' }}" placeholder="Shift Name" class="form-control">
                                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <br>

            {{-- Bulk Actions --}}
            <form action="{{ route('admin.shifts') }}" method="GET">
                <div class="row mb-2">
                    <div class="col-md-4">

                    </div>
                    <div class="col-md-4"></div>
                    <div class="col-md-4 text-end">
                        <ul class="statuslist list-unstyled d-flex gap-2">
                            <li><a href="{{ route('admin.shifts') }}">All ({{ $totals->total }})</a></li>
                            <li><a href="{{ route('admin.shifts', ['status'=>'active']) }}">Active ({{ $totals->active }})</a></li>
                            <li><a href="{{ route('admin.shifts', ['status'=>'inactive']) }}">Inactive ({{ $totals->inactive }})</a></li>
                        </ul>
                    </div>
                </div>

                {{-- Shifts Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-stripedx">
                        <thead class="">
                            <tr>
                                <th style="width:50px;">
                                    SL
                                </th>
                                <th>Name</th>
                                <th>BN Name</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Red Mark On</th>
                                <th>Over Time Allow</th>
                                <th>Card Accept From</th>
                                <th>Card Accept To</th>
                                <th>Lunch</th>
                                <th>Tiffin Allowance</th>
                                <th>Dinner</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shifts as $i => $shift)
                                <tr>
                                    <td>
                                        {{ $i + 1 }}
                                    </td>
                                    <td>{{ $shift->name_of_shift }}</td>
                                    <td>{{ $shift->name_of_shift_bn }}</td>
                                    <td>{{ $shift->shift_starting_time }}</td>
                                    <td>{{ $shift->shift_closing_time }} {{ $shift->shift_closing_time_next_day ? '(Next Day)' : '' }}</td>
                                    <td>{{ $shift->red_marking_on }}</td>
                                    <td>{{ $shift->over_time_allowed_up_to }} {{ $shift->over_time_allowed_up_to_next_day ? '(Next Day)' : '' }}</td>
                                    <td>{{ $shift->card_accept_from }}</td>
                                    <td>{{ $shift->card_accept_to }} {{ $shift->card_accept_to_next_day ? '(Next Day)' : '' }}</td>
                                    <td>{{ $shift->meal_option ?? 'NA' }}</td>
                                    <td>{{ $shift->tiffin_allowance }}</td>
                                    <td>{{ $shift->dinner_allowance ? 'Yes' : 'No' }}</td>
                                    <td>
                                        <a href="{{ route('admin.shiftsAction', ['form', $shift->id]) }}" class="btn btn-sm btn-success mb-1"><i class="bx bx-edit"></i></a>
                                        <a href="{{ route('admin.shiftsAction', ['delete', $shift->id]) }}" class="btn btn-sm btn-danger mb-1" onclick="return confirm('Are you sure?')"><i class="bx bx-trash"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    {{ $shifts->links('pagination') }}
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('js')
<script>
    // Check/uncheck all checkboxes
    document.getElementById('checkall')?.addEventListener('change', function() {
        document.querySelectorAll('input[name="checkid[]"]').forEach(el => el.checked = this.checked);
    });
</script>
@endpush
