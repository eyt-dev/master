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
            <h4 class="page-title mb-0">Roles</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="fe fe-layout mr-2 fs-14"></i>Roles</a></li>
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
                    <div class="card-title">Roles Data</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap" id="role-tabel">
                            <thead>
                                <tr>
                                    <th width="30px"></th>
                                    <th>Role</th>
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

    <div class="modal fade bd-example-modal-lg" id="role_form_modal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myLargeModalLabel">Add Role</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                        <span aria-hidden="true">×</span> 
                    </button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script>
        $(document).ready(function () {
            var table = $('#role-tabel').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('role.index') }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'guard_name', name: 'guard_name'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });

            $('#add_new').on('click', function () {
                $.ajax({
                    url: "{{ route('role.create') }}",
                    success: function (response) {
                        $(".modal-body").html(response);
                        $(".modal-title").html("Add Role");
                        $("#role_form_modal").modal('show');
                        checkInput();
                        checkValidation();
                    }
                });
            });

            $(document).on('click', '.edit_form', function () {
                $.ajax({
                    url: $(this).data('path'),
                    success: function (response) {
                        $(".modal-body").html(response);
                        $(".modal-title").html("Update Role");
                        $("#role_form_modal").modal('show');
                        checkInput();
                        checkValidation();
                    }
                });
            });

            // $(document).on('click', '.delete-role', function () {
            //     if (confirm("Are you sure to delete this role?")) {
            //         let id = $(this).data("id");
            //         $.ajax({
            //             type: 'DELETE',
            //             url: 'role/' + id,
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

            $(document).on('click', '.delete-role', function() {
                var id = $(this).attr("data-id");
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this role!",
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
                            url: "{{ route('role.destroy', ':id') }}".replace(':id', id),
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

            $(document).on('submit', '#module_form', function (e) {
                    e.preventDefault();

                    let formData = $(this).serialize(); // Serialize form data
                    let submitUrl = $(this).attr('action'); // Get form action URL

                    $.ajax({
                        url: submitUrl,
                        method: 'POST',
                        data: formData,
                        success: function (response) {
                            if (response.status === 'success') {
                                // Append the new module to the dropdown
                                $('#moduleId').append(
                                    `<option value="${response.module.id}" selected>${response.module.name}</option>`
                                );

                                // Close the modal
                                $('#module_form_modal').modal('hide');

                                // Display success message
                                alert('Module created successfully!');
                            }
                        },
                        error: function (xhr) {
                            // Handle validation errors
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                for (let field in errors) {
                                    alert(errors[field][0]); // Display the first error
                                }
                            }
                        },
                    });
            });
        });

        function checkInput() {
            console.warn(1);
            $('.permission .check-all').click(function() {
                var check = this.checked;
                $(this).parents('.nav-item').find('.check-one').prop("checked", check);
            });
            $('.permission .check-one').click(function() {
                var parentItem = $(this).parents('.nav-treeview').parents('.nav-item');
                var check = $(parentItem).find('.check-one:checked').length == $(parentItem).find(
                    '.check-one').length;
                $(parentItem).find('.check-all').prop("checked", check)
            });
            $('.permission .check-all').each(function() {
                var parentItem = $(this).parents('.nav-item');
                var check = $(parentItem).find('.check-one:checked').length == $(parentItem).find(
                    '.check-one').length;
                $(parentItem).find('.check-all').prop("checked", check)
            });
        }

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
