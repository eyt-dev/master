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
            <h4 class="page-title mb-0">Farms</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">
                        <i class="fe fe-layout mr-2 fs-14"></i>Farms
                    </a>
                </li>
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
                    <div class="card-title">Farms Data</div>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap" id="farm_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Farm Name</th>
                                    <th>Location</th>
                                    <th>Number of Hangars</th>
                                    <th>Type</th>
                                    <th>Assigned To</th>
                                    <th>Created By</th>
                                    <th>Created At</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="farm_form_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Farm</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/select2.full.min.js') }}"></script>
    <script>
        var table;
        var farmId = null;
        var formAction = null;

        $(document).on('click', '#add_new', function() {
            farmId = null;
            $.ajax({
                url: "{{ route('farm.create', ['username' => $siteSlug]) }}",
                type: "GET",
                success: function(response) {
                    $(".modal-body").html(response);
                    $(".modal-title").html("Add Farm");
                    $("#farm_form_modal").modal('show');
                    attachFormHandler();
                }
            });
        });
        
        $(document).on('click', '.edit-farm', function() {
            var id = $(this).data('id');
            farmId = id;
            $.ajax({
                url: $(this).data('path'),
                type: "GET",
                success: function(response) {
                    $(".modal-body").html(response);
                    $(".modal-title").html("Update Farm");
                    $("#farm_form_modal").modal('show');
                    attachFormHandler();
                }
            });
        });

        function attachFormHandler() {
            checkValidation();
            
            $(document).off('submit', '#farm_form').on('submit', '#farm_form', function(e) {
                e.preventDefault();
                
                var form = $(this);
                
                // Check client-side validation first
                if (form[0].checkValidity() === false) {
                    e.stopPropagation();
                    form.addClass('was-validated');
                    return false;
                }
                
                var url = farmId 
                    ? "{{ route('farm.update', ['username' => $siteSlug, 'farm' => ':id']) }}".replace(':id', farmId)
                    : "{{ route('farm.store', ['username' => $siteSlug]) }}";
                
                var method = 'POST';

                $.ajax({
                    url: url,
                    type: method,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: form.serialize(),
                    success: function(response) {
                        if (response.success) {
                            swal({
                                title: "Success!",
                                text: response.message,
                                icon: "success",
                                buttons: false,
                                timer: 2000
                            });
                            $("#farm_form_modal").modal('hide');
                            table.ajax.reload(function() {
                                table.row(0).node().scrollIntoView();
                            });
                        }
                    },
                    error: function(response) {
                        if (response.status === 422) {
                            var errors = response.responseJSON.errors;
                            
                            // Clear previous errors
                            form.find('.error').remove();
                            form.find('.form-control').removeClass('is-invalid');
                            
                            // Display new errors
                            $.each(errors, function(field, messages) {
                                var input = form.find('[name="' + field + '"]');
                                input.addClass('is-invalid');
                                input.after('<label class="error" for="' + field + '">' + messages[0] + '</label>');
                            });
                        } else {
                            swal({
                                title: "Error!",
                                text: "Something went wrong",
                                icon: "error"
                            });
                        }
                    }
                });
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
        
        table = $('#farm_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('farm.index', ['username' => $siteSlug]) }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'location', name: 'location' },
                { data: 'number_of_hangars', name: 'number_of_hangars' },
                { data: 'type', name: 'type' },
                { data: 'assigned_admin', name: 'assigned_admin' },
                { data: 'creator' },
                { data: 'created_at', name: 'created_at' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
        
        $(document).on('click', '.delete-farm', function() {
            var id = $(this).attr("data-id");
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this farm!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }, function(willDelete) {
                if (willDelete) {
                    $.ajax({
                        type: "GET",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: "{{ route('farm.destroy', ['username' => $siteSlug, 'farm' => ':id']) }}".replace(':id', id),
                        success: function(response) {
                            swal({
                                title: response.msg
                            }, function(result) {
                                table.ajax.reload();
                            });
                        }
                    });
                }
            });
        });
    </script>
@endsection
