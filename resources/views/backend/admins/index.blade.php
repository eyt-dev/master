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
                $breadcrumbText = 'Admins'; // Default text

                switch($type) {
                    case 1:
                        $breadcrumbText = 'Admins';
                        break;
                    case 2:
                        $breadcrumbText = 'Public Vendors';
                        break;
                    case 3:
                        $breadcrumbText = 'Private Vendors';
                        break;
                }
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
                @if(auth()->user()->type == 0 && request('type') != 3 || auth()->user()->type == 1 && request('type') == 3)
                    {{-- Super Admin (0) can create admins except Private Vendors (3) --}}
                    {{-- Admin (1) can create only Private Vendors (3) --}}

                    <a id="add_new" class="btn btn-info" data-type="{{ $type }}" data-toggle="tooltip" title="Add new">
                        <i class="fe fe-plus mr-1"></i> Add new 
                    </a>
                @endif

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
                                    <th>Username</th>
                                    <th>Type</th>
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
        var adminType = {{ $type }};
        var routeName = '';
        switch(adminType) {
            case 1:
                routeName = "{{ route('admins.index', ['username' => request()->get('username', $siteSlug)]) }}";
                break;
            case 2:
                routeName = "{{ route('admins.publicVendor', ['username' => request()->get('username', $siteSlug)]) }}";
                break;
            case 3:
                routeName = "{{ route('admins.privateVendor', ['username' => request()->get('username', $siteSlug)]) }}";
                break;
        }
        $(document).on('click', '#add_new', function() {
            $.ajax({
                url: "{{ route('admins.create', ['username' => request()->get('username', $siteSlug)]) }}/" + adminType,
                type: "GET",
                success: function(response) {
                    console.log(response);
                    
                    $(".modal-body").html(response);
                    $(".modal-title").html("Create Form");
                    $("#admin_form_modal").modal('show');
                    // $("#admin_form input[name='type']").val(adminType);
                    checkValidation();
                }
            });
        });
        $(document).on('click', '.edit_form', function() {
            var id = $(this).data('id');
            $.ajax({
                url: $(this).data('path'),
                success: function(response) {
                    $(".modal-body").html(response);
                    $(".modal-title").html("Update Form");
                    $("#admin_form_modal").modal('show');
                    checkValidation();
                }
            });
        });
        var table = $('#admin_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: {
                url: routeName,
                data: function (d) {
                    d.type = adminType; // Pass type dynamically
                }
            },
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'username', name: 'username' },
                { 
                    data: 'type', 
                    name: 'type',
                    render: function(data) {
                        const types = {
                            0: 'Super Admin',
                            1: 'Admin',
                            2: 'Public Vendor',
                            3: 'Private Vendor',
                            4: 'User'
                        };
                        return types[data] || data;
                    }
                },
                { 
                    data: 'created_by_name', 
                    name: 'created_by_name',
                    orderable: false,
                    searchable: false
                },
                { 
                    data: 'status_dropdown', 
                    name: 'status_dropdown',
                    orderable: false,
                    searchable: false
                },
                { 
                    data: 'action', 
                    name: 'action', 
                    orderable: false, 
                    searchable: false 
                }
            ],
            order: [
                [1, 'asc']
            ]
        });
        
        $(document).on('click', '.delete-admin', function() {
            var id = $(this).attr("data-id");
          
            const siteSlug = "{{ request()->get('username', $siteSlug) }}";
            const destroyUrlTemplate = "{{ route('admins.destroy', ['username' => '__SITE__', 'admin' => '__ID__']) }}";
            const destroyUrl = destroyUrlTemplate
                .replace('__SITE__', siteSlug)
                .replace('__ID__', id);

            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this admin!",
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
        $(document).on('change', '.status-dropdown', function() {
            var status = $(this).val();
            var id = $(this).data('id');
            var routeName = "{{ route('admins.update-status', ['username' => request()->route('username')]) }}";
            
            $.ajax({
                url: routeName,
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    status: status,
                    id: id,
                    _method: 'PATCH'
                },
                success: function(response) {
                    swal('Success', 'Status updated successfully', 'success');
                    // Reload the table without resetting pagination
                    if (typeof table !== 'undefined') {
                        table.ajax.reload(null, false);
                    }
                },
                error: function(xhr) {
                    swal('Error', 'Error updating status', 'error');
                    console.error(xhr.responseText);
                }
            });
        });
        function checkValidation(){
            $("#admin_form").validate({
                ignore: ":hidden",
                rules: {
                    name: {
                        required: true,
                        maxlength: 100,
                    },
                    email: {
                        required: true,
                        email: true,
                        maxlength: 250,
                    }
                },
                messages: {
                    name: {
                        required: "The Name field is required",
                        maxlength: "Name cannot exceed 100 characters",
                    },
                    email: {
                        required: "The Email field is required",
                        email: "Email must be a valid email",
                        maxlength: "Email cannot exceed 250 characters",
                    }
                },
            });
            // Add this inside your document ready function
            // Add this inside your document ready function
            
            // function checkValidation() {
            //     var forms = document.getElementsByClassName('needs-validation');
            //     var validation = Array.prototype.filter.call(forms, function(form) {
            //         form.addEventListener('submit', function(event) {
            //             if (form.checkValidity() === false) {
            //                 event.preventDefault();
            //                 event.stopPropagation();
            //             }
            //             form.classList.add('was-validated');
            //         }, false);
            //     });
            // }
        }

    </script>
@endsection