@extends('Auth.AdminApp')
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
                             <a href="javascript:void(0)"><img src="{{ asset(general()->logo()) }}" alt="logo" style="max-height 100px" /></a>
                         </div>
                         <h2>Welcome </h2>
                         @include('alerts')
                        <form action="#" method="post">
                            @csrf
                            <div class="form-group">
                                <input type="text" class="form-control" name="user" placeholder="Email or Mobile" value="{{ old('user') }}" required />
                                <span class="label-title"><i class='bx bx-user'></i></span>

                                @if($errors->has('user'))
                                    <span style="color:red;display: block;">{{ $errors->first('user') }}</span>
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
