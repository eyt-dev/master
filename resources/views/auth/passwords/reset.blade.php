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
                                            <h1 class="mb-2">Reset Password</h1>
                                            <hr>
                                            <p class="text-muted">Reset your password</p>
                                        </div>

                                        <form id="reset_password" method="POST" class="form-horizontal form-simple"
                                              action="{{ route('password.update') }}" id="login">
                                            @csrf
                                            <input type="hidden" name="token" value="{{ $token }}">
                                            <div class="mb-2">
                                                <div class="input-group mb-1">
                                                    <div class="input-group-prepend">
                                                        <div class="input-group-text">
                                                            <i class="fe fe-user"></i>
                                                        </div>
                                                    </div>
                                                    <input type="text" class="form-control" placeholder="Email" name="email" id="email">
                                                </div>
                                                @error('email')
                                                    <label id="email-error" class="error" for="email">{{ $message }}</label>
                                                @else
                                                    <label id="email-error" class="error hide" for="email">The Email field is required</label>
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
                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="submit" class="btn  btn-primary btn-block px-4">Reset Password</button>
                                                </div>
                                            </div>
                                        </form>
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
        $("#reset_password").validate({
            ignore: ":hidden",
            rules: {
                email: {
                    required: true,
                    email: true,
                    maxlength: 250,
                },
                password: 'required',
                password_confirmation: {
                    required: true,
                    equalTo: "#password"
                }
            },
            messages: {
                email: {
                    required: "The Email field is required",
                    email: "Email must be a valid email",
                },
                password: {
                    required: "The Password field is required"
                },
                password_confirmation: {
                    required: "The Confirm Password field is required",
                    equalTo:"Confirm Password should be similar to Password"
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter($(element).parent());
            },
        });
    });
</script>
@endsection