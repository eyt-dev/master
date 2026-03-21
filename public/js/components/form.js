$(document).ready(function () {

    const $form        = $('#component_form');
    const $type        = $('#type');
    const $formSelect  = $('#form');
    const $unitSelect  = $('#unit');
    const $elements    = $('#elements-container');
    const $addBtnWrap  = $('#add-element-container');
    const $addBtn      = $('#add-element');

    let elementIndex = 0;

    // ────────────────────────────────────────────────
    //  Init Select2 on static fields
    // ────────────────────────────────────────────────
    $('.select2').select2({ width: '100%', placeholder: "Select...", allowClear: true });

    // ────────────────────────────────────────────────
    //  Load units when form changes (AJAX)
    // ────────────────────────────────────────────────
    $formSelect.on('change', function () {
        const formId = $(this).val();
        $unitSelect.prop('disabled', true).empty();

        if (!formId) {
            $unitSelect.append('<option value="">Select Form first</option>');
            return;
        }

        $.get(`/component/getUnitByForm/${formId}`)
            .done(units => {
                $unitSelect.empty().append('<option value="">Select Unit</option>');
                units.forEach(u => $unitSelect.append(`<option value="${u.id}">${u.symbol}</option>`));
                $unitSelect.prop('disabled', false);
            })
            .fail(() => {
                $unitSelect.append('<option>Error loading units</option>');
            });
    });

    // Trigger if value already selected (edit mode)
    if ($formSelect.val()) $formSelect.trigger('change');

    // ────────────────────────────────────────────────
    //  Element row template (as JS object – cleaner)
    // ────────────────────────────────────────────────
    function createElementRow(data = {}) {
        elementIndex++;

        const $row = $(`
            <div class="element-row row g-3 mb-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label">Element <span class="text-danger">*</span></label>
                    <select name="elements[${elementIndex}][element_id]" class="form-select element-select" required></select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Amount <span class="text-danger">*</span></label>
                    <input type="number" step="0.001" min="0.001" name="elements[${elementIndex}][amount]"
                           class="form-control amount-input" value="${data.amount || ''}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Unit <span class="text-danger">*</span></label>
                    <select name="elements[${elementIndex}][element_unit_id]" class="form-select unit-select" required></select>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger btn-sm remove-element">×</button>
                </div>
            </div>
        `);

        // Populate selects
        populateElementSelect($row.find('.element-select'), data.element_id);
        populateUnitSelect($row.find('.unit-select'),   data.element_unit_id);

        return $row;
    }

    function populateElementSelect($select, selected = null) {
        $select.empty().append('<option value="">Select Element</option>');
        // Assume global window.elements exists or fetch once
        window.elements?.forEach(el => {
            $select.append(`<option value="${el.id}" ${el.id == selected ? 'selected' : ''}>${el.name}</option>`);
        });
        $select.select2({ width: '100%' });
    }

    function populateUnitSelect($select, selected = null) {
        $select.empty().append('<option value="">Select Unit</option>');
        window.units?.forEach(u => {
            $select.append(`<option value="${u.id}" ${u.id == selected ? 'selected' : ''}>${u.symbol}</option>`);
        });
        $select.select2({ width: '100%' });
    }

    // You should pass these arrays from Blade too:
    // <script>window.elements = {!! json_encode($elements->toArray()) !!};</script>
    // <script>window.units   = {!! json_encode($units->toArray())   !!};</script>

    // ────────────────────────────────────────────────
    //  Type change → show/hide elements logic
    // ────────────────────────────────────────────────
    function handleTypeChange() {
        const type = $type.val();
        $elements.empty();
        $addBtnWrap.addClass('d-none');

        if (!type) return;

        if (type === '1') { // Individual
            if (window.componentElements.length > 0) {
                window.componentElements.forEach(el => $elements.append(createElementRow(el)));
            } else {
                const $row = createElementRow();
                $row.find('.remove-element').hide(); // usually only one → no remove
                $elements.append($row);
            }
        } else { // Complex / Carrier
            $addBtnWrap.removeClass('d-none');
            if (window.componentElements.length > 0) {
                window.componentElements.forEach(el => $elements.append(createElementRow(el)));
            } else {
                $elements.append(createElementRow());
            }
        }

        updateElementUniqueness();
    }

    $type.on('change', handleTypeChange);
    if ($type.val()) handleTypeChange(); // init

    // ────────────────────────────────────────────────
    //  Add / Remove element
    // ────────────────────────────────────────────────
    $addBtn.on('click', () => $elements.append(createElementRow()));

    $elements.on('click', '.remove-element', function () {
        $(this).closest('.element-row').remove();
        updateElementUniqueness();
    });

    // ────────────────────────────────────────────────
    //  Prevent duplicate elements
    // ────────────────────────────────────────────────
    function updateElementUniqueness() {
        const used = new Set();
        $elements.find('.element-select').each(function () {
            const $sel = $(this);
            const val  = $sel.val();

            $sel.find('option').prop('disabled', false);
            if (val) used.add(val);
        });

        $elements.find('.element-select').each(function () {
            const $sel = $(this);
            const val  = $sel.val();

            used.forEach(id => {
                if (id != val) $sel.find(`option[value="${id}"]`).prop('disabled', true);
            });
            $sel.trigger('change.select2');
        });
    }

    $elements.on('change', '.element-select', updateElementUniqueness);

    // ────────────────────────────────────────────────
    //  Code uniqueness check (debounced)
    // ────────────────────────────────────────────────
    let codeTimer;
    $('#code').on('input', function () {
        clearTimeout(codeTimer);
        const code = $(this).val().trim();
        const id   = $('#component_id').val() || null;
        const $err = $('#code-error');

        if (!code) {
            $err.text('').removeClass('text-danger text-success');
            return;
        }

        $err.text('Checking...').removeClass('text-danger').addClass('text-muted');

        codeTimer = setTimeout(() => {
            $.post("{{ route('component.check-code', $siteSlug) }}", {
                code,
                id,
                _token: $('meta[name="csrf-token"]').attr('content')
            })
            .done(res => {
                if (res.available) {
                    $err.text('Available').addClass('text-success').removeClass('text-danger text-muted');
                } else {
                    $err.text(res.message || 'Already taken').addClass('text-danger').removeClass('text-success text-muted');
                }
            })
            .fail(() => $err.text('Error checking code').addClass('text-danger'));
        }, 600);
    });

    // ────────────────────────────────────────────────
    //  Form submit validation (can be extended)
    // ────────────────────────────────────────────────
    $form.on('submit', function (e) {
        let valid = true;

        // Simple required check for dynamic fields
        if ($type.val() && $elements.children().length === 0) {
            alert('At least one element is required.');
            valid = false;
        }

        // You can add more checks here...

        if (!valid) {
            e.preventDefault();
            $(this).addClass('was-validated');
        }
    });
});