@extends('layouts.master')
@section('css')
<style>
    .hide{display: none;}
    label.error{font-size: 87.5%; color: #dc0441;}
</style>
@endsection
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
    <div class="row">
        <div class="col-xl-12 col-lg-12 col-md-12">
            <div class="border-0">
                <div class="tab-content">
                    <div class="tab-pane active" id="myProfile">
                        <div class="card">
                            <form action="{{ route('profile.update', $admin->id) }}" method="POST" id="editProfile">
                                @csrf
                                <div class="card-header">
                                    <div class="card-title">Edit Profile</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Name <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ $admin->name }}">
                                                @error('name')
                                                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Username <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ $admin->username }}">
                                                @error('username')
                                                    <label id="username-error" class="error" for="username">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-6 col-md-6">
                                            <div class="form-group">
                                                <label class="form-label">Email address <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="email" id="email" placeholder="Email address" value="{{ $admin->email }}">
                                                @error('email')
                                                    <label id="email-error" class="error" for="email">{{ $message }}</label>
                                                @enderror
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
                            <form action="{{ route('profile.change-password', $admin->id) }}" method="POST"
                                id="changePasswordForm">
                                @csrf
                                <div class="card-header">
                                    <div class="card-title">Change Password</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Old Password <span class="text-red">*</span></label>
                                                <input type="password" class="form-control" name="old_password" id="old_password" placeholder="Old Password">
                                                @error('old_password')
                                                    <label id="old_password-error" class="error" for="old_password">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">New Password <span class="text-red">*</span></label>
                                                <input type="password" class="form-control" name="password" id="password" placeholder="New Password">
                                                @error('password')
                                                    <label id="password-error" class="error" for="password">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Confirm Password <span class="text-red">*</span></label>
                                                <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" placeholder="Confirm Password">
                                                @error('password_confirmation')
                                                    <label id="password_confirmation-error" class="error" for="password_confirmation">{{ $message }}</label>
                                                @enderror
                                            </div>
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
    <script src="{{URL::asset('assets/plugins/forn-wizard/js/jquery.validate.min.js')}}"></script>
    <script>
        $(document).ready(function() {
            $("#editProfile").validate({
                ignore: ":hidden",
                rules: {
                    name: {
                        required: true,
                        maxlength: 100,
                    },
                    email: {
                        required: true,
                        email: true,
                        maxlength: 250,
                    },
                    password: {
                        required: true,
                        minlength: 6,
                    },
                    password_confirmation: {
                        required: true,
                        minlength: 6,
                        equalTo: "#password",
                    },
                },
                messages: {
                    name: {
                        required: "The Name field is required",
                        maxlength: "Name cannot exceed 100 characters",
                    },
                    email: {
                        required: "The Email field is required",
                        email: "Email must be a valid email",
                        maxlength: "Email cannot exceed 250 characters",
                    },
                    password: {
                        required: "The Password field is required",
                        minlength: "Password must be at least 6 characters long",
                    },
                    password_confirmation: {
                        required: "The Confirm Password field is required",
                        minlength: "Password must be at least 6 characters long",
                        equalTo: "Passwords do not match",
                    },
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
                        minlength: 6,
                    },
                    password_confirmation: {
                        required: true,
                        minlength: 6,
                        equalTo: "#password",
                    },
                },
                messages: {
                    old_password: {
                        required: "The Old Password field is required",
                        minlength: "Password must be at least 6 characters long",
                    },
                    password: {
                        required: "The Password field is required",
                        minlength: "Password must be at least 6 characters long",
                    },
                    password_confirmation: {
                        required: "The Confirm Password field is required",
                        minlength: "Password must be at least 6 characters long",
                        equalTo: "Passwords do not match",
                    },
                },
            });

            // $.validator.addMethod("strong_password", function (value, element) {
            //     let password = value;
            //     if (!(/^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[@#$%&])(.{8,20}$)/.test(password))) {
            //         return false;
            //     }
            //     return true;
            // }, function (value, element) {
            //     let password = $(element).val();
            //     if (!(/^(.{8,20}$)/.test(password))) {
            //         return 'Password must be between 8 to 20 characters long.';
            //     }
            //     else if (!(/^(?=.*[A-Z])/.test(password))) {
            //         return 'Password must contain at least one uppercase.';
            //     }
            //     else if (!(/^(?=.*[a-z])/.test(password))) {
            //         return 'Password must contain at least one lowercase.';
            //     }
            //     else if (!(/^(?=.*[0-9])/.test(password))) {
            //         return 'Password must contain at least one digit.';
            //     }
            //     else if (!(/^(?=.*[@#$%&])/.test(password))) {
            //         return "Password must contain special characters from @#$%&.";
            //     }
            //     return false;
            // });
        });

    </script>

@endsection