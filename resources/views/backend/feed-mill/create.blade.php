<form 
    action="{{ isset($feed_mill) && $feed_mill->id ? route('feed-mill.update', ['username' => $siteSlug, 'feed_mill' => $feed_mill->id]) : route('feed-mill.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="feed_mill_form"
    novalidate=""
    class="needs-validation">

    @csrf
    
    <div class="row">
        <!-- Name Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Feed Mill Name" 
                    value="{{ old('name', $feed_mill->name ?? '') }}" required="" />
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
                    value="{{ old('contact_person', $feed_mill->contact_person ?? '') }}" required="" />
                @error('contact_person')
                    <label id="contact_person-error" class="error" for="contact_person">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Location Textarea -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="location" class="form-label">Location <span class="text-red">*</span></label>
                <textarea class="form-control" name="location" id="location" placeholder="Feed Mill Location" required="">{{ old('location', $feed_mill->location ?? '') }}</textarea>
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
                <textarea class="form-control" name="address" id="address" placeholder="Feed Mill Address" required="">{{ old('address', $feed_mill->address ?? '') }}</textarea>
                @error('address')
                    <label id="address-error" class="error" for="address">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Mobile Number Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="mobile_number" class="form-label">Mobile Number <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="mobile_number" id="mobile_number" placeholder="Mobile Number" 
                    value="{{ old('mobile_number', $feed_mill->mobile_number ?? '') }}" required="" />
                @error('mobile_number')
                    <label id="mobile_number-error" class="error" for="mobile_number">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('feed-mill.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
