<form 
    action="{{ isset($dailyRecord) && $dailyRecord->id ? route('daily-record.update', ['username' => $siteSlug, 'daily_record' => $dailyRecord->id]) : route('daily-record.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="daily_record_form"
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
        <!-- Record Date -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="record_date" class="form-label">Record Date <span class="text-red">*</span></label>
                <input type="date" class="form-control" name="record_date" id="record_date" 
                    value="{{ old('record_date', isset($dailyRecord) ? $dailyRecord->record_date->format('Y-m-d') : '') }}" required="" />
                @error('record_date')
                    <label id="record_date-error" class="error" for="record_date">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Flock Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="flock_id" class="form-label">Flock <span class="text-red">*</span></label>
                <select class="form-control" name="flock_id" id="flock_id" required="">
                    <option value="">Select Flock</option>
                    @if(isset($flocks))
                        @foreach($flocks as $flock)
                            <option value="{{ $flock['id'] }}" {{ old('flock_id', $dailyRecord->flock_id ?? '') == $flock['id'] ? 'selected' : '' }}>
                                {{ $flock['label'] }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('flock_id')
                    <label id="flock_id-error" class="error" for="flock_id">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Hangar List with Input Fields -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label class="form-label">
                    Hangar Details 
                    <span class="text-red">*</span>
                    <span id="breed_label" style="display: none; margin-left: 10px;">
                        <span class="badge badge-info" id="breed_badge">Layer (Lohmann Brown)</span>
                    </span>
                </label>
                <small class="form-text text-muted d-block mb-3">Enter details for each hangar allocated to this flock.</small>
                
                <div id="hangars_container" class="bg-light rounded-lg p-0" style="border: 1px solid #dee2e6; background-color: #f8f9fa !important; display: none;">
                    <!-- Hangar rows will be generated here -->
                </div>

                <p id="no_hangars_message" class="text-warning">Please select a flock first to see allocated hangars.</p>

                @error('hangar_records')
                    <label id="hangar_records-error" class="error" for="hangar_records">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>

<script>
    $(document).ready(function() {
        // Existing records data for edit mode
        var existingRecordsData = @json(isset($existingRecords) ? $existingRecords : []);
        
        // Load flock details and hangars when flock is selected
        $('#flock_id').on('change', function() {
            var flockId = $(this).val();
            var container = $('#hangars_container');
            var noHangarsMsg = $('#no_hangars_message');
            var breedLabel = $('#breed_label');
            var breedBadge = $('#breed_badge');

            container.html('');
            container.hide();
            noHangarsMsg.hide();
            breedLabel.hide();

            window.breedType = 'Layer'; // Default

            if (flockId) {
                // Fetch flock breed details FIRST
                $.ajax({
                    url: "{{ route('daily-record.flock-details', ['username' => $siteSlug, 'flock' => ':flock']) }}".replace(':flock', flockId),
                    type: 'GET',
                    success: function(flockDetails) {
                        window.breedType = flockDetails.breed_type || 'Layer';
                        console.log('Flock Details - Breed Type:', window.breedType);

                        // Update breed label
                        var breedText = flockDetails.breed_type + ' (' + flockDetails.breed_name + ')';
                        breedBadge.text(breedText);

                        // Update badge color based on breed type
                        if (window.breedType === 'Broiler') {
                            breedBadge.removeClass('badge-info').addClass('badge-danger');
                        } else {
                            breedBadge.removeClass('badge-danger').addClass('badge-info');
                        }
                        breedLabel.show();

                        // Now load hangars AFTER breed is set
                        loadHangars();
                    }
                });
            } else {
                noHangarsMsg.show().text('Please select a flock first to see allocated hangars.');
            }
        });

        // Function to load hangars
        function loadHangars() {
            var flockId = $('#flock_id').val();
            var container = $('#hangars_container');
            var noHangarsMsg = $('#no_hangars_message');

            $.ajax({
                    url: "{{ route('daily-record.hangars-by-flock', ['username' => $siteSlug, 'flock' => ':flock']) }}".replace(':flock', flockId),
                    type: 'GET',
                    success: function(response) {
                        console.log('Hangars Response:', response);

                        // Handle both response formats: array or object with hangars key
                        var hangars = Array.isArray(response) ? response : (response.hangars || response);
                        var breedTypeFromResponse = (response && response.breed_type) ? response.breed_type : (window.breedType || 'Layer');

                        console.log('Breed Type:', breedTypeFromResponse);

                        if (!hangars || hangars.length === 0) {
                            noHangarsMsg.show().text('No hangars allocated to this flock.');
                            container.hide();
                            return;
                        }

                        hangars.forEach(function(hangar, index) {
                            // Get existing data if in edit mode
                            var existingData = existingRecordsData[hangar.id] || {};
                            var breedType = breedTypeFromResponse;

                            // Format decimal values with comma as separator
                            var feedValue = existingData.feed_kg ? parseFloat(existingData.feed_kg).toLocaleString('de-DE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
                            var eggsWeightValue = existingData.eggs_weight ? parseFloat(existingData.eggs_weight).toLocaleString('de-DE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
                            var chicksWeightValue = existingData.chicks_weight ? parseFloat(existingData.chicks_weight).toLocaleString('de-DE', {minimumFractionDigits: 2, maximumFractionDigits: 2}) : '';
                            var eggsTraySValue = existingData.eggs_tray_30 || '';
                            var eggsCountValue = existingData.eggs_count || '';
                            var mortalityValue = existingData.mortality || '';

                            // Build HTML based on breed type
                            var html = `
                                <div class="hangar-record-row p-3" style="border-bottom: 1px solid #dee2e6;" data-hangar-id="${hangar.id}">
                                    <div class="row">
                                        <div class="col-md-12 mb-3">
                                            <h6 class="mb-0" style="color: #007bff;">
                                                <i class="fe fe-home mr-2"></i>${hangar.name} (Allocated: ${hangar.quantity})
                                            </h6>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group mb-0">
                                                <label class="form-label mb-1" style="font-size: 0.85rem;">Feed (Kg) <span class="text-red">*</span></label>
                                                <input type="text" class="form-control feed-input" name="hangar_feed[${hangar.id}]"
                                                    value="${feedValue}" placeholder="0,00" data-hangar-id="${hangar.id}" required />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="form-group mb-0">
                                                <label class="form-label mb-1" style="font-size: 0.85rem;">Mortality <span class="text-red">*</span></label>
                                                <input type="number" class="form-control mortality-input" name="hangar_mortality[${hangar.id}]"
                                                    value="${mortalityValue}" placeholder="0" min="0" data-hangar-id="${hangar.id}" required />
                                            </div>
                                        </div>
                                        ${breedType === 'Broiler' ? `
                                            <div class="col-md-3">
                                                <div class="form-group mb-0">
                                                    <label class="form-label mb-1" style="font-size: 0.85rem;">Chicks Weight (Kg) <small class="text-muted">(Optional)</small></label>
                                                    <input type="text" class="form-control chicks-weight-input" name="hangar_chicks_weight[${hangar.id}]"
                                                        value="${chicksWeightValue}" placeholder="0,00" data-hangar-id="${hangar.id}" />
                                                </div>
                                            </div>
                                        ` : `
                                            <div class="col-md-3">
                                                <div class="form-group mb-0">
                                                    <label class="form-label mb-1" style="font-size: 0.85rem;">Eggs (Tray 30)</label>
                                                    <input type="number" class="form-control eggs-tray-input" name="hangar_eggs_tray[${hangar.id}]"
                                                        value="${eggsTraySValue}" placeholder="0" min="0" data-hangar-id="${hangar.id}" />
                                                </div>
                                            </div>
                                            <div class="col-md-3">
                                                <div class="form-group mb-0">
                                                    <label class="form-label mb-1" style="font-size: 0.85rem;">Eggs (Count)</label>
                                                    <input type="number" class="form-control eggs-count-input" name="hangar_eggs_count[${hangar.id}]"
                                                        value="${eggsCountValue}" placeholder="0" min="0" data-hangar-id="${hangar.id}" />
                                                </div>
                                            </div>
                                        `}
                                    </div>
                                    ${breedType === 'Layer' ? `
                                        <div class="row mt-3">
                                            <div class="col-md-3">
                                                <div class="form-group mb-0">
                                                    <label class="form-label mb-1" style="font-size: 0.85rem;">Eggs Weight (Kg) <span class="text-red">*</span></label>
                                                    <input type="text" class="form-control eggs-weight-input" name="hangar_eggs_weight[${hangar.id}]"
                                                        value="${eggsWeightValue}" placeholder="0,00" data-hangar-id="${hangar.id}" required />
                                                </div>
                                            </div>
                                        </div>
                                    ` : ``}
                                </div>
                            `;
                            container.append(html);
                        });
                        
                        container.show();
                        noHangarsMsg.hide();
                    },
                    error: function() {
                        noHangarsMsg.show().text('Error loading hangars.');
                        container.hide();
                    }
                });
        }

        // Form submission
        $('#daily_record_form').on('submit', function(e) {
            var container = $('#hangars_container');
            var hangarRecords = [];
            
            container.find('.hangar-record-row').each(function() {
                var hangarId = $(this).data('hangar-id');
                // Convert comma to dot for decimal values
                var feedValue = $(this).find('.feed-input').val().replace(',', '.');
                var feedKg = parseFloat(feedValue) || 0;
                var eggsTray = parseInt($(this).find('.eggs-tray-input').val()) || 0;
                var eggsCount = parseInt($(this).find('.eggs-count-input').val()) || 0;
                
                var eggsWeightValue = $(this).find('.eggs-weight-input').val().replace(',', '.');
                var eggsWeight = parseFloat(eggsWeightValue) || 0;
                
                var chicksWeightValue = $(this).find('.chicks-weight-input').val().replace(',', '.');
                var chicksWeight = parseFloat(chicksWeightValue) || 0;
                
                var mortality = parseInt($(this).find('.mortality-input').val()) || 0;
                
                hangarRecords.push({
                    hangar_id: hangarId,
                    feed_kg: feedKg,
                    eggs_tray_30: eggsTray,
                    eggs_count: eggsCount,
                    eggs_weight: eggsWeight,
                    chicks_weight: chicksWeight,
                    mortality: mortality
                });
            });
            
            if (hangarRecords.length === 0) {
                e.preventDefault();
                swal({
                    title: 'Validation Error',
                    text: 'Please select a flock and enter hangar details.',
                    icon: 'warning',
                    button: 'OK'
                });
                return false;
            }
            
            // Store hangar records as JSON
            $('<input>').attr({
                type: 'hidden',
                name: 'hangar_records',
                value: JSON.stringify(hangarRecords)
            }).appendTo('#daily_record_form');
        });

        // Trigger flock loading on page load if in edit mode
        @if(isset($dailyRecord) && isset($flockHangars))
            var flockId = $('#flock_id').val();
            if (flockId) {
                $('#flock_id').trigger('change');
            }
        @endif
    });
</script>
