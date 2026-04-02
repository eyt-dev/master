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
                <div id="name-error" class="text-danger" style="display: none;"></div>

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

{{-- Template is OUTSIDE the form so its fields are never submitted --}}
<div id="element-template" class="d-none">
    <div class="element-group-wrapper row mb-2">
        <div class="col-md-4">
            <select name="elements[__index__][element_id]" class="form-control select2-template" disabled>
                <option value="" disabled selected>{{ __('Select Element') }}</option>
                @foreach($elements as $element)
                    <option value="{{ $element->id }}">{{ $element->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <input type="text" name="elements[__index__][amount]" class="form-control amount-input" placeholder="{{ __('Enter Amount') }}" disabled>
        </div>

        <div class="col-md-3">
            <select name="elements[__index__][element_unit_id]" class="form-control select2-template" disabled>
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

@if(!isset($csrfAdded))
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endif

<script>
    $(document).ready(function () {
        // ===== Select2 init =====
        $('.select2').select2({ width: '100%', allowClear: true, placeholder: "Select an option" });

        // Bind shared amount input formatting (typing + paste)
        bindAmountInputs();

        // ===== Live code uniqueness check =====
        let codeCheckTimer;
        $('#code').on('input', function () {
            clearTimeout(codeCheckTimer);
            const val = $(this).val().trim();
            const componentId = $('#component_id').val();
            if (!val) return;
            codeCheckTimer = setTimeout(function () {
                $.post('{{ route("component.check-code", ["username" => $siteSlug]) }}', {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    code: val,
                    id: componentId || null
                }, function (res) {
                    const $err = $('#code-error');
                    if (!res.available) {
                        $('#code').addClass('is-invalid').removeClass('is-valid');
                        $err.text(res.message).show();
                    } else {
                        $('#code').removeClass('is-invalid').addClass('is-valid');
                        $err.hide();
                    }
                });
            }, 400);
        });

        // ===== Live name uniqueness check =====
        let nameCheckTimer;
        $('#name').on('input', function () {
            clearTimeout(nameCheckTimer);
            const val = $(this).val().trim();
            const componentId = $('#component_id').val();
            if (!val) return;
            nameCheckTimer = setTimeout(function () {
                $.post('{{ route("component.check-name", ["username" => $siteSlug]) }}', {
                    _token: $('meta[name="csrf-token"]').attr('content'),
                    name: val,
                    id: componentId || null
                }, function (res) {
                    const $err = $('#name-error');
                    if (!res.available) {
                        $('#name').addClass('is-invalid').removeClass('is-valid');
                        $err.text(res.message).show();
                    } else {
                        $('#name').removeClass('is-invalid').addClass('is-valid');
                        $err.hide();
                    }
                });
            }, 400);
        });

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
                const rawVal = normalizeMoneyEU($amountInput.val());
                if (!rawVal || parseFloat(rawVal) <= 0) {
                    $amountInput.addClass('is-invalid'); isValid = false;
                } else {
                    $amountInput.addClass('is-valid').removeClass('is-invalid');
                }
                if (!validateSelect2Field($row.find('select[name*="[element_unit_id]"]'))) isValid = false;
            });
            return isValid;
        }

        // Client-side validation only — actual submit handled via AJAX in index.blade.php
        $('#component_form').on('submit', function (e) {
            let formIsValid = true;

            if ($('#code').hasClass('is-invalid') || $('#name').hasClass('is-invalid')) formIsValid = false;

            ['#code', '#name'].forEach(fieldId => {
                const $f = $(fieldId);
                if (!$f.val()) { $f.addClass('is-invalid'); formIsValid = false; }
                else { $f.removeClass('is-invalid'); }
            });

            if (!validateAllElements()) formIsValid = false;

            if (!formIsValid) {
                e.stopImmediatePropagation();
                e.preventDefault();
                alert('Please fix the errors before saving.');
                return false;
            }
        });

        let elementIndex = 0;

        function updateHeaderVisibility() {
            if ($('#elements-container').children().length > 0) {
                $('#elements-header').removeClass('d-none');
            } else {
                $('#elements-header').addClass('d-none');
            }
        }

        // ===== Get all currently selected element IDs across all rows =====
        function getSelectedElementIds(excludeRow) {
            const ids = [];
            $('#elements-container .element-group-wrapper').each(function () {
                if (excludeRow && $(this).is(excludeRow)) return;
                const val = $(this).find('select[name*="[element_id]"]').val();
                if (val) ids.push(val);
            });
            return ids;
        }

        // ===== Refresh disabled state on all element dropdowns =====
        function refreshElementOptions() {
            $('#elements-container .element-group-wrapper').each(function () {
                const $row = $(this);
                const $sel = $row.find('select[name*="[element_id]"]');
                const currentVal = $sel.val();
                const selectedElsewhere = getSelectedElementIds($row);

                $sel.find('option').each(function () {
                    const optVal = $(this).val();
                    if (optVal && selectedElsewhere.includes(optVal)) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });

                // Destroy and reinit select2 so it picks up the updated disabled options
                $sel.select2('destroy');
                $sel.select2({ width: '100%', placeholder: "Select Element" });

                // Re-bind change handler after reinit
                $sel.off('change.elementRefresh').on('change.elementRefresh', function () {
                    refreshElementOptions();
                });

                // Restore selected value
                if (currentVal) {
                    $sel.val(currentVal).trigger('change.select2');
                }
            });
        }

        function addElementRow(elementData = null) {
            const template = $('#element-template').html();
            const html = template.replace(/__index__/g, elementIndex);
            const $newRow = $(html);

            // Remove disabled from cloned fields so they get submitted
            $newRow.find('select, input').removeAttr('disabled');

            $('#elements-container').append($newRow);
            updateHeaderVisibility();

            const $elSel = $newRow.find('select[name*="[element_id]"]');
            const $unSel = $newRow.find('select[name*="[element_unit_id]"]');
            const $amInp = $newRow.find('input[name*="[amount]"]');

            if (elementData) {
                $elSel.val(elementData.element_id);
                $unSel.val(elementData.element_unit_id);
                if (elementData.amount) {
                    $amInp.val(formatMoneyEU(elementData.amount));
                }
            }

            $elSel.select2({ width: '100%', placeholder: "Select Element" });
            $unSel.select2({ width: '100%', placeholder: "Unit" });

            // When element selection changes, refresh disabled options on all rows
            $elSel.on('change.elementRefresh', function () {
                refreshElementOptions();
            });

            // Apply current disabled state
            refreshElementOptions();

            elementIndex++;
            return $newRow;
        }

        function handleTypeChange() {
            const type = $('#type').val();
            $('#elements-container').empty();
            if (!type) return;

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

        setTimeout(function() { handleTypeChange(); }, 100);

        $('#add-element').on('click', function() { addElementRow(); });
        $(document).on('click', '.btn-remove', function() {
            $(this).closest('.element-group-wrapper').remove();
            updateHeaderVisibility();
            refreshElementOptions(); // re-enable freed element in other rows
        });
    });
</script>