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
            <h4 class="page-title mb-0">Global Contacts</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="fe fe-users mr-2 fs-14"></i>Contacts</a></li>
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
                    <div class="card-title">Contacts Data</div>
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
                        <table class="table table-bordered text-nowrap" id="contact_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Image</th>
                                    <th>Name</th>
                                    <th>Formal Name</th>
                                    <th>VAT</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>City</th>
                                    <th>Created By</th>
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

    <div class="modal fade bd-example-modal-lg" id="contact_form_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Contact</h4>
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
        $(document).on('click', '#add_new', function() {
            $.ajax({
                url: "{{ route('global_contacts.create', ['username' => $siteSlug]) }}",
                type: "GET",
                success: function(response) {
                    $("#contact_form_modal .modal-body").html(response);
                    $("#contact_form_modal .modal-title").html("Add Contact");
                    $("#contact_form_modal").modal('show');
                    checkValidation();
                }
            });
        });

        $(document).on('click', '.edit-contact', function() {
            $.ajax({
                url: $(this).data('path'),
                success: function(response) {
                    $("#contact_form_modal .modal-body").html(response);
                    $("#contact_form_modal .modal-title").html("Update Contact");
                    $("#contact_form_modal").modal('show');
                    checkValidation();
                }
            });
        });


        var table = $('#contact_table').DataTable({
            processing: true,
            serverSide: true,
                ajax: "{{ route('global_contacts.index', ['username' => $siteSlug]) }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'formal_name', name: 'formal_name' },
                { data: 'vat', name: 'vat', orderable: false, searchable: false },
                { data: 'email', name: 'email' },
                { data: 'phone', name: 'phone' },
                { data: 'city', name: 'city' },
                { data: 'creator' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('click', '.delete-contact', function () {
            var id = $(this).attr("data-id");

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this contact!",
                type: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel",
                closeOnConfirm: false
            }, function () {

                $.ajax({
                    type: "DELETE",
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: "{{ route('global_contacts.destroy', ['username' => $siteSlug, 'contact' => ':id']) }}"
                            .replace(':id', id),

                    success: function (response) {
                        swal("Deleted!", response.msg, "success");
                        table.ajax.reload();
                    }
                });

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
