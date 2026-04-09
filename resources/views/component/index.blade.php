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
            <h4 class="page-title mb-0">{{__('Components')}}</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">
                        <i class="fe fe-layout mr-2 fs-14"></i>{{__('Components')}}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">{{__('Listing')}}</a></li>
            </ol>
        </div>
        <div class="page-rightheader">
            <div class="btn btn-list">
                <a id="add_new" class="btn btn-info" data-toggle="tooltip" title="{{__('Add new')}}">
                    <i class="fe fe-plus mr-1"></i> {{__('Add new')}}
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
                    <div class="card-title">{{__('Components Data')}}</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap" id="component_table">
                            <thead>
                                <tr>
                                    <th>{{__('Code')}}</th>
                                    <th>{{__('Name')}}</th>
                                    <th>{{__('Description')}}</th>
                                    <th>{{__('Form')}}</th>
                                    {{-- <th>{{__('Type')}}</th> --}}
                                    {{-- <th>{{__('Unit')}}</th> --}}
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

    <div class="modal fade bd-example-modal-lg" id="component_form_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('Add Component')}}</h4>
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
                url: "{{ route('component.create', ['username' => $siteSlug]) }}",
                type: "GET",
                success: function(response) {
                    $(".modal-body").html(response);
                    $(".modal-title").html("Add Component");
                    $("#component_form_modal").modal('show');
                }
            });
        });

        $(document).on('click', '.edit-component', function() {
            $.ajax({
                url: $(this).data('path'),
                success: function(response) {
                    $(".modal-body").html(response);
                    $(".modal-title").html("Update Component");
                    $("#component_form_modal").modal('show');
                }
            });
        });

        // AJAX form submit — handles both store and update
        $(document).on('submit', '#component_form', function(e) {
            e.preventDefault();

            const $form = $(this);
            const url = $form.attr('action');
            const method = $form.find('input[name="_method"]').val() || 'POST';

            // Normalize European amounts (1.234,56 → 1234.56) before sending
            normalizeFormAmounts($form);

            // Use FormData so file uploads are included
            const formData = new FormData($form[0]);

            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                headers: { 'X-HTTP-Method-Override': method },
                success: function(response) {
                    if (response.success) {
                        $("#component_form_modal").modal('hide');
                        table.ajax.reload();
                        // Show success toast/alert
                        $('body').append('<div class="alert alert-success position-fixed" style="top:20px;right:20px;z-index:9999">' + response.message + '</div>');
                        setTimeout(function() { $('.alert.alert-success').fadeOut('slow', function(){ $(this).remove(); }); }, 3000);
                    }
                },
                error: function(xhr) {
                    const res = xhr.responseJSON;
                    // Re-format amounts back to European display on error
                    $form.find('.amount-input').each(function() {
                        const val = $(this).val();
                        if (val) $(this).val(formatMoneyEU(val));
                    });
                    if (res && res.errors) {
                        let msg = '';
                        $.each(res.errors, function(field, messages) {
                            msg += messages.join('<br>') + '<br>';
                        });
                        let $err = $form.find('#ajax-errors');
                        if (!$err.length) {
                            $form.prepend('<div id="ajax-errors" class="alert alert-danger mb-3"></div>');
                            $err = $form.find('#ajax-errors');
                        }
                        $err.html(msg).show();
                    } else if (res && res.message) {
                        alert(res.message);
                    }
                }
            });
        });

        var table = $('#component_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('component.index', ['username' => $siteSlug]) }}",
            columns: [
                { data: 'code', name: 'code' },
                { data: 'name', name: 'name' },
                { data: 'description', name: 'description' },
                { data: 'form', name: 'form' },
                // { data: 'type', name: 'type' },
                // { data: 'unit', name: 'unit' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('click', '.delete-component', function() {
            var id = $(this).attr("data-id");
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this element!",
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
                        url: "{{ route('component.destroy', ['username' => $siteSlug, ':id']) }}".replace(':id', id),
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

        
    </script>
@endsection
