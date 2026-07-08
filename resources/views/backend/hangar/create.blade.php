<form 
    action="{{ isset($hangar) && $hangar->id ? route('hangar.update', ['username' => $siteSlug, 'hangar' => $hangar->id]) : route('hangar.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="hangar_form"
    novalidate=""
    class="needs-validation">

    @csrf
    
    <div class="row">
        <!-- Farm Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="farm_id" class="form-label">Farm <span class="text-red">*</span></label>
                <select class="form-control" name="farm_id" id="farm_id" required="">
                    <option value="">Select Farm</option>
                    @foreach($farms as $farm)
                        <option value="{{ $farm->id }}" {{ old('farm_id', $hangar->farm_id ?? '') == $farm->id ? 'selected' : '' }}>
                            {{ $farm->name }}
                        </option>
                    @endforeach
                </select>
                @error('farm_id')
                    <label id="farm_id-error" class="error" for="farm_id">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Hangar Name Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">Hangar Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Hangar Name (e.g., Hangar 1)" 
                    value="{{ old('name', $hangar->name ?? '') }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Area SQM Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="area_sqm" class="form-label">Area (SQM) <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="area_sqm" id="area_sqm" placeholder="Area in Square Meters" 
                    value="{{ old('area_sqm', $hangar->area_sqm ?? '') }}" required="" step="0.01" min="0" />
                @error('area_sqm')
                    <label id="area_sqm-error" class="error" for="area_sqm">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Layer Hens Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="layer_hens" class="form-label">Layer Hens <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="layer_hens" id="layer_hens" placeholder="Number of Layer Hens" 
                    value="{{ old('layer_hens', $hangar->layer_hens ?? '') }}" required="" />
                @error('layer_hens')
                    <label id="layer_hens-error" class="error" for="layer_hens">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Broiler Hens Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="broiler_hens" class="form-label">Broiler Hens <span class="text-red">*</span></label>
                <input type="number" class="form-control" name="broiler_hens" id="broiler_hens" placeholder="Number of Broiler Hens" 
                    value="{{ old('broiler_hens', $hangar->broiler_hens ?? '') }}" required="" />
                @error('broiler_hens')
                    <label id="broiler_hens-error" class="error" for="broiler_hens">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('hangar.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
