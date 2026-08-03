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
        <!-- Hangar Dropdown (Cascading from Flock) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="hangar_id" class="form-label">Hangar <span class="text-red">*</span></label>
                <select class="form-control" name="hangar_id" id="hangar_id" required="">
                    <option value="">Select Hangar</option>
                    @if(isset($hangars))
                        @foreach($hangars as $hangar)
                            <option value="{{ $hangar->id }}" {{ old('hangar_id', $dailyRecord->hangar_id ?? '') == $hangar->id ? 'selected' : '' }}>
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

        <!-- Feed (Kg) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="feed_kg" class="form-label">Feed (Kg) <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="feed_kg" id="feed_kg" placeholder="Feed in Kg" 
                    value="{{ old('feed_kg', $dailyRecord->feed_kg ?? '') }}" required="" step="0.01" min="0" />
                @error('feed_kg')
                    <label id="feed_kg-error" class="error" for="feed_kg">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Eggs (Tray 30) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="eggs_tray_30" class="form-label">Eggs (Tray 30) <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="eggs_tray_30" id="eggs_tray_30" placeholder="Eggs in Trays of 30" 
                    value="{{ old('eggs_tray_30', $dailyRecord->eggs_tray_30 ?? '') }}" required="" min="0" />
                @error('eggs_tray_30')
                    <label id="eggs_tray_30-error" class="error" for="eggs_tray_30">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Eggs (Eggs Count) -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="eggs_count" class="form-label">Eggs (Eggs) <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="eggs_count" id="eggs_count" placeholder="Eggs Count" 
                    value="{{ old('eggs_count', $dailyRecord->eggs_count ?? '') }}" required="" min="0" />
                @error('eggs_count')
                    <label id="eggs_count-error" class="error" for="eggs_count">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Mortality -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="mortality" class="form-label">Mortality <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="mortality" id="mortality" placeholder="Mortality Count" 
                    value="{{ old('mortality', $dailyRecord->mortality ?? '') }}" required="" min="0" />
                @error('mortality')
                    <label id="mortality-error" class="error" for="mortality">{{ $message }}</label>
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
        // Load hangars when flock is selected
        $('#flock_id').on('change', function() {
            var flockId = $(this).val();
            $('#hangar_id').html('<option value="">Select Hangar</option>');
            
            if (flockId) {
                $.ajax({
                    url: "{{ route('daily-record.hangars-by-flock', ['username' => $siteSlug, 'flock' => ':flock']) }}".replace(':flock', flockId),
                    type: 'GET',
                    success: function(hangars) {
                        hangars.forEach(function(hangar) {
                            $('#hangar_id').append('<option value="' + hangar.id + '">' + hangar.name + '</option>');
                        });
                    }
                });
            }
        });
    });
</script>
