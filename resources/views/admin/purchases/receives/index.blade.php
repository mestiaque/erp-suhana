@extends(adminTheme().'layouts.app')

@section('title')
<title>{{ websiteTitle('Purchase Receive List') }}</title>
@endsection

@push('css')
<style type="text/css">

table.table a {
    color: #000;
}
.badge-warning {
    color: #000;
    background-color: #d9a50c4d;
}
.badge-success {
    color: #035415;
    background-color: #17e64642;
}
    
</style>
@endpush

@section('contents')
    <div class="flex-grow-1">
        <div class="card mb-30">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Purchase Receive List</h3>

                <div class="dropdown d-flex">
                    @can('purchases_received.add')
                    <a href="javascript:void(0)" class="btn-custom primary mr-1" style="padding:5px 15px;" data-toggle="modal" data-target="#createReceiveModal"  >
                        <i class="fa fa-plus"></i> Add Receive
                    </a>
                    @endcan
                    <a href="{{route('admin.purchasesReceived')}}" class="btn-custom yellow">
                        <i class="bx bx-rotate-left"></i>
                    </a>

                </div>
            </div>


            <div class="card-body">
                @include(adminTheme().'alerts')

                <!-- Search Form -->
                <form action="{{route('admin.purchasesReceived')}}">
                    <div class="row">
                        <div class="col-md-6 mb-1">
                            <div class="input-group">
                                <input type="date" name="startDate" value="{{request()->startDate?Carbon\Carbon::parse(request()->startDate)->format('Y-m-d') :''}}" class="form-control" />
                                <input type="date" name="endDate" value="{{request()->endDate?Carbon\Carbon::parse(request()->endDate)->format('Y-m-d') :''}}" class="form-control" />
                            </div>
                        </div>

                        <div class="col-md-6 mb-1">
                            <div class="input-group">
                                <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Search Receive No, Purchase No" class="form-control" />
                                <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                            </div>
                        </div>
                    </div>
                </form>
                <br>

                <!-- Bulk Actions -->
                <form action="{{route('admin.purchasesReceived')}}">
                    <div class="row">
                        <div class="col-md-4">
                            {{-- <div class="input-group mb-1">
                                <select class="form-control form-control-sm rounded-0" name="action" required>
                                    <option value="">Select Action</option>
                                    <option value="1">Pending</option>
                                    <option value="2">Approved</option>
                                    <option value="3">Rejected</option>
                                    <option value="4">Trash</option>
                                    <option value="5">Delete</option>
                                </select>
                                <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Sure?')">Apply</button>
                            </div> --}}
                        </div>

                        <div class="col-md-8">
                            <ul class="statuslist p-0">
                                <li><a href="{{route('admin.purchasesReceived')}}">All ({{$totals->total}})</a></li>
                                <li><a href="{{route('admin.purchasesReceived',['status'=>'pending'])}}">Pending ({{$totals->pending}})</a></li>
                                <li><a href="{{route('admin.purchasesReceived',['status'=>'approved'])}}">Approved ({{$totals->approved}})</a></li>
                                <li><a href="{{route('admin.purchasesReceived',['status'=>'rejected'])}}">Rejected ({{$totals->rejected}})</a></li>
                                <li><a href="{{route('admin.purchasesReceived',['status'=>'trash'])}}">Trash ({{$totals->trash}})</a></li>
                            </ul>
                        </div>
                    </div>

                    <!-- Receive Table -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Branch</th>
                                <th>Receive No</th>
                                <th>Purchase No</th>
                                <th>Challan No</th>
                                <th>Items</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach($receives as $i => $r)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $r->branch?->name }}</td>
                                    <td><a href="{{route('admin.purchasesReceivedAction',['view',$r->id])}}" target="_blank">{{ $r->purchase_receive_no }}</a></td>
                                    <td>{{ $r->purchase_no ?? '--' }}</td>
                                    <td>{{ $r->challan_no ?? '--' }}</td>
                                    <td>{{ $r->items()->count() }} Items</td>
                                    <td>
                                        @if($r->status=='pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($r->status=='approved')
                                            <span class="badge bg-success text-white">Approved</span>
                                        @elseif($r->status=='temp')
                                            <span class="badge bg-secondary text-white">Temp</span>
                                        @elseif($r->status=='rejected')
                                            <span class="badge bg-danger text-white">Rejected</span>
                                        @elseif($r->status=='trash')
                                            <span class="badge bg-secondary">Trash</span>
                                        @endif
                                    </td>
                                    <td>{{ $r->created_at->format('d.m.Y') }}</td>
                                    <td class="text-center">
                                        @if(can('purchases_received.edit') || can('purchases_received.delete'))
                                        @can('purchases_received.edit')
                                        <a href="{{route('admin.purchasesReceivedAction',['edit',$r->id])}}" class="btn-custom success"><i class="bx bx-edit"></i></a>
                                        @endcan
                                        @can('purchases_received.delete')
                                        <a href="{{route('admin.purchasesReceivedAction',['delete',$r->id])}}"
                                            onclick="return confirm('Are You Sure To Delete?')"
                                            class="btn-custom danger"><i class="bx bx-trash"></i></a>
                                        @endcan
                                        @else -- @endif
                                    </td>
                                </tr>
                            @endforeach

                            @if($receives->count()==0)
                                <tr>
                                    <td colspan="9" style="text-align:center;color:#aaa;">No Record Found</td>
                                </tr>
                            @endif

                            </tbody>
                        </table>

                        {{ $receives->links('pagination') }}
                    </div>
                </form>
            </div>
        </div>
    </div>


    <!-- Create Purchase Receive Modal -->
    <div class="modal fade text-left" id="createReceiveModal" tabindex="-1">
       <div class="modal-dialog">
         <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Purchase Receive</h4>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                   <span aria-hidden="true">&times; </span>
                </button>
            </div>
            <div class="modal-body">
                <!-- Branch Selection -->
                <div class="form-group">
                    <label for="branchSelect">Select Branch</label>
                    <select id="branchSelect" class="form-control">
                        <option value="">-- Choose Branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Purchase Number Input (Initially Hidden) -->
                <div id="purchaseSection" style="display:none;">
                    <input type="text" id="searchPurchaseNo" class="form-control mt-2" placeholder="Type Purchase Number">
                    @include(adminTheme().'purchases.receives.includes.searchResults', [
                        'purchases' => $purchases,
                    ])
                </div>
                <p id="searchMsg" class="mt-2 text-danger" style="display:none;"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="createReceiveBtn">
                    <i class="bx bx-plus"></i> Create
                </button>
            </div>
         </div>
       </div>
    </div>
@endsection



@push('js')
    <script>
        $(function(){

            $('#branchSelect').on('change', function() {
                let branchId = $(this).val();
                if(branchId) {
                    $('#purchaseSection').slideDown();
                } else {
                    $('#purchaseSection').slideUp();
                    $('#searchPurchaseNo').val('');
                }
            });

            // ---------- LIVE SEARCH ----------
            $('#searchPurchaseNo').on('keyup', function(){
                let search = $(this).val();
                if(search.length < 1){
                    $('#purchaseSearchResults').hide();
                    return;
                }

                $.get("{{ route('admin.purchasesReceivedAction', 'search-purchase') }}", { search: search }, function(res){
                    console.log(res);
                    if(res.success){
                        $('#purchaseSearchResults').html(res.view).show();
                    }
                });
            });

            // ---------- SELECT FROM SEARCH ----------
            $(document).on('click','.selectPurchase', function(){
                let val = $(this).data('val');
                $('#searchPurchaseNo').val(val);
                $('#purchaseSearchResults').hide();
            });

            // ---------- CREATE RECEIVE ----------
            $('#createReceiveBtn').click(function(){
                let no = $('#searchPurchaseNo').val().trim();
                let branch_id = $('#branchSelect').val();

                $('#searchMsg').hide();

                if(branch_id === '' || branch_id === null){
                    $('#searchMsg').text('Please select branch').show();
                    return;
                }

                if(no === ''){
                    $('#searchMsg').text('Please enter purchase number').show();
                    return;
                }

                $.post("{{ route('admin.purchasesReceivedAction', ['create']) }}",
                    {_token: "{{ csrf_token() }}", purchase_no: no, branch_id: branch_id},
                    function(res){
                        if(res.success){
                            window.location.href = res.redirect;
                        } else {
                            $('#searchMsg').text(res.message).show();
                        }
                    }
                );
            });

        });
    </script>
@endpush
