@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Tasks List')}}</title>
@endsection @push('css')
<style type="text/css">
    
    .reminderGrid {
        display: flex;
        justify-content: space-between;
        border: 1px solid #d3cfcf;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 10px;
        position: relative; /* Positioning for the dropdown */
    }
    
    .reminderGrid .text{
        width:100%;
    }
    .reminderGrid .text h5{
        margin: 0;
        border-bottom: 1px solid #f3f3f3;
        padding-bottom: 8px;
    }
    .action {
        position: relative;
    }
    
    .dropdown-content {
        display: none; /* Hide dropdown by default */
        position: absolute;
        top: 20px; /* Position the dropdown below the icon */
        right: 0;
        background-color: #f9f9f9;
        min-width: 160px;
        box-shadow: 0px 8px 16px rgba(0, 0, 0, 0.2);
        z-index: 1;
    }
    
    .dropdown-content a {
        color: black;
        padding: 8px 15px;
        text-decoration: none;
        display: block;
    }
    
    .dropdown-content a:hover {
        background-color: #ddd;
    }
    
    .bx {
        cursor: pointer;
        font-size: 24px;
    }

</style>
@endpush @section('contents')

<div class="flex-grow-1">
    

    <!-- Start -->
    <div class="card mb-30">
        <div class="card-header d-flex justify-content-between align-items-center">
             <h3>Reminder List</h3>
             <div class="dropdown">
                 <a href="{{route('admin.reminders')}}" class="btn-custom yellow">
                     <i class="bx bx-rotate-left"></i>
                 </a>
             </div>
        </div>
        <div class="card-body">
            @include(adminTheme().'alerts')
            
            <ul class="nav nav-tabs" id="myTab" role="tablist">
              <li class="nav-item">
                <a class="nav-link active" id="task-tab" data-toggle="tab" href="#task" role="tab" aria-controls="task" aria-selected="true">Task ({{$tasks->count()}})</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="meeting-tab" data-toggle="tab" href="#meeting" role="tab" aria-controls="meeting" aria-selected="false">Meeting ({{$meetings->count()}})</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="Visit-tab" data-toggle="tab" href="#Visit" role="tab" aria-controls="Visit" aria-selected="false">Visits ({{$visits->count()}})</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="Commitment-tab" data-toggle="tab" href="#Commitment" role="tab" aria-controls="Commitment" aria-selected="false">Commitment ({{$commitments->count()}})</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="dueCollect-tab" data-toggle="tab" href="#dueCollect" role="tab" aria-controls="dueCollect" aria-selected="false">Due Collection ({{$dueCollects->count()}})</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" id="service-tab" data-toggle="tab" href="#service" role="tab" aria-controls="service" aria-selected="false">Service ({{$services->count()}})</a>
              </li>
            </ul>
            
            <div class="tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="task" role="tabpanel" aria-labelledby="task-tab">
                    <div class="card">
                        
                        
                        <div class="reminderList">
                            @foreach($tasks as $task)
                            <div class="reminderGrid">
                                <div class="text">
                                    <h5>
                                        <b>Due Date:</b> {{Carbon\Carbon::parse($task->due_date)->format('Y-m-d')}} <b>Status:</b> {{ucfirst($task->status)}}
                                    </h5>
                                    <p>
                                        <b>Assinee:</b> 
                                        @if($task->type==1)
                                            {{$task->company?$task->company->name:'Not Found'}}
                                        @else
                                            {{$task->company?$task->company->factory_name:'Not Found'}}
                                        @endif
                                        company task for
                                        {{$task->assinee?$task->assinee->name:'Not Found'}}
                                        @if($task->description)
                                          <br><b>Description:</b> {{$task->description}}
                                        @endif
                                    </p>
                                    @if($task->imageFile)
                                        <b>Attachment:</b> <a href="{{asset($task->image())}}" download="">Download</a>
                                    @endif
                                </div>
                                <div class="action">
                                    <i class="bx bx-dots-vertical-rounded" onclick="toggleDropdown()"></i>
                                    <!--<div id="dropdown" class="dropdown-content">-->
                                    <!--    <a href="#" onclick="editTask()"><span style="display: flex;"><i class='bx bx-edit' style="font-size: 18px;color: #13bb37;margin-right:8px;" ></i> Edit</span></a>-->
                                    <!--    <a href="#" onclick="deleteTask()"><span style="display: flex;"><i class='bx bx-trash' style="font-size: 18px;color: #ff172d;margin-right:8px;" ></i> Delete</span></a>-->
                                    <!--    <a href="#" onclick="markDone()"><span style="display: flex;"><i class='bx bx-check-circle' style="font-size: 18px;color: #007bff;margin-right:8px;"></i> Done</span></a>-->
                                    <!--</div>-->
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        @if($tasks->count()==0)
                        <div style="padding: 50px 0;font-size: 24px;color: #cfcdcd;">
                            No Task reminder
                        </div>
                        @endif
                        
                    </div>
                </div>
                <div class="tab-pane fade" id="meeting" role="tabpanel" aria-labelledby="meeting-tab">
                    <div class="card">
                        
                        <div class="reminderList">
                            @foreach($meetings as $meeting)
                            <div class="reminderGrid">
                                <div class="text">
                                    <h5>
                                        <b>Meeting Date:</b> {{$meeting->created_at->format('d-m-Y')}} <b>Status:</b> {{ucfirst($meeting->status)}}
                                    </h5>
                                    <p>
                                        <b>Perticipants:</b> 
                                        @foreach($meeting->participantsUsers()->get() as $user)
                                        @if($meeting->type==1)
                                        <span>{{$user->name}} - {{$user->email}}</span>
                                        @else
                                        <span>{{$user->factory_name}} - {{$user->owner_name}}</span>
                                        @endif
                                        @endforeach
                                        
                                        @if($meeting->hostUser)
                                        meeting host by  {{$meeting->hostUser->name}}
                                        @endif
                                        
                                        @if($meeting->description)
                                          <br><b>Description:</b> {{$meeting->description}}
                                        @endif
                                    </p>
                                    @if($meeting->location)
                                        <b>Location:</b> 
                                        @if(preg_match('/^https?:\/\//i', $meeting->location))
                                        <a href="{{$meeting->location}}" download="">{{$meeting->location}}</a>
                                        @else
                                        {{$meeting->location}}
                                        @endif
                                    @endif
                                </div>
                                <div class="action">
                                    <i class="bx bx-dots-vertical-rounded" onclick="toggleDropdown()"></i>
                                    <!--<div id="dropdown" class="dropdown-content">-->
                                    <!--    <a href="#" onclick="editTask()"><span style="display: flex;"><i class='bx bx-edit' style="font-size: 18px;color: #13bb37;margin-right:8px;" ></i> Edit</span></a>-->
                                    <!--    <a href="#" onclick="deleteTask()"><span style="display: flex;"><i class='bx bx-trash' style="font-size: 18px;color: #ff172d;margin-right:8px;" ></i> Delete</span></a>-->
                                    <!--    <a href="#" onclick="markDone()"><span style="display: flex;"><i class='bx bx-check-circle' style="font-size: 18px;color: #007bff;margin-right:8px;"></i> Done</span></a>-->
                                    <!--</div>-->
                                </div>
                            </div>
                            @endforeach
                            
                            @if($meetings->count()==0)
                            <div style="padding: 50px 0;font-size: 24px;color: #cfcdcd;">
                                No Meeting reminder
                            </div>
                            @endif
                            
                        </div>
                        
                    </div>
                </div>
                <div class="tab-pane fade" id="Visit" role="tabpanel" aria-labelledby="Visit-tab">
                    <div class="card">
                        
                        <div class="reminderList">
                            @foreach($visits as $visit)
                            <div class="reminderGrid">
                                <div class="text">
                                    <h5>
                                        <b>Visit Date:</b> {{$visit->visit_date?Carbon\Carbon::parse($visit->visit_date)->format('d-m-Y'):''}} <b>Status:</b> {{ucfirst($visit->status)}}
                                    </h5>
                                    <p>
                                        <b>Assinee:</b> 
                                        @if($visit->type==1)
                                            {{$visit->company?$visit->company->name:'Not Found'}}
                                        @else
                                            {{$visit->company?$visit->company->factory_name:'Not Found'}}
                                        @endif
                                        
                                        visit by  {{$visit->assinee?$visit->assinee->name:'Not Found'}}

                                        @if($visit->location)
                                        <b>Address:</b>  {{$visit->location}}
                                        @endif
                                        
                                        @if($visit->description)
                                          <br><b>Description:</b> {{$visit->description}}
                                        @endif
                                    </p>
                                    @if($visit->imageFile)
                                        <b>Attachment:</b> <a href="{{asset($visit->image())}}" download="">Download</a>
                                    @endif
                                </div>
                                <div class="action">
                                    <i class="bx bx-dots-vertical-rounded" onclick="toggleDropdown()"></i>
                                    <!--<div id="dropdown" class="dropdown-content">-->
                                    <!--    <a href="#" onclick="editTask()"><span style="display: flex;"><i class='bx bx-edit' style="font-size: 18px;color: #13bb37;margin-right:8px;" ></i> Edit</span></a>-->
                                    <!--    <a href="#" onclick="deleteTask()"><span style="display: flex;"><i class='bx bx-trash' style="font-size: 18px;color: #ff172d;margin-right:8px;" ></i> Delete</span></a>-->
                                    <!--    <a href="#" onclick="markDone()"><span style="display: flex;"><i class='bx bx-check-circle' style="font-size: 18px;color: #007bff;margin-right:8px;"></i> Done</span></a>-->
                                    <!--</div>-->
                                </div>
                            </div>
                            @endforeach
                            @if($visits->count()==0)
                            <div style="padding: 50px 0;font-size: 24px;color: #cfcdcd;">
                                No Visit reminder
                            </div>
                            @endif
                        </div>
                        
                    </div>
                </div>
                
                <div class="tab-pane fade" id="Commitment" role="tabpanel" aria-labelledby="Commitment-tab">
                    <div class="card">
                        
                        <div class="reminderList">
                            @foreach($commitments as $commitment)
                            <div class="reminderGrid">
                                <div class="text">
                                    <h5>
                                        <b>Commitment Date:</b> {{$commitment->date_time?Carbon\Carbon::parse($commitment->date_time)->format('d-m-Y'):''}} <b>Status:</b> {{ucfirst($commitment->status)}}
                                    </h5>
                                    <p>
                                        <b>{{$commitment->commitment_type}}</b>
                                        <br>
                                        <b>Assinee:</b> {{$commitment->assinee?$commitment->assinee->name:'Not Found'}}

                                        @if($commitment->company)
                                        <b>Company:</b> ({{$commitment->company->deed_serial}}/{{$commitment->company->concernShort()}}) -
                                        <a href="{{route('admin.companiesAction',['commitment',$commitment->company->id])}}" target="_blank">{{$commitment->company->factory_name ?: $commitment->company->owner_name}}</a>
                                        @endif
                                        <br>
                                        <b>Payment Type:</b> {{$commitment->payment_type}} , <b>Amount:</b> {{priceFullFormat($commitment->amount)}}
                                        <br>
                                        @if($commitment->note)
                                          <br><b>Description:</b> {{$commitment->note}}
                                        @endif
                                    </p>
                                        <b>Created Date:</b> {{$commitment->created_at->format('d-m-Y')}}
                                </div>
                                <div class="action">
                                    <i class="bx bx-dots-vertical-rounded" onclick="toggleDropdown()"></i>
                                    <!--<div id="dropdown" class="dropdown-content">-->
                                    <!--    <a href="#" onclick="editTask()"><span style="display: flex;"><i class='bx bx-edit' style="font-size: 18px;color: #13bb37;margin-right:8px;" ></i> Edit</span></a>-->
                                    <!--    <a href="#" onclick="deleteTask()"><span style="display: flex;"><i class='bx bx-trash' style="font-size: 18px;color: #ff172d;margin-right:8px;" ></i> Delete</span></a>-->
                                    <!--    <a href="#" onclick="markDone()"><span style="display: flex;"><i class='bx bx-check-circle' style="font-size: 18px;color: #007bff;margin-right:8px;"></i> Done</span></a>-->
                                    <!--</div>-->
                                </div>
                            </div>
                            @endforeach
                            @if($commitments->count()==0)
                            <div style="padding: 50px 0;font-size: 24px;color: #cfcdcd;">
                                No Commitment reminder
                            </div>
                            @endif
                        </div>
                        
                    </div>
                </div>
                
                <div class="tab-pane fade" id="dueCollect" role="tabpanel" aria-labelledby="dueCollect-tab">
                    <div class="card">
                        
                        <div class="reminderList">
                            @foreach($dueCollects as $collect)
                            <div class="reminderGrid">
                                <div class="text">
                                    <h5>
                                        <b>Due Date:</b> {{$collect->created_at->format('d-m-Y')}} <b>Status:</b> {{ucfirst($collect->status)}}
                                    </h5>
                                    <p>
                                        <b>Assinee:</b> 
                                       {{$collect->assinee?$collect->assinee->name:'Not found'}}
                                       <b>Due Amount:</b> {{priceFullFormat($collect->amount)}}
                                       <b>Company:</b> 
                                       @if($collect->company)
                                       <a href="{{route('admin.companiesAction',['sales',$collect->user_id])}}">{{$collect->billing_name}}</a>
                                       @else
                                       Not Found
                                       @endif
                                    </p>

                                </div>
                                <div class="action">
                                    <i class="bx bx-dots-vertical-rounded" onclick="toggleDropdown()"></i>
                                    <!--<div id="dropdown" class="dropdown-content">-->
                                    <!--    <a href="{{route('admin.companiesAction',['sales',$collect->user_id])}}"><span style="display: flex;"><i class='bx bx-eye' style="font-size: 18px;color: #13bb37;margin-right:8px;" ></i> View</span></a>-->
                                    <!--</div>-->
                                </div>
                            </div>
                            @endforeach
                            @if($dueCollects->count()==0) 
                            <div style="padding: 50px 0;font-size: 24px;color: #cfcdcd;">
                                No due Collect reminder
                            </div>
                            @endif
                        </div>
                        
                    </div>
                </div>
                
                <div class="tab-pane fade" id="service" role="tabpanel" aria-labelledby="service-tab">
                    <div class="card">
                        
                        <div class="reminderList">
                            @foreach($services as $service)
                            <div class="reminderGrid">
                                <div class="text">
                                    <h5>
                                        <b>Due Date:</b> {{$service->created_at->format('d-m-Y')}} <b>Status:</b> {{ucfirst($service->status)}}
                                    </h5>
                                    <p>
                                        <b>Employee:</b> 
                                       {{$service->employee?$service->employee->name:'Not found'}}
                                       <b>Company:</b> 
                                       @if($service->company)
                                       <a href="{{route('admin.companiesAction',['service',$service->company_id])}}">{{$service->company->owner_name}}</a>
                                       @else
                                       Not Found
                                       @endif
                                       
                                       
                                       
                                    </p>

                                      <b> 
                                      {{$service->title}}
                                      </b>
                                    <div>
                                        {{$service->description}}
                                    </div>

                                </div>
                                <div class="action">
                                    <i class="bx bx-dots-vertical-rounded" onclick="toggleDropdown()"></i>
                                    
                                </div>
                            </div>
                            @endforeach
                            @if($services->count()==0) 
                            <div style="padding: 50px 0;font-size: 24px;color: #cfcdcd;">
                                No due Collect reminder
                            </div>
                            @endif
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
    $(document).ready(function() {
        // Toggle the dropdown when clicking on the three dots icon
        $('.action .bx-dots-vertical-rounded').click(function(event) {
            // Find the associated dropdown for the clicked icon
            const $dropdown = $(this).siblings('.dropdown-content');
            
            // Close all dropdowns before opening the clicked one
            $('.dropdown-content').not($dropdown).css('display', 'none');
            
            // Toggle the visibility of the clicked dropdown
            const isDropdownVisible = $dropdown.css('display') === 'block';
            $dropdown.css('display', isDropdownVisible ? 'none' : 'block');
            
            // Prevent event propagation to avoid document click handler from triggering
            event.stopPropagation();
        });
    
        // Close the dropdown when clicking outside of the action element
        $(document).click(function(event) {
            // If the click is outside of the action or dropdown, hide all dropdowns
            if (!$(event.target).closest('.action').length) {
                $('.dropdown-content').css('display', 'none');
            }
        });
    
        // Prevent closing the dropdown when clicking inside the dropdown options
        $('.dropdown-content').click(function(event) {
            event.stopPropagation();
        });
    });


</script>
@endpush

