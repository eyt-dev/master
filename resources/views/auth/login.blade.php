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
                                        <h1 class="mb-2">Login</h1>
                                        <hr>
                                        <p class="text-muted">Sign In to your account</p>
                                    </div>
                                    <form method="POST" class="form-horizontal form-simple" action="{{ route('login', ['username' => request()->segment(1)]) }}" id="login">
                                    @csrf
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
                                                <input type="password" class="form-control" placeholder="Password" name="password" id="password" >
                                            </div>
                                            @error('password')
                                                <label id="password-error" class="error" for="password">{{ $message }}</label>
                                            @else
                                                <label id="password-error" class="error hide" for="password">The Password field is required</label>
                                            @enderror
                                        </div>
                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn  btn-primary btn-block px-4">Login</button>
                                            </div>
                                            <div class="col-12 text-center">
                                                <a href="{{ route('password.request', ['username' => request()->segment(1)]) }}" class="btn btn-link box-shadow-0 px-0">Forgot password?</a>
                                            </div>
                                        </div>
                                    </form>
                                    {{-- <div class="text-center pt-4">
                                        <div class="font-weight-normal fs-16">You Don't have an account <a class="btn-link font-weight-normal" href="{{ route('register', ['username' => request()->segment(1)]) }}">Register Here</a></div>
                                    </div> --}}
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
        $("#login").validate({
            ignore: ":hidden",
            rules: {
                email: {
                    required: true,
                    email: true,
                    maxlength: 250,
                },
                password: 'required'
            },
            messages: {
                email: {
                    required: "The Email field is required",
                    email: "Email must be a valid email",
                },
                password: {
                    required: "The Password field is required"
                }
            },
        });
    });
</script>
@endsection