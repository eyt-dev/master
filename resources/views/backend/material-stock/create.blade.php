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
                <label for="quantity" class="form-label">Total Quantity <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="quantity" id="quantity" placeholder="Total Quantity" 
                    value="{{ old('quantity', $materialStock->quantity ?? '') }}" required="" min="1" />
                @error('quantity')
                    <label id="quantity-error" class="error" for="quantity">{{ $message }}</label>
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
                
                <div id="hangars_allocation_container" class="bg-light rounded-lg p-0" style="border: 1px solid #dee2e6; background-color: #f8f9fa !important; display: none;">
                    <!-- Hangar rows will be generated here -->
                </div>

                <p id="no_hangars_message" class="text-warning">Please select a farm first.</p>

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
        
        // Load all hangars for the selected farm
        function loadHangarsForFarm(farmId) {
            var container = $('#hangars_allocation_container');
            var noHangarsMsg = $('#no_hangars_message');

            if (!farmId) {
                container.hide();
                noHangarsMsg.show().text('Please select a farm first.');
                $('#quantity').val('');
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

        // Initialize hangar rows as a list
        function initializeHangarRows(hangars) {
            var container = $('#hangars_allocation_container');
            var noHangarsMsg = $('#no_hangars_message');

            if (hangars.length === 0) {
                container.hide();
                noHangarsMsg.show().text('No hangars found for this farm.');
                return;
            }

            container.html('');
            noHangarsMsg.hide();

        // Check if editing - load previous data
            var existingAllocations = {};
            var existingRemaining = {};
            @if(isset($materialStockHangars))
                @foreach($materialStockHangars as $msh)
                    existingAllocations[{{ $msh->hangar_id }}] = {{ $msh->quantity }};
                    existingRemaining[{{ $msh->hangar_id }}] = {{ $msh->remaining_quantity }};
                @endforeach
            @endif

            // Build the hangar list with Qty and Remaining Qty columns
            hangars.forEach(function(hangar) {
                var quantity = existingAllocations[hangar.id] || '';
                var remainingQty = existingRemaining[hangar.id] || quantity || '';
                var html = `
                    <div class="d-flex align-items-center justify-content-between p-3" style="border-bottom: 1px solid #dee2e6;">
                        <div class="d-flex align-items-center flex-grow-1" style="min-width: 200px;">
                            <div class="mr-3">
                                <i class="fe fe-home" style="font-size: 18px; color: #007bff;"></i>
                            </div>
                            <div>
                                <p class="mb-0 font-weight-600" style="color: #212529;">${hangar.name}</p>
                            </div>
                        </div>
                        <div class="ml-3" style="min-width: 150px;">
                            <input type="number" class="form-control hangar-quantity-input" name="hangar_qty[${hangar.id}]" 
                                placeholder="Qty" value="${quantity}" min="0" data-hangar-id="${hangar.id}" />
                        </div>
                        <div class="ml-3" style="min-width: 150px;">
                            <input type="number" class="form-control hangar-remaining-qty-input" name="hangar_remaining_qty[${hangar.id}]" 
                                placeholder="Remaining" value="${remainingQty}" min="0" data-hangar-id="${hangar.id}" />
                        </div>
                    </div>
                `;
                container.append(html);
            });

            container.show();
            attachEventListeners();
        }

        // Attach event listeners for quantity inputs
        function attachEventListeners() {
            $(document).off('change keyup', '.hangar-quantity-input').on('change keyup', '.hangar-quantity-input', function() {
                // Do not calculate total - use manual entry
            });
        }

        // When farm changes, reload hangars
        $('#farm_id').on('change', function() {
            var farmId = $(this).val();
            loadHangarsForFarm(farmId);
        });

        // On page load (edit mode), load hangars if farm is selected
        @if(isset($materialStock))
            var farmId = $('#farm_id').val();
            if (farmId) {
                loadHangarsForFarm(farmId);
            }
        @endif

        // Form validation on submit
        $('#material_stock_form').on('submit', function(e) {
            var selectedHangars = [];
            var hasError = false;

            $('.hangar-quantity-input').each(function() {
                var hangarId = $(this).data('hangar-id');
                var quantity = parseInt($(this).val()) || 0;
                var remainingQty = parseInt($('input[name="hangar_remaining_qty[' + hangarId + ']"]').val()) || 0;

                if (quantity > 0) {
                    selectedHangars.push({ 
                        hangar_id: hangarId, 
                        quantity: quantity,
                        remaining_quantity: remainingQty 
                    });
                }
            });

            if (selectedHangars.length === 0) {
                e.preventDefault();
                swal({
                    title: 'Validation Error',
                    text: 'Please select at least one hangar with quantity.',
                    icon: 'warning',
                    button: 'OK'
                });
                return false;
            }

            // Validate quantity is entered
            var qty = parseInt($('#quantity').val());
            if (!qty || qty < 1) {
                e.preventDefault();
                swal({
                    title: 'Validation Error',
                    text: 'Please enter a valid total quantity.',
                    icon: 'warning',
                    button: 'OK'
                });
                return false;
            }

            // Store hangar data for submission - this will be sent as JSON
            $('#hangar_quantities_json').val(JSON.stringify(selectedHangars));
        });
    });
</script>
