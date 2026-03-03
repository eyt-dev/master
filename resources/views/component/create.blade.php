<form
    action="{{ isset($component) && $component->id ? route('component.update', $component->id) : route('component.store') }}"
    method="post"
    id="component_form"
    novalidate=""
    class="needs-validation"
    enctype="multipart/form-data">

@csrf

@if(isset($component) && $component->id)
    @method('PUT')
@endif

<!-- Hidden field for component ID (for edit mode) -->
    @if(isset($component) && $component->id)
        <input type="hidden" name="component_id" id="component_id" value="{{ $component->id }}">
    @else
        <input type="hidden" name="component_id" id="component_id" value="">
    @endif

    <div class="row">
        <!-- Code Textbox -->
        <div class="col-md-4 mb-3">
            <div class="form-group">
                <label for="code" class="form-label">{{__('Code')}} <span class="text-red">*</span></label>
                <input type="text" class="form-control @error('code') is-invalid @enderror" name="code" id="code" placeholder="{{__('Enter Code')}}"
                       value="{{ old('code', $component->code ?? '') }}" required=""/>
                <div id="code-error" class="text-danger" style="display: none;"></div>

                @error('code')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Name Textbox -->
        <div class="col-md-4 mb-3">
            <div class="form-group">
                <label for="name" class="form-label">{{__('Name')}} <span class="text-red">*</span></label>
                <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" id="name" placeholder="{{__('Enter Name')}}"
                       value="{{ old('name', $component->name ?? '') }}" required=""/>

                @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div class="col-md-4 mb-3">
            <div class="form-group">
                <label for="description" class="form-label">{{__('Description')}}</label>
                <input type="text" class="form-control @error('description') is-invalid @enderror" name="description" id="description"
                       placeholder="{{__('Enter Description')}}"
                       value="{{ old('description', $component->description ?? '') }}"/>

                @error('description')
                <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Form --}}
        <div class="col-md-4 mb-3">
            <label for="form" class="form-label fw-bold">{{ __('Form') }} <span
                    class="text-red">*</span></label>
            <select name="form" id="form" class="form-control select2 @error('form') is-invalid @enderror" required="">
                <option value="">{{__('Select an option')}}</option>
                @foreach($forms as $form)
                    <option
                        value="{{$form->id}}" {{ old('form', $component->form_id ?? '') == $form->id ? 'selected' : '' }}>
                        {{ $form->name }}
                    </option>
                @endforeach
            </select>

            @error('form')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Unit --}}
        <div class="col-md-4 mb-3">
            <label for="unit" class="form-label fw-bold">{{ __('Unit') }} <span
                    class="text-red">*</span></label>
            <select name="unit" id="unit" class="form-control select2 @error('unit') is-invalid @enderror" required="">
                <option value="">{{__('Select an option')}}</option>
                @foreach($units as $unit)
                    <option
                        value="{{$unit->id}}" {{ old('unit', $component->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                        {{ $unit->symbol }}
                    </option>
                @endforeach
            </select>

            @error('unit')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Type --}}
        <div class="col-md-4 mb-3">
            <label for="type" class="form-label fw-bold">{{ __('Type') }} <span
                    class="text-red">*</span></label>
            <select name="type" id="type" class="form-control select2 @error('type') is-invalid @enderror" required="">
                <option value="">{{__('Select an option')}}</option>
                @foreach(\App\Constants\NutritionType::getNutritionType() as $key => $value)
                    <option value="{{$key}}" {{ old('type', $component->type ?? '') == $key ? 'selected' : '' }}>
                        {{ $value }}
                    </option>
                @endforeach
            </select>

            @error('type')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <!-- Elements Container -->
    <div id="elements-container">
        <!-- Dynamic elements will be inserted here -->
    </div>

    <!-- Display general elements error -->
    @error('elements')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror

<!-- Element Template (hidden) -->
    <div id="element-template" class="d-none">
        <div class="element-group-wrapper row mb-3">
            <div class="col-md-4">
                <label class="form-label">{{ __('Element') }} <span class="text-red">*</span></label>
                <select name="elements[__index__][element_id]" class="form-control select2-template">
                    <option value="" disabled selected>{{ __('Select Element') }}</option>
                    @foreach($elements as $element)
                        <option value="{{ $element->id }}">{{ $element->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('Amount') }} <span class="text-red">*</span></label>
                <input type="number" step="0.001" min="0.001" name="elements[__index__][amount]" class="form-control"
                       placeholder="{{ __('Enter Amount') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ __('Unit') }} <span class="text-red">*</span></label>
                <select name="elements[__index__][element_unit_id]" class="form-control select2-template">
                    <option value="" disabled selected>{{ __('Select Unit') }}</option>
                    @foreach($units as $unit)
                        <option value="{{$unit->id}}">{{ $unit->symbol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end mb-1">
                <button type="button" class="btn btn-outline-danger btn-sm btn-remove" title="Remove Element">
                    X
                </button>
            </div>
        </div>
    </div>

    <!-- Add Element Button -->
    <div id="add-element-container" class="mb-3 d-none">
        <button type="button" id="add-element" class="btn btn-success btn-sm">
            {{ __('Add Element') }}
        </button>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit" id="submit-btn">
            {{ isset($component) ? __('Update') : __('Save') }}
        </button>
        <a href="{{ route('component.index') }}" class="btn btn-secondary">
            {{ __('Cancel') }}
        </a>
    </div>

    @if(isset($component) && isset($componentElementsJson))
        <script>
            // Make component elements data available to JavaScript
            const componentElements = {!! $componentElementsJson !!};
        </script>
    @else
        <script>
            // Define empty array if we're in create mode
            const componentElements = [];
        </script>
    @endif
</form>

<!-- Add CSRF token meta tag if not already present -->
@if(!isset($csrfAdded))
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endif

<script>
    $(document).ready(function () {
        // ===== Select2 init =====
        $('.select2').select2({
            width: '100%',
            allowClear: true,
            placeholder: "Select an option"
        });

        // Custom validation for select2 fields
        function validateSelect2Field($select) {
            const isValid = $select.val() && $select.val() !== '';
            const $container = $select.next('.select2-container');

            $container.removeClass('is-invalid is-valid');
            $select.removeClass('is-invalid is-valid');

            if (isValid) {
                $container.addClass('is-valid');
                $select.addClass('is-valid');
            } else {
                $container.addClass('is-invalid');
                $select.addClass('is-invalid');
            }

            return isValid;
        }

        // Function to validate all elements
        function validateAllElements() {
            let isValid = true;
            const $elementsContainer = $('#elements-container');

            // Only validate if elements container has content
            if ($elementsContainer.children().length === 0) {
                return true;
            }

            $elementsContainer.find('.element-group-wrapper').each(function() {
                const $row = $(this);

                // Validate element select
                const $elementSelect = $row.find('select[name*="[element_id]"]');
                if (!validateSelect2Field($elementSelect)) {
                    isValid = false;
                }

                // Validate amount input
                const $amountInput = $row.find('input[name*="[amount]"]');
                const amountValue = $amountInput.val();
                if (!amountValue || parseFloat(amountValue) <= 0) {
                    $amountInput.addClass('is-invalid').removeClass('is-valid');
                    isValid = false;
                } else {
                    $amountInput.addClass('is-valid').removeClass('is-invalid');
                }

                // Validate unit select
                const $unitSelect = $row.find('select[name*="[element_unit_id]"]');
                if (!validateSelect2Field($unitSelect)) {
                    isValid = false;
                }
            });

            return isValid;
        }

        $('.select2').on('change.select2', function () {
            validateSelect2Field($(this));
        });

        // Add error class to Select2 elements that have validation errors
        function applyErrorsToSelect2() {
            $('.select2.is-invalid').each(function () {
                const selectId = $(this).attr('id');
                $(`#${selectId}`).next('.select2-container').addClass('is-invalid');
                $(`#${selectId}-error`).show();
            });
        }

        applyErrorsToSelect2();

        $('.select2').on('select2:close', function () {
            $(this).valid && $(this).valid();
        });

        // SINGLE CONSOLIDATED FORM VALIDATION
        $('#component_form').on('submit', function (e) {
            console.log('Form submission started');

            const form = this;
            const $form = $(this);
            const $codeField = $('#code');
            let formIsValid = true;

            // 1. Check code field validity
            if ($codeField.hasClass('is-invalid')) {
                console.log('Form blocked: Code field is invalid');
                $('#code-error').text('Please fix the code field error').addClass('text-danger').show();
                $codeField.focus();
                formIsValid = false;
            }

            // 2. Validate main form fields (non-dynamic)
            const requiredFields = ['#code', '#name', '#form', '#unit', '#type'];
            requiredFields.forEach(function(fieldId) {
                const $field = $(fieldId);
                if ($field.length) {
                    if ($field.is('select')) {
                        if (!validateSelect2Field($field)) {
                            formIsValid = false;
                        }
                    } else {
                        if (!$field.val() || $field.val().trim() === '') {
                            $field.addClass('is-invalid').removeClass('is-valid');
                            formIsValid = false;
                        } else {
                            $field.addClass('is-valid').removeClass('is-invalid');
                        }
                    }
                }
            });

            // 3. Validate dynamic elements
            if (!validateAllElements()) {
                console.log('Form blocked: Element validation failed');
                formIsValid = false;
            }

            // 4. Check if elements are required based on type
            const selectedType = $('#type').val();
            const $elementsContainer = $('#elements-container');

            if (selectedType && $elementsContainer.children().length === 0) {
                console.log('Form blocked: No elements added');
                alert('Please add at least one element for this component type.');
                formIsValid = false;
            }

            // If any validation failed, prevent submission
            if (!formIsValid) {
                e.preventDefault();
                e.stopPropagation();
                $form.addClass('was-validated');
                console.log('Form submission blocked due to validation errors');
                return false;
            }

            console.log('Form validation passed, submitting...');
            // Form will submit normally here
        });

        // Mark required Select2 fields
        $('.select2').each(function () {
            const $select = $(this);
            const $label = $('label[for="' + $select.attr('id') + '"]');

            if ($label.find('.text-red').length) {
                $select.addClass('required');
            }
        });

        // ===== Display unit based on form select =====
     //   $('#unit').prop('disabled', true);

        $('#form').on('change', function () {
            const selectedForm = $(this).val();

            if (!selectedForm) {
                $('#unit').prop('disabled', true).empty().append('<option value="">Select an option</option>');
                return;
            }

            $('#unit').prop('disabled', true).empty().append('<option disabled selected>Loading...</option>');

            $.ajax({
                url: `/component/getUnitByForm/${selectedForm}`,
                type: 'GET',
                success: function (units) {
                    console.log(units);
                    $('#unit').prop('disabled', false).empty();

                    units.forEach(unit => {
                        $('#unit').append(`<option value="${unit.id}" selected>${unit.symbol}</option>`);
                    });
                },
                error: function () {
                    $('#unit').prop('disabled', true).empty().append('<option disabled selected>Error loading unit</option>');
                }
            });
        });

        // Global element index counter
        let elementIndex = 0;

        // Function to get all selected element IDs
        function getSelectedElementIds() {
            const selectedIds = [];
            $('#elements-container select[name*="[element_id]"]').each(function() {
                const value = $(this).val();
                if (value) {
                    selectedIds.push(value);
                }
            });
            return selectedIds;
        }

        // Function to update element options availability
        function updateElementOptionsAvailability() {
            const selectedIds = getSelectedElementIds();

            $('#elements-container select[name*="[element_id]"]').each(function() {
                const $currentSelect = $(this);
                const currentValue = $currentSelect.val();

                // Reset all options to enabled
                $currentSelect.find('option').prop('disabled', false);

                // Disable selected options in other dropdowns
                selectedIds.forEach(function(selectedId) {
                    if (selectedId !== currentValue) {
                        $currentSelect.find(`option[value="${selectedId}"]`).prop('disabled', true);
                    }
                });

                // Trigger change to update Select2 display
                $currentSelect.trigger('change.select2');
            });
        }

        // ===== Element management functions =====
        function createElementRow(index, elementData = null) {
            const template = $('#element-template').html();
            const element = template.replace(/__index__/g, index);
            const $row = $(element);

            if (elementData) {
                $row.find('select[name^="elements"][name$="[element_id]"]').val(elementData.element_id);
                $row.find('input[name^="elements"][name$="[amount]"]').val(elementData.amount);
                $row.find('select[name^="elements"][name$="[element_unit_id]"]').val(elementData.element_unit_id);
            }

            return $row;
        }

        function clearElementsContainer() {
            $('#elements-container').empty();
        }

        function addElementRow(elementData = null) {
            const $newRow = createElementRow(elementIndex, elementData);
            $('#elements-container').append($newRow);

            // Initialize Select2 on the new row - REMOVE required attribute from HTML
            const $elementSelect = $newRow.find('select[name*="[element_id]"]');
            const $unitSelect = $newRow.find('select[name*="[element_unit_id]"]');
            const $amountInput = $newRow.find('input[name*="[amount]"]');

            // Remove required attributes since we handle validation manually
            $elementSelect.removeAttr('required');
            $unitSelect.removeAttr('required');
            $amountInput.removeAttr('required');

            // Set values BEFORE initializing Select2 if we have element data
            if (elementData) {
                $elementSelect.val(elementData.element_id);
                $unitSelect.val(elementData.element_unit_id);
                $amountInput.val(elementData.amount);
            }

            // Initialize Select2 AFTER setting values
            $elementSelect.select2({
                width: '100%',
                allowClear: true,
                placeholder: "Select an option"
            }).addClass('required');

            $unitSelect.select2({
                width: '100%',
                allowClear: true,
                placeholder: "Select an option"
            }).addClass('required');

            // Add validation on change
            $elementSelect.on('change', function() {
                validateSelect2Field($(this));
                updateElementOptionsAvailability();
            });

            $unitSelect.on('change', function() {
                validateSelect2Field($(this));
            });

            $amountInput.on('input blur', function() {
                const value = $(this).val();
                if (!value || parseFloat(value) <= 0) {
                    $(this).addClass('is-invalid').removeClass('is-valid');
                } else {
                    $(this).addClass('is-valid').removeClass('is-invalid');
                }
            });

            // Trigger change events to update Select2 display and validation
            if (elementData) {
                // Small delay to ensure Select2 is fully initialized
                setTimeout(function() {
                    $elementSelect.trigger('change.select2');
                    $unitSelect.trigger('change.select2');
                }, 50);
            }

            // Update availability after adding new row
            setTimeout(function() {
                updateElementOptionsAvailability();
            }, 100);

            elementIndex++;
            return $newRow;
        }

        // ===== FIXED: Display inputs based on type =====
        function handleTypeChange() {
            const type = $('#type').val();

            clearElementsContainer();
            $('#add-element-container').addClass('d-none');

            if (!type) {
                return;
            }

            const hasExistingElements = typeof componentElements !== 'undefined' && componentElements.length > 0;

            // Debug logging
            console.log('Type selected:', type);
            console.log('Has existing elements:', hasExistingElements);
            console.log('Component elements:', componentElements);

            if (type == 1) { // Individual
                if (hasExistingElements) {
                    // FIXED: Display ALL elements, not just the first one
                    componentElements.forEach(function (element, index) {
                        console.log(`Adding element ${index}:`, element);

                        const $row = addElementRow({
                            element_id: element.id,
                            amount: element.pivot.amount,
                            element_unit_id: element.pivot.element_unit_id
                        });

                        // Show remove button for all elements in Individual type
                        // If you want to hide remove for first element only when there's just one element:
                        // if (componentElements.length === 1 && index === 0) {
                        //     $row.find('.btn-remove').addClass('d-none');
                        // }
                    });

                    // Show add button for Individual type when editing
                 //   $('#add-element-container').removeClass('d-none');
                } else {
                    // For new Individual components, add one empty row
                    const $row = addElementRow();
                    // Hide remove button for single element in new Individual component
                    $row.find('.btn-remove').addClass('d-none');
                }

            } else if (type == 2 || type == 3) { // Complex or Carrier Material
                if (hasExistingElements) {
                    componentElements.forEach(function (element, index) {
                        console.log(`Adding element ${index}:`, element);

                        addElementRow({
                            element_id: element.id,
                            amount: element.pivot.amount,
                            element_unit_id: element.pivot.element_unit_id
                        });
                    });
                } else {
                    addElementRow();
                }
                $('#add-element-container').removeClass('d-none');
            }

            // Update availability after initial setup
            setTimeout(function() {
                updateElementOptionsAvailability();
            }, 200);
        }

        // Initialize type change handling
        handleTypeChange();
        $('#type').on('change', handleTypeChange);

        // ===== Add new element button =====
        $('#add-element').on('click', function () {
            addElementRow();
        });

        // ===== Remove element button (delegated event) =====
        $(document).on('click', '.btn-remove', function () {
            $(this).closest('.element-group-wrapper').remove();
            // Update availability after removal
            setTimeout(function() {
                updateElementOptionsAvailability();
            }, 100);
        });

        // Trigger type change if value exists
        if ($('#type').val()) {
            $('#type').trigger('change');
        }

        // ===== Code uniqueness validation =====
        let codeCheckTimeout;
        $('#code').on('input', function () {
            const code = $(this).val().trim();
            const componentId = $('#component_id').val() || null;
            const $codeError = $('#code-error');
            const $codeField = $(this);

            // Clear previous timeout
            clearTimeout(codeCheckTimeout);

            // Clear previous error
            $codeError.text('').hide();
            $codeField.removeClass('is-invalid is-valid');

            if (code === '') {
                return;
            }

            // Add loading state
            $codeError.text('Checking availability...').removeClass('text-danger').addClass('text-info').show();

            // Debounce the request
            codeCheckTimeout = setTimeout(function() {
                $.ajax({
                    url: "/component/check-code",
                    method: 'POST',
                    data: {
                        code: code,
                        id: componentId,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (response) {
                        $codeError.hide();
                        if (response.available) {
                            $codeField.removeClass('is-invalid').addClass('is-valid');
                            $codeError.text('Code is available').removeClass('text-danger text-info').addClass('text-success').show();
                        } else {
                            $codeField.removeClass('is-valid').addClass('is-invalid');
                            $codeError.text(response.message || 'Code is already taken').removeClass('text-success text-info').addClass('text-danger').show();
                        }
                    },
                    error: function (xhr) {
                        console.error('Code check error:', xhr.responseJSON);
                        $codeField.removeClass('is-valid').addClass('is-invalid');
                        const errorMessage = xhr.responseJSON?.message || 'Error checking code availability';
                        $codeError.text(errorMessage).removeClass('text-success text-info').addClass('text-danger').show();
                    }
                });
            }, 500); // 500ms delay
        });
    });
</script>
