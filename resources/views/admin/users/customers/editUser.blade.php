@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Edit User')}}</title>
@endsection
@push('css')
<style>
    #imagePreview {
        width: 100px;
        height: 100px;
        object-fit: cover;
        border-radius: 8px;
        border: 2px dashed #dee2e6;
    }
</style>
@endpush
@section('contents')
<div class="breadcrumb-area">
    <h1>Edit User</h1>
    <ol class="breadcrumb">
        <li class="item"><a href="{{route('admin.dashboard')}}"><i class="bx bx-home-alt"></i></a></li>
        <li class="item"><a href="{{route('admin.usersCustomer')}}">User List</a></li>
        <li class="item">Edit</li>
    </ol>
</div>

@include(adminTheme().'alerts')

<div class="flex-grow-1">

    {{-- Header with Copy Button --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">{{ $user->name }}</h3>
        <button type="button" class="btn btn-sm btn-outline-secondary" id="copyBtn">
            <i class="bx bx-copy" id="copyIcon"></i>
            <span id="copyBtnText">Copy Login</span>
        </button>
    </div>

    <div class="row">

        {{-- Left: Profile Form --}}
        <div class="col-md-8">
            <div class="card mb-30">
                <div class="card-header"><h3>Profile Information</h3></div>
                <div class="card-body">
                    <form action="{{route('admin.usersCustomerAction',['update',$user->id])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        {{-- Pass name/bn_name as hidden so the controller required validation passes --}}
                        <input type="hidden" name="name" value="{{ $user->name }}">
                        <input type="hidden" name="bn_name" value="{{ $user->bn_name }}">

                        {{-- Image with Preview --}}
                        <div class="d-flex align-items-center mb-4">
                            <img id="imagePreview" src="{{ asset($user->image()) }}" alt="Profile photo" />
                            <div class="ml-3">
                                <label class="btn btn-sm btn-primary mb-1" for="imageInput">
                                    <i class="bx bx-upload"></i> Change Photo
                                </label>
                                <input type="file" name="image" id="imageInput" accept="image/*" hidden>
                                @if($user->imageFile)
                                <a href="{{route('admin.mediesDelete',$user->imageFile->id)}}" class="btn btn-sm btn-secondary d-block mt-1"
                                   onclick="return confirm('Reset photo?')">Reset</a>
                                @endif
                                <small class="text-muted d-block mt-1">JPG, PNG. Max 2MB</small>
                                @error('image')<p class="text-danger small mb-0">{{$message}}</p>@enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email</label>
                                    <input type="email" class="form-control form-control-sm {{$errors->has('email')?'is-invalid':''}}"
                                           name="email" value="{{ old('email', $user->email) }}" placeholder="Email address">
                                    @error('email')<div class="invalid-feedback">{{$message}}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Phone</label>
                                    <input type="text" class="form-control form-control-sm {{$errors->has('mobile')?'is-invalid':''}}"
                                           name="mobile" value="{{ old('mobile', $user->mobile) }}" placeholder="Phone number">
                                    @error('mobile')<div class="invalid-feedback">{{$message}}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Employee ID</label>
                                    <input type="text" class="form-control form-control-sm {{$errors->has('employee_id')?'is-invalid':''}}"
                                           name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" placeholder="Employee ID">
                                    @error('employee_id')<div class="invalid-feedback">{{$message}}</div>@enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control form-control-sm">
                                        <option value="1" {{$user->status == 1 ? 'selected' : ''}}>Active</option>
                                        <option value="0" {{$user->status == 0 ? 'selected' : ''}}>Inactive</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>User Role</label>
                                    <select name="role" class="form-control form-control-sm">
                                        <option value="">No Role</option>
                                        @foreach($roles as $role)
                                        <option value="{{$role->id}}" {{$user->permission_id == $role->id ? 'selected' : ''}}>{{$role->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Change Password --}}
        <div class="col-md-4">
            <div class="card mb-30">
                <div class="card-header"><h3>Change Password</h3></div>
                <div class="card-body">
                    <form action="{{route('admin.usersCustomerAction',['change-password',$user->id])}}" method="post">
                        @csrf
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" class="form-control form-control-sm {{$errors->has('old_password')?'is-invalid':''}}"
                                   name="old_password" placeholder="Current password" minlength="8" autocomplete="current-password">
                            @error('old_password')<div class="invalid-feedback">{{$message}}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control form-control-sm {{$errors->has('password')?'is-invalid':''}}"
                                       name="password" id="newPassword" placeholder="New password" minlength="8" autocomplete="new-password">
                                <div class="input-group-append">
                                    <span class="input-group-text showPassword" style="cursor:pointer;"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            @error('password')<div class="text-danger small">{{$message}}</div>@enderror
                        </div>
                        <div class="form-group">
                            <label>Confirm Password</label>
                            <input type="password" class="form-control form-control-sm"
                                   name="password_confirmation" placeholder="Confirm new password" minlength="8" autocomplete="new-password">
                        </div>
                        <button type="submit" class="btn btn-warning btn-block">Change Password</button>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('js')
<script>
    // Image live preview
    document.getElementById('imageInput').addEventListener('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => document.getElementById('imagePreview').src = e.target.result;
            reader.readAsDataURL(file);
        }
    });

    // Show/hide password toggle
    $(document).on('click', '.showPassword', function () {
        const $input = $(this).closest('.input-group').find('input');
        const isText = $input.attr('type') === 'text';
        $input.attr('type', isText ? 'password' : 'text');
        $(this).find('i').toggleClass('bx-hide bx-show');
    });

    // Copy login info button
    document.getElementById('copyBtn').addEventListener('click', function () {
        const appUrl  = "{{ config('app.url') }}";
        const email   = @json($user->email);
        const phone   = @json($user->mobile);
        const pass    = @json($user->password_show);
        const loginId = email ? ('Email: ' + email) : ('Phone: ' + phone);
        const text    = 'Login: ' + appUrl + '\n' + loginId + '\nPassword: ' + pass;

        navigator.clipboard.writeText(text).then(function () {
            const btn  = document.getElementById('copyBtn');
            const icon = document.getElementById('copyIcon');
            const txt  = document.getElementById('copyBtnText');
            btn.classList.replace('btn-outline-secondary', 'btn-success');
            icon.classList.replace('bx-copy', 'bx-check');
            txt.textContent = 'Copied!';
            setTimeout(function () {
                btn.classList.replace('btn-success', 'btn-outline-secondary');
                icon.classList.replace('bx-check', 'bx-copy');
                txt.textContent = 'Copy Login';
            }, 1000);
        }).catch(function () {
            alert('Copy failed. Please copy manually.');
        });
    });
</script>
@endpush
