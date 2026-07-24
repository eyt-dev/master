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
                <label class="form-label">Project & Status</label>
                <div id="project-status-rows">
                    @php
                        $projectRows = old('project_rows', isset($admin->id) ? $admin->projectStatuses->map(function ($row) {
                            return ['project_id' => $row->project_id, 'status' => $row->status];
                        })->values()->all() : []);
                    @endphp

                    @if(empty($projectRows))
                        <div class="project-status-row row mb-2 align-items-end">
                            <div class="col-sm-5">
                                <select class="form-control" name="project_rows[0][project_id]">
                                    <option value="">Select Project</option>
                                    @foreach($projects as $project)
                                        <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-5">
                                <select class="form-control" name="project_rows[0][status]">
                                    <option value="">Select Status</option>
                                    <option value="Active">Active</option>
                                    <option value="Inactive">Inactive</option>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-outline-danger remove-project-row">Remove</button>
                            </div>
                        </div>
                    @else
                        @foreach($projectRows as $index => $row)
                            <div class="project-status-row row mb-2 align-items-end">
                                <div class="col-sm-5">
                                    <select class="form-control" name="project_rows[{{ $index }}][project_id]">
                                        <option value="">Select Project</option>
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ ($row['project_id'] ?? '') == $project->id ? 'selected' : '' }}>{{ $project->project_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-sm-5">
                                    <select class="form-control" name="project_rows[{{ $index }}][status]">
                                        <option value="">Select Status</option>
                                        <option value="Active" {{ ($row['status'] ?? '') == 'Active' ? 'selected' : '' }}>Active</option>
                                        <option value="Inactive" {{ ($row['status'] ?? '') == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-outline-danger remove-project-row">Remove</button>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
                <button type="button" class="btn btn-outline-primary mt-2" id="add-project-row">Add New</button>
                @error('project_rows')
                    <label id="project_rows-error" class="error" for="project_rows">{{ $message }}</label>
                @enderror
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
        const rowsContainer = document.getElementById('project-status-rows');
        const addButton = document.getElementById('add-project-row');

        function reindexRows() {
            if (!rowsContainer) {
                return;
            }

            const rows = rowsContainer.querySelectorAll('.project-status-row');
            rows.forEach(function (row, index) {
                row.querySelectorAll('select').forEach(function (select) {
                    const name = select.getAttribute('name');
                    if (name) {
                        select.setAttribute('name', name.replace(/project_rows\[\d+\]/, 'project_rows[' + index + ']'));
                    }
                });
            });
        }

        function updateProjectDisabled() {
            if (!rowsContainer) return;

            const rows = rowsContainer.querySelectorAll('.project-status-row');
            const selectedProjects = new Set();

            rows.forEach(function (row) {
                const projectSelect = row.querySelector('select[name$="[project_id]"]');
                if (projectSelect?.value) {
                    selectedProjects.add(projectSelect.value);
                }
            });

            rows.forEach(function (row) {
                const projectSelect = row.querySelector('select[name$="[project_id]"]');
                const currentValue = projectSelect?.value;

                projectSelect?.querySelectorAll('option').forEach(function (option) {
                    if (option.value && option.value !== currentValue && selectedProjects.has(option.value)) {
                        option.disabled = true;
                    } else {
                        option.disabled = false;
                    }
                });
            });
        }

        function attachProjectChangeListener(projectSelect) {
            projectSelect?.addEventListener('change', updateProjectDisabled);
        }

        addButton?.addEventListener('click', function () {
            if (!rowsContainer) {
                return;
            }

            const rowCount = rowsContainer.querySelectorAll('.project-status-row').length;
            const row = document.createElement('div');
            row.className = 'project-status-row row mb-2 align-items-end';
            row.innerHTML = `
                <div class="col-sm-5">
                    <select class="form-control" name="project_rows[${rowCount}][project_id]">
                        <option value="">Select Project</option>
                        @foreach($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5">
                    <select class="form-control" name="project_rows[${rowCount}][status]">
                        <option value="">Select Status</option>
                        <option value="Active">Active</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
                <div class="col-sm-2">
                    <button type="button" class="btn btn-outline-danger remove-project-row">Remove</button>
                </div>`;
            rowsContainer.appendChild(row);

            const newProjectSelect = row.querySelector('select[name$="[project_id]"]');
            attachProjectChangeListener(newProjectSelect);
            updateProjectDisabled();
        });

        rowsContainer?.addEventListener('click', function (event) {
            if (event.target.classList.contains('remove-project-row')) {
                event.target.closest('.project-status-row')?.remove();
                reindexRows();
                updateProjectDisabled();
            }
        });

        const projectSelects = rowsContainer?.querySelectorAll('select[name$="[project_id]"]') || [];
        projectSelects.forEach(function (select) {
            attachProjectChangeListener(select);
        });
        updateProjectDisabled();

        const form = document.getElementById('admin_form');
        form?.addEventListener('submit', function (event) {
            const rows = rowsContainer?.querySelectorAll('.project-status-row') || [];
            let hasValue = false;

            rows.forEach(function (row) {
                const projectSelect = row.querySelector('select[name$="[project_id]"]');
                const statusSelect = row.querySelector('select[name$="[status]"]');
                if (projectSelect?.value || statusSelect?.value) {
                    hasValue = true;
                }
            });

            if (rows.length > 0) {
                for (const row of rows) {
                    const projectSelect = row.querySelector('select[name$="[project_id]"]');
                    const statusSelect = row.querySelector('select[name$="[status]"]');
                    if ((projectSelect?.value && !statusSelect?.value) || (!projectSelect?.value && statusSelect?.value)) {
                        event.preventDefault();
                        alert('Each project/status row must have both a project and a status.');
                        return false;
                    }
                }
            }
        });
    })();
</script>