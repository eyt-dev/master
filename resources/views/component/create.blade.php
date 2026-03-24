<form
    action="{{ isset($component) && $component->id ? route('component.update', ['username' => $siteSlug, 'component' => $component->id]) : route('component.store', ['username' => $siteSlug]) }}"
    method="post"
    id="component_form"
    novalidate=""
    class="needs-validation"
    enctype="multipart/form-data">

    @csrf

    @if(isset($component) && $component->id)
        @method('PUT')
    @endif

    @if(isset($component) && $component->id)
        <input type="hidden" name="component_id" id="component_id" value="{{ $component->id }}">
    @else
        <input type="hidden" name="component_id" id="component_id" value="">
    @endif

    <div class="row">
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
        {{-- Form (Radio buttons in one line) --}}
        <div class="col-md-12 mb-3">
            <label for="form" class="form-label fw-bold">{{ __('Form') }} <span class="text-red">*</span></label>
            <div class="form-group">
                <div class="mt-2"> 
                    @foreach($forms as $form)
                        <div class="form-check form-check-inline @error('form') is-invalid @enderror" style="display: inline-block; margin-right: 15px;">
                            <input 
                                type="radio"
                                name="form" 
                                id="form_{{$form->id}}"
                                class="form-check-input @error('form') is-invalid @enderror" 
                                value="{{$form->id}}" 
                                {{ old('form', $component->form_id ?? '') == $form->id ? 'checked' : '' }} 
                                required>
                            <label class="form-check-label" for="form_{{$form->id}}">
                                {{ $form->name }}
                            </label>
                        </div>
                    @endforeach
                </div>

                @error('form')
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
        </div>
        <input type="hidden" name="type" id="type" value="2">
    </div>

    <hr>
    
    <div id="elements-header" class="row mb-2 d-none" style="font-weight: bold;">
        <div class="col-md-4">{{ __('Element') }} <span class="text-red">*</span></div>
        <div class="col-md-4">{{ __('Amount') }} <span class="text-red">*</span></div>
        <div class="col-md-3">{{ __('Unit') }} <span class="text-red">*</span></div>
        <div class="col-md-1"></div>
    </div>

    <div id="elements-container">
        </div>

    @error('elements')
    <div class="alert alert-danger">{{ $message }}</div>
    @enderror

    <div id="element-template" class="d-none">
        <div class="element-group-wrapper row mb-2">
            <div class="col-md-4">
                <select name="elements[__index__][element_id]" class="form-control select2-template">
                    <option value="" disabled selected>{{ __('Select Element') }}</option>
                    @foreach($elements as $element)
                        <option value="{{ $element->id }}">{{ $element->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <input type="text" name="elements[__index__][amount]" class="form-control amount-input" placeholder="{{ __('Enter Amount') }}">
            </div>

            <div class="col-md-3">
                <select name="elements[__index__][element_unit_id]" class="form-control select2-template">
                    <option value="" disabled selected>{{ __('Select Unit') }}</option>
                    @foreach($units as $unit)
                        <option value="{{$unit->id}}">{{ $unit->symbol }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-1">
                <button type="button" class="btn btn-outline-danger btn-sm btn-remove" title="Remove Element">
                    X
                </button>
            </div>
        </div>
    </div>

    <div id="add-element-container" class="mb-3 d-none">
        <button type="button" id="add-element" class="btn btn-success btn-sm">
            <i class="fa fa-plus"></i> {{ __('Add Element') }}
        </button>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit" id="submit-btn">
            {{ isset($component) ? __('Update') : __('Save') }}
        </button>
        <a href="{{ route('component.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">
            {{ __('Cancel') }}
        </a>
    </div>

    @if(isset($component) && isset($componentElementsJson))
        <script>window.componentElements = {!! $componentElementsJson !!};</script>
    @else
        <script>window.componentElements = [];</script>
    @endif
</form>

@if(!isset($csrfAdded))
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endif

<script>
    $(document).ready(function () {
        // ===== Select2 init =====
        $('.select2').select2({ width: '100%', allowClear: true, placeholder: "Select an option" });

        function validateSelect2Field($select) {
            const isValid = $select.val() && $select.val() !== '';
            const $container = $select.next('.select2-container');
            $container.removeClass('is-invalid is-valid');
            $select.removeClass('is-invalid is-valid');
            if (isValid) {
                $container.addClass('is-valid'); $select.addClass('is-valid');
            } else {
                $container.addClass('is-invalid'); $select.addClass('is-invalid');
            }
            return isValid;
        }

        function validateAllElements() {
            let isValid = true;
            const $elementsContainer = $('#elements-container');
            if ($elementsContainer.children().length === 0) return true;

            $elementsContainer.find('.element-group-wrapper').each(function() {
                const $row = $(this);
                if (!validateSelect2Field($row.find('select[name*="[element_id]"]'))) isValid = false;
                const $amountInput = $row.find('input[name*="[amount]"]');
                const amountValue = $amountInput.val();
                
                // Normalize the formatted amount for validation
                const normalizedAmount = normalizeMoney(amountValue);
                
                if (!amountValue || parseFloat(normalizedAmount) <= 0) {
                    $amountInput.addClass('is-invalid'); isValid = false;
                } else {
                    $amountInput.addClass('is-valid').removeClass('is-invalid');
                }
                if (!validateSelect2Field($row.find('select[name*="[element_unit_id]"]'))) isValid = false;
            });
            return isValid;
        }

        // Form Submit
        $('#component_form').on('submit', function (e) {
            let formIsValid = true;

            if ($('#code').hasClass('is-invalid')) formIsValid = false;

            ['#code', '#name'].forEach(fieldId => {
                const $f = $(fieldId);
                if (!$f.val()) { $f.addClass('is-invalid'); formIsValid = false; }
            });

            if (!validateAllElements()) formIsValid = false;

            if (!formIsValid) {
                e.preventDefault();
                alert('Please fix errors before saving.');
                return false;
            }

            // convert all formatted amounts before submit
            $('.amount-input').each(function () {
                let val = $(this).val();
                $(this).val(normalizeMoney(val));
            });
        });

        let elementIndex = 0;

        function updateHeaderVisibility() {
            if ($('#elements-container').children().length > 0) {
                $('#elements-header').removeClass('d-none');
            } else {
                $('#elements-header').addClass('d-none');
            }
        }

        function addElementRow(elementData = null) {
            const template = $('#element-template').html();
            const element = template.replace(/__index__/g, elementIndex);
            const $newRow = $(element);
            
            $('#elements-container').append($newRow);
            updateHeaderVisibility();

            const $elSel = $newRow.find('select[name*="[element_id]"]');
            const $unSel = $newRow.find('select[name*="[element_unit_id]"]');
            const $amInp = $newRow.find('input[name*="[amount]"]');

            if (elementData) {
                $elSel.val(elementData.element_id);
                $unSel.val(elementData.element_unit_id);
                // Format the amount value properly
                if (elementData.amount) {
                    // Convert database amount to number first, then format
                    const numericAmount = parseFloat(elementData.amount);
                    const formattedAmount = formatMoney(numericAmount);
                    $amInp.val(formattedAmount);
                }
            }

            $elSel.select2({ width: '100%', placeholder: "Select Element" });
            $unSel.select2({ width: '100%', placeholder: "Unit" });

            // Initialize amount formatting
            formatAmountsInContainer($newRow);

            elementIndex++;
            return $newRow;
        }

        // Handle Type Logic
        function handleTypeChange() {
            const type = $('#type').val();
            $('#elements-container').empty();
            if (!type) return;

            // Check if we're in edit mode and have existing elements
            if (typeof componentElements !== 'undefined' && componentElements.length > 0) {
                componentElements.forEach(el => {
                    addElementRow({
                        element_id: el.id,
                        amount: el.pivot.amount,
                        element_unit_id: el.pivot.element_unit_id
                    });
                });
            } else {
                addElementRow();
            }
            $('#add-element-container').removeClass('d-none');
        }

        // Initialize after DOM is ready
        setTimeout(function() {
            handleTypeChange();
        }, 100);
        
        $('#add-element').on('click', function() { addElementRow(); });
        $(document).on('click', '.btn-remove', function() {
            $(this).closest('.element-group-wrapper').remove();
            updateHeaderVisibility();
        });

        $('#component_form').on('submit', function (e) {
            // convert all formatted amounts before submit
            $('.amount-input').each(function () {
                let val = $(this).val();
                $(this).val(normalizeMoney(val));
            });

            let formIsValid = true;

            if ($('#code').hasClass('is-invalid')) formIsValid = false;

            ['#code', '#name'].forEach(fieldId => {
                const $f = $(fieldId);
                if (!$f.val()) { $f.addClass('is-invalid'); formIsValid = false; }
            });

            if (!validateAllElements()) formIsValid = false;

            if (!formIsValid) {
                e.preventDefault();
                alert('Please fix errors before saving.');
                return false;
            }
        });

        // ===== Money Format (European style: 1.00.000,20) =====
        function formatMoney(value, preserveCursor = false) {
            if (!value && value !== 0) return '';
            
            // Store cursor position if needed
            let cursorPos = preserveCursor ? this.selectionStart : 0;
            
            // If value is a number, convert to string and handle properly
            value = value.toString();
            
            // Check if it's a numeric input (from database) - has dots but no commas
            const isNumericInput = value.includes('.') && !value.includes(',');
            
            if (isNumericInput) {
                // For numeric database values, split properly
                let [integerPart, decimalPart] = value.split('.');
                integerPart = integerPart.replace(/\D/g, '');
                
                if (integerPart === '') return '';
                
                // Format integer part with thousand separators (periods)
                let formattedInteger = '';
                let temp = integerPart;
                while (temp.length > 3) {
                    formattedInteger = '.' + temp.slice(-3) + formattedInteger;
                    temp = temp.slice(0, -3);
                }
                formattedInteger = temp + formattedInteger;
                
                // Handle decimal part (max 2 digits)
                if (decimalPart !== undefined) {
                    decimalPart = decimalPart.replace(/\D/g, '').slice(0, 2);
                    return formattedInteger + ',' + decimalPart;
                }
                
                return formattedInteger;
            } else {
                // For user input (may have commas), remove everything except digits and comma
                value = value.replace(/[^\d,]/g, '');
                
                // Handle multiple commas - keep only the last one as decimal separator
                const commaCount = (value.match(/,/g) || []).length;
                if (commaCount > 1) {
                    const parts = value.split(',');
                    value = parts.slice(0, -1).join('') + ',' + parts[parts.length - 1];
                }
                
                // Split into integer and decimal parts
                let [integerPart, decimalPart] = value.split(',');
                
                // Remove any non-digits from integer part
                integerPart = integerPart.replace(/\D/g, '');
                
                if (integerPart === '') return '';
                
                // Format integer part with thousand separators (periods)
                let formattedInteger = '';
                let temp = integerPart;
                while (temp.length > 3) {
                    formattedInteger = '.' + temp.slice(-3) + formattedInteger;
                    temp = temp.slice(0, -3);
                }
                formattedInteger = temp + formattedInteger;
                
                // Handle decimal part (max 2 digits)
                if (decimalPart !== undefined) {
                    decimalPart = decimalPart.replace(/\D/g, '').slice(0, 2);
                    return formattedInteger + ',' + decimalPart;
                }
                
                return formattedInteger;
            }
        }

        // ===== Convert back to normal format for backend processing =====
        function normalizeMoney(value) {
            if (!value && value !== 0) return '0';
            
            // Remove all periods (thousand separators)
            value = value.toString().replace(/\./g, '');
            
            // Replace comma with dot for decimal separator
            value = value.replace(',', '.');
            
            // Return as string to preserve precision
            return value;
        }

        // ===== Initialize amount fields with proper formatting =====
        function initializeAmountFields($container) {
            $container.find('.amount-input').each(function() {
                const $input = $(this);
                const currentValue = $input.val();
                if (currentValue && currentValue !== '0') {
                    // If it's already in European format, keep it
                    if (currentValue.includes(',') || currentValue.includes('.')) {
                        // Convert to number and back to ensure proper formatting
                        const normalized = normalizeMoney(currentValue);
                        const formatted = formatMoney(normalized);
                        $input.val(formatted);
                    }
                }
            });
        }

        // ===== Apply formatting while typing with cursor preservation =====
        $(document).on('input', '.amount-input', function (e) {
            const $input = $(this);
            const currentValue = $input.val();
            const cursorPos = this.selectionStart;
            
            // Format the value
            const formatted = formatMoney.call(this, currentValue, true);
            
            // Update the value
            $input.val(formatted);
            
            // Restore cursor position (adjusted for formatting changes)
            const lengthDiff = formatted.length - currentValue.length;
            const newCursorPos = Math.max(0, cursorPos + lengthDiff);
            this.setSelectionRange(newCursorPos, newCursorPos);
        });

        // ===== Handle paste events =====
        $(document).on('paste', '.amount-input', function (e) {
            e.preventDefault();
            const pastedData = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
            const $input = $(this);
            
            // Normalize and format the pasted value
            const normalized = normalizeMoney(pastedData);
            const formatted = formatMoney(normalized);
            
            $input.val(formatted);
        });

        // ===== Format amounts when elements are added =====
        function formatAmountsInContainer($container) {
            initializeAmountFields($container);
        }
    });
</script>