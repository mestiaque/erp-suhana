@extends(adminTheme().'layouts.app') @section('title')
<title>Engineer Visit</title>
@endsection @push('css')
<style type="text/css">
.companyGrid {
    border: 1px solid #ece5e5;
    padding: 10px;
    border-radius: 7px;
    background: #f9f9f9;
}
</style>

@endpush @section('contents')


@include(adminTheme().'alerts')

<div class="flex-grow-1" >
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                    <h4 class="card-title">Engineer Visit Area </h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{route('admin.engineerVisitsAction',['update',$visit->id])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <div class="col-md-4">
                                <div class="">
                                    <div class="gorm-group mb-3">
                                        <label>Visit Date*</label>
                                        <input type="date" name="created_at" value="{{$visit->created_at->format('Y-m-d')}}" class="form-control" required="" >
                                        @if ($errors->has('created_at'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('created_at') }}</p>
                                        @endif
                                    </div>
                                    <div class="gorm-group mb-3">
                                        <label>Enginners*</label>
                                        <select class="form-control" name="enginner_id" required="">
                                            <option value="">Select Enginner</option>
                                            @foreach($engineers as $engineer)
                                            <option value="{{$engineer->id}}" {{$visit->engineer_id==$engineer->id?'selected':''}}  >{{$engineer->name}} - {{$engineer->mobile?:$engineer->email}}</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('enginner_id'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('enginner_id') }}</p>
                                        @endif
                                    </div>
                                    <div>
                                        <p>
                                            <b>Total Company:</b> <span class="totalCompany">0</span> Compnay
                                            <br> 
                                            <b>Mail Notify:</b> <label><input type="checkbox" name="mail_send" > Send </label>
                                            <br>
                                            <b>App Notify:</b> <label><input type="checkbox" name="app_notify" > Send </label>
                                
                                        </p>
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <h3><b>{{$visit->fullAddress()}}</b> Area Company list</h3>

                                @php
                                    $companyIds = json_decode($visit->company_ids ?? '[]', true);
                                @endphp
            
                                <div>
                                    @if($companiesAll->count() > 0)
                                    <div class="row" style="margin:0 -5px;">
                                        @foreach($companiesAll as $company)
                                        <div class="col-md-4" style="padding:5px;">
                                            <div class="companyGrid">
                                                <div class="checkbox">
                                                     <input class="inp-cbx companyCheck" id="status_{{$company->id}}" type="checkbox" name="company[]" value="{{$company->id}}" style="display: none;"
                                                     
                                                     @if(
                                                        $visit->status == 'temp'
                                                        || in_array((string)$company->id, $companyIds)
                                                    )
                                                        checked
                                                    @endif
                                                     />
                                                     <label class="cbx" for="status_{{$company->id}}">
                                                         <span>
                                                             <svg width="12px" height="10px" viewbox="0 0 12 10">
                                                                 <polyline points="1.5 6 4.5 9 10.5 1"></polyline>
                                                             </svg>
                                                         </span>
                                                         Active
                                                     </label>
                                                 </div>
                                                <br><b>Company:</b> {{$company->factory_name}}
                                                <br><b>Name:</b> {{$company->owner_name}}
                                                <br><b>Address:</b> {{$company->fullAddress()}}
                                                <br><b>Mobile:</b> {{$company->owner_mobile}}
                                             </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                        <div>
                                            <h4>No Company Found </h4>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection @push('js')

<script>
    $(document).ready(function () {
        
        function updateTotal() {
            let total = $('.companyCheck:checked').length;
            $('.totalCompany').text(total);
        }
    
        // Update on first load
        updateTotal();
    
        // Update whenever a checkbox is changed
        $('.companyCheck').on('change', function() {
            updateTotal();
        });
    });
</script>

@endpush