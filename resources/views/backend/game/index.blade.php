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
            <h4 class="page-title mb-0">Games</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="fe fe-layout mr-2 fs-14"></i>Games</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Listing</a></li>
            </ol>
        </div>
        <div class="page-rightheader">
            <div class="btn btn-list">
                <a id="add_new_game" class="btn btn-info" href="{{route('game.create', ['site' => $siteSlug])}}" data-toggle="tooltip" title="Add new">
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
                    <div class="card-title">Games Data</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap" id="game-table">
                            <thead>
                                <tr>
                                    <th width="30px">#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Visibility</th>
                                    <th>Display</th>
                                    <th>Clips</th>
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

    <!-- Modal for game form (Add/Edit) -->
    <div class="modal fade bd-example-modal-lg" id="game_form_modal" tabindex="-1" game="dialog" aria-labelledby="gameModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="gameModalLabel">Add Game</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> 
                        <span aria-hidden="true">×</span> 
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Game form content goes here -->
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
            var table = $('#game-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('game.index', ['site' => $siteSlug]) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'name', name: 'name'},
                    {data: 'type', name: 'type'},
                    {data: 'visibility', name: 'visibility'},
                    {data: 'display', name: 'display'},
                    {data: 'clips', name: 'clips'},
                    {data: 'created_by', name: 'created_by'},
                    {data: 'action', name: 'action', orderable: false, searchable: false}
                ],
                order: [[1, 'asc']]
            });

            $(document).on('click', '.delete-game', function() {
                var id = $(this).attr("data-id");
                swal({
                    title: "Are you sure?",
                    text: "Once deleted, you will not be able to recover this game!",
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
                            url: "{{ route('game.destroy', ['site' => $siteSlug, 'game' => ':id']) }}".replace(':id', id),
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
