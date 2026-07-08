<form 
    action="{{ isset($materialStock) && $materialStock->id ? route('material-stock.update', ['username' => $siteSlug, 'material_stock' => $materialStock->id]) : route('material-stock.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="material_stock_form"
    novalidate=""
    class="needs-validation">

    @csrf

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif
    
    <div class="row">
        <!-- Stock Date -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="stock_date" class="form-label">Stock Date <span class="text-red">*</span></label>
                <input type="date" class="form-control" name="stock_date" id="stock_date" 
                    value="{{ old('stock_date', isset($materialStock) ? $materialStock->stock_date->format('Y-m-d') : '') }}" required="" />
                @error('stock_date')
                    <label id="stock_date-error" class="error" for="stock_date">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Material Name -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">Material Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Material Name"
                    value="{{ old('name', $materialStock->name ?? '') }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Farm Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="farm_id" class="form-label">Farm <span class="text-red">*</span></label>
                <select class="form-control" name="farm_id" id="farm_id" required="">
                    <option value="">Select Farm</option>
                    @if(isset($farms) && $farms->count() > 0)
                        @foreach($farms as $farm)
                            <option value="{{ $farm->id }}" {{ old('farm_id', $materialStock->farm_id ?? '') == $farm->id ? 'selected' : '' }}>
                                {{ $farm->name }}
                            </option>
                        @endforeach
                    @else
                        <option disabled>No farms available. Please create a farm first.</option>
                    @endif
                </select>
                @error('farm_id')
                    <label id="farm_id-error" class="error" for="farm_id">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Supplier Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="supplier_id" class="form-label">Supplier <span class="text-red">*</span></label>
                <select class="form-control" name="supplier_id" id="supplier_id" required="">
                    <option value="">Select Supplier</option>
                    @if(isset($suppliers) && $suppliers->count() > 0)
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $materialStock->supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                                {{ $supplier->name }}
                            </option>
                        @endforeach
                    @else
                        <option disabled>No suppliers available. Please create a supplier first.</option>
                    @endif
                </select>
                @error('supplier_id')
                    <label id="supplier_id-error" class="error" for="supplier_id">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Total Quantity -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="total_quantity" class="form-label">Total Quantity <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="total_quantity" id="total_quantity" placeholder="Total Quantity" 
                    value="{{ old('total_quantity', $materialStock->quantity ?? '') }}" required="" min="1" readonly />
                @error('total_quantity')
                    <label id="total_quantity-error" class="error" for="total_quantity">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <!-- Hangar Allocation Section -->
    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label class="form-label">Hangar Allocation <span class="text-red">*</span></label>
                <small class="form-text text-muted d-block mb-3">Select hangars and enter quantity for each. You can add up to 10 hangars.</small>
                
                <div id="hangars_allocation_container">
                    <!-- Hangar rows will be generated here -->
                </div>

                <button type="button" id="add_hangar_row" class="btn btn-sm btn-success mt-2" style="display:none;">
                    <i class="fa fa-plus mr-1"></i> Add Hangar
                </button>

                @error('hangar_quantities')
                    <label id="hangar_quantities-error" class="error" for="hangar_quantities">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <input type="hidden" id="hangar_quantities_json" name="hangar_quantities_json" />
        <button class="btn btn-primary" type="submit">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        var allHangars = [];
        var currentRowCount = 0;
        const maxRows = 10;
        
        // Load all hangars for the selected farm
        function loadHangarsForFarm(farmId) {
            if (!farmId) {
                $('#hangars_allocation_container').html('<p class="text-warning">Please select a farm first.</p>');
                $('#total_quantity').val('');
                $('#add_hangar_row').hide();
                currentRowCount = 0;
                return;
            }

            farmId = parseInt(farmId);

            $.ajax({
                url: "{{ route('material-stock.hangars-by-farm', ['username' => $siteSlug, 'farm' => ':farm']) }}".replace(':farm', farmId),
                type: 'GET',
                success: function(hangars) {
                    allHangars = hangars;
                    initializeHangarRows(hangars);
                }
            });
        }

        function initializeHangarRows(hangars) {
            var container = $('#hangars_allocation_container');
            container.html('');
            currentRowCount = 0;

            if (hangars.length === 0) {
                container.html('<p class="text-warning">No hangars found for this farm.</p>');
                $('#add_hangar_row').hide();
                return;
            }

            var existingAllocations = {};
            @if(isset($materialStockHangars))
                @foreach($materialStockHangars as $msh)
                    existingAllocations[{{ $loop->index }}] = {
                        hangar_id: {{ $msh->hangar_id }},
                        quantity: {{ $msh->quantity }}
                    };
                @endforeach
            @endif

            if (Object.keys(existingAllocations).length > 0) {
                Object.keys(existingAllocations).forEach(function(index) {
                    addHangarRow(hangars, existingAllocations[index].hangar_id, existingAllocations[index].quantity);
                });
            } else {
                addHangarRow(hangars);
            }

            updateAddButton();
            updateHangarDropdowns();
        }

        function addHangarRow(hangars, selectedHangarId = null, selectedQuantity = null) {
            if (currentRowCount >= maxRows) {
                alert('Maximum 10 hangars allowed.');
                return;
            }

            var container = $('#hangars_allocation_container');
            var rowIndex = currentRowCount;

            var html = `
                <div class="row mb-2 hangar-row" data-row-index="${rowIndex}">
                    <div class="col-md-5">
                        <select class="form-control hangar-select" name="hangar_id[]" data-row-index="${rowIndex}" required>
                            <option value="">Select Hangar</option>
            `;

            hangars.forEach(function(hangar) {
                var selected = selectedHangarId == hangar.id ? 'selected' : '';
                html += `<option value="${hangar.id}" ${selected}>${hangar.name}</option>`;
            });

            html += `
                        </select>
                    </div>
                    <div class="col-md-5">
                        <input type="number" class="form-control hangar-quantity" name="hangar_qty[]" placeholder="Quantity" 
                            value="${selectedQuantity || ''}" min="0" data-row-index="${rowIndex}" required />
                    </div>
                    <div class="col-md-2">
            `;

            if (currentRowCount > 0) {
                html += `<button type="button" class="btn btn-sm btn-danger remove-hangar-row" data-row-index="${rowIndex}">
                            <i class="fa fa-trash"></i>
                        </button>`;
            }

            html += `
                    </div>
                </div>
            `;

            container.append(html);
            currentRowCount++;
            attachEventListeners();
            calculateTotalQuantity();
        }

        function removeHangarRow(rowIndex) {
            $(`.hangar-row[data-row-index="${rowIndex}"]`).remove();
            currentRowCount--;
            updateAddButton();
            calculateTotalQuantity();
            updateHangarDropdowns();
        }

        function updateAddButton() {
            if (currentRowCount < maxRows) {
                $('#add_hangar_row').show();
            } else {
                $('#add_hangar_row').hide();
            }
        }

        function updateHangarDropdowns() {
            var selectedHangars = [];

            $('.hangar-select').each(function() {
                var value = $(this).val();
                if (value) {
                    selectedHangars.push(parseInt(value));
                }
            });

            $('.hangar-select').each(function() {
                var currentValue = $(this).val();

                $(this).find('option').each(function() {
                    var optionValue = $(this).val();
                    
                    if (optionValue && selectedHangars.includes(parseInt(optionValue)) && parseInt(optionValue) !== parseInt(currentValue)) {
                        $(this).attr('disabled', 'disabled');
                    } else if (optionValue === '') {
                        $(this).removeAttr('disabled');
                    } else {
                        $(this).removeAttr('disabled');
                    }
                });
            });
        }

        function attachEventListeners() {
            $(document).off('change keyup', '.hangar-quantity').on('change keyup', '.hangar-quantity', function() {
                calculateTotalQuantity();
            });

            $(document).off('change', '.hangar-select').on('change', '.hangar-select', function() {
                updateHangarDropdowns();
            });

            $(document).off('click', '.remove-hangar-row').on('click', '.remove-hangar-row', function(e) {
                e.preventDefault();
                var rowIndex = $(this).data('row-index');
                removeHangarRow(rowIndex);
            });
        }

        function calculateTotalQuantity() {
            var total = 0;
            $('.hangar-quantity').each(function() {
                var qty = parseInt($(this).val()) || 0;
                total += qty;
            });
            $('#total_quantity').val(total);
        }

        $(document).on('click', '#add_hangar_row', function(e) {
            e.preventDefault();
            addHangarRow(allHangars);
        });

        $('#farm_id').on('change', function() {
            var farmId = $(this).val();
            loadHangarsForFarm(farmId);
        });

        @if(isset($materialStock))
            var farmId = $('#farm_id').val();
            if (farmId) {
                loadHangarsForFarm(farmId);
            }
        @endif

        $('#material_stock_form').on('submit', function(e) {
            var selectedHangars = [];
            var totalQty = 0;
            var hasError = false;

            $('.hangar-row').each(function() {
                var hangarId = $(this).find('.hangar-select').val();
                var quantity = parseInt($(this).find('.hangar-quantity').val()) || 0;

                if (!hangarId) {
                    e.preventDefault();
                    alert('Please select a hangar for each row.');
                    hasError = true;
                    return false;
                }

                if (quantity <= 0) {
                    e.preventDefault();
                    alert('Please enter a quantity greater than 0 for each hangar.');
                    hasError = true;
                    return false;
                }

                if (selectedHangars.some(h => h.hangar_id == hangarId)) {
                    e.preventDefault();
                    alert('Duplicate hangars are not allowed. Each hangar can only be selected once.');
                    hasError = true;
                    return false;
                }

                selectedHangars.push({ hangar_id: hangarId, quantity: quantity });
                totalQty += quantity;
            });

            if (hasError) {
                return false;
            }

            if (selectedHangars.length === 0) {
                e.preventDefault();
                alert('Please select at least one hangar with quantity.');
                return false;
            }

            $('#hangar_quantities_json').val(JSON.stringify(selectedHangars));
        });
    });
</script>
