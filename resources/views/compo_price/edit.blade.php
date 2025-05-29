<form
    action="{{ isset($compoPrice) && $compoPrice->id ? route('compo_price.update', $compoPrice->id) : route('compo_price.store') }}"
    method="POST"
    id="compo_price_form_edit"
    novalidate
    class="needs-validation"
    enctype="multipart/form-data">

@csrf
@if(isset($compoPrice) && $compoPrice->id)
    @method('PUT')
@endif

<!-- Hidden field for record ID (for validation) -->
    @if(isset($compoPrice) && $compoPrice->id)
        <input type="hidden" id="compo_price_id" value="{{ $compoPrice->id }}">
    @endif

    <div class="row row-cols-1 row-cols-md-2 g-3 pb-5">
        <!-- Component Select -->
        <div class="col">
            <label for="component" class="form-label fw-bold">{{ __('Component') }} <span
                    class="text-danger">*</span></label>
            <select name="component" id="component"
                    class="form-select select2 @error('component') is-invalid @enderror" required="">
                @php
                    $selectedComponent = old('component', isset($compoPrice) ? $compoPrice->component_id . '_' . $compoPrice->element_id : '');
                @endphp

                <option value="">{{ __('Select an option') }}</option>
                @foreach($components as $component)
                    @foreach($component->elements as $element)
                        <option value="{{ $component->id }}_{{ $element->id }}"
                            {{ $selectedComponent == $component->id . '_' . $element->id ? 'selected' : '' }}>
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
                   value="{{ old('pricing_date', isset($compoPrice->pricing_date) ? $compoPrice->pricing_date : '') }}">
            <div class="form-check mt-1 d-flex align-items-center">
                <input type="checkbox" id="set_last_date" name="set_last_date"
                       class="form-check-input @error('set_last_date') is-invalid @enderror"
                    {{ old('set_last_date', isset($compoPrice) && $compoPrice->set_last_date ? 'checked' : '') }}>
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
                   value="{{ old('price', $compoPrice->price ?? '') }}"
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
                    class="form-select select2 @error('unit') is-invalid @enderror">
                <option value="">{{ __('Select an option') }}</option>
                @foreach(\App\Constants\Unit::getUnit() as $key => $value)
                    <option value="{{ $key }}"
                        {{ old('unit', $compoPrice->unit ?? '') == $key ? 'selected' : '' }}>
                        {{$value}}
                    </option>
                @endforeach
            </select>
            <div class="form-check mt-1 d-flex align-items-center">
                <input type="checkbox" id="set_last_unit" name="set_last_unit"
                       class="form-check-input @error('set_last_unit') is-invalid @enderror"
                    {{ old('set_last_unit', isset($compoPrice) && $compoPrice->set_last_unit ? 'checked' : '') }}>
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
        <button class="btn btn-primary" id="update_btn" type="submit">Save</button>
        <a href="{{ route('compo_price.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

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

        // Check initial state and disable fields if checkboxes are checked
        if ($('#set_last_date').is(':checked')) {
            $('#pricing_date').prop('disabled', true);
            $('#pricing_date').closest('.col').find('label .text-danger').hide();
        }

        if ($('#set_last_unit').is(':checked')) {
            $('#unit').prop('disabled', true).prop('required', false);
        }

        // Handle pricing date interactions
        $('#set_last_date').on('change', function () {
            if ($(this).is(':checked')) {
                $('#pricing_date').val('').prop('disabled', true);
                $('#pricing_date').closest('.col').find('label .text-danger').hide();
            } else {
                $('#pricing_date').prop('disabled', false);
                $('#pricing_date').closest('.col').find('label .text-danger').show();
            }
        });

        // Handle unit interactions
        $('#set_last_unit').on('change', function () {
            if ($(this).is(':checked')) {
                $('#unit').val('').trigger('change').prop('disabled', true);
                $('#unit').prop('required', false);
            } else {
                $('#unit').prop('disabled', false).prop('required', true);
            }
        });

        // Handle manual pricing date input
        $('#pricing_date').on('change', function () {
            if ($(this).val()) {
                $('#set_last_date').prop('checked', false);
            }
        });

        // Handle manual unit selection
        $('#unit').on('change', function () {
            if ($(this).val()) {
                $('#set_last_unit').prop('checked', false);
            }
        });

        // Form validation
        $("#compo_price_form_edit").validate({
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

        // Handle form submission
        $('#update_btn').click(function (e) {
            e.preventDefault();

            if (!$('#compo_price_form_edit').valid()) {
                return;
            }

            $.ajax({
                url: $('#compo_price_form_edit').attr('action'),
                type: "POST",
                data: $('#compo_price_form_edit').serialize(),
                success: function (response) {
                    $('#compo_price_form_modal').modal('hide');

                    // Reload the main table
                    if (typeof table !== 'undefined') {
                        table.ajax.reload();
                    }

                    swal({
                        title: "Success!",
                        text: "Compo Price updated successfully.",
                        icon: "success",
                    });
                },
                error: function (xhr) {
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
                                errorHtml = '<div class="text-danger small">' + messages[0] + '</div>';
                                input.closest('.form-check').after(errorHtml);
                                input.addClass('is-invalid');
                            } else if (field === 'set_last_unit') {
                                errorHtml = '<div class="text-danger small">' + messages[0] + '</div>';
                                input.closest('.form-check').after(errorHtml);
                                input.addClass('is-invalid');
                            } else if (field === 'component' || field === 'unit') {
                                input.addClass('is-invalid');
                                input.next('.select2').after(errorHtml);
                            } else {
                                input.addClass('is-invalid');
                                input.after(errorHtml);
                            }
                        });
                    } else {
                        swal({
                            title: "Error!",
                            text: "Something went wrong. Please try again.",
                            icon: "error",
                        });
                    }
                }
            });
        });
    });
</script>
