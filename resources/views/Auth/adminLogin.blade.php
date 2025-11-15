@extends('auth.adminApp') 
@section('title')
<title>Login</title>
@endsection 
@section('SEO')

@endsection 
@push('css')
<style>

</style>
@endpush 
@section('contents')
         <!-- Start Login Area -->
         <div class="login-area">
             <div class="d-table">
                 <div class="d-table-cell">
                     <div class="login-form">
                         <div class="logo">
                             <a href="javascript:void(0)"><img src="" alt="logo" /></a>
                         </div>
                         <h2>Welcome </h2>
                         @include('alerts')
                        <form action="#" method="post">
                            @csrf
                            <div class="form-group">
                                <input type="email" class="form-control" name="email" placeholder="Email" value="{{old('email')}}" required="" />
                                <span class="label-title"><i class='bx bx-user'></i></span>
                                @if($errors->has('email'))
                                    <span style="color:red;display: block;">{{ $errors->first('email') }}</span>
                                @endif
                            </div>
                            
                            <div class="form-group">
                                <input type="password" class="form-control" name="password" placeholder="Password" value="{{old('password')}}" required="" />
                                <span class="label-title"><i class='bx bx-lock'></i></span>
                            </div>

                            <div class="form-group">
                                <div class="remember-forgot">
                                    <label class="checkbox-box">Remember me
                                        <input type="checkbox" name="remember"  />
                                        <span class="checkmark"></span>
                                    </label>
                                </div>
                            </div>
                            <button type="submit" class="login-btn btn-block"><i class='bx bx-right-arrow-alt'></i> Login </button>
                        </form>
                     </div>
                 </div>
             </div>
         </div>
         <!-- End Login Area -->
@endsection 
@push('js') @endpush