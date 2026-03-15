@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Leads '.$action)}}</title>
@endsection @push('css')
<style type="text/css">
    .leadInfoTable tr th,.leadInfoTable tr td{
        padding:5px 10px;        
    }
    .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
        background-color: #fafafa;
    }
    .nav-tabs .nav-link {
        font-size: 18px;
        font-weight: bold;
    }
</style>
@endpush 

@section('contents')

<div class="flex-grow-1">
    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Leads {{$action}}</h3>
             <div class="dropdown">
                 <a href="{{route('admin.leads')}}" class="btn-custom primary"  style="padding:5px 15px;">
                      Back
                 </a>
                 @if($lead->status=='temp')
                 <a href="{{route('admin.leadsAction',['edit',$lead->id])}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
                 @else
                 <a href="{{route('admin.leadsAction',['view',$lead->id])}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
                 @endif
             </div>
        </div>
        <div class="card-body">
             @include(adminTheme().'alerts')
            <div>
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Lead Information</a>
                  </li>
                  @isset(json_decode(Auth::user()->permission->permission, true)['leads']['add'])
                  @if($lead->status!='temp')
                  <li class="nav-item">
                    <a class="nav-link" id="visits-tab" data-toggle="tab" href="#visits" role="tab" aria-controls="visits" aria-selected="false">Re-Visits</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="attachment-tab" data-toggle="tab" href="#attachment" role="tab" aria-controls="attachment" aria-selected="false">Attachment</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="meeting-tab" data-toggle="tab" href="#meeting" role="tab" aria-controls="meeting" aria-selected="false">Meeting</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="task-tab" data-toggle="tab" href="#task" role="tab" aria-controls="task" aria-selected="false">Task</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="noteList-tab" data-toggle="tab" href="#noteList" role="tab" aria-controls="noteList" aria-selected="false">Note</a>
                  </li>
                  @endif
                  @endisset
                </ul>
                <div class="tab-content" id="myTabContent">
                  <div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
                      <div class="card" style="padding: 5px;">
                          <div class="row">
                                @if($action=='edit')
                                <div class="col-md-10">
                                    @include(adminTheme().'leads.includes.leadEditForm')
                                </div>
                                @else
                                <div class="col-md-6">
                                    <div class="card" style="box-shadow: none;border: 1px solid #e6e3e3;">
                                        <div class="card-header">
                                            Details 
                                            @isset(json_decode(Auth::user()->permission->permission, true)['leads']['add'])
                                            <a href="{{route('admin.leadsAction',['edit',$lead->id])}}"  style="display: inline-block;margin-left: 15px;"><i class="bx bx-edit" style="font-size: 20px;"></i></a>
                                            @endisset
                                        </div>
                                        <div class="card-body">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <p>
                                                            <b>Assignee :</b> {{$lead->assineeUser?$lead->assineeUser->name:''}},</br>
                                                            <b>Created Date:</b> {{$lead->created_at->format('d-m-Y')}} <br>
                                                            
                                                            @php 
                                                                $nextVisit = $lead->next_visit_day ? Carbon\Carbon::parse($lead->next_visit_day): null; 
                                                                $isRed = false; 
                                                                if($nextVisit){
                                                                    $oneDayBefore = $nextVisit->copy()->subDay(2); 
                                                                    $today = Carbon\Carbon::today(); 
                                                                    $isRed = $today->gte($oneDayBefore);
                                                                } 
                                                            @endphp
                                                        
                                                            <b>Date of Next Call:</b> <span style="color: {{ $isRed ? 'red' : 'black' }}"> {{ $nextVisit ? $nextVisit->format('d-m-Y') : '' }} </span>
                                                                                        <br>
                                                            <b>Customer Status:</b> 
                                                            @if($lead->customer_status=='Not Potential')
                                                            <span class="badge" style="background: #9baaff;font-size: 14px;color: white;" >Not Potential</span>
                                                            @elseif($lead->customer_status=='Potential')
                                                            <span class="badge" style="background: #5970f3;font-size: 14px;color: white;" >Potential</span>
                                                            @elseif($lead->customer_status=='Very Potential')
                                                            <span class="badge" style="background: #0829e5;font-size: 14px;color: white;" >Very Potential</span>
                                                            @endif
                                                            
                                                            
                                                            <br> <b>Company Category:</b> {{$lead->company_category}} <br> <b>Company Status:</b> {{$lead->company_status}} <br>
                                                            <b>Lead Status:</b> 
                                                            
                                                            @if($lead->status=='Contacted')
                                                            <span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>
                                                            @elseif($lead->status=='Interested')
                                                            <span class="badge" style="background: #d5ab05;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>
                                                            @elseif($lead->status=='Follow-up Scheduled')
                                                            <span class="badge" style="background: #d5ab05;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>
                                                            @elseif($lead->status=='Meeting Done')
                                                            <span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>
                                                            @elseif($lead->status=='Proposal Sent')
                                                            <span class="badge" style="background: #0b9e97;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>
                                                            @elseif($lead->status=='Win')
                                                            <span class="badge" style="background: #670bc1;font-size: 14px;color: white;" >{{ucfirst($lead->status)}} (Convert Customer)</span>
                                                            @elseif($lead->status=='Canceled')
                                                            <span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>
                                                            @else
                                                            <span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($lead->status)}}</span>
                                                            @endif
                                                         </p>
                                                    </div>
                                                    <div class="col-md-6" style="align-items: center;display: flex;margin-bottom: 15px;justify-content: center;">
                                                        @isset(json_decode(Auth::user()->permission->permission, true)['leads']['add'])
                                                        @if($lead->status!='Canceled' && $lead->status!='Win')
                                                        <div>
                                                            <a href="{{route('admin.leadsAction',['convert',$lead->id])}}" class="btn btn-md btn-danger" onclick="return confirm('Are you want to convert Customer.')">Convert Customer</a>
                                                        </div>
                                                        @endif
                                                        @endisset
                                                    </div>
                                                </div>
                                            <div class="table-responsive leadInfoTable">
                                                <table class="table table-bordered">
                                                    <tr>
                                                        <th>Sister Concern</th>
                                                        <td>{{$lead->concern}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Lead Type</th>
                                                        <td>{{$lead->source}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Company Name</th>
                                                        <td>{{$lead->factory_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Owner Name</th>
                                                        <td>{{$lead->name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Owner Designation</th>
                                                        <td>{{$lead->designation}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Owner Mobile</th>
                                                        <td>{{$lead->mobile}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Owner Email</th>
                                                        <td>{{$lead->email}}</td>
                                                    </tr>
                                                    <tr style="background: #f8f8f8;">
                                                        <th></th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Key Person Name</th>
                                                        <td>{{$lead->key_parson_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Key Person Designation</th>
                                                        <td>{{$lead->key_parson_designation}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Mobile No</th>
                                                        <td>{{$lead->key_parson_mobile}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Whatsapps</th>
                                                        <td>{{$lead->key_parson_whatsapp_mobile}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>E-mail</th>
                                                        <td>{{$lead->key_parson_email}}</td>
                                                    </tr>
                                                    <tr style="background: #f8f8f8;">
                                                        <th></th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Partner Name</th>
                                                        <td>{{$lead->partner_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Partner Designation</th>
                                                        <td>{{$lead->partner_designation}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Partner Details</th>
                                                        <td>{{$lead->partner_details}}</td>
                                                    </tr>
                                                    <tr style="background: #f8f8f8;">
                                                        <th></th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>PM Name</th>
                                                        <td>{{$lead->pm_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>PM Designation</th>
                                                        <td>{{$lead->pm_designation}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>PM Details</th>
                                                        <td>{{$lead->pm_details}}</td>
                                                    </tr>
                                                    <tr style="background: #f8f8f8;">
                                                        <th></th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Engineer Name</th>
                                                        <td>{{$lead->engineer_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Engineer Designation</th>
                                                        <td>{{$lead->engineer_designation}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Engineer Detials</th>
                                                        <td>{{$lead->engineer_details}}</td>
                                                    </tr>
                                                    <tr style="background: #f8f8f8;">
                                                        <th></th>
                                                        <td></td>
                                                    </tr>
                                                    <tr>
                                                        <th>Company Address</th>
                                                        <td>{{$lead->fullAddress()}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th>Interested Service</th>
                                                        <td>
                                                            @foreach($lead->services()->get() as $ser)
                                                            {{$ser->name}} ,
                                                            @endforeach
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th>Remarks</th>
                                                        <td>{{$lead->requirement}}</td>
                                                    </tr>
                                                    <!--<tr>-->
                                                    <!--    <th>Remarks</th>-->
                                                    <!--    <td>{{$lead->notes}}</td>-->
                                                    <!--</tr>-->
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                  @if($lead->status!='temp')
                                    <div class="card mb-3" style="box-shadow: none;border: 1px solid #e6e3e3;">
                                        <div class="card-header">
                                            Visits 
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered leadInfoTable">
                                                    <tr>
                                                        <th style="min-width: 220px;">Visit Date</th>
                                                        <th style="min-width: 180px;width: 180px;">Location</th>
                                                        <th style="min-width:150px;width:150px;">Status</th>
                                                    </tr>
                                                    @foreach($lead->visits()->latest()->get() as $visit)
                                                    <tr>
                                                        <td>
                                                            {{$visit->visit_date?Carbon\Carbon::parse($visit->visit_date)->format('d-m-Y h:i A'):''}}
                                                            @if($visit->imageFile)
                                                            <a href="{{asset($visit->image())}}" download="" style="margin-left: 5px;color: #e1000a;"><i class="bx bx-file"></i></a>
                                                            @endif
                                                        </td>
                                                        <td>{{$visit->location}}</td>
                                                        <td >
                                                            @if($visit->status=='Not Potential')
                                                            <span class="badge" style="background: #9baaff;font-size: 14px;color: white;" >Not Potential</span>
                                                            @elseif($visit->status=='Potential')
                                                            <span class="badge" style="background: #5970f3;font-size: 14px;color: white;" >Potential</span>
                                                            @elseif($visit->status=='Very Potential')
                                                            <span class="badge" style="background: #0829e5;font-size: 14px;color: white;" >Very Potential</span>
                                                            @endif
                                                            <!--@if($visit->status=='In progress')-->
                                                            <!--<span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                                            <!--@elseif($visit->status=='Completed')-->
                                                            <!--<span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                                            <!--@elseif($visit->status=='Canceled')-->
                                                            <!--<span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                                            <!--@elseif($visit->status=='Rescheduled')-->
                                                            <!--<span class="badge" style="background: #f326eb;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                                            <!--@else-->
                                                            <!--<span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($visit->status)}}</span>-->
                                                            <!--@endif-->
                                                            <a href="javascript:void(0)" class="EditVisit" data-url="{{route('admin.leadsAction',['call-editvisit',$lead->id,'visit_id'=>$visit->id])}}" ><i class="bx bx-edit" style="font-size: 20px;margin-left: 5px;"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-3" style="box-shadow: none;border: 1px solid #e6e3e3;">
                                        <div class="card-header">
                                            Attachment 
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered leadInfoTable">
                                                    <tr>
                                                        <th style="min-width: 220px;">Attachment</th>
                                                        <th style="min-width: 180px;width: 180px;">Title</th>
                                                        <th style="min-width:50px;width:50px;">Action</th>
                                                    </tr>
                                                    @foreach($lead->attachmentFiles as $attachment)
                                                    <tr>
                                                        <td>
                                                            <a href="{{asset($attachment->file_url)}}" download="" style="margin-left: 5px;color: #e1000a;"><i class="bx bx-file"></i>  Download</a>
                                                        </td>
                                                        <td>{{$attachment->alt_text}}</td>
                                                        <td style="text-align:center;">
                                                            <a href="{{route('admin.leadsAction',['call-deletenattachment',$lead->id,'file_id'=>$attachment->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Are you want to delete?')"><i class="bx bx-x"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-3" style="box-shadow: none;border: 1px solid #e6e3e3;">
                                        <div class="card-header">
                                            Meeting 
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered leadInfoTable">
                                                    <tr>
                                                        <th style="min-width: 220px;">Meeting Title</th>
                                                        <th style="min-width: 180px;width: 180px;">Date & Time</th>
                                                        <th style="min-width:150px;width:150px;">Status</th>
                                                    </tr>
                                                    @foreach($lead->meetings()->latest()->get() as $meeting)
                                                    <tr>
                                                        <td>
                                                            <a href="javascript:void(0)">{{$meeting->name}}</a>
                                                        </td>
                                                        <td>{{$meeting->created_at->format('d-m-Y h:i A')}}</td>
                                                        <td >
                                                            @if($meeting->status=='In progress')
                                                            <span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                                            @elseif($meeting->status=='Completed')
                                                            <span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                                            @elseif($meeting->status=='Canceled')
                                                            <span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                                            @elseif($meeting->status=='Rescheduled')
                                                            <span class="badge" style="background: #f326eb;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                                            @else
                                                            <span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($meeting->status)}}</span>
                                                            @endif
                                                            <a href="javascript:void(0)" class="EditMeeting" data-url="{{route('admin.leadsAction',['call-editmeeting',$lead->id,'meeting_id'=>$meeting->id])}}" ><i class="bx bx-edit" style="font-size: 20px;margin-left: 5px;"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="card mb-3" style="box-shadow: none;border: 1px solid #e6e3e3;">
                                        <div class="card-header">
                                            Task 
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered leadInfoTable">
                                                    <tr>
                                                        <th style="min-width: 220px;" >Task Title</th>
                                                        <th style="min-width: 100px;width: 100px;">Due Date</th>
                                                        <th style="min-width: 100px;width: 100px;">Priority</th>
                                                        <th style="min-width:150px;width:150px;">Status</th>
                                                    </tr>
                                                    @foreach($lead->tasks()->latest()->get() as $task)
                                                    <tr>
                                                        <td>
                                                            <a href="javascript:void(0)">{{$task->name}}</a>
                                                            @if($task->imageFile)
                                                            <a href="{{asset($task->image())}}" download="" style="margin-left: 5px;color: #e1000a;"><i class="bx bx-file"></i></a>
                                                            @endif
                                                        </td>
                                                        <td>{{$task->created_at->format('d-m-Y')}}</td>
                                                        <td>{{ucfirst($task->priority)}}</td>
                                                        <td>
                                                            @if($task->status=='in progress')
                                                            <span class="badge" style="background: #ff108c;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                                            @elseif($task->status=='review')
                                                            <span class="badge" style="background: #d5ab05;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                                            @elseif($task->status=='completed')
                                                            <span class="badge" style="background: #13c238;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                                            @elseif($task->status=='canceled')
                                                            <span class="badge" style="background: #ff2e37;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                                            @elseif($task->status=='on hold')
                                                            <span class="badge" style="background: #f326eb;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                                            @else
                                                            <span class="badge" style="background: #2c66cb;font-size: 14px;color: white;" >{{ucfirst($task->status)}}</span>
                                                            @endif
     
                                                            <a href="javascript:void(0)" class="EditTask" data-url="{{route('admin.leadsAction',['call-edittask',$lead->id,'task_id'=>$task->id])}}" ><i class="bx bx-edit" style="font-size: 20px;margin-left: 5px;"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="card mb-3" style="box-shadow: none;border: 1px solid #e6e3e3;">
                                        <div class="card-header">
                                            Note 
                                        </div>
                                        <div class="card-body">
                                            <div class="table-responsive">
                                                <table class="table table-bordered leadInfoTable">
                                                    <tr>
                                                        <th style="min-width: 220px;">Note</th>
                                                        <th style="min-width:150px;width:150px;">Date</th>
                                                    </tr>
                                                    @foreach($lead->notes()->latest()->get() as $note)
                                                    <tr>
                                                       
                                                        <td>
                                                            <div>
                                                                <a href="{{route('admin.leadsAction',['call-deletenote',$lead->id,'note_id'=>$note->id])}}" style="border: 1px solid #cb2020;padding: 1px;color:red;border-radius: 5px;" onclick="return confirm('Are you want to delete?')"><i class="bx bx-x"></i></a>
                                                            </div>
                                                            {!!nl2br(e($note->description))!!}
                                                            
                                                        </td>
                                                        <td>
                                                            {{$note->created_at->format('d-m-Y')}}
     
                                                            <a href="javascript:void(0)" class="EditNote" data-url="{{route('admin.leadsAction',['call-editnote',$lead->id,'note_id'=>$note->id])}}" ><i class="bx bx-edit" style="font-size: 20px;margin-left: 5px;"></i></a>
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                              </div>
                                @endif
                              
                          </div>
                      </div>
                  </div>
                  <div class="tab-pane fade" id="visits" role="tabpanel" aria-labelledby="visits-tab">
                      <div class="card">
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="visitAreaAppend">
                                    @include(adminTheme().'leads.includes.visitForm')
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="tab-pane fade" id="attachment" role="tabpanel" aria-labelledby="attachment-tab">
                      <div class="card">
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="attachmentAreaAppend">
                                    @include(adminTheme().'leads.includes.attachmentForm')
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                  <div class="tab-pane fade" id="meeting" role="tabpanel" aria-labelledby="meeting-tab">
                      <div class="card">
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="meetingAreaAppend">
                                    @include(adminTheme().'leads.includes.meetingForm')
                                  </div>
                              </div>
                          </div>
                      </div>
                  </div>
                    <div class="tab-pane fade" id="task" role="tabpanel" aria-labelledby="task-tab">
                      <div class="card">
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="taskAreaAppend">
                                    @include(adminTheme().'leads.includes.taskForm')
                                  </div>
                              </div>
                          </div>
                      </div>
                    </div>
                    <div class="tab-pane fade" id="noteList" role="tabpanel" aria-labelledby="noteList-tab">
                        <div class="card">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="noteAreaAppend">
                                        @include(adminTheme().'leads.includes.noteForm')
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection 
@push('js')
<script>
    $(document).ready(function(){
        
        $('.select2').select2({
            placeholder: $('.select2').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        
        $('.select22').select2({
            placeholder: $('.select22').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        
        $('.select23').select2({
            placeholder: $('.select23').data('placeholder'),
            width: '100%',
            allowClear: true
        });
        
        $('.EditVisit').click(function(){
            $('#visits-tab').tab('show');
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            var url =$(this).data('url');
            visitCall(url);
            $('.visitAreaAppend').addClass('editForm');
        });
        
        $('#visits-tab').click(function(){
            var edit = $('.visitAreaAppend').hasClass('editForm');
            if (edit){
               $('.visitAreaAppend').empty().append('<img src="{{asset('medies/loading.gif')}}" />');
                var url ="{{route('admin.leadsAction',['call-visit',$lead->id])}}";
                visitCall(url);   
                $('.visitAreaAppend').removeClass('editForm');
            }
        });
        
        function visitCall(url){
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              success : function(data){
                $('.visitAreaAppend').empty().append(data.view);
              },error: function () {
                  alert('error');
    
                }
            });
        }
        
        $('.EditMeeting').click(function(){
            $('#meeting-tab').tab('show');
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            var url =$(this).data('url');
            meetingCall(url);
            $('.meetingAreaAppend').addClass('editForm');
        });
        
        $('#meeting-tab').click(function(){
            var edit = $('.meetingAreaAppend').hasClass('editForm');
            if (edit){
               $('.meetingAreaAppend').empty().append('<img src="{{asset('medies/loading.gif')}}" />');
                var url ="{{route('admin.leadsAction',['call-meeting',$lead->id])}}";
                meetingCall(url);   
                $('.meetingAreaAppend').removeClass('editForm');
            }
        });
        
        function meetingCall(url){
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              success : function(data){
                $('.meetingAreaAppend').empty().append(data.view);
              },error: function () {
                  alert('error');
    
                }
            });
        }
        
        
        $('.customerStatusChenage').change(function(){
            
            var status =$(this).val();
            
            if(status=='Not Potential'){
                $('.nextCall').attr('required',false);
                $('.nextCallStar').text('');
                $('.customerRequire').attr('required',false);
                $('.customerRequireStar').text('');
            }else{
                $('.customerRequire').attr('required',true);
                $('.customerRequireStar').text('*');
                $('.nextCall').attr('required',true);
                $('.nextCallStar').text('*');
            }
            
        });
        
        
        $('.EditTask').click(function(){
            $('#task-tab').tab('show');
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            var url =$(this).data('url');
            taskCall(url);
            $('.taskAreaAppend').addClass('editForm');
        });
        
        $('#task-tab').click(function(){
            var edit = $('.taskAreaAppend').hasClass('editForm');
            if (edit){
               $('.taskAreaAppend').empty().append('<img src="{{asset('medies/loading.gif')}}" />');
                var url ="{{route('admin.leadsAction',['call-task',$lead->id])}}";
                taskCall(url);   
                $('.taskAreaAppend').removeClass('editForm');
            }
        });
        
        function taskCall(url){
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              success : function(data){
                $('.taskAreaAppend').empty().append(data.view);
              },error: function () {
                  alert('error');
    
                }
            });
        }
        
        $('.EditNote').click(function(){
            $('#noteList-tab').tab('show');
            $('html, body').animate({ scrollTop: 0 }, 'fast');
            var url =$(this).data('url');
            noteCall(url);
            $('.noteAreaAppend').addClass('editForm');
        });
        
        $('#noteList-tab').click(function(){
            var edit = $('.noteAreaAppend').hasClass('editForm');
            if (edit){
               $('.noteAreaAppend').empty().append('<img src="{{asset('medies/loading.gif')}}" />');
                var url ="{{route('admin.leadsAction',['call-note',$lead->id])}}";
                noteCall(url);   
                $('.noteAreaAppend').removeClass('editForm');
            }
        });
        
        function noteCall(url){
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              success : function(data){
                $('.noteAreaAppend').empty().append(data.view);
              },error: function () {
                  alert('error');
    
                }
            });
        }
        
        
        $(document).on('keyup','.mobileInput',function(){
            $('.mobileDoubleErr').html('');
        });
        $(document).on('change','.mobileInput',function(){
            var mobile =$(this).val();
            var url =$(this).data('url');
            if (mobile.length > 1) {
                $.ajax({
                  url:url,
                  dataType: 'json',
                  cache: false,
                  data: {mobile:mobile},
                  success : function(res){
                      $('.mobileDoubleErr').html(res.message);
                  },error: function () {
                      alert('error');
        
                    }
                });
            }
            
        });
        
        $(document).on('click','.addPerson, .removePerson',function(){
            var url =$(this).data('url');
            var type =$(this).data('type');
            $.ajax({
                url:url,
                dataType: 'json',
                cache: false,
                data: {'type':type},
                success : function(data){
                $('.personList_'+type).empty().append(data.view);
                },error: function () {
                  alert('error');
                }
            });
        });
        
        $(document).on('change','.updatePerson',function(){
            var url ="{{route('admin.leadsAction',['update-person',$lead->id])}}";
            var person_id =$(this).data('id');
            var type =$(this).data('type');
            var column =$(this).data('column');
            var key_value =$(this).val();
            $.ajax({
                url:url,
                dataType: 'json',
                cache: false,
                data: {'type':type,'person_id':person_id,'column':column,'key_value':key_value},
                success : function(data){
                // $('.machineList').empty().append(data.view);
                },error: function () {
                  alert('error');
                }
            });
        });
        
    });
</script>
@endpush