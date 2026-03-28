/**
 * money-format.js
 * European format: dot as thousands separator, comma as decimal
 * Display: 1.234.567,89  |  DB/backend: 1234567.89
 */

/**
 * Format a plain numeric string to European display format.
 * Input:  "11222.22"  or  "11222,22"  or  11222.22 (number)
 * Output: "11.222,22"
 */
function formatMoneyEU(value) {
    if (value === null || value === undefined || value === '') return '';

    value = value.toString().trim();

    // Normalize to plain numeric first
    // Remove dot thousands separators, swap comma decimal to dot
    value = value.replace(/\.(?=\d{3})/g, '').replace(',', '.');

    const parts = value.split('.');
    let intPart = parts[0].replace(/\D/g, '');
    const decPart = parts[1] !== undefined ? parts[1].replace(/\D/g, '').slice(0, 2) : null;

    if (!intPart) return '';

    // Add dot thousand separators
    intPart = intPart.replace(/\B(?=(\d{3})+(?!\d))/g, '.');

    return decPart !== null ? intPart + ',' + decPart : intPart;
}

/**
 * Format while the user is actively typing (preserves trailing comma and partial decimals).
 * Input is always in European format (dots = thousands, comma = decimal).
 */
function formatMoneyEUTyping(value) {
    if (!value) return '';

    // Allow only digits, dots, and one comma
    // Split on comma to separate integer and decimal parts
    const commaIdx = value.indexOf(',');
    let intRaw, decRaw, hasComma;

    if (commaIdx !== -1) {
        hasComma = true;
        intRaw = value.slice(0, commaIdx).replace(/\D/g, '');
        decRaw = value.slice(commaIdx + 1).replace(/\D/g, '').slice(0, 2);
    } else {
        hasComma = false;
        intRaw = value.replace(/\D/g, '');
        decRaw = '';
    }

    if (!intRaw && !hasComma) return '';

    // Add dot thousand separators to integer part
    const formattedInt = intRaw
        ? intRaw.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
        : '';

    if (hasComma) {
        return formattedInt + ',' + decRaw;
    }
    return formattedInt;
}

/**
 * Normalize European display value back to plain numeric for backend.
 * Input:  "11.222,22"
 * Output: "11222.22"
 */
function normalizeMoneyEU(value) {
    if (value === null || value === undefined || value === '') return '';
    value = value.toString().trim();
    // Remove dot thousands separators, swap comma decimal to dot
    return value.replace(/\.(?=\d{3})/g, '').replace(',', '.');
}

/**
 * Bind live formatting to all inputs with class .amount-input
 * Call once after DOM ready (and after dynamic rows are added).
 */
function bindAmountInputs() {
    // Format on input (typing) — use typing-aware formatter
    $(document).off('input.moneyEU', '.amount-input').on('input.moneyEU', '.amount-input', function () {
        const $input = $(this);
        const cursorPos = this.selectionStart;
        const oldVal = $input.val();
        const formatted = formatMoneyEUTyping(oldVal);
        $input.val(formatted);
        const diff = formatted.length - oldVal.length;
        this.setSelectionRange(Math.max(0, cursorPos + diff), Math.max(0, cursorPos + diff));
    });

    // Format on paste — use full formatter since paste gives a complete value
    $(document).off('paste.moneyEU', '.amount-input').on('paste.moneyEU', '.amount-input', function (e) {
        e.preventDefault();
        const pasted = (e.originalEvent.clipboardData || window.clipboardData).getData('text');
        $(this).val(formatMoneyEU(pasted));
    });
}

/**
 * Format all existing .amount-input values in a container (for edit/load scenarios).
 * DB values arrive as plain numeric e.g. "11222.22"
 */
function initAmountInputs($container) {
    ($container || $(document)).find('.amount-input').each(function () {
        const val = $(this).val().trim();
        if (val && val !== '0') {
            $(this).val(formatMoneyEU(val));
        }
    });
}

/**
 * Normalize all .amount-input values in a form before AJAX submit.
 */
function normalizeFormAmounts($form) {
    ($form || $(document)).find('.amount-input').each(function () {
        const normalized = normalizeMoneyEU($(this).val());
        $(this).val(normalized);
    });
}
