@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet" />
    <style>
        .hide{display: none;}
        label.error{font-size: 87.5%; color: #dc0441;}
    </style>
@endsection
@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            @php
                $breadcrumbText = 'Users'; // Default text
            @endphp
            <h4 class="page-title mb-0">{{ $breadcrumbText }}</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">
                        <i class="fe fe-layout mr-2 fs-14"></i>{{ $breadcrumbText }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Listing</a></li>
            </ol>
        </div>
        <div class="page-rightheader">
            <div class="btn btn-list">
                <a id="add_new" class="btn btn-info" data-type="4" data-toggle="tooltip" title="Add new">
                    <i class="fe fe-plus mr-1"></i> Add new
                </a>
            </div>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{$breadcrumbText}} Data</div>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap" id="admin_table">
                            <thead>
                                <tr>
                                    <th width="30px"></th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Created By</th>
                                    @foreach($projects as $project)
                                        <th>{{ $project->project_name }}</th>
                                    @endforeach
                                    <th>Status</th>
                                    <th data-priority="1">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
    </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="admin_form_modal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Add Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span
                            aria-hidden="true">×</span> </button>
                </div>
                <div class="modal-body">

                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <!-- INTERNAL Select2 js -->
    <script src="{{ URL::asset('assets/plugins/select2/select2.full.min.js') }}"></script>
    <script src="{{ URL::asset('assets/js/sweet-alert.js') }}"></script>
    <script src="{{URL::asset('assets/plugins/forn-wizard/js/jquery.validate.min.js')}}"></script>
    <script type="text/javascript">
        var routeName = "{{ route('admins.users', ['username' => request()->get('username', $siteSlug)]) }}";
        var projects = {!! json_encode($projects) !!};
        var projectsMap = {};

        console.log('Projects loaded:', projects);
        console.log('Number of projects:', projects ? projects.length : 0);

        if (projects && projects.length > 0) {
            projects.forEach(p => projectsMap[p.id] = p.project_name);
            console.log('Projects map:', projectsMap);
        }

        // Build column definitions dynamically
        var columnDefs = [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'email', name: 'email' },
            {
                data: 'created_by_name',
                name: 'created_by_name',
                orderable: false,
                searchable: false
            }
        ];

        // Add dynamic project columns
        console.log('Creating dynamic project columns for', projects.length, 'projects');
        projects.forEach(project => {
            console.log('Adding column for project:', project.id, project.project_name);
            columnDefs.push({
                data: null,
                name: 'project_' + project.id,
                orderable: false,
                searchable: false,
                render: function(data, type, row) {
                    console.log('Rendering project', project.id, 'for row:', row.id, 'data:', row.project_statuses_json);
                    try {
                        let statuses = {};

                        // Try to get the statuses from the row data
                        if (row.project_statuses_json) {
                            const statusJson = row.project_statuses_json;
                            console.log('statusJson type:', typeof statusJson, 'value:', statusJson);

                            // Handle different data formats
                            if (typeof statusJson === 'object') {
                                statuses = statusJson;
                            } else if (typeof statusJson === 'string') {
                                try {
                                    let cleanData = statusJson.trim();

                                    // Decode HTML entities if present
                                    if (cleanData.includes('&quot;')) {
                                        cleanData = cleanData
                                            .replace(/&quot;/g, '"')
                                            .replace(/&#34;/g, '"')
                                            .replace(/&amp;/g, '&')
                                            .replace(/&lt;/g, '<')
                                            .replace(/&gt;/g, '>');
                                    }

                                    if (cleanData.startsWith('{') || cleanData.startsWith('[')) {
                                        statuses = JSON.parse(cleanData);
                                        console.log('Successfully parsed statuses:', statuses);
                                    }
                                } catch (parseError) {
                                    console.warn('Failed to parse project statuses JSON for project', project.id, ':', statusJson, parseError);
                                }
                            }
                        }

                        // Get the status for this specific project
                        const currentStatus = statuses[project.id];
                        const userId = row.id;
                        const dropdownId = 'project_' + userId + '_' + project.id;

                        // Build the dropdown with proper selected states
                        let html = '<select class="form-control project-status-dropdown" id="' + dropdownId + '" data-admin-id="' + userId + '" data-project-id="' + project.id + '" style="width: 100%;">';

                        // Empty option (no assignment)
                        html += '<option value="" ' + (!currentStatus ? 'selected' : '') + '>--</option>';

                        // Active option
                        html += '<option value="Active" ' + (currentStatus === 'Active' ? 'selected' : '') + '>Active</option>';

                        // Inactive option
                        html += '<option value="Inactive" ' + (currentStatus === 'Inactive' ? 'selected' : '') + '>Inactive</option>';

                        // Pending option
                        html += '<option value="Pending" ' + (currentStatus === 'Pending' ? 'selected' : '') + '>Pending</option>';

                        html += '</select>';

                        return html;
                    } catch (error) {
                        console.error('Error rendering project column for project', project.id, 'error:', error);
                        return '<span class="text-danger">Error</span>';
                    }
                }
            });
        });

        // Add remaining columns
        columnDefs.push({
            data: 'status',
            name: 'status',
            orderable: false,
            searchable: false
        });

        columnDefs.push({
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false
        });

        console.log('Total column definitions:', columnDefs.length);
        console.log('Column definitions:', columnDefs);

        var table = $('#admin_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: false,
            ajax: {
                url: routeName,
                error: function(xhr, status, error) {
                    console.error('DataTable AJAX Error:', error, xhr);
                },
                complete: function(jqXHR, textStatus) {
                    console.log('AJAX complete, status:', textStatus);
                }
            },
            columns: columnDefs,
            order: [
                [1, 'asc']
            ],
            initComplete: function() {
                console.log('DataTable initialized with', columnDefs.length, 'columns');
                attachProjectStatusChangeHandlers();
            },
            drawCallback: function(settings) {
                console.log('DataTable drawn, rows displayed:', settings.fnRecordsDisplay());
                console.log('First row data:', settings.aoData[0] ? settings.aoData[0]._aData : 'No data');
            }
        });

        // Reattach handlers when table is redrawn
        $('#admin_table').on('draw.dt', function() {
            attachProjectStatusChangeHandlers();
        });

        // Handle "Add new" button click
        $(document).on('click', '#add_new', function() {
            $.ajax({
                url: "{{ route('admins.create', ['username' => request()->get('username', $siteSlug)]) }}/4",
                type: "GET",
                success: function(response) {
                    console.log(response);
                    $(".modal-body").html(response);
                    $(".modal-title").html("Create User");
                    $("#mode").val("add");
                    $("#admin_form_modal").modal('show');
                    checkValidation();
                }
            });
        });

        // Handle edit button click
        $(document).on('click', '.edit_form', function() {
            var id = $(this).data('id');
            $.ajax({
                url: $(this).data('path'),
                success: function(response) {
                    $(".modal-body").html(response);
                    $(".modal-title").html("Update User");
                    $("#mode").val("edit");
                    $("#admin_form_modal").modal('show');
                    checkValidation();
                }
            });
        });

        // Function to get color for status
        function getStatusColor(status) {
            switch(status) {
                case 'Active':
                    return '#28a745';
                case 'Inactive':
                    return '#dc3545';
                case 'Pending':
                    return '#ffc107';
                default:
                    return '#ced4da';
            }
        }

        // Function to style dropdown based on status
        function styleStatusDropdown($select) {
            const status = $select.val();
            $select.css('border-color', getStatusColor(status)).css('border-width', status ? '2px' : '1px');
        }

        // Function to attach change handlers to project status dropdowns
        function attachProjectStatusChangeHandlers() {
            // Style all existing dropdowns based on their current value
            $('.project-status-dropdown').each(function() {
                styleStatusDropdown($(this));
            });

            $(document).off('change', '.project-status-dropdown').on('change', '.project-status-dropdown', function() {
                var adminId = $(this).data('admin-id');
                var projectId = $(this).data('project-id');
                var status = $(this).val();
                var $select = $(this);

                // Update styling immediately
                styleStatusDropdown($select);

                if (!status) {
                    return; // Don't update if no status selected
                }

                $.ajax({
                    url: "{{ route('admins.update-project-status', ['username' => request()->get('username', $siteSlug)]) }}",
                    type: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        admin_id: adminId,
                        project_id: projectId,
                        status: status
                    },
                    success: function(response) {
                        if (response.success) {
                            // Visual feedback: briefly show green then revert to status color
                            $select.css('border-color', '#28a745').css('background-color', '#f0fff4');
                            setTimeout(function() {
                                styleStatusDropdown($select);
                                $select.css('background-color', '');
                            }, 1500);

                            // Show success alert
                            swal({
                                title: 'Success!',
                                text: response.message || 'Status updated successfully',
                                icon: 'success',
                                button: 'OK'
                            });
                        } else {
                            swal('Error', response.message || 'Failed to update status', 'error');
                        }
                    },
                    error: function(xhr) {
                        let errorMsg = 'Failed to update status';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMsg = xhr.responseJSON.message;
                        } else if (xhr.responseText) {
                            try {
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMsg = response.message;
                                }
                            } catch(e) {
                                // Ignore JSON parse errors
                            }
                        }
                        console.error('Update status error:', xhr, errorMsg);
                        swal('Error', errorMsg, 'error');
                        $select.val('').change();
                    }
                });
            });
        }

        // Handle delete button click
        $(document).on('click', '.delete-admin', function() {
            var id = $(this).attr("data-id");

            const siteSlug = "{{ request()->get('username', $siteSlug) }}";
            const destroyUrlTemplate = "{{ route('admins.destroy', ['username' => '__SITE__', 'admin' => '__ID__']) }}";
            const destroyUrl = destroyUrlTemplate
                .replace('__SITE__', siteSlug)
                .replace('__ID__', id);

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this user!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }, function(willDelete) {
                if (willDelete) {
                    $.ajax({
                        type: "get",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: destroyUrl,
                        success: function(response) {
                            swal({
                                title: response.msg
                            }, function(result) {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        // Form validation
        function checkValidation(){
            $.validator.addMethod("noSpace", function(value, element) {
                return value.indexOf(" ") < 0 && value !== "";
            }, "Spaces are not allowed.");

            // Attach change event listener to country dropdown
            $(document).off('change', '#vat_country_code').on('change', '#vat_country_code', function () {
                updateVatAndPhoneCode();
            });

            // Initialize values if country is already selected
            var countryValue = $('#vat_country_code').val();
            if (countryValue) {
                updateVatAndPhoneCode();
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
            var countryCode = selectedOption ? selectedOption.value : '';
            var dialCode = selectedOption ? selectedOption.getAttribute('data-dial-code') : '';

            console.log('Country Code:', countryCode);
            console.log('Dial Code:', dialCode);

            var vatCodeField = document.getElementById('vat_code');
            var phoneCodeField = document.getElementById('phone_code');

            if (vatCodeField) {
                vatCodeField.value = countryCode || '';
                console.log('VAT Code updated:', vatCodeField.value);
            }

            if (phoneCodeField) {
                phoneCodeField.value = dialCode || '';
                console.log('Phone Code updated:', phoneCodeField.value);
            }
        }
    </script>
@endsection