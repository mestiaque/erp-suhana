@extends(general()->adminTheme.'.layouts.app')
@section('title')
<title>{{websiteTitle('Comments List')}}</title>
@endsection

@push('css')
<style type="text/css">
  .commentauthor img{
    float: left;
    margin-right: 10px;
    margin-top: 1px;
    width: 40px;
  }
  .table-responsive table tr.inactive {
    background: #ffcece;
  }
</style>
@endpush
@section('contents')


<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
     <h3 class="content-header-title mb-0">Comments List</h3>
     <div class="row breadcrumbs-top">
       <div class="breadcrumb-wrapper col-12">
         <ol class="breadcrumb">
           <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a>
           </li>
           <li class="breadcrumb-item active">Comments List</li>
         </ol>
       </div>
     </div>
   </div>
   <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
     <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
       	<a class="btn btn-outline-primary" href="{{route('admin.postsComments',[$post->id,'actionType'=>'addComment'])}}">Add Comment</a>
       	
       	<a class="btn btn-outline-primary reloadPage1" href="{{route('admin.postsComments',$post->id)}}">
       		<i class="fa-solid fa-rotate"></i>
       	</a>
		   <a class="btn btn-outline-primary" href="{{route('admin.postsCommentsAll')}}"><i class="fa-solid fa-bars"></i></a>
     </div>
   </div>
</div>
 
	

 <div class="content-body">
	<!-- Basic Elements start -->
	 <section class="basic-elements">
	     <div class="row">
	         <div class="col-md-12">
         		@include(general()->adminTheme.'.alerts')
         		<div class="card">
     				<div class="card-content">
 						<div class="card-body">
 							<div id="accordion">
							    <div class="card-header collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" id="headingTwo" style="background: #f5f7fa;padding: 10px;cursor: pointer;border: 1px solid #00b5b8;">
							          Search click Here..
							    </div>
							    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion" style="border: 1px solid #00b5b8;border-top: 0;">
							      <div class="card-body">
							       	
							       	<form action="{{route('admin.postsComments',$post->id)}}">
							       		<div class="row">
							       			<div class="col-md-12 mb-0">
						       					<div class="input-group">
				                             		<input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Comments Title, email, website" class="form-control {{$errors->has('search')?'error':''}}">
				                             		<button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
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

	             <div class="card">
	             	<div class="card-header " style="border-bottom: 1px solid #e3ebf3;">
					 	<h4 class="card-title">Categories List</h4>
				 	</div>
	                 <div class="card-content">
	                     <div class="card-body">
	                     	<table class="table table-bordered">
	                     		<tr>
	                     			<td>
	                     				<b>Post: </b><a href="{{route('blogView',$post->slug)}}" target="_blank">{{$post->name}}</a>
	                     			</td>
	                     			<td>
	                     				<i class="fa fa-comments"></i> Comments ({{$post->postComments->where('status','<>','temp')->count()}})
	                     			</td>
	                     		</tr>
	                     	</table>
	                     	<form action="{{route('admin.postsComments',$post->id)}}">
	                     	<div class="row">
                     			<div class="col-md-4">
                     				<div class="input-group mb-1">
                     					<select class="form-control form-control-sm rounded-0" name="action" required="">
                     						<option value="">Select Action</option>
                     						@isset(json_decode(Auth::user()->permission->permission, true)['postsComment']['edit'])
                     						<option value="1">Approve</option>
                     						<option value="2">Un-approve</option>
                     						<option value="3">Feature</option>
                     						<option value="4">Un-feature</option>
                     						@endisset
                     						@isset(json_decode(Auth::user()->permission->permission, true)['postsComment']['delete'])
                     						<option value="5">Comment Delete</option>
                     						@endisset
                     					</select>
                     					<button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                     				</div>
                     			</div>
                     		</div>
		                     <div class="table-responsive">


		                     	<table class="table table-striped table-bordered table-hover" >
								    <thead>
								        <tr>
								            <th width="5%"></th>
								            <th width="20%">Author</th>
								            <th>Comments</th>
								            <th width="25%">Action</th>
								        </tr>
								    </thead>
								    <tbody>
								        @foreach($comments as $i=>$comment)
								        <tr class="{{$comment->status=='inactive'?'inactive':''}}">
								            <td>
								              <input class="checkbox" type="checkbox" name="checkid[]" value="{{$comment->id}}"> 
								            </td>
								            <td class="commentauthor">
								            @if($comment->user)
								            <span><img src="{{asset($comment->user->image())}}"></span>
								            @else
								            <span><img src="{{asset('public/medies/profile.png')}}"></span>
								            @endif

								            @if($comment->website==null)
								            <span>{{$comment->name}}</span>
								            @else
								            <a href="//{{$comment->website}}" rel="nofollow" target="_blank">{{$comment->name}}</a>
								            @endif            
								            <a href="mailto:{{$comment->email}}">{{$comment->email}}</a>
								            
								            <br>
										   @if($comment->status=='active')
								           <span><i class="fa fa-check" style="color: #1ab394;"></i></span>
								           <a href="{{route('admin.postsCommentsAction',['status',$comment->id])}}" class="badge btn-danger" style="color: black !important;">Un-approve</a>
								           @else
								           <span><i class="fa fa-times" style="color: #ed5565;"></i></span>
								           <a href="{{route('admin.postsCommentsAction',['status',$comment->id])}}"  class="badge btn-success">Approved</a>
								           @endif
								          	
								            </td>
								            <td>
								            <span>
								            {!!$comment->content!!}
								            </span>
								            </td>
								            <td class="center">
								            <a href="{{route('admin.postsCommentsAction',['edit',$comment->id])}}" class="btn btn-md btn-info">Edit</a>
								            <a href="{{route('admin.postsCommentsAction',['replay',$comment->id])}}" class="btn btn-md btn-info"><i class="fa fa-reply"></i></a>
								            <a href="{{route('admin.postsCommentsAction',['delete',$comment->id])}}" onclick="return confirm('Are You Want To Delete?')" class="btn btn-md btn-danger" ><i class="fa fa-trash"></i></a>
								              <br>
								              <span>{{$comment->created_at->format('d-m-Y h:i A')}}</span>       
								            </td>
								        </tr>
								        @endforeach
								    </tbody>
								</table>
								{{$comments->links('pagination')}}
		                     </div>
		                   	</form>
	                     </div>
	                 </div>
	             </div>

	         </div>
	     </div>
	 </section>
	 <!-- Basic Inputs end -->
</div>



@endsection
@push('js')

@endpush