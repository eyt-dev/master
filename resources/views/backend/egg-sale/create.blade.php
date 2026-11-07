<form
    action="{{ isset($eggSale) && $eggSale->id ? route('egg-sale.update', ['username' => $siteSlug, 'egg_sale' => $eggSale->id]) : route('egg-sale.store', ['username' => $siteSlug]) }}"
    method="POST"
    id="egg_sale_form"
    novalidate=""
    class="needs-validation">

    @csrf

    <div class="row">
        <!-- Sale Date -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="sale_date" class="form-label">Date <span class="text-red">*</span></label>
                <input type="date" class="form-control" name="sale_date" id="sale_date"
                    value="{{ old('sale_date', isset($eggSale) ? $eggSale->sale_date->format('Y-m-d') : '') }}" required="" />
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
                        <option value="{{ $farm->id }}" {{ old('farm_id', $eggSale->farm_id ?? '') == $farm->id ? 'selected' : '' }}>
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
        <!-- Flock Dropdown (Cascading from Farm) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="flock_id" class="form-label">Flock <span class="text-red">*</span></label>
                <select class="form-control" name="flock_id" id="flock_id" required="">
                    <option value="">Select Flock</option>
                    @if(isset($flocks))
                        @foreach($flocks as $flock)
                            <option value="{{ $flock->id }}" {{ old('flock_id', $eggSale->flock_id ?? '') == $flock->id ? 'selected' : '' }}>
                                {{ ($eggSale->farm->name ?? 'N/A') }}-{{ $flock->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('flock_id')
                    <label id="flock_id-error" class="error" for="flock_id">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Wholesaler Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="egg_wholesaler_id" class="form-label">Wholesaler <span class="text-red">*</span></label>
                <select class="form-control" name="egg_wholesaler_id" id="egg_wholesaler_id" required="">
                    <option value="">Select Wholesaler</option>
                    @foreach($wholesalers as $wholesaler)
                        <option value="{{ $wholesaler->id }}" {{ old('egg_wholesaler_id', $eggSale->egg_wholesaler_id ?? '') == $wholesaler->id ? 'selected' : '' }}>
                            {{ $wholesaler->name }}
                        </option>
                    @endforeach
                </select>
                @error('egg_wholesaler_id')
                    <label id="egg_wholesaler_id-error" class="error" for="egg_wholesaler_id">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Total: eggs produced by the flock (daily records). Remaining: Total minus eggs already sold. -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="total_eggs_flock" class="form-label">Total (Flock Eggs)</label>
                <input type="number" class="form-control" id="total_eggs_flock" placeholder="Select a flock" value="" readonly />
                <small class="text-muted">Eggs produced by this flock (daily records)</small>
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="available_eggs" class="form-label">Remaining</label>
                <input type="number" class="form-control" id="available_eggs" placeholder="Select a flock" value="" readonly />
                <small class="text-muted">Total minus eggs already sold</small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Tray (input) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="tray" class="form-label">Tray <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="tray" id="tray" placeholder="Number of trays"
                    value="{{ old('tray', $eggSale->tray ?? '') }}" required="" min="1" step="1" />
                <small class="text-muted">1 tray = 30 eggs</small>
                <small id="tray_availability_error" class="d-block font-weight-bold" style="display: none;"></small>
                @error('tray')
                    <label id="tray-error" class="error" for="tray">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Total Eggs (Auto Calculated) -->
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label for="total_eggs" class="form-label">Total Eggs (Auto)</label>
                <input type="number" class="form-control" name="total_eggs_display" id="total_eggs" placeholder="Auto Calculated"
                    value="{{ old('total_eggs', isset($eggSale) ? $eggSale->total_eggs : '') }}" readonly />
                <small class="text-muted">Tray × 30</small>
            </div>
        </div>

        <!-- Box (Auto Calculated) -->
        <div class="col-sm-6 col-md-3">
            <div class="form-group">
                <label for="box" class="form-label">Box (Auto)</label>
                <input type="text" class="form-control" name="box_display" id="box" placeholder="Auto Calculated"
                    value="{{ isset($eggSale) ? number_format($eggSale->box, 2, '.', '') : '' }}" readonly />
                <small class="text-muted">Total Eggs ÷ 360</small>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Notes -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="notes" class="form-label">Notes</label>
                <textarea class="form-control" name="notes" id="notes" placeholder="Additional notes (optional)">{{ old('notes', $eggSale->notes ?? '') }}</textarea>
                @error('notes')
                    <label id="notes-error" class="error" for="notes">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('egg-sale.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Load flocks when farm is selected
        $('#farm_id').on('change', function() {
            var farmId = $(this).val();
            $('#flock_id').html('<option value="">Select Flock</option>');
            setAvailable(null);

            if (farmId) {
                $.ajax({
                    url: "{{ route('egg-sale.flocks-by-farm', ['username' => $siteSlug, 'farm' => ':farm']) }}".replace(':farm', farmId),
                    type: 'GET',
                    success: function(flocks) {
                        flocks.forEach(function(flock) {
                            $('#flock_id').append('<option value="' + flock.id + '">' + flock.display_name + '</option>');
                        });
                    }
                });
            }
        });

        // Tray → Total Eggs (tray × 30) → Box (total eggs ÷ 360)
        function calculateEggs() {
            var tray = parseInt($('#tray').val(), 10) || 0;

            if (tray > 0) {
                var totalEggs = tray * 30;
                $('#total_eggs').val(totalEggs);
                $('#box').val((totalEggs / 360).toFixed(2));
            } else {
                $('#total_eggs').val('');
                $('#box').val('');
            }
        }

        // Available eggs for the selected flock (edit excludes this sale's own eggs)
        var excludeSaleId = {{ isset($eggSale) ? (int) $eggSale->id : 'null' }};
        var availableEggs = null;

        function setAvailable(value, total) {
            availableEggs = value;
            $('#available_eggs').val(value === null ? '' : value);
            $('#total_eggs_flock').val(total === undefined || total === null ? '' : total);
            validateTrayAvailability();
        }

        // More trays than the flock has remaining: error, and the save is refused.
        function validateTrayAvailability() {
            var tray = parseInt($('#tray').val(), 10) || 0;
            var needed = tray * 30;
            var msg = '';

            if (availableEggs !== null && needed > availableEggs) {
                if (availableEggs === 0) {
                    msg = 'No eggs are available for this flock.';
                } else {
                    var maxTray = Math.floor(availableEggs / 30);
                    msg = 'Only ' + availableEggs + ' eggs are available for this flock. Maximum is ' + maxTray + ' tray(s) (' + (maxTray * 30) + ' eggs).';
                }
            }

            $('#tray_availability_error')
                .text(msg)
                .toggle(msg !== '')
                .toggleClass('text-danger', msg !== '');
            return msg === '';
        }

        function loadAvailableEggs(flockId) {
            if (!flockId) {
                setAvailable(null);
                return;
            }

            $.ajax({
                url: "{{ route('egg-sale.available-eggs', ['username' => $siteSlug, 'flock' => ':flock']) }}".replace(':flock', flockId),
                type: 'GET',
                data: { exclude: excludeSaleId },
                success: function(response) {
                    setAvailable(response.available_eggs, response.total_eggs);
                },
                error: function() {
                    setAvailable(null);
                }
            });
        }

        $('#flock_id').on('change', function() {
            loadAvailableEggs($(this).val());
        });

        $('#tray').on('change keyup input', function() {
            calculateEggs();
            validateTrayAvailability();
        });

        // Block submit when the tray count is more than the flock has available
        $('#egg_sale_form').on('submit', function(event) {
            if (!validateTrayAvailability()) {
                event.preventDefault();
                event.stopPropagation();
                $('#tray').focus();
            }
        });

        // Ensure values are shown on load (add or edit)
        calculateEggs();
        loadAvailableEggs($('#flock_id').val());
    });
</script>
