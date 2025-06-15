<form 
    action="{{ isset($testimonial) ? route('testimonial.update', ['username' => $siteSlug, 'testimonial' => $testimonial->id]) : route('testimonial.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="testimonial_form" 
    enctype="multipart/form-data" 
    novalidate="" 
    class="needs-validation">

    @csrf

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Store View <span class="text-red">*</span></label>
                <select class="form-control" name="store_view" required>
                    <option value="">Select Store View</option>
                    @foreach($store_views as $storeView)
                        <option value="{{ $storeView->id }}" {{ old('store_view', $testimonial->store_view_id ?? '') == $storeView->id ? 'selected' : '' }}>
                            {{ $storeView->region }} - {{ $storeView->language }}
                        </option>
                    @endforeach
                </select>
                @error('store_view')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Name <span class="text-red">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Name" 
                    value="{{ old('name', $testimonial->name ?? '') }}" required>
                @error('name')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Description <span class="text-red">*</span></label>
                <textarea name="description" class="form-control" placeholder="Description" required>{{ old('description', $testimonial->description ?? '') }}</textarea>
                @error('description')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                @error('image')
                    <label class="error">{{ $message }}</label>
                @enderror
                @if(isset($testimonial) && $testimonial->image)
                    <img src="{{ asset('storage/' . $testimonial->image) }}" alt="Image" class="img-thumbnail mt-2" width="150" />
                @endif
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('testimonial.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
