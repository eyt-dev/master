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
            var Testcount = $(".permission input:last").attr("name");
            var count = 1;    

            if (Testcount) {
                count = parseInt(Testcount.slice(5, 6)) + 1;
            }

            var table = $('#permission-tabel').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('permission.index') }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {data: 'name', name: 'name'},
                    {data: 'module', name: 'module'},
                    {data: 'guard_name', name: 'guard_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });

            $(document).on('click', '#add_new', function () {
                $.ajax({
                    url: "{{ route('permission.create') }}",
                    success: function (response) {
                        $(".modal-body").html(response);
                        $(".modal-title").html("Add Permission");
                        $("#permission_form_modal").modal('show');
                        checkValidation();
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

            // $(document).on('click', '.delete-permission', function () {
            //     if (confirm("Are you sure to delete this permission?")) {
            //         let id = $(this).data("id");
            //         $.ajax({
            //             type: 'DELETE',
            //             url: 'permission/' + id,
            //             headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            //             success: function () {
            //                 table.draw();
            //             },
            //             error: function () {
            //                 alert('Something went wrong, please try again.');
            //             }
            //         });
            //     }
            // });

            $(document).on('click', '.delete-permission', function() {
                var id = $(this).attr("data-id");
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this permission!",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                    showCancelButton: true,
                    confirmButtonText: "Yes, delete it!",
                    cancelButtonText: "Cancel"
                }, function(willDelete) {
                    if (willDelete) {
                        $.ajax({
                            type: "DELETE",
                            url: "{{ route('permission.destroy', ':id') }}".replace(':id', id),
                            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
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
                
            $(document).on('click', '#add', function () {
                let rules = '';
                let appendHtml = '<div class="each-input"> <input class="permissionInput form-control" name="name['+count+']" type="text" placeholder="Enter permission name" required=""> <button type="button" class="btn btn-danger btn-remove">Remove</button> </div>';
                $('.append-list').append(appendHtml);
                rules = {
                    required: true,
                    maxlength: 250,
                    messages: {
                        required: 'The Permission field is required'
                    }
                };
                count++;
            });

            $(document).on('click', '.btn-remove', function () {
                $(this).parent('.each-input').remove();
            });

            $(document).on('click', '.remove-permission', function () {
                let id = $(this).data("id");
                $.ajax({
                    type:'POST',
                    url:"{{ route('permission.delete') }}",
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: {'id': id},
                    success: function(data) {
                        toastr.success(data.msg);
                    },
                    error: function(data){
                        console.log('error while deleting')
                    }
                });
            });

            $(document).on('submit', '#submitform', function (e) {
                if ($("#submitform").hasClass("update-permission")) {
                    event.preventDefault();
                }
            });

        });

        function checkValidation() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }
    </script>
@endsection
