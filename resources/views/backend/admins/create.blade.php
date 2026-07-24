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
                    @php $iso = strtoupper(substr($country->name,0,2)); @endphp
                        <option value="{{ $iso }}" {{ (old('vat_country_code')??$admin->vat_country_code) == $iso ? 'selected' : '' }}>
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
                <input type="text" class="form-control" placeholder="VAT Code" name="vat_code" id="vat_code" value="{{ old('vat_code') }}" readonly>
                @error('vat_code')
                    <label id="vat_code-error" class="error" for="vat_code">{{ $message }}</label>
                @enderror
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
    (function () {
        const form = document.getElementById('admin_form');
        const container = document.getElementById('project-status-container');

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
        form?.addEventListener('submit', function (event) {
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
                swal({
                    title: 'Validation Error',
                    text: 'Please select at least one project with a status (Active or Inactive).',
                    icon: 'warning',
                    button: 'OK'
                });
                event.preventDefault();
                return false;
            }
        });
    })();
</script>