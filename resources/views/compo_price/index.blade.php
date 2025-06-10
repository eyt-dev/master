@extends('layouts.master')

@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet"/>
    <link href="{{ URL::asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet"/>
    <link href="{{ URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.css') }}" rel="stylesheet"/>
    <link href="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet"/>
    <style>
        .hide {
            display: none;
        }

        label.error {
            font-size: 87.5%;
            color: #dc0441;
        }
    </style>
@endsection

@section('page-header')

@endsection

@section('content')
    <div class="container d-flex justify-content-center align-items-start" style="min-height: 40vh;">
        <div class="card shadow mt-5 w-100" style="max-width: 1200px;">
            <div class="card-header text-black-50 fw-bold text-center">
                {{ 'Add Compo Price' }}
            </div>

            <div class="card-body">
                <form
                    action="{{ route('compo_price.store') }}"
                    method="POST"
                    id="compo_price_form"
                    novalidate
                    class="needs-validation"
                    enctype="multipart/form-data">

                    @csrf

                    <div class="row row-cols-1 row-cols-md-2 g-3 pb-5">
                        <!-- Component Select -->
                        <div class="col">
                            <label for="component" class="form-label fw-bold">{{ __('Component') }} <span
                                    class="text-danger">*</span></label>
                            <select name="component" id="component"
                                    class="form-select select2 @error('component') is-invalid @enderror" required="">
                                <option value="">{{ __('Select an option') }}</option>
                                @foreach($components as $component)
                                    @foreach($component->elements as $element)
                                        <option value="{{ $component->id }}_{{ $element->id }}"
                                            {{ old('component') }}>
                                            {{ $component->code }} - {{ $element->name }}
                                        </option>
                                    @endforeach
                                @endforeach
                            </select>
                            @error('component')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Pricing Date -->
                        <div class="col">
                            <label for="pricing_date" class="form-label fw-bold">{{ __('Pricing Date') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="pricing_date" id="pricing_date"
                                   class="form-control datepicker @error('pricing_date') is-invalid @enderror"
                                   value="{{ old('pricing_date') }}">
                            <div class="form-check mt-1 d-flex align-items-center">
                                <input type="checkbox" id="set_last_date" name="set_last_date" class="form-check-input @error('set_last_date') is-invalid @enderror">
                                <label class="form-check-label text-black-50 mt-1" for="set_last_date">
                                    {{__('Set Last Used As Default')}}
                                </label>
                            </div>
                            @error('set_last_date')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror
                            @error('pricing_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Price -->
                        <div class="col">
                            <label for="price" class="form-label fw-bold">{{ __('Price') }} <span
                                    class="text-danger">*</span></label>
                            <input type="text" name="price" id="price"
                                   class="form-control @error('price') is-invalid @enderror"
                                   placeholder="{{ __('Enter Price') }}"
                                   value="{{ old('price') }}"
                                   required="">
                            @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Unit -->
                        <div class="col">
                            <label for="unit" class="form-label fw-bold">{{ __('Unit') }} <span
                                    class="text-danger">*</span></label>
                            <select name="unit" id="unit"
                                    class="form-select select2 @error('unit') is-invalid @enderror" >
                                <option value="">{{ __('Select an option') }}</option>
                                @foreach(\App\Constants\Unit::getUnit() as $key => $value)
                                    <option value="{{ $key }}">
                                        {{$value}}
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-check mt-1 d-flex align-items-center">
                                <input type="checkbox" id="set_last_unit" name="set_last_unit" class="form-check-input @error('set_last_unit') is-invalid @enderror">
                                <label class="form-check-label text-black-50 mt-1" for="set_last_unit">
                                    {{__('Set Last Used As Default')}}
                                </label>
                            </div>
                            @error('set_last_unit')
                            <div class="text-danger small">{{ $message }}</div>
                            @enderror

                            @error('unit')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="card-footer">
                        <button class="btn btn-primary" id="submit_btn" type="submit">Save</button>
                        <a href="{{ route('compo_price.index') }}" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">{{__('Compo Price Data')}}</div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap" id="compo_price_table">
                            <thead>
                            <tr>
                                <th>{{__('Code')}}</th>
                                <th>{{__('Name')}}</th>
                                <th>{{__('Price')}}</th>
                                <th>{{__('Pricing Date')}}</th>
                                <th>{{__('Unit')}}</th>
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

    <div class="modal fade bd-example-modal-lg" id="compo_price_form_modal" tabindex="-1" role="dialog"
         aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{__('Edit Compo Price')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                            aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body"></div>
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
    <script>
        $(document).ready(function () {
            // ===== Select2 init =====
            $('.select2').select2({
                width: '100%',
                allowClear: true,
                placeholder: "Select an option"
            });

            $('.datepicker').datepicker({
                startDate: '-3d',
                format: "yyyy-m-d",
                orientation: 'bottom',
                autoclose: true,
                todayHighlight: true
            });

            $('#set_last_date').on('change', function () {
                if ($(this).is(':checked')) {
                    // Clear the manual input and disable it
                    $('#pricing_date').val('').prop('disabled', true);
                    $('#pricing_date').closest('.col').find('label .text-danger').hide();
                } else {
                    // Enable manual input
                    $('#pricing_date').prop('disabled', false);
                    $('#pricing_date').closest('.col').find('label .text-danger').show();
                }
            });

            // Handle unit interactions
            $('#set_last_unit').on('change', function () {
                if ($(this).is(':checked')) {
                    // Clear the manual selection and disable it
                    $('#unit').val('').trigger('change').prop('disabled', true);
                    $('#unit').prop('required', false);
                } else {
                    // Enable manual selection
                    $('#unit').prop('disabled', false).prop('required', true);
                }
            });


            function checkValidation() {
                $("#compo_price_form").validate({
                    ignore: ":hidden:not(.select2-hidden-accessible), :disabled",
                    rules: {
                        component: {required: true},
                        pricing_date: {
                            required: function() {
                                return !$('#set_last_date').is(':checked');
                            },
                            remote: {
                                url: "/compo_price/check-compo-price-unique",
                                type: "GET",
                                data: {
                                    component: function () {
                                        return $("#component").val();
                                    },
                                    pricing_date: function () {
                                        return $("#pricing_date").val();
                                    },
                                    id: function () {
                                        return $("#compo_price_id").val();
                                    }
                                }
                            }
                        },
                        price: {required: true},
                        unit: {
                            required: function() {
                                return !$('#set_last_unit').is(':checked');
                            }
                        }
                    },
                    messages: {
                        component: {required: "The Component is required"},
                        pricing_date: {
                            required: "The Pricing Date is required",
                            remote: "This Component already has a price on this date"
                        },
                        price: {required: "The Price is required"},
                        unit: {required: "The Unit is required"}
                    },
                    errorPlacement: function (error, element) {
                        if (element.hasClass('select2-hidden-accessible')) {
                            error.insertAfter(element.next('.select2'));
                        } else {
                            error.insertAfter(element);
                        }
                    }
                });
            }

            checkValidation();

            $('#submit_btn').click(function (e) {
                console.log($('#pricing_date').val());

                e.preventDefault();

                if (!$('#compo_price_form').valid()) {
                    return;
                }

                $.ajax({
                    url: '{{route('compo_price.store')}}',
                    type: "post",
                    data: $('#compo_price_form').serialize(),
                    success: function (response) {
                        $('#compo_price_form')[0].reset();
                        $('.select2').val(null).trigger('change');

                        // Re-enable fields that might have been disabled
                        $('#pricing_date').prop('disabled', false);
                        $('#unit').prop('disabled', false);

                        // Show required asterisks again
                        $('#pricing_date').closest('.col').find('label .text-danger').show();

                        table.ajax.reload();

                        // Show success message
                        swal({
                            title: "Success!",
                            text: "Compo Price created successfully.",
                            icon: "success",
                        });
                    },
                    error: function (xhr) {
                        // Handle validation errors
                        if (xhr.status === 422) {
                            var errors = xhr.responseJSON.errors;

                            // Clear previous error messages
                            $('.invalid-feedback, .text-danger.small').remove();
                            $('.is-invalid').removeClass('is-invalid');

                            // Display each error
                            $.each(errors, function (field, messages) {
                                var input = $('#' + field);
                                var errorHtml = '<div class="invalid-feedback d-block">' + messages[0] + '</div>';

                                if (field === 'set_last_date') {
                                    // Special handling for set_last_date checkbox error
                                    errorHtml = '<div class="text-danger small">' + messages[0] + '</div>';
                                    input.closest('.form-check').after(errorHtml);
                                    input.addClass('is-invalid');
                                } else if (field === 'set_last_unit') {
                                    // Special handling for set_last_unit checkbox error
                                    errorHtml = '<div class="text-danger small">' + messages[0] + '</div>';
                                    input.closest('.form-check').after(errorHtml);
                                    input.addClass('is-invalid');
                                } else if (field === 'component' || field === 'unit') {
                                    // Handle select2 fields
                                    input.addClass('is-invalid');
                                    input.next('.select2').after(errorHtml);
                                } else {
                                    // Handle regular input fields
                                    input.addClass('is-invalid');
                                    input.after(errorHtml);
                                }
                            });
                        } else {
                            // Handle other errors
                            swal({
                                title: "Error!",
                                text: "Something went wrong. Please try again.",
                                icon: "error",
                            });
                        }
                    }
                });
            });

            $(document).on('click', '.edit-compo-price', function () {
                var id = $(this).data('id');
                $.ajax({
                    url: $(this).data('path'),
                    success: function (response) {
                        $(".modal-body").html(response);
                        $(".modal-title").html("Update Compo Price");
                        $("#compo_price_form_modal").modal('show');
                        checkValidation();
                    }
                });
            });

        });


        var table = $('#compo_price_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('compo_price.get') }}",
            columns: [
                {data: 'code', name: 'code'},
                {data: 'name', name: 'name'},
                {data: 'price', name: 'price'},
                {data: 'pricing_date', name: 'pricing_date'},
                {data: 'unit', name: 'unit'},
                {data: 'action', name: 'action', ordertable: false, searchable: false}
            ]
        });

        $(document).on('click', '.delete-compo-price', function () {
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
            }, function (willDelete) {
                if (willDelete) {
                    $.ajax({
                        type: "get",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: "{{ route('compo_price.destroy', ':id') }}".replace(':id', id),
                        success: function (response) {
                            swal({
                                title: response.msg
                            }, function (result) {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        $('#set_last_date').on('change', function () {
            if ($(this).is(':checked')) {
                $('#pricing_date').closest('.col').find('label .text-danger').hide();
            } else {
                $('#pricing_date').closest('.col').find('label .text-danger').show();
            }
        });

        $('#set_last_unit').on('change', function () {
            if ($(this).is(':checked')) {
                $('#unit').prop('required', false);
            } else {
                $('#unit').prop('required', true);
            }
        });

    </script>
@endsection
