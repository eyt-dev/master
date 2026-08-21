<form 
    action="{{ isset($admin) && $admin->id
        ? route('admins.update', ['username' => $siteSlug, 'admin' => $admin->id])
        : route('admins.store', ['username' => $siteSlug]) }}"
    method="POST" 
    id="admin_form"
    novalidate=""
    class="needs-validation" 
    enctype="multipart/form-data">

    @csrf
    <input type="hidden" id="mode" value="add">
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <input type="hidden" name="type" value="{{ request('type', 1) }}">

                <label for="name" class="form-label">Formal Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Formal Name" value="{{ old('name', $admin->name) }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="username" class="form-label">Username <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ old('username', $admin->username) }}" required="" />
                @error('username')
                    <label id="username-error" class="error" for="username">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="email" class="form-label">Email <span class="text-red">*</span></label>
                <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email', $admin->email) }}" {{ !isset($admin->id) ? '' : 'readonly' }} required="" />
                @error('email')
                    <label id="email-error" class="error" for="email">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="Password" value="{{ old('password') }}" />
                @error('password')
                    <label id="password-error" class="error" for="password">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="vat_country_code" class="form-label">Country <span class="text-red">*</span></label>
                <select class="form-control" name="vat_country_code" id="vat_country_code">
                    <option value="">Select Country</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" data-dial-code="{{ $country->dial_code ?? '' }}" data-iso-code="{{ strtoupper(substr($country->name,0,2)) }}" {{ (old('vat_country_code')??$admin->vat_country_code) == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('vat_country_code')
                    <label id="vat_country_code-error" class="error" for="vat_country_code">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="vat_code" class="form-label">VAT Code <span class="text-red">*</span></label>
                <input type="text" class="form-control" placeholder="VAT Code" id="vat_code" value="" readonly>
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="vat_number" class="form-label">VAT Number <span class="text-red">*</span></label>
                <input type="text" class="form-control" placeholder="VAT Number" name="vat_number" id="vat_number" value="{{ old('vat_number')??$admin->vat_number }}">
                @error('vat_number')
                    <label id="vat_number-error" class="error" for="vat_number">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="phone_code" class="form-label">Phone Code</label>
                <input type="text" class="form-control" placeholder="Phone Code" id="phone_code" value="{{ old('phone_code', ($admin->phone_code ?? '') ? (str_starts_with($admin->phone_code, '+') ? $admin->phone_code : '+' . $admin->phone_code) : '') }}" readonly autocomplete="off">
                <input type="hidden" name="phone_code" id="phone_code_hidden" value="{{ old('phone_code', $admin->phone_code ?? '') }}">
                @error('phone_code')
                    <label id="phone_code-error" class="error" for="phone_code">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="mobile_number" class="form-label">Mobile Number</label>
                <input type="tel" class="form-control" placeholder="Mobile Number" name="mobile_number" id="mobile_number" value="{{ old('mobile_number', $admin->mobile_number ?? '') }}" maxlength="20">
                @error('mobile_number')
                    <label id="mobile_number-error" class="error" for="mobile_number">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
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

        <div class="col-sm-12">
            <div class="form-group">
                <!-- Section Header -->
                <div class="d-flex align-items-center mb-3">
                    <label class="form-label mb-0 mr-2">
                        <i class="fe fe-briefcase mr-2" style="color: #007bff;"></i>Project & Status
                    </label>
                </div>

                @php
                    // Build a map of saved statuses for quick lookup
                    $savedStatuses = [];
                    if (isset($admin->id)) {
                        foreach ($admin->projectStatuses as $ps) {
                            $savedStatuses[$ps->project_id] = $ps->status;
                        }
                    }
                @endphp

                <!-- Projects Container -->
                <div class="bg-light rounded-lg p-0" id="project-status-container" style="border: 1px solid #dee2e6; background-color: #f8f9fa !important;">
                    @forelse($projects as $index => $project)
                        @php
                            $projectId = $project->id;
                            $savedStatus = $savedStatuses[$projectId] ?? null;
                            $oldStatus = old("project_rows.{$index}.status");
                            $selectedStatus = $oldStatus !== null ? $oldStatus : $savedStatus;
                            $statusColor = match($selectedStatus) {
                                'Active' => '#28a745',
                                'Inactive' => '#dc3545',
                                'Pending' => '#ffc107',
                                default => '#6c757d'
                            };
                        @endphp
                        <div class="d-flex align-items-center justify-content-between p-3" style="border-bottom: 1px solid #dee2e6;">
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="mr-3">
                                    <i class="fe fe-folder-open" style="font-size: 18px; color: #007bff;"></i>
                                </div>
                                <div>
                                    <p class="mb-0 font-weight-600" style="color: #212529;">{{ $project->project_name }}</p>
                                    <input type="hidden" name="project_rows[{{ $index }}][project_id]" value="{{ $projectId }}">
                                </div>
                            </div>
                            <div class="ml-3" style="min-width: 160px;">
                                <select class="form-control form-control-sm project-status-select" name="project_rows[{{ $index }}][status]" style="border-color: {{ $statusColor }}; border-width: 2px;">
                                    <option value="">-- (Not Assigned)</option>
                                    <option value="Active" {{ $selectedStatus === 'Active' ? 'selected' : '' }}>Active</option>
                                    <option value="Inactive" {{ $selectedStatus === 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="Pending" {{ $selectedStatus === 'Pending' ? 'selected' : '' }}>Pending</option>
                                </select>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5" style="color: #6c757d;">
                            <i class="fe fe-inbox mb-3" style="font-size: 32px; display: block; opacity: 0.5;"></i>
                            <p class="mb-0">No projects available</p>
                        </div>
                    @endforelse
                </div>

                @error('project_rows')
                    <label class="error text-danger d-block mt-2" for="project_rows">{{ $message }}</label>
                @enderror

                <!-- Helper Text -->
                <small class="text-muted d-block mt-2">
                    <i class="fe fe-info mr-1"></i>Assign at least one project to this admin with an Active or Inactive status
                </small>
            </div>
        </div>

    </div>
    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('admins.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

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

    // Function to update VAT code and phone code based on selected country
    function updateVatAndPhoneCode() {
        var countrySelect = document.getElementById('vat_country_code');
        if (!countrySelect) {
            console.log('Country select not found');
            return;
        }

        var selectedOption = countrySelect.options[countrySelect.selectedIndex];
        var isoCode = selectedOption ? selectedOption.getAttribute('data-iso-code') : '';
        var dialCode = selectedOption ? selectedOption.getAttribute('data-dial-code') : '';

        console.log('ISO Code:', isoCode);
        console.log('Dial Code:', dialCode);

        var vatCodeField = document.getElementById('vat_code');
        var phoneCodeField = document.getElementById('phone_code');
        var phoneCodeHiddenField = document.getElementById('phone_code_hidden');

        if (vatCodeField) {
            vatCodeField.value = isoCode || '';
            console.log('VAT Code updated:', vatCodeField.value);
        }

        if (phoneCodeField && phoneCodeHiddenField) {
            // Add + prefix to dial code if it exists and doesn't already have it
            var formattedDialCode = dialCode && dialCode.trim() ? (dialCode.startsWith('+') ? dialCode : '+' + dialCode) : '';
            phoneCodeField.value = formattedDialCode;
            
            // Store the raw dial code (without +) in the hidden field for submission
            phoneCodeHiddenField.value = dialCode || '';
            console.log('Phone Code displayed:', formattedDialCode);
            console.log('Phone Code hidden (for submission):', phoneCodeHiddenField.value);
        }
    }

    (function () {
        const form = document.getElementById('admin_form');
        const container = document.getElementById('project-status-container');
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

        // Update select styling when status changes
        if (container) {
            container.addEventListener('change', function(e) {
                if (e.target.classList.contains('project-status-select')) {
                    const select = e.target;

                    // Update border color based on selected value
                    if (select.value === 'Active') {
                        select.style.borderColor = '#28a745';
                        select.style.borderWidth = '2px';
                    } else if (select.value === 'Inactive') {
                        select.style.borderColor = '#dc3545';
                        select.style.borderWidth = '2px';
                    } else if (select.value === 'Pending') {
                        select.style.borderColor = '#ffc107';
                        select.style.borderWidth = '2px';
                    } else {
                        select.style.borderColor = '#ced4da';
                        select.style.borderWidth = '1px';
                    }
                }
            });
        }

        // Form validation on submit
        if (form) {
            form.addEventListener('submit', function (event) {
                const statusSelects = container?.querySelectorAll('select[name$="[status]"]') || [];

                // Count how many projects have a status selected
                let assignedCount = 0;
                statusSelects.forEach(function (select) {
                    if (select.value && select.value !== '') {
                        assignedCount++;
                    }
                });

                // Show message if no projects assigned
                if (assignedCount === 0) {
                    event.preventDefault();
                    alert('Please select at least one project with a status (Active or Inactive).');
                    return false;
                }
            });
        }
    })();
</script>