<form 
    action="{{ isset($flock) && $flock->id ? route('flock.update', ['username' => $siteSlug, 'flock' => $flock->id]) : route('flock.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="flock_form"
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
        <!-- Farm Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="farm_id" class="form-label">Farm <span class="text-red">*</span></label>
                <select class="form-control" name="farm_id" id="farm_id" required="">
                    <option value="">Select Farm</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}" {{ old('farm_id', $flock->farm_id ?? '') == $farm->id ? 'selected' : '' }}>
                            {{ $farm->name }}
                        </option>
                    @endforeach
                </select>
                @error('farm_id')
                    <label id="farm_id-error" class="error" for="farm_id">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Chicks Supplier Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="chicks_supplier_id" class="form-label">Chicks Supplier <span class="text-red">*</span></label>
                <select class="form-control" name="chicks_supplier_id" id="chicks_supplier_id" required="">
                    <option value="">Select Chicks Supplier</option>
                    @foreach($chicksSuppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('chicks_supplier_id', $flock->chicks_supplier_id ?? '') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }}
                        </option>
                    @endforeach
                </select>
                @error('chicks_supplier_id')
                    <label id="chicks_supplier_id-error" class="error" for="chicks_supplier_id">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Breed Dropdown with Grouped Options -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="breed" class="form-label">Breed <span class="text-red">*</span></label>
                <select class="form-control" name="breed" id="breed" required="">
                    <option value="">Select Breed</option>
                    <optgroup label="Broiler">
                        <option value="Ross 308" {{ old('breed', $flock->breed ?? '') == 'Ross 308' ? 'selected' : '' }}>Ross 308</option>
                        <option value="Cobb 500" {{ old('breed', $flock->breed ?? '') == 'Cobb 500' ? 'selected' : '' }}>Cobb 500</option>
                    </optgroup>
                    <optgroup label="Layer">
                        <option value="Lohmann Brown" {{ old('breed', $flock->breed ?? '') == 'Lohmann Brown' ? 'selected' : '' }}>Lohmann Brown</option>
                        <option value="Lohmann White" {{ old('breed', $flock->breed ?? '') == 'Lohmann White' ? 'selected' : '' }}>Lohmann White</option>
                    </optgroup>
                </select>
                @error('breed')
                    <label id="breed-error" class="error" for="breed">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Start Date -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="start_date" class="form-label">Start Date <span class="text-red">*</span></label>
                <input type="date" class="form-control" name="start_date" id="start_date" 
                    value="{{ old('start_date', isset($flock) ? $flock->start_date->format('Y-m-d') : '') }}" required="" />
                @error('start_date')
                    <label id="start_date-error" class="error" for="start_date">{{ $message }}</label>
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
                    value="{{ old('total_quantity', $flock->total_quantity ?? '') }}" required="" min="1" readonly />
                @error('total_quantity')
                    <label id="total_quantity-error" class="error" for="total_quantity">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <!-- Hangar Selection & Quantity Section -->
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

    <div class="card-footer">
        <input type="hidden" id="hangar_quantities_json" name="hangar_quantities_json" />
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('flock.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
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

            // Ensure farmId is an integer
            farmId = parseInt(farmId);

            $.ajax({
                url: "{{ route('flock.hangars-by-farm', ['username' => $siteSlug, 'farm' => ':farm']) }}".replace(':farm', farmId),
                type: 'GET',
                success: function(hangars) {
                    allHangars = hangars;
                    initializeHangarRows(hangars);
                }
            });
        }

        // Initialize with 1 hangar row (or from edit data)
        function initializeHangarRows(hangars) {
            var container = $('#hangars_allocation_container');
            container.html('');
            currentRowCount = 0;

            if (hangars.length === 0) {
                container.html('<p class="text-warning">No hangars found for this farm.</p>');
                $('#add_hangar_row').hide();
                return;
            }

            // Check if editing - load previous data
            var existingAllocations = {};
            @if(isset($flockHangars))
                @foreach($flockHangars as $fh)
                    existingAllocations[{{ $loop->index }}] = {
                        hangar_id: {{ $fh->hangar_id }},
                        quantity: {{ $fh->quantity }}
                    };
                @endforeach
            @endif

            // If editing, restore previous rows
            if (Object.keys(existingAllocations).length > 0) {
                Object.keys(existingAllocations).forEach(function(index) {
                    addHangarRow(hangars, existingAllocations[index].hangar_id, existingAllocations[index].quantity);
                });
            } else {
                // Add first empty row
                addHangarRow(hangars);
            }

            // Show/hide add button based on current count
            updateAddButton();
            updateHangarDropdowns();
        }

        // Add a hangar row
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

            // Show remove button only if not the first row or if there are multiple rows
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

        // Remove a hangar row
        function removeHangarRow(rowIndex) {
            $(`.hangar-row[data-row-index="${rowIndex}"]`).remove();
            currentRowCount--;
            updateAddButton();
            calculateTotalQuantity();
            updateHangarDropdowns();
        }

        // Update add button visibility
        function updateAddButton() {
            if (currentRowCount < maxRows) {
                $('#add_hangar_row').show();
            } else {
                $('#add_hangar_row').hide();
            }
        }

        // Attach event listeners
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

        // Update all hangar dropdowns to disable already selected hangars
        function updateHangarDropdowns() {
            var selectedHangars = [];

            // Collect all selected hangars
            $('.hangar-select').each(function() {
                var value = $(this).val();
                if (value) {
                    selectedHangars.push(parseInt(value));
                }
            });

            // Update all dropdowns
            $('.hangar-select').each(function() {
                var currentValue = $(this).val();
                var currentRow = $(this).data('row-index');

                $(this).find('option').each(function() {
                    var optionValue = $(this).val();
                    
                    // Disable option if it's selected in another row
                    if (optionValue && selectedHangars.includes(parseInt(optionValue)) && parseInt(optionValue) !== parseInt(currentValue)) {
                        $(this).attr('disabled', 'disabled');
                    } else if (optionValue === '') {
                        // Keep placeholder enabled
                        $(this).removeAttr('disabled');
                    } else {
                        $(this).removeAttr('disabled');
                    }
                });
            });
        }

        // Calculate total quantity from all hangar quantities
        function calculateTotalQuantity() {
            var total = 0;
            $('.hangar-quantity').each(function() {
                var qty = parseInt($(this).val()) || 0;
                total += qty;
            });
            $('#total_quantity').val(total);
        }

        // Add hangar row button click
        $(document).on('click', '#add_hangar_row', function(e) {
            e.preventDefault();
            addHangarRow(allHangars);
        });

        // When farm changes, reload hangars
        $('#farm_id').on('change', function() {
            var farmId = $(this).val();
            loadHangarsForFarm(farmId);
        });

        // On page load (edit mode), load hangars if farm is selected
        @if(isset($flock))
            var farmId = $('#farm_id').val();
            if (farmId) {
                loadHangarsForFarm(farmId);
            }
        @endif

        // Form validation on submit
        $('#flock_form').on('submit', function(e) {
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

                // Check for duplicate hangars
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

            // Check for unique combination (Farm + Chicks Supplier + Breed + Start Date)
            @if(!isset($flock))
                var farmId = $('#farm_id').val();
                var chicksId = $('#chicks_supplier_id').val();
                var breed = $('#breed').val();
                var startDate = $('#start_date').val();

                $.ajax({
                    url: "{{ route('flock.check-duplicate', ['username' => $siteSlug]) }}",
                    type: 'POST',
                    data: {
                        _token: $('input[name="_token"]').val(),
                        farm_id: farmId,
                        chicks_supplier_id: chicksId,
                        breed: breed,
                        start_date: startDate
                    },
                    async: false,
                    success: function(response) {
                        if (response.exists) {
                            e.preventDefault();
                            alert('A flock with the same Farm, Chicks Supplier, Breed, and Start Date already exists.');
                            hasError = true;
                        }
                    },
                    error: function() {
                        // Continue on error
                    }
                });

                if (hasError) {
                    return false;
                }
            @endif

            // Store hangar data for submission
            $('#hangar_quantities_json').val(JSON.stringify(selectedHangars));
        });
    });
</script>
