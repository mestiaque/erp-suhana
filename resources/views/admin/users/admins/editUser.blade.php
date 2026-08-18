@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Edit User')}}</title>
@endsection @push('css')

<style type="text/css">
    .edit-profile-page {
        color: #17233c;
    }

    .edit-profile-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr) minmax(320px, 0.8fr);
        gap: 22px;
        align-items: start;
    }

    .edit-profile-card {
        background: #ffffff;
        border: 1px solid #e8edf5;
        border-radius: 8px;
        box-shadow: 0 12px 32px rgba(21, 32, 56, 0.06);
        overflow: hidden;
    }

    .edit-profile-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 18px;
        padding: 22px 24px;
        background: linear-gradient(135deg, #f6fbff 0%, #eef7f2 100%);
        border-bottom: 1px solid #e8edf5;
    }

    .edit-profile-header h3 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #122033;
    }

    .edit-profile-header p {
        margin: 6px 0 0;
        color: #6b778c;
        font-size: 13px;
    }

    .view-profile-btn,
    .save-profile-btn,
    .change-password-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 38px;
        padding: 9px 15px;
        border: 0;
        border-radius: 6px;
        color: #ffffff;
        font-weight: 700;
        line-height: 1;
    }

    .view-profile-btn {
        background: #00d6f2;
        box-shadow: 0 8px 18px rgba(0, 214, 242, 0.24);
    }

    .view-profile-btn:hover {
        color: #ffffff;
        background: #00b5d8;
        text-decoration: none;
    }

    .save-profile-btn {
        background: #1769e0;
        box-shadow: 0 8px 18px rgba(23, 105, 224, 0.18);
    }

    .change-password-btn {
        background: #d94848;
        box-shadow: 0 8px 18px rgba(217, 72, 72, 0.18);
    }

    .edit-profile-body {
        padding: 24px;
    }

    .profile-upload-box {
        display: flex;
        align-items: center;
        gap: 18px;
        padding: 18px;
        margin-bottom: 22px;
        border: 1px solid #e8edf5;
        border-radius: 8px;
        background: #fbfcfe;
    }

    .ProfileImage {
        width: 92px;
        height: 92px;
        max-width: 92px;
        max-height: 92px;
        object-fit: cover;
        border-radius: 50% !important;
        border: 5px solid #ffffff;
        box-shadow: 0 10px 24px rgba(21, 32, 56, 0.13);
        background: #eef1f5;
    }

    .upload-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
    }

    .upload-btn,
    .reset-photo-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-height: 34px;
        padding: 8px 12px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 700;
        line-height: 1;
    }

    .upload-btn {
        color: #ffffff;
        background: #1769e0;
        cursor: pointer;
    }

    .reset-photo-btn {
        color: #17233c;
        background: #eef1f5;
    }

    .reset-photo-btn:hover {
        color: #17233c;
        text-decoration: none;
        background: #e0e7ef;
    }

    .upload-help {
        margin: 9px 0 0;
        color: #7b8794;
        font-size: 12px;
    }

    .form-section-title {
        display: flex;
        align-items: center;
        gap: 9px;
        margin-bottom: 16px;
    }

    .form-section-title i {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: #fff5db;
        color: #c68300;
        font-size: 18px;
    }

    .form-section-title h4 {
        margin: 0;
        font-size: 18px;
        font-weight: 700;
    }

    .edit-profile-page .form-group label {
        color: #667085;
        font-size: 12px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .edit-profile-page .form-control {
        min-height: 42px;
        border: 1px solid #dce4ef;
        border-radius: 6px;
        color: #17233c;
        font-size: 14px;
        box-shadow: none;
    }

    .edit-profile-page .form-control:focus {
        border-color: #8db6f5;
        box-shadow: 0 0 0 3px rgba(23, 105, 224, 0.1);
    }

    .readonly-field {
        min-height: 42px;
        display: flex;
        align-items: center;
        padding: 9px 12px;
        border: 1px solid #e8edf5;
        border-radius: 6px;
        background: #fbfcfe;
        color: #17233c;
        font-size: 14px;
        font-weight: 600;
        word-break: break-word;
    }

    .edit-profile-page .input-group-text {
        min-height: 42px;
        border-color: #dce4ef;
        border-radius: 0 6px 6px 0;
        background: #fbfcfe;
    }

    .field-error {
        color: #d94848;
        margin: 5px 0 0;
        font-size: 11px;
        font-weight: 600;
    }

    .password-note {
        padding: 14px;
        margin-bottom: 18px;
        border-radius: 8px;
        border: 1px solid #fde3e3;
        background: #fff8f8;
        color: #8a3b3b;
        font-size: 13px;
        line-height: 1.6;
    }

    @media only screen and (max-width: 991px) {
        .edit-profile-grid {
            grid-template-columns: 1fr;
        }
    }

    @media only screen and (max-width: 575px) {
        .edit-profile-header,
        .profile-upload-box {
            flex-direction: column;
            align-items: stretch;
        }

        .edit-profile-body,
        .edit-profile-header {
            padding: 18px;
        }

        .view-profile-btn,
        .save-profile-btn,
        .change-password-btn {
            width: 100%;
        }
    }
</style>
@endpush @section('contents')

<!-- Breadcrumb Area -->



@include(adminTheme().'alerts')
<div class="flex-grow-1">
    <div class="edit-profile-page">
        <div class="edit-profile-grid">
            <div class="edit-profile-card mb-30">
                <div class="edit-profile-header">
                    <div>
                        <h3>Edit User : <span class="text-primary">{{$user->name}}</span></h3>
                        <p>Update your essential account information.</p>
                    </div>
                    <button type="button" class=" view-profile-btn" id="copyBtn">
                        <i class="bx bx-copy" id="copyIcon"></i>
                        <span id="copyBtnText">Copy Login</span>
                    </button>
                    {{-- <a href="{{route('admin.myProfile')}}" class="view-profile-btn"><i class="bx bx-show"></i> View Profile</a> --}}
                </div>

                <div class="edit-profile-body">
                    <form action="{{route('admin.usersAdminAction',['update',$user->id])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" value="profile" name="actionType">

                        <div class="profile-upload-box">
                            <a href="javascript: void(0);">
                                <img src="{{$user->image()}}" class="ProfileImage image_{{$user->id}}" alt="profile image" />
                            </a>
                            <div>
                                <div class="upload-actions">
                                    <label class="upload-btn" for="account-upload"><i class="bx bx-upload"></i> Upload Photo</label>
                                    <input type="file" name="image" id="account-upload" class="account-upload" data-imageshow="image_{{$user->id}}" hidden="" />
                                    @if($user->imageFile)
                                    <a href="{{route('admin.mediesDelete',$user->imageFile->id)}}" class="mediaDelete reset-photo-btn"><i class="bx bx-reset"></i> Reset</a>
                                    @endif
                                </div>
                                @if ($errors->has('image'))
                                <p class="field-error">{{ $errors->first('image') }}</p>
                                @endif
                                <p class="upload-help">Allowed JPG, GIF or PNG. Max size of 2048kB.</p>
                            </div>
                        </div>

                        <div class="profile-upload-box">
                            @if($user->signature)
                            <img src="{{$user->signatureUrl()}}" id="signature-preview" alt="signature" style="width:120px;height:60px;object-fit:contain;border-radius:6px;border:1px solid #e8edf5;background:#fff;" />
                            @else
                            <div id="signature-preview" style="width:120px;height:60px;border-radius:6px;border:1px solid #e8edf5;background:#fff;"></div>
                            @endif
                            <div>
                                <div class="upload-actions">
                                    <label class="upload-btn" for="signature-upload"><i class="bx bx-upload"></i> Upload Signature</label>
                                    <input type="file" name="signature" id="signature-upload" hidden="" />
                                    @if($user->signature)
                                    <a href="{{route('admin.userFileReset',['signature',$user->id])}}" class="reset-photo-btn" onclick="return confirm('Reset signature?')"><i class="bx bx-reset"></i> Reset</a>
                                    @endif
                                </div>
                                @if ($errors->has('signature'))
                                <p class="field-error">{{ $errors->first('signature') }}</p>
                                @endif
                                <p class="upload-help">Allowed JPG, GIF or PNG. Max size of 2048kB.</p>
                            </div>
                        </div>

                        <div class="form-section-title">
                            <i class="bx bx-user-circle"></i>
                            <h4>Personal Information</h4>
                        </div>

                        <div class="row">
                            <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                <label for="name">Name*</label>
                                <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" value="{{$user->name?:old('name')}}" required="" />
                                @if ($errors->has('name'))
                                <p class="field-error">{{ $errors->first('name') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                <label>Role</label>
                                        <select name="role" class="form-control form-control-sm">
                                            <option value="">No Role</option>
                                            @foreach($roles as $role)
                                            <option value="{{$role->id}}" {{$user->permission_id == $role->id ? 'selected' : ''}}>{{$role->name}}</option>
                                            @endforeach
                                        </select>
                                        @error('role')<div class="text-danger small">{{$message}}</div>@enderror
                                {{-- <div class="readonly-field">{{$user->permission->name ?? 'N/A'}}</div> --}}
                            </div>
                            <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                <label>Employee ID</label>
                                    <input type="text" class="form-control form-control-sm {{$errors->has('employee_id')?'is-invalid':''}}"
                                               name="employee_id" value="{{ old('employee_id', $user->employee_id) }}" placeholder="Employee ID">
                                    @error('employee_id')<div class="invalid-feedback">{{$message}}</div>@enderror
                            </div>
                            <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                <label for="mobile">Mobile*</label>
                                <input type="tel" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" minlength="11" maxlength="11" pattern="[0-9]{11}" title="Please enter exactly 11 digits" oninput="this.value = this.value.slice(0, 11);" placeholder="Please enter exactly 11 digits with start 0" value="{{$user->mobile?:old('mobile')}}" required>
                                {{-- <input type="text" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" placeholder="Enter Mobile" value="{{$user->mobile?:old('mobile')}}" /> --}}
                                @if ($errors->has('mobile'))
                                <p class="field-error">{{ $errors->first('mobile') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-6 col-lg-6 col-md-12">
                                <label for="email">Email*</label>
                                <input type="email" class="form-control {{$errors->has('email')?'error':''}}" name="email" placeholder="Enter Email" value="{{$user->email?:old('email')}}" required="" />
                                @if ($errors->has('email'))
                                <p class="field-error">{{ $errors->first('email') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-6 col-lg-6 col-md-12 align-self-end text-right">
                                <button type="submit" class="save-profile-btn"><i class="bx bx-save"></i> Save Changes</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <div class="edit-profile-card mb-30">
                <div class="edit-profile-header">
                    <div>
                        <h3>Change Password</h3>
                        <p>Keep your account secure with a strong password.</p>
                    </div>
                </div>

                <div class="edit-profile-body">
                    <div class="password-note">
                        Use a password that is hard to guess and different from your previous password.
                    </div>
                    <form action="{{route('admin.usersAdminAction',['change-password',$user->id])}}" method="post" >
                        @csrf
                        <input type="hidden" value="change-password" name="actionType">
                        <div class="row">
                            <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                <label for="old_password">Old Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control password" placeholder="Old Password" name="old_password" value="{{ old('old_password', $user->password_show) }}" required="" />
                                    <div class="input-group-append">
                                        <span class="input-group-text showPassword"><i class='bx bx-hide'></i></span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                <label for="password">New Password</label>
                                <input type="password" class="form-control password {{$errors->has('password')?'error':''}}" name="password" placeholder="New password" required="" />
                                @if ($errors->has('password'))
                                <p class="field-error">{{ $errors->first('password') }}</p>
                                @endif
                            </div>
                            <div class="form-group col-xl-12 col-lg-12 col-md-12">
                                <label for="password_confirmation">Confirm Password</label>
                                <input type="password" class="form-control password {{$errors->has('password_confirmation')?'error':''}}" name="password_confirmation" placeholder="Confirmed password" required="" />
                                @if ($errors->has('password_confirmation'))
                                <p class="field-error">{{ $errors->first('password_confirmation') }}</p>
                                @endif
                            </div>
                        </div>
                        <button type="submit" class="change-password-btn float-right mb-4"><i class="bx bx-lock-alt"></i> Change Password</button>
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
        const imageInput = document.getElementById('account-upload');

        if (imageInput) {
            imageInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        document.querySelector('.ProfileImage').src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

    // Signature live preview
        const signatureInput = document.getElementById('signature-upload');

        if (signatureInput) {
            signatureInput.addEventListener('change', function () {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        let preview = document.getElementById('signature-preview');
                        if (preview.tagName !== 'IMG') {
                            const newImg = document.createElement('img');
                            newImg.id = 'signature-preview';
                            newImg.style.cssText = preview.style.cssText;
                            preview.replaceWith(newImg);
                            preview = newImg;
                        }
                        preview.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

    // Show/hide password toggle


    // Copy login info
    document.getElementById('copyBtn').addEventListener('click', function () {
        const appUrl  = "{{ config('app.url') }}";
        const email   = @json($user->email);
        const phone   = @json($user->mobile);
        const pass    = @json($user->password_show);
        const loginId = email ? ('Email: ' + email) : ('Phone: ' + phone);
        const text    = 'Login: ' + appUrl + '\n' + loginId + '\nPassword: ' + (pass || '(not set)');

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
            alert('Copy failed. Please copy manually:\n\nLogin: ' + appUrl);
        });
    });
</script>
@endpush
