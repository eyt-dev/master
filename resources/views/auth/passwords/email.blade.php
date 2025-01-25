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
                                            <h1 class="mb-2">Forgot Password</h1>
                                            <hr>
                                            <p class="text-muted">Forgot your password</p>
                                        </div>
                                        <form method="POST" action="{{ route('password.email') }}" class="form-horizontal form-simple" id="forgot_password">
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
                                            <div class="row">
                                                <div class="col-12">
                                                    <button type="submit" class="btn  btn-primary btn-block px-4">Forgot Password</button>
                                                </div>
                                                <div class="col-12 text-center">
                                                    <a href="{{ route('login') }}" class="btn btn-link box-shadow-0 px-0">Login</a>
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
    <!-- END: Content-->


@endsection
@section('js')
<script src="{{URL::asset('assets/plugins/forn-wizard/js/jquery.validate.min.js')}}"></script>
<script>    
    $(document).ready(function() {
        $("#forgot_password").validate({
            ignore: ":hidden",
            rules: {
                email: {
                    required: true,
                    email: true,
                    maxlength: 250,
                }
            },
            messages: {
                email: {
                    required: "The Email field is required",
                    email: "Email must be a valid email",
                }
            },
            errorPlacement: function (error, element) {
                error.insertAfter($(element).parent());
            },
        });
    });
</script>
@endsection