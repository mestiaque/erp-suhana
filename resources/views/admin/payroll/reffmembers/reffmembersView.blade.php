@extends(adminTheme().'layouts.app') @section('title')
<title>{{$title->name}} Reff/Title Member Report</title>
@endsection @push('css')
<style type="text/css">
.single-stats-card-box.success h3{
    color: #28a745;
}
.single-stats-card-box.success .icon{
    background-color: #28a745;
}
</style>
@endpush @section('contents')

<div class="flex-grow-1" >
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3>Reff/Title Member View</h3>
            <div class="dropdown">
                <a href="{{route('admin.reffTitleList')}}" class="btn-custom yellow">
                    <i class='bx bx-left-arrow-alt'></i> Back
                </a> 
            </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            <div class="row">
                <div class="col-md-6">
                    <form action="{{route('admin.reffTitleListAction',['view',$title->id])}}">
                        <div class="row">
                            <div class="col-md-12 mb-0">
                                <label>Seach Here..</label>
                                <div class="input-group">
                                    <input type="date" name="startDate" value="{{$from->format('Y-m-d')}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                                    <input type="date" value="{{$to->format('Y-m-d')}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                                    <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-md-6">
                    <div class="single-stats-card-box {{$title->amount >=0?'success':'' }}">
                         <div class="icon">
                             <i class="bx bxs-badge-dollar"></i>
                         </div>
                         <span class="sub-title">{{$title->name}} </span>
                         <h3>BDT {{priceFormat($expenses->sum('amount')+$traddings->sum('amount'))}} <span class="badge"></h3>
                     </div>
                </div>
            </div>
            <hr>
            
            <div class="table-responsive">
                <table id="example" class="display nowrap" cellspacing="0" width="100%">
                    <thead>
                        <tr>
                            <th>Date</th>
                             <th>Title</th>
                            <th>Descrition</th>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>Date</th>
                            <th>Title</th>
                            <th>Descrition</th>
                            <th>Type</th>
                            <th>Amount</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @foreach($expenses as $j=>$expense)
                        <tr>
                            <td>{{$expense->created_at->format('d-m-Y')}}</td>
                            <td>
                                <span>{{$expense->name}}</span>
                            </td>
                            <td>{{$expense->description}}</td>
                            <td>Expense</td>
                            <td>
                                {{priceFormat($expense->amount)}}
                            </td>
                        </tr>
                        @endforeach
                        @foreach($traddings as $i=>$tradding)
                        <tr>
                            <td>{{$tradding->created_at->format('d-m-Y')}}</td>
                            <td>
                                <span>{{$tradding->title}}</span>
                                @if($tradding->imageFile) <a href="{{asset($tradding->imageFile->file_url)}}" target="_blank"><i class="bx bx-file"></i></a> @endif
                            </td>
                            <td>{{$tradding->description}}</td>
                            <td>Supplier Trading</td>
                            <td>
                                {{priceFormat($tradding->amount)}}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>
</div>

@endsection @push('js')

<script>
    $(document).ready(function () {
        
        $('#example').DataTable( {
	        dom: 'Bfrtip',
	        buttons: [
	            'excel', 'pdf', 'print'
	        ]
	    } );
        
    });
</script>

@endpush