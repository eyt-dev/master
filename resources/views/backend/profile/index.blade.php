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
                            <form action="{{ route('profile.update', ['username' => $siteSlug, 'id' => $admin->id]) }}" method="POST" id="editProfile" enctype="multipart/form-data">
                                @csrf
                                <div class="card-header">
                                    <div class="card-title">Edit Profile</div>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Formal Name <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="name" id="name" placeholder="Formal Name" value="{{ $admin->name }}" required>
                                                @error('name')
                                                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Username <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ $admin->username }}" required>
                                                @error('username')
                                                    <label id="username-error" class="error" for="username">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label class="form-label">Email <span class="text-red">*</span></label>
                                                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ $admin->email }}" required>
                                                @error('email')
                                                    <label id="email-error" class="error" for="email">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label for="vat_country_code" class="form-label">Country <span class="text-red">*</span></label>
                                                <select class="form-control" name="vat_country_code" id="vat_country_code" required>
                                                    <option value="">Select Country</option>
                                                    @if(isset($countries))
                                                        @foreach($countries as $country)
                                                            <option value="{{ $country->id }}" data-dial-code="{{ $country->dial_code ?? '' }}" data-iso-code="{{ strtoupper(substr($country->name,0,2)) }}" {{ (old('vat_country_code')??$admin->vat_country_code) == $country->id ? 'selected' : '' }}>
                                                                {{ $country->name }}
                                                            </option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                                @error('vat_country_code')
                                                    <label id="vat_country_code-error" class="error" for="vat_country_code">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label for="vat_code" class="form-label">VAT Code</label>
                                                <input type="text" class="form-control" placeholder="VAT Code" id="vat_code" readonly>
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label for="vat_number" class="form-label">VAT Number <span class="text-red">*</span></label>
                                                <input type="text" class="form-control" placeholder="VAT Number" name="vat_number" id="vat_number" value="{{ old('vat_number')??$admin->vat_number }}" required>
                                                @error('vat_number')
                                                    <label id="vat_number-error" class="error" for="vat_number">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label for="phone_code" class="form-label">Phone Code</label>
                                                <input type="text" class="form-control" placeholder="Phone Code" id="phone_code" readonly autocomplete="off" value="{{ old('phone_code', ($admin->phone_code ?? '') ? (str_starts_with($admin->phone_code, '+') ? $admin->phone_code : '+' . $admin->phone_code) : '') }}">
                                                <input type="hidden" name="phone_code" id="phone_code_hidden" value="{{ old('phone_code', $admin->phone_code ?? '') }}">
                                                @error('phone_code')
                                                    <label id="phone_code-error" class="error" for="phone_code">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label for="mobile_number" class="form-label">Mobile Number <span class="text-red">*</span></label>
                                                <input type="tel" class="form-control" placeholder="Mobile Number" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $admin->mobile_number ?? '') }}" maxlength="20" required>
                                                @error('mobile_number')
                                                    <label id="mobile_number-error" class="error" for="mobile_number">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-4 col-md-4">
                                            <div class="form-group">
                                                <label for="image" class="form-label">Profile Photo</label>
                                                <div class="custom-file">
                                                    <input type="file" class="custom-file-input" name="image" id="image" accept="image/*" onchange="previewImage(this)">
                                                    <label class="custom-file-label" for="image">Choose file</label>
                                                </div>
                                                <small class="form-text text-muted d-block mt-2">Accepted formats: JPEG, PNG, GIF (Max 2MB)</small>
                                                @if($admin->image ?? null)
                                                    <div class="mt-2">
                                                        <img src="{{ asset('storage/' . $admin->image) }}" alt="Profile" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">
                                                    </div>
                                                @endif
                                                @error('image')
                                                    <label id="image-error" class="error" for="image">{{ $message }}</label>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-sm-12">
                                            <div class="form-group">
                                                <label for="notes" class="form-label">Notes</label>
                                                <textarea class="form-control" placeholder="Additional notes" name="notes" id="notes" rows="3" maxlength="1000">{{ old('notes', $admin->notes ?? '') }}</textarea>
                                                <small class="form-text text-muted d-block mt-1">Max 1000 characters</small>
                                                @error('notes')
                                                    <label id="notes-error" class="error" for="notes">{{ $message }}</label>
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
                            <form action="{{ route('profile.change-password', ['username' => $siteSlug, 'id' => $admin->id]) }}" method="POST"
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
        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const preview = document.createElement('div');
                    preview.className = 'mt-2';
                    preview.innerHTML = `<img src="${e.target.result}" alt="Profile" class="img-thumbnail" style="max-width: 100px; max-height: 100px;">`;

                    const existingPreview = input.parentElement.querySelector('.mt-2');
                    if (existingPreview) {
                        existingPreview.remove();
                    }
                    input.parentElement.appendChild(preview);
                };
                reader.readAsDataURL(file);
            }
        }

        function updateVatAndPhoneCode() {
            var countrySelect = document.getElementById('vat_country_code');
            if (!countrySelect) {
                return;
            }

            var selectedOption = countrySelect.options[countrySelect.selectedIndex];
            var isoCode = selectedOption ? selectedOption.getAttribute('data-iso-code') : '';
            var dialCode = selectedOption ? selectedOption.getAttribute('data-dial-code') : '';

            var vatCodeField = document.getElementById('vat_code');
            var phoneCodeField = document.getElementById('phone_code');
            var phoneCodeHiddenField = document.getElementById('phone_code_hidden');

            if (vatCodeField) {
                vatCodeField.value = isoCode || '';
            }

            if (phoneCodeField && phoneCodeHiddenField) {
                var formattedDialCode = dialCode && dialCode.trim() ? (dialCode.startsWith('+') ? dialCode : '+' + dialCode) : '';
                phoneCodeField.value = formattedDialCode;
                phoneCodeHiddenField.value = dialCode || '';
            }
        }

        $(document).ready(function() {
            const countrySelect = document.getElementById('vat_country_code');

            // Initialize phone code and VAT code on page load
            if (countrySelect && countrySelect.value) {
                updateVatAndPhoneCode();
            }

            // Update on country selection change
            if (countrySelect) {
                countrySelect.addEventListener('change', function() {
                    updateVatAndPhoneCode();
                });
            }

            $.validator.addMethod("filesize", function(value, element) {
                if (element.files.length == 0) return true;
                return (element.files[0].size <= 2048000);
            }, "File size must be less than 2MB");

            $.validator.addMethod("accept", function(value, element) {
                if (element.files.length == 0) return true;
                let file = element.files[0];
                return /\.(jpg|jpeg|png|gif)$/i.test(file.name);
            }, "Only JPEG, PNG, GIF files are allowed");

            $("#editProfile").validate({
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
                    username: {
                        required: true,
                        maxlength: 255,
                    },
                    mobile_number: {
                        required: true,
                        maxlength: 20,
                    },
                    vat_country_code: {
                        required: true,
                    },
                    vat_number: {
                        required: true,
                        maxlength: 255,
                    },
                    phone_code: {
                        required: true,
                    },
                    image: {
                        filesize: true,
                        accept: true,
                    },
                    notes: {
                        maxlength: 1000,
                    },
                },
                messages: {
                    name: {
                        required: "The Formal Name field is required",
                        maxlength: "Formal Name cannot exceed 255 characters",
                    },
                    email: {
                        required: "The Email field is required",
                        email: "Email must be a valid email",
                        maxlength: "Email cannot exceed 255 characters",
                    },
                    username: {
                        required: "The Username field is required",
                        maxlength: "Username cannot exceed 255 characters",
                    },
                    mobile_number: {
                        required: "The Mobile Number field is required",
                        maxlength: "Mobile Number cannot exceed 20 characters",
                    },
                    vat_country_code: {
                        required: "Please select a country",
                    },
                    vat_number: {
                        required: "The VAT Number field is required",
                        maxlength: "VAT Number cannot exceed 255 characters",
                    },
                    phone_code: {
                        required: "Please select a country to populate phone code",
                    },
                    image: {
                        filesize: "File size must be less than 2MB",
                        accept: "Only JPEG, PNG, GIF files are allowed",
                    },
                    notes: {
                        maxlength: "Notes cannot exceed 1000 characters",
                    },
                },
                errorElement: "label",
                errorClass: "error",
                highlight: function(element) {
                    $(element).closest(".form-group").addClass("has-error");
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).closest(".form-group").removeClass("has-error");
                    $(element).removeClass("is-invalid");
                },
                errorPlacement: function(error, element) {
                    if (element.type === 'file') {
                        error.insertAfter(element.closest(".custom-file"));
                    } else {
                        error.insertAfter(element);
                    }
                },
            });

            $("#changePasswordForm").validate({
                ignore: ":hidden",
                rules: {
                    old_password: {
                        required: true,
                    },
                    password: {
                        required: true,
                        minlength: 8,
                    },
                    password_confirmation: {
                        required: true,
                        minlength: 8,
                        equalTo: "#password",
                    },
                },
                messages: {
                    old_password: {
                        required: "The Old Password field is required",
                    },
                    password: {
                        required: "The Password field is required",
                        minlength: "Password must be at least 8 characters long",
                    },
                    password_confirmation: {
                        required: "The Confirm Password field is required",
                        minlength: "Password must be at least 8 characters long",
                        equalTo: "Passwords do not match",
                    },
                },
            });
        });

    </script>

@endsection