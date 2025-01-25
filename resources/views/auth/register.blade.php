@extends('layouts.master4')
@section('css')
<style>
    .hide{display: none;}
    label.error{font-size: 87.5%; color: #dc0441;}
</style>
@endsection
@section('content')
<div class="page">
    <div class="page-single">
        <div class="container">
            <div class="row">
                <div class="col mx-auto">
                    <div class="row justify-content-center">
                        <div class="col-md-5">
                            <div class="card">
                                <div class="card-body">
                                    <div class="text-center title-style mb-6">
                                        <h1 class="mb-2">Register</h1>
                                        <hr>
                                        <p class="text-muted">Create New Account</p>
                                    </div>
                                    <form method="POST" class="form-horizontal form-simple" action="{{ route('register') }}" id="signup">
                                        @csrf
                                        <div class="mb-2">
                                            <div class="input-group mb-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-user"></i>
                                                    </div>
                                                </div>
                                                <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" placeholder="Name">
                                            </div>
                                            @error('name')
                                                <label id="name-error" class="error" for="name">{{ $message }}</label>
                                            @else
                                                <label id="name-error" class="error hide" for="name">The name field is required</label>
                                            @enderror
                                        </div>
                                        <div class="mb-2">
                                            <div class="input-group mb-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-mail"></i>
                                                    </div>
                                                </div>
                                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" placeholder="Email Address">
                                            </div>
                                            @error('email')
                                                <label id="email-error" class="error" for="email">{{ $message }}</label>
                                            @else
                                                <label id="email-error" class="error hide" for="email">The email field is required</label>
                                            @enderror
                                        </div>

                                        <div class="mb-2">
                                            <div class="input-group mb-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-lock"></i>
                                                    </div>
                                                </div>
                                                <input id="password" type="password" class="form-control" name="password" placeholder="Password">
                                            </div>
                                            @error('password')
                                                <label id="password-error" class="error" for="password">{{ $message }}</label>
                                            @else
                                                <label id="password-error" class="error hide" for="password">The password field is required</label>
                                            @enderror
                                        </div>

                                        <div class="mb-2">
                                            <div class="input-group mb-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-lock"></i>
                                                    </div>
                                                </div>
                                                <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" placeholder="Confirm Password">
                                            </div>
                                            @error('password_confirmation')
                                                <label id="password_confirmation-error" class="error" for="password_confirmation">{{ $message }}</label>
                                            @else
                                                <label id="password_confirmation-error" class="error hide" for="password_confirmation">The password     confirmation field is required</label>
                                            @enderror
                                        </div>
                                        <div class="row mb-0">
                                            <div class="col-md-6 offset-md-4">
                                                <button type="submit" class="btn btn-primary">
                                                    {{ __('Register') }}
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="text-center pt-4">
                                        <div class="font-weight-normal fs-16">Already have an account <a class="btn-link font-weight-normal" href="{{ route('login') }}">Login Now</a></div>
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
@section('js')
<script src="{{URL::asset('assets/plugins/forn-wizard/js/jquery.validate.min.js')}}"></script>
<script>
    $(document).ready(function() {
        $("#signup").validate({
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
    });
</script>
@endsection
