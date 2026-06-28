<form 
    action="{{ isset($farm) && $farm->id ? route('farm.update', ['username' => $siteSlug, 'farm' => $farm->id]) : route('farm.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="farm_form"
    novalidate=""
    class="needs-validation">

    @csrf
    
    <div class="row">
        <!-- Farm Name Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">Farm Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Farm Name" 
                    value="{{ old('name', $farm->name ?? '') }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Number of Hangars Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="number_of_hangars" class="form-label">Number of Hangars <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="number_of_hangars" id="number_of_hangars" placeholder="Number of Hangars" 
                    value="{{ old('number_of_hangars', $farm->number_of_hangars ?? '') }}" required="" min="1" />
                @error('number_of_hangars')
                    <label id="number_of_hangars-error" class="error" for="number_of_hangars">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Location Textarea -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="location" class="form-label">Location <span class="text-red">*</span></label>
                <textarea class="form-control" name="location" id="location" placeholder="Farm Location" required="">{{ old('location', $farm->location ?? '') }}</textarea>
                @error('location')
                    <label id="location-error" class="error" for="location">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Assigned To Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="assigned_to" class="form-label">Assigned To <span class="text-red">*</span></label>
                <select class="form-control" name="assigned_to" id="assigned_to" required="">
                    <option value="">Select Admin</option>
                    @foreach($admins as $admin)
                        <option value="{{ $admin->id }}" {{ old('assigned_to', $farm->assigned_to ?? '') == $admin->id ? 'selected' : '' }}>
                            {{ $admin->name }}
                        </option>
                    @endforeach
                </select>
                @error('assigned_to')
                    <label id="assigned_to-error" class="error" for="assigned_to">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('farm.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
