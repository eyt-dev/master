<form
    action="{{ isset($component) && $component->id ? route('component.update', $component->id) : route('component.store') }}"
    method="POST"
    id="component_form"
    novalidate=""
    class="needs-validation"
    enctype="multipart/form-data">

    @csrf

    <div class="row">
        <!-- Code Textbox -->
        <div class="col-md-4 mb-3">
            <div class="form-group">
                <label for="code" class="form-label">{{__('Code')}} <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="code" id="code" placeholder="{{__('Enter Code')}}"
                       value="{{ old('code', $component->code ?? '') }}" required=""/>

                @error('code')
                <label id="code-error" class="error" for="code">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Name Textbox -->
        <div class="col-md-4 mb-3">
            <div class="form-group">
                <label for="name" class="form-label">{{__('Name')}} <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="{{__('Enter Name')}}"
                       value="{{ old('name', $component->name ?? '') }}" required=""/>

                @error('name')
                <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Description -->
        <div class="col-md-4 mb-3">
            <div class="form-group">
                <label for="description" class="form-label">{{__('Description')}}</label>
                <input type="text" class="form-control" name="description" id="description"
                       placeholder="{{__('Enter Description')}}"
                       value="{{ old('description', $component->description ?? '') }}"/>

                @error('description')
                <label id="description-error" class="error" for="description">{{ $message }}</label>
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
                        {{ $unit->name }}
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

    <!-- Element Template (hidden) -->
    <div id="element-template" class="d-none">
        <div class="element-group-wrapper row mb-3">
            <div class="col-md-4">
                <label class="form-label">{{ __('Element') }}</label>
                <select name="elements[__index__][element_id]" class="form-control select2-template">
                    <option value="" disabled selected>{{ __('Select Element') }}</option>
                    @foreach($elements as $element)
                        <option value="{{ $element->id }}">{{ $element->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">{{ __('Amount') }}</label>
                <input type="text" name="elements[__index__][amount]" class="form-control"
                       placeholder="{{ __('Enter Amount') }}">
            </div>

            <div class="col-md-3">
                <label class="form-label">{{ __('Unit') }}</label>
                <select name="elements[__index__][element_unit_id]" class="form-control select2-template">
                    <option value="" disabled selected>{{ __('Unit') }}</option>
                    @foreach($units as $unit)
                        <option
                            value="{{$unit->id}}" {{ old('unit', $component->unit_id ?? '') == $unit->id ? 'selected' : '' }}>
                            {{ $unit->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1 d-flex align-items-end mb-1">
                <button type="button" class="btn btn-outline-danger btn-sm btn-remove">X</button>
            </div>
        </div>
    </div>

    <!-- Add Element Button -->
    <div id="add-element-container" class="mb-3 d-none">
        <button type="button" id="add-element" class="btn btn-success btn-sm">+ {{ __('Add Element') }}</button>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">{{ isset($component) ? __('Update') : __('Save') }}</button>
        <a href="{{ route('component.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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
        }

        $('.select2').on('change.select2', function () {
            validateSelect2Field($(this));
        });

        // Add error class to Select2 elements that have validation errors
        function applyErrorsToSelect2() {
            // For each error on a select2 field
            $('.select2.is-invalid').each(function () {
                const selectId = $(this).attr('id');
                // Add error class to the Select2 container
                $(`#${selectId}`).next('.select2-container').addClass('is-invalid');
                // Make the error message visible
                $(`#${selectId}-error`).show();
            });
        }

        // Apply on page load (for when validation fails and page reloads)
        applyErrorsToSelect2();

        // Apply custom validation styling to Select2
        $('.select2').on('select2:close', function () {
            $(this).valid && $(this).valid();
        });

        // Form validation
        $('#component_form').on('submit', function (e) {
            const form = $(this)[0];

            if (!form.checkValidity()) {
                e.preventDefault();
                e.stopPropagation();
            }

            $(form).addClass('was-validated');

            // Check Select2 fields manually
            $('.select2.required').each(function () {
                validateSelect2Field($(this));
            });

            // Re-apply select2 validation styling
            $(form).addClass('was-validated');
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
        $('#unit').prop('disabled', true);

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
                        $('#unit').append(`<option value="${unit.id}" selected>${unit.name}</option>`);

                    });
                },
                error: function () {
                    $('#unit').prop('disabled', true).empty().append('<option disabled selected>Error loading unit</option>');
                }
            });
        });

        // Global element index counter
        let elementIndex = 0;

        // ===== Element management functions =====
        function createElementRow(index, elementData = null) {
            // Clone the template
            const template = $('#element-template').html();
            // Replace index placeholder
            const element = template.replace(/__index__/g, index);
            // Return as jQuery object
            const $row = $(element);

            // If we have element data, set it
            if (elementData) {
                $row.find('select[name^="elements"][name$="[element_id]"]').val(elementData.element_id);
                $row.find('input[name^="elements"][name$="[amount]"]').val(elementData.amount);
                $row.find('input[name^="elements"][name$="[element_unit_id]"]').val(elementData.element_unit_id);
            }

            return $row;
        }

        function clearElementsContainer() {
            $('#elements-container').empty();
        }

        function addElementRow(elementData = null) {
            const $newRow = createElementRow(elementIndex, elementData);
            $('#elements-container').append($newRow);

            // Initialize Select2 on the new row
            const $select = $newRow.find('select');
            $select.select2({
                width: '100%',
                allowClear: true,
                placeholder: "Select an option"
            }).addClass('required'); // Add required class

            // If we have element data, set the select2 value
            if (elementData) {
                $select.val(elementData.element_id).trigger('change');
            }

            elementIndex++;
            return $newRow;
        }

        // ===== Display inputs based on type =====
        function handleTypeChange() {
            const type = $('#type').val();

            // Clear all existing elements
            clearElementsContainer();

            // Hide controls by default
            $('#add-element-container').addClass('d-none');

            if (!type) {
                return; // No type selected
            }

            // Check if we have existing component elements data
            const hasExistingElements = typeof componentElements !== 'undefined' && componentElements.length > 0;

            if (type == 1) { // Individual
                if (hasExistingElements && componentElements.length > 0) {
                    // Add a single element row with existing data
                    const $row = addElementRow({
                        element_id: componentElements[0].id,
                        amount: componentElements[0].pivot.amount
                    });
                    // Hide remove button for Individual type
                    $row.find('.btn-remove').addClass('d-none');
                } else {
                    // Add empty row
                    const $row = addElementRow();
                    // Hide remove button for Individual type
                    $row.find('.btn-remove').addClass('d-none');
                }
            } else if (type == 2 || type == 3) { // Complex
                if (hasExistingElements) {
                    // Add rows for each existing element
                    componentElements.forEach(function (element) {
                        addElementRow({
                            element_id: element.id,
                            amount: element.pivot.amount
                        });
                    });
                } else {
                    // Add an initial empty element row
                    addElementRow();
                }
                // Show the add element button
                $('#add-element-container').removeClass('d-none');
            }
        }

        // Initialize elements on page load based on selected type
        handleTypeChange();

        // Update when type changes
        $('#type').on('change', handleTypeChange);

        // ===== Add new element button =====
        $('#add-element').on('click', function () {
            addElementRow();
        });

        // ===== Remove element button (delegated event) =====
        $(document).on('click', '.btn-remove', function () {
            $(this).closest('.element-group-wrapper').remove();
        });
        // Trigger change manually if type is pre-selected
        if ($('#type').val()) {
            $('#type').trigger('change');
        }
    });
</script>
