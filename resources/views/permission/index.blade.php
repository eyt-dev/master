@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet" />
@endsection

@section('page-header')
<meta name="csrf-token" content="{{ csrf_token() }}">

    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Permissions</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="fe fe-layout mr-2 fs-14"></i>Permissions</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Listing</a></li>
            </ol>
        </div>
        <div class="page-rightheader">
            <div class="btn btn-list">
                <a id="add_new" class="btn btn-info" data-toggle="tooltip" title="Add new">
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
                    <div class="card-title">Permissions Data</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap" id="permission-tabel">
                            <thead>
                                <tr>
                                    <th width="30px">No.</th>
                                    <th>Permission</th>
                                    <th>Module</th>
                                    <th>Guard</th>
                                    <th width="300px">Action</th>
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

    <div class="modal fade bd-example-modal-lg" id="permission_form_modal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Add Permission</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                        <span aria-hidden="true">×</span> 
                    </button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
    <div class="modal fade moduleFormModel" id="module_form_modal" tabindex="-1" role="dialog" aria-labelledby="moduleModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moduleModalLabel">Create Module</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    
                </div>
            </div>
        </div>
    </div>
@endsection
<script>
    var moduleCreateUrl = "{{ route('module.create') }}";
</script>
@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            var table = $('#permission-tabel').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('permission.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'module', name: 'module'},
                    {data: 'guard_name', name: 'guard_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });

            $('#add_new').on('click', function () {
                $.ajax({
                    url: "{{ route('permission.create') }}",
                    success: function (response) {
                        $(".modal-body").html(response);
                        $(".modal-title").html("Add Permission");
                        $("#permission_form_modal").modal('show');
                    }
                });
            });

            $(document).on('click', '.edit_form', function () {
                $.ajax({
                    url: $(this).data('path'),
                    success: function (response) {
                        $(".modal-body").html(response);
                        $(".modal-title").html("Update Permission");
                        $("#permission_form_modal").modal('show');
                    }
                });
            });

            $(document).on('click', '.delete-permission', function () {
                if (confirm("Are you sure to delete this permission?")) {
                    let id = $(this).data("id");
                    $.ajax({
                        type: 'DELETE',
                        url: 'permission/' + id,
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function () {
                            table.draw();
                        },
                        error: function () {
                            alert('Something went wrong, please try again.');
                        }
                    });
                }
            });

            $(document).on('click', '.add-module', function () {
                $.ajax({
                    url: moduleCreateUrl, // Route to fetch the module form
                    success: function (response) {
                        $("#module_form_modal").modal('show');
                        // Insert the response (module form) into the modal body
                        $("#module_form_modal .modal-body").html(response);
                        // Update modal title
                        $("#module_form_modal .modal-title").html("Create Module");
                        // Show the modal
                        
                    },
                    error: function () {
                        alert('Failed to load the module form. Please try again.');
                    }
                });
            });
            $(document).on('submit', '#ajax_module_form', function (e) {
                e.preventDefault();
                let formData = $(this).serialize();

                $.ajax({
                    type: 'POST',
                    url: 'module/store',
                    data: formData,
                    success: function (response) {
                        
                        $('#module_form_modal').modal('hide');

                        // Assuming response.module contains the newly created module data
                        const newModule = response.module;

                        // Check if newModule exists and contains the necessary fields
                        if (newModule && newModule.id && newModule.name) {
                            // Create a new <option> element for the new module
                            const newOption = $('<option>', {
                                value: newModule.id,   // Set the value to the new module's ID
                                text: newModule.name,  // Set the display text to the new module's name
                                selected: true         // Optionally, select the newly added module
                            });
                            // Add the new option to the beginning of the dropdown
                            $('#moduleId').append(newOption);
                        } else {
                            toastr.error('Invalid module data received.');
                        }
                        // Add the new option to the beginning of the dropdown (or where appropriate)
                        
                    },
                    error: function (response) {
                        toastr.error('Failed to create the module. Check input.');
                    },
                });
            });
            $(document).ready(function () {
        // When a "check-all" box is toggled
        $('.check-all').change(function () {
            var groupKey = $(this).attr('id'); // Get the group key (id)
            var isChecked = $(this).prop('checked'); // Check if the "check-all" is checked or unchecked

            // Select all checkboxes within the same group
            $('#' + groupKey).closest('.col-sm-12').find('.check-one').each(function () {
                $(this).prop('checked', isChecked); // Set each permission checkbox to match the "check-all" checkbox state
            });
        });

        // When any individual permission checkbox is clicked, update the "check-all" checkbox state
        $('.check-one').change(function () {
            var groupKey = $(this).closest('.col-sm-12').find('.check-all'); // Get the "check-all" checkbox for the current group
            var totalPermissions = $(this).closest('.col-sm-12').find('.check-one').length; // Total number of permissions in the group
            var checkedPermissions = $(this).closest('.col-sm-12').find('.check-one:checked').length; // Total number of checked permissions

            // If all permissions are checked, check the "check-all" box, otherwise uncheck it
            if (totalPermissions === checkedPermissions) {
                groupKey.prop('checked', true); // Check the "check-all" box
            } else {
                groupKey.prop('checked', false); // Uncheck the "check-all" box
            }
        });
    });
                
        });
    </script>
@endsection
