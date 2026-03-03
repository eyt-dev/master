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
                                    <form method="POST" class="form-horizontal form-simple" action="{{ route('register', ['username' => request()->segment(1)]) }}" id="register">
                                        @csrf
                                        <input type="hidden" name="url" value="{{ url()->full() }}">
                                        <input type="hidden" name="userType" value="{{ request()->segment(1)=='vendor' ? 2 : (request()->segment(1)=='register' ? 1 : 3) }}">
                                        <div class="mb-2">
                                            <div class="input-group mb-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-user"></i>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" placeholder="Formal Name" name="name" id="name" value="{{ old('name') }}">
                                            </div>
                                            @error('name')
                                                <label id="name-error" class="error" for="name">{{ $message }}</label>
                                            @else
                                                <label id="name-error" class="error hide" for="name">The formal name field is required</label>
                                            @enderror
                                        </div>

                                        <div class="mb-2">
                                            <div class="input-group mb-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-mail"></i>
                                                    </div>
                                                </div>
                                                <input type="email" class="form-control" placeholder="Email" name="email" id="email" value="{{ old('email') }}">
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
                                                        <i class="fe fe-mail"></i>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ old('username') }}" required="" />
                                            </div>
                                            @error('username')
                                                <label id="username-error" class="error" for="username">{{ $message }}</label>
                                            @else
                                                <label id="username-error" class="error hide" for="username">The username field is required</label>
                                            @enderror                                           
                                        </div>
                                        <div class="mb-2">
                                            <div class="input-group mb-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-flag"></i>
                                                    </div>
                                                </div>
                                                <select class="form-control" name="vat_country_code" id="vat_country_code">
                                                    <option value="">Select Country</option>
                                                    @foreach($countries as $country)
                                                    @php $iso = strtoupper(substr($country->name,0,2)); @endphp
                                                        <option value="{{ $iso }}" {{ old('vat_country_code') == $iso ? 'selected' : '' }}>
                                                            {{ $country->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @error('vat_country_code')
                                                <label id="vat_country_code-error" class="error" for="vat_country_code">{{ $message }}</label>
                                            @else
                                                <label id="vat_country_code-error" class="error hide" for="vat_country_code">The country field is required</label>
                                            @enderror
                                        </div>

                                        <div class="mb-2">
                                            <div class="input-group mb-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-credit-card"></i>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" placeholder="VAT Code" name="vat_code" id="vat_code" value="{{ old('vat_code') }}" readonly>
                                            </div>
                                            @error('vat_code')
                                                <label id="vat_code-error" class="error" for="vat_code">{{ $message }}</label>
                                            @else
                                                <label id="vat_code-error" class="error hide" for="vat_code">The VAT number field is required</label>
                                            @enderror
                                        </div>

                                        <div class="mb-2">
                                            <div class="input-group mb-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-credit-card"></i>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" placeholder="VAT Number" name="vat_number" id="vat_number" value="{{ old('vat_number') }}">
                                            </div>
                                            @error('vat_number')
                                                <label id="vat_number-error" class="error" for="vat_number">{{ $message }}</label>
                                            @else
                                                <label id="vat_number-error" class="error hide" for="vat_number">The VAT number field is required</label>
                                            @enderror
                                        </div>

                                        <div class="mb-2">
                                            <div class="input-group mb-1">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="fe fe-lock"></i>
                                                    </div>
                                                </div>
                                                <input type="password" class="form-control" placeholder="Password" name="password" id="password">
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
                                                <input type="password" class="form-control" placeholder="Confirm Password" name="password_confirmation" id="password_confirmation">
                                            </div>
                                            <label id="password_confirmation-error" class="error hide" for="password_confirmation">The password confirmation does not match</label>
                                        </div>

                                        <div class="row">
                                            <div class="col-12">
                                                <button type="submit" class="btn btn-primary btn-block px-4">Register</button>
                                            </div>
                                        </div>
                                    </form>
                                    <div class="text-center pt-4">
                                        <div class="font-weight-normal fs-16">Already have an account? <a href="{{ route('login', ['username' => request()->segment(1)]) }}" class="btn-link font-weight-normal">Login</a></div>
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
<script src="{{ URL::asset('assets/plugins/forn-wizard/js/jquery.validate.min.js') }}"></script>

<script>
$(document).ready(function () {
    $.validator.addMethod("noSpace", function(value, element) {
        return value.indexOf(" ") < 0 && value !== "";
    }, "Spaces are not allowed.");
    // jQuery validation
    $("#register").validate({
        ignore: ":hidden",
        rules: {
            name: { required: true, maxlength: 255 },
            email: { required: true, email: true, maxlength: 255 },
            username: { required: true, maxlength: 255, noSpace: true },
            vat_country_code: { required: true },
            vat_number: { required: true, maxlength: 50 },
            password: { required: true, minlength: 8 },
            password_confirmation: { required: true, equalTo: "#password" }
        },
        messages: {
            name: {
                required: "The formal name field is required",
                maxlength: "Formal name cannot exceed 255 characters"
            },
            email: {
                required: "The email field is required",
                email: "Please enter a valid email address",
                maxlength: "Email cannot exceed 255 characters"
            },
            username: {
                required: "The username field is required",
                maxlength: "Username cannot exceed 255 characters",
                noSpace: "Spaces are not allowed."
            },
            vat_country_code: {
                required: "Please select a country"
            },
            vat_number: {
                required: "The VAT number is required",
                maxlength: "VAT number cannot exceed 50 characters"
            },
            password: {
                required: "The password field is required",
                minlength: "Password must be at least 8 characters long"
            },
            password_confirmation: {
                required: "Please confirm your password",
                equalTo: "Passwords do not match"
            }
        }
    });

    // 🔹 Function to update VAT number
    function updateVatNumber() {
        var countryCode = $('#vat_country_code').val();

        if (countryCode) {
            $('#vat_code')
                .val(countryCode)
                .trigger('keyup'); // trigger validation
        } else {
            $('#vat_code').val('');
        }
    }

    // 🔹 On country change
    $('#vat_country_code').on('change', function () {
        updateVatNumber();
    });

    // 🔹 Set initial VAT number if old value exists (page reload / validation fail)
    if ($('#vat_country_code').val()) {
        updateVatNumber();
    }

});
</script>

@endsection