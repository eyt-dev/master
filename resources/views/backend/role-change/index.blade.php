@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
@endsection

@section('page-header')

    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Role Changes</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="#"><i class="fe fe-layout mr-2 fs-14"></i>Authorization</a></li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Role Changes</a></li>
            </ol>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Role Change History</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap" id="role-change-table">
                            <thead>
                                <tr>
                                    <th width="30px">No.</th>
                                    <th>Date &amp; Time</th>
                                    <th>User</th>
                                    <th>Action</th>
                                    <th>From</th>
                                    <th>To</th>
                                    <th>Changed By</th>
                                    <th>Request ID</th>
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
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script>
        $(document).ready(function () {
            $('#role-change-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('role-change.index', ['username' => $siteSlug]) }}",
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false},
                    {data: 'created_at', name: 'created_at'},
                    {data: 'user', name: 'user', orderable: false},
                    {data: 'action', name: 'action'},
                    {data: 'from', name: 'from', orderable: false},
                    {data: 'to', name: 'to', orderable: false},
                    {data: 'changed_by', name: 'changed_by', orderable: false},
                    {data: 'upgrade_request_id', name: 'upgrade_request_id'}
                ],
                order: [[1, 'desc']]
            });
        });
    </script>
@endsection
