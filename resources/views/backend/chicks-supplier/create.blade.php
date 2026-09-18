<form 
    action="{{ isset($chicks_supplier) && $chicks_supplier->id ? route('chicks-supplier.update', ['username' => $siteSlug, 'chicks_supplier' => $chicks_supplier->id]) : route('chicks-supplier.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="chicks_supplier_form"
    novalidate=""
    class="needs-validation">

    @csrf

    <div class="row">
        <!-- Name Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Chicks Supplier Name"
                    value="{{ old('name', $chicks_supplier->name ?? '') }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Contact Person Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="contact_person" class="form-label">Contact Person <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="contact_person" id="contact_person" placeholder="Contact Person Name"
                    value="{{ old('contact_person', $chicks_supplier->contact_person ?? '') }}" required="" />
                @error('contact_person')
                    <label id="contact_person-error" class="error" for="contact_person">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Phone Code Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="phone_code" class="form-label">Phone Code <span class="text-red">*</span></label>
                <select class="form-control" name="phone_code" id="phone_code" required="">
                    <option value="">Select Phone Code</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->dial_code_with_plus }}" {{ old('phone_code', $chicks_supplier->phone_code ?? '') == $country->dial_code_with_plus ? 'selected' : '' }}>
                            {{ $country->name }} ({{ $country->dial_code_with_plus }})
                        </option>
                    @endforeach
                </select>
                @error('phone_code')
                    <label id="phone_code-error" class="error" for="phone_code">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Mobile Number Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="mobile_number" class="form-label">Mobile Number <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="mobile_number" id="mobile_number" placeholder="Mobile Number"
                    value="{{ old('mobile_number', $chicks_supplier->mobile_number ?? '') }}" required="" maxlength="20" />
                @error('mobile_number')
                    <label id="mobile_number-error" class="error" for="mobile_number">{{ $message }}</label>
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
                    @foreach($breeds as $type => $breedList)
                        <optgroup label="{{ $type }}">
                            @foreach($breedList as $breed)
                                <option value="{{ $breed }}" {{ old('breed', $chicks_supplier->breed ?? '') == $breed ? 'selected' : '' }}>
                                    {{ $breed }}
                                </option>
                            @endforeach
                        </optgroup>
                    @endforeach
                </select>
                @error('breed')
                    <label id="breed-error" class="error" for="breed">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Location Textarea -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="location" class="form-label">Location <span class="text-red">*</span></label>
                <textarea class="form-control" name="location" id="location" placeholder="Chicks Supplier Location" required="">{{ old('location', $chicks_supplier->location ?? '') }}</textarea>
                @error('location')
                    <label id="location-error" class="error" for="location">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Latitude Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="latitude" class="form-label">Latitude</label>
                <input type="number" class="form-control" name="latitude" id="latitude" placeholder="Latitude (-90 to 90)"
                    value="{{ old('latitude', $chicks_supplier->latitude ?? '') }}" step="0.00000001" min="-90" max="90" />
                @error('latitude')
                    <label id="latitude-error" class="error" for="latitude">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Longitude Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="number" class="form-control" name="longitude" id="longitude" placeholder="Longitude (-180 to 180)"
                    value="{{ old('longitude', $chicks_supplier->longitude ?? '') }}" step="0.00000001" min="-180" max="180" />
                @error('longitude')
                    <label id="longitude-error" class="error" for="longitude">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('chicks-supplier.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
