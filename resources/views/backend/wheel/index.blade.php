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
            <h4 class="page-title mb-0">Wheels</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="fe fe-layout mr-2 fs-14"></i>Wheels</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Listing</a></li>
            </ol>
        </div>
        <div class="page-rightheader">
            <div class="btn btn-list">
                <a id="add_new_wheel" class="btn btn-info" href="{{ route('wheel.create', ['site' => $siteSlug]) }}" data-toggle="tooltip" title="Add new">
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
                    <div class="card-title">Wheels Data</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap" id="wheel-table">
                            <thead>
                                <tr>
                                    <th width="30px">#</th>
                                    <th>Game</th>
                                    <th>Clips Count</th>
                                    <th>Created At</th>
                                    <th>Created By</th>
                                    <th width="300px">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data populated by DataTables -->
                            </tbody>
                        </table>
                    </div>
                </div>
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
            var table = $('#wheel-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('wheel.index', ['site' => $siteSlug]) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'game', name: 'game'},
                    {data: 'clips_count', name: 'clips_count'},
                    {data: 'created_at', name: 'created_at'},
                    { data: 'creator' },
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[3, 'desc']]
            });

            $(document).on('click', '.delete-wheel', function() {
                var id = $(this).attr("data-id");
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this wheel!",
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
                            url: "{{ route('wheel.destroy', ['site' => $siteSlug, 'wheel' => ':id']) }}".replace(':id', id),
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
        });
    </script>
@endsection
