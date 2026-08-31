<form 
    action="{{ isset($chickenSale) && $chickenSale->id ? route('chicken-sale.update', ['username' => $siteSlug, 'chicken_sale' => $chickenSale->id]) : route('chicken-sale.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="chicken_sale_form"
    novalidate=""
    class="needs-validation">

    @csrf
    
    <div class="row">
        <!-- Sale Date -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="sale_date" class="form-label">Date <span class="text-red">*</span></label>
                <input type="date" class="form-control" name="sale_date" id="sale_date" 
                    value="{{ old('sale_date', isset($chickenSale) ? $chickenSale->sale_date->format('Y-m-d') : '') }}" required="" />
                @error('sale_date')
                    <label id="sale_date-error" class="error" for="sale_date">{{ $message }}</label>
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
                        <option value="{{ $farm->id }}" {{ old('farm_id', $chickenSale->farm_id ?? '') == $farm->id ? 'selected' : '' }}>
                            {{ $farm->name }}
                        </option>
                    @endforeach
                </select>
                @error('farm_id')
                    <label id="farm_id-error" class="error" for="farm_id">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Flock Dropdown (Cascading) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="flock_id" class="form-label">Flock <span class="text-red">*</span></label>
                <select class="form-control" name="flock_id" id="flock_id" required="">
                    <option value="">Select Flock</option>
                    @if(isset($flocks))
                        @foreach($flocks as $flock)
                            <option value="{{ $flock->id }}" {{ old('flock_id', $chickenSale->flock_id ?? '') == $flock->id ? 'selected' : '' }}>
                                {{ $flock->name }} - {{ $flock->breed }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('flock_id')
                    <label id="flock_id-error" class="error" for="flock_id">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Hangar Dropdown (Cascading) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="hangar_id" class="form-label">Hangar <span class="text-red">*</span></label>
                <select class="form-control" name="hangar_id" id="hangar_id" required="">
                    <option value="">Select Hangar</option>
                    @if(isset($hangars))
                        @foreach($hangars as $hangar)
                            <option value="{{ $hangar->id }}" {{ old('hangar_id', $chickenSale->hangar_id ?? '') == $hangar->id ? 'selected' : '' }}>
                                {{ $hangar->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('hangar_id')
                    <label id="hangar_id-error" class="error" for="hangar_id">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Slaughter Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="slaughter_id" class="form-label">Sold To (Slaughter) <span class="text-red">*</span></label>
                <select class="form-control" name="slaughter_id" id="slaughter_id" required="">
                    <option value="">Select Slaughter</option>
                    @foreach($slaughters as $slaughter)
                        <option value="{{ $slaughter->id }}" {{ old('slaughter_id', $chickenSale->slaughter_id ?? '') == $slaughter->id ? 'selected' : '' }}>
                            {{ $slaughter->name }}
                        </option>
                    @endforeach
                </select>
                @error('slaughter_id')
                    <label id="slaughter_id-error" class="error" for="slaughter_id">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Quantity -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="quantity" class="form-label">Quantity <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="quantity" id="quantity" placeholder="Quantity" 
                    value="{{ old('quantity', $chickenSale->quantity ?? '') }}" required="" min="1" />
                @error('quantity')
                    <label id="quantity-error" class="error" for="quantity">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Batch Weight -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="batch_weight" class="form-label">Batch Weight <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="batch_weight" id="batch_weight" placeholder="Batch Weight"
                    value="{{ old('batch_weight', $chickenSale->total_weight ?? '') }}" required="" step="0.01" min="0" />
                @error('batch_weight')
                    <label id="batch_weight-error" class="error" for="batch_weight">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Caged Weight -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="cages_weight" class="form-label">Cages Weight (kg) <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="cages_weight" id="cages_weight" placeholder="Caged Weight"
                    value="{{ old('cages_weight', $chickenSale->gross_weight ?? '') }}" required="" step="0.01" min="0" />
                @error('cages_weight')
                    <label id="cages_weight-error" class="error" for="cages_weight">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Cages Count -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="cages_count" class="form-label">Cages Count <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="cages_count" id="cages_count" placeholder="Cages Count"
                    value="{{ old('cages_count', $chickenSale->no_of_cages ?? '') }}" required="" min="1" />
                @error('cages_count')
                    <label id="cages_count-error" class="error" for="cages_count">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Birds per Cage -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="birds_per_cage" class="form-label">Birds per Cage <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="birds_per_cage" id="birds_per_cage" placeholder="Birds per Cage"
                    value="{{ old('birds_per_cage', isset($chickenSale) ? round($chickenSale->no_of_birds / $chickenSale->no_of_cages) : '') }}" required="" min="1" />
                @error('birds_per_cage')
                    <label id="birds_per_cage-error" class="error" for="birds_per_cage">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Total Quantity (Auto Calculated) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="quantity_display" class="form-label">Total Quantity (Auto)</label>
                <input type="number" class="form-control" name="quantity_display" id="quantity_display" placeholder="Auto Calculated"
                    value="" readonly />
                <small class="text-muted">Cages × Birds per Cage</small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Net Weight -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="net_weight" class="form-label">Net Weight <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="net_weight" id="net_weight" placeholder="Net Weight" 
                    value="{{ old('net_weight', $chickenSale->net_weight ?? '') }}" required="" step="0.01" min="0" />
                @error('net_weight')
                    <label id="net_weight-error" class="error" for="net_weight">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Average Weight per Bird (Auto Calculated) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="avg_weight_per_bird" class="form-label">Average Weight per Bird (Auto)</label>
                <input type="number" class="form-control" name="avg_weight_per_bird" id="avg_weight_per_bird" placeholder="Auto Calculated" 
                    value="{{ old('avg_weight_per_bird', isset($chickenSale) ? round($chickenSale->avg_weight_per_bird, 2) : '') }}" readonly step="0.01" />
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Notes -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" name="notes" id="notes" placeholder="Additional notes (optional)">{{ old('notes', $chickenSale->notes ?? '') }}</textarea>
                @error('notes')
                    <label id="notes-error" class="error" for="notes">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('chicken-sale.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Load flocks when farm is selected
        $('#farm_id').on('change', function() {
            var farmId = $(this).val();
            $('#flock_id').html('<option value="">Select Flock</option>');
            $('#hangar_id').html('<option value="">Select Hangar</option>');
            
            if (farmId) {
                $.ajax({
                    url: "{{ route('chicken-sale.flocks-by-farm', ['username' => $siteSlug, 'farm' => ':farm']) }}".replace(':farm', farmId),
                    type: 'GET',
                    success: function(flocks) {
                        flocks.forEach(function(flock) {
                            $('#flock_id').append('<option value="' + flock.id + '">' + flock.name + ' - ' + flock.breed + '</option>');
                        });
                    }
                });
            }
        });

        // Load hangars when flock is selected
        $('#flock_id').on('change', function() {
            var flockId = $(this).val();
            $('#hangar_id').html('<option value="">Select Hangar</option>');
            
            if (flockId) {
                $.ajax({
                    url: "{{ route('chicken-sale.hangars-by-flock', ['username' => $siteSlug, 'flock' => ':flock']) }}".replace(':flock', flockId),
                    type: 'GET',
                    success: function(hangars) {
                        hangars.forEach(function(hangar) {
                            $('#hangar_id').append('<option value="' + hangar.id + '">' + hangar.name + '</option>');
                        });
                    }
                });
            }
        });

        // Calculate total quantity (Cages × Birds per Cage)
        function calculateQuantity() {
            var cagesCount = parseFloat($('#cages_count').val()) || 0;
            var birdsPerCage = parseFloat($('#birds_per_cage').val()) || 0;

            if (cagesCount > 0 && birdsPerCage > 0) {
                var totalQty = Math.round(cagesCount * birdsPerCage);
                $('#quantity_display').val(totalQty);
                calculateAvgWeight(totalQty);
            } else {
                $('#quantity_display').val('');
                $('#avg_weight_per_bird').val('');
            }
        }

        // Calculate average weight per bird
        function calculateAvgWeight(quantity) {
            var netWeight = parseFloat($('#net_weight').val()) || 0;
            var totalQty = quantity || parseFloat($('#quantity_display').val()) || 0;

            if (netWeight > 0 && totalQty > 0) {
                var avgWeight = (netWeight / totalQty).toFixed(2);
                $('#avg_weight_per_bird').val(avgWeight);
            } else {
                $('#avg_weight_per_bird').val('');
            }
        }

        // Trigger calculations when inputs change
        $('#cages_count, #birds_per_cage').on('change keyup', function() {
            calculateQuantity();
        });

        $('#net_weight').on('change keyup', function() {
            calculateAvgWeight();
        });

        // Trigger calculations on page load if editing
        @if(isset($chickenSale))
            calculateQuantity();
        @endif
    });
</script>
