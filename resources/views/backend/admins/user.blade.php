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

        var table = $('#admin_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: routeName
            },
            columns: [
                {
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'name',
                    name: 'name'
                },
               {
                    data: 'email',
                    name: 'email'
                },
                {
                    data: 'created_by_name',
                    name: 'created_by'
                },
                {
                    data: 'status',
                    name: 'status',
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ],
            order: [
                [1, 'asc']
            ]
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

            $('#vat_country_code').on('change', function () {
                updateVatNumber();
            });

            // Set initial VAT number if old value exists
            if ($('#vat_country_code').val()) {
                updateVatNumber();
            }
        }

        // Function to update VAT number
        function updateVatNumber() {
            var countryCode = $('#vat_country_code').val();

            if (countryCode) {
                $('#vat_code')
                    .val(countryCode)
                    .trigger('keyup');
            } else {
                $('#vat_code').val('');
            }
        }
    </script>
@endsection