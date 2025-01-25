@extends('layouts.master')

@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Hi! Welcome Back</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/' . ($page = '#')) }}"><i
                            class="fe fe-home mr-2 fs-14"></i>Home</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="{{ url('/' . ($page = '#')) }}">Profile</a>
                </li>
            </ol>
        </div>
    </div>
@endsection
@section('content')
    <div class="main-proifle">
        <div class="row">
            <div class="col-lg-8">
                <div class="box-widget widget-user">
                    <div class="widget-user-image1 d-sm-flex">
                        <div class="mt-1 ml-lg-5">
                            <h4 class="pro-user-username mb-3 font-weight-bold">{{ $user->name }} <i
                                    class="fa fa-check-circle text-success"></i></h4>
                            <ul class="mb-0 pro-details">
                                <li><span class="profile-icon"><i class="fe fe-mail"></i></span><span
                                        class="h6 mt-3">{{ $user->email }}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="profile-cover">
            <div class="wideget-user-tab">
                <div class="tab-menu-heading p-0">
                    <div class="tabs-menu1 px-3">
                        <ul class="nav">
                            <li><a href="#myProfile" class="active fs-14" data-toggle="tab"> Profile</a></li>
                           
                        </ul>
                    </div>
                </div>
            </div>
        </div><!-- /.profile-cover -->
    </div>
    <!-- Row -->
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="border-0">
                <div class="tab-content">
                    <div class="tab-pane active" id="myProfile">
                        <div class="card">
                            <form action="{{ route('profile.update', $user->id) }}" method="POST" id="editProfile">
                                @csrf
                                <div class="card-header">
                                    <div class="card-title">Edit Profile</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6">
                                            <div class="input-box">
                                                <label class="input-label">Name</label>
                                                <input type="text" class="google-input" name="name" id="name"
                                                    value="{{ $user->name }}" />
                                            </div>
                                            @error('name')
                                                <label id="name-error" class="error"
                                                    for="name">{{ $message }}</label>
                                            @enderror
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="input-box">
                                                <label class="input-label">Email address</label>
                                                <input type="email" name="email" value="{{ $user->email }}"
                                                    class="google-input">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                        <div class="card">
                            <form action="{{ route('profile.change-password', $user->id) }}" method="POST"
                                id="changePasswordForm">
                                @csrf
                                <div class="card-header">
                                    <div class="card-title">Change Password</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4 col-md-4">
                                            <div class="input-box">
                                                <label class="input-label">Old Password</label>
                                                <input type="password" name="old_password" class="google-input"
                                                    id="old_password">
                                            </div>
                                            @error('old_password')
                                                <label id="name-error" class="error"
                                                    for="name">{{ $message }}</label>
                                            @enderror
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="input-box">
                                                <label class="input-label">New Password</label>
                                                <input type="password" name="password" class="google-input"
                                                    id="password">
                                            </div>
                                            @error('password')
                                                <label id="name-error" class="error"
                                                    for="name">{{ $message }}</label>
                                            @enderror
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="input-box">
                                                <label class="input-label">Confirm Password</label>
                                                <input type="password" name="password_confirmation" class="google-input"
                                                    id="PasswordConfirmation">
                                            </div>
                                            @error('password_confirmation')
                                                <label id="name-error" class="error"
                                                    for="name">{{ $message }}</label>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                                <div class="card-footer text-right">
                                    <button type="submit" class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </div>
                    </div>
        
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $("#editProfile").validate({
                onkeyup: function(el, e) {
                    $(el).valid();
                },
                ignore: ":hidden",
                rules: {
                    name: {
                        required: true,
                        maxlength: 255,
                    },
                    email: {
                        required: true,
                        email: true,
                        maxlength: 255,
                    },
                },
                messages: {},
                errorPlacement: function(error, element) {
                    error.insertAfter($(element).parent());
                },
            });

            $("#changePasswordForm").validate({
                ignore: ":hidden",
                rules: {
                    old_password: {
                        required: true,
                        // strong_password: true,
                    },
                    password: {
                        required: true,
                        strong_password: true,
                    },
                    password_confirmation: {
                        required: true,
                        strong_password: true,
                        equalTo: "#password"
                    }
                },
                messages: {
                    password_confirmation: {
                        equalTo: "To create a valid password, both the password and confirm password field values must be matched."
                    }
                },
            });

            $.validator.addMethod("strong_password", function (value, element) {
                let password = value;
                if (!(/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@#$%&])(.{8,20}$)/.test(password))) {
                    return false;
                }
                return true;
            }, function (value, element) {
                let password = $(element).val();
                if (!(/^(.{8,20}$)/.test(password))) {
                    return 'Password must be between 8 to 20 characters long.';
                }
                else if (!(/^(?=.*[A-Z])/.test(password))) {
                    return 'Password must contain at least one uppercase.';
                }
                else if (!(/^(?=.*[a-z])/.test(password))) {
                    return 'Password must contain at least one lowercase.';
                }
                else if (!(/^(?=.*[0-9])/.test(password))) {
                    return 'Password must contain at least one digit.';
                }
                else if (!(/^(?=.*[@#$%&])/.test(password))) {
                    return "Password must contain special characters from @#$%&.";
                }
                return false;
            });
        });

    </script>

@endsection