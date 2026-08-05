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
        <!-- Flock Name (Auto-generated) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="flock_name" class="form-label">Flock Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" id="flock_name" placeholder="Auto-generated" readonly />
                @error('name')
                    <label id="flock_name-error" class="error" for="flock_name">{{ $message }}</label>
                @enderror
            </div>
        </div>

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
                    value="{{ old('total_quantity', $flock->total_quantity ?? '') }}" required="" min="1" />
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

    <div class="card-footer">
        <input type="hidden" id="hangar_quantities_json" name="hangar_quantities_json" />
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('flock.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
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
                $('#total_quantity').val('');
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
            @if(isset($flockHangars))
                @foreach($flockHangars as $fh)
                    existingAllocations[{{ $fh->hangar_id }}] = {{ $fh->quantity }};
                @endforeach
            @endif

            // Build the hangar list
            hangars.forEach(function(hangar) {
                var quantity = existingAllocations[hangar.id] || '';
                var html = `
                    <div class="d-flex align-items-center justify-content-between p-3" style="border-bottom: 1px solid #dee2e6;">
                        <div class="d-flex align-items-center flex-grow-1">
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

        // Calculate total quantity from all hangar quantities (helper function, not used for final submission)
        function calculateTotalQuantity() {
            var total = 0;
            $('.hangar-quantity-input').each(function() {
                var qty = parseInt($(this).val()) || 0;
                total += qty;
            });
            // Show suggestion to user but don't override their input
            // User can choose to use this or set their own value
        }

        // Store original farm and flock name for edit mode
        var originalFarmId = null;
        var originalFlockName = null;
        
        @if(isset($flock))
            originalFarmId = {{ $flock->farm_id }};
            originalFlockName = '{{ $flock->name }}';
        @endif

        // When farm changes, reload hangars and update flock name
        $('#farm_id').on('change', function() {
            var farmId = $(this).val();
            loadHangarsForFarm(farmId);
            updateFlockName(farmId);
        });

        // Function to update flock name based on selected farm
        function updateFlockName(farmId) {
            if (!farmId) {
                $('#flock_name').val('');
                return;
            }

            // In edit mode, check if we're changing back to original farm
            @if(isset($flock))
                if (farmId == originalFarmId) {
                    // Changed back to original farm - restore original name
                    $('#flock_name').val(originalFlockName);
                    return;
                }
            @endif

            // Get the farm name from the selected option
            var farmName = $('#farm_id option:selected').text();
            var farmId = parseInt(farmId);

            // Make AJAX call to get next sequence number for this farm
            $.ajax({
                url: "{{ route('flock.get-sequence', ['username' => $siteSlug]) }}?farm_id=" + farmId,
                type: 'GET',
                success: function(response) {
                    // Remove spaces and special characters from farm name
                    var sanitizedName = farmName.replace(/\s+/g, '').replace(/[^a-zA-Z0-9]/g, '');
                    if (!sanitizedName) {
                        sanitizedName = 'Farm';
                    }
                    var flockName = sanitizedName + '-Flock' + response.sequence;
                    $('#flock_name').val(flockName);
                }
            });
        }

        // On page load (edit mode), load hangars and show existing name
        @if(isset($flock))
            var farmId = $('#farm_id').val();
            if (farmId) {
                loadHangarsForFarm(farmId);
                $('#flock_name').val('{{ $flock->name }}');
            }
        @endif

        // Form validation on submit
        $('#flock_form').on('submit', function(e) {
            var selectedHangars = [];
            var hasError = false;

            $('.hangar-quantity-input').each(function() {
                var hangarId = $(this).data('hangar-id');
                var quantity = parseInt($(this).val()) || 0;

                if (quantity > 0) {
                    selectedHangars.push({ hangar_id: hangarId, quantity: quantity });
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

            // Validate total_quantity is entered
            var totalQty = parseInt($('#total_quantity').val());
            if (!totalQty || totalQty < 1) {
                e.preventDefault();
                swal({
                    title: 'Validation Error',
                    text: 'Please enter a valid total quantity.',
                    icon: 'warning',
                    button: 'OK'
                });
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
                            swal({
                                title: 'Duplicate Flock',
                                text: 'A flock with the same Farm, Chicks Supplier, Breed, and Start Date already exists.',
                                icon: 'warning',
                                button: 'OK'
                            });
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

            // Store hangar data for submission - this will be sent as JSON
            $('#hangar_quantities_json').val(JSON.stringify(selectedHangars));
        });
    });
</script>
