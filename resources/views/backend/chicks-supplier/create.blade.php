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

        <!-- Breed Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="breed" class="form-label">Breed <span class="text-red">*</span></label>
                <select class="form-control" name="breed" id="breed" required="">
                    <option value="">Select Breed</option>
                    @foreach($breeds as $breed)
                        <option value="{{ $breed }}" {{ old('breed', $chicks_supplier->breed ?? '') == $breed ? 'selected' : '' }}>
                            {{ $breed }}
                        </option>
                    @endforeach
                </select>
                @error('breed')
                    <label id="breed-error" class="error" for="breed">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
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

        <!-- Mobile Number Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="mobile_number" class="form-label">Mobile Number <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="mobile_number" id="mobile_number" placeholder="Mobile Number" 
                    value="{{ old('mobile_number', $chicks_supplier->mobile_number ?? '') }}" required="" />
                @error('mobile_number')
                    <label id="mobile_number-error" class="error" for="mobile_number">{{ $message }}</label>
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
        <!-- Address Textarea -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="address" class="form-label">Address <span class="text-red">*</span></label>
                <textarea class="form-control" name="address" id="address" placeholder="Chicks Supplier Address" required="">{{ old('address', $chicks_supplier->address ?? '') }}</textarea>
                @error('address')
                    <label id="address-error" class="error" for="address">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('chicks-supplier.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
