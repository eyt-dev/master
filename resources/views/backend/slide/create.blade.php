<form 
    action="{{ isset($slide) && $slide->id ? route('slide.update', ['username' => $siteSlug, 'slide'=> $slide->id]) : route('slide.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="slide_form"
    novalidate=""
    class="needs-validation" 
    enctype="multipart/form-data">

    @csrf

    <div class="row">
        <!-- Store View Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="store_view_id" class="form-label">Store View <span class="text-red">*</span></label>
                <select class="form-control" name="store_view_id" id="store_view_id" required="">
                    <option value="">Select Store View</option>
                    @foreach($store_views as $storeView)
                        <option value="{{ $storeView->id }}" {{ old('store_view_id', $slide->store_view_id ?? '') == $storeView->id ? 'selected' : '' }}>
                            {{ $storeView->region }} - {{ $storeView->language }}
                        </option>
                    @endforeach
                </select>
                @error('store_view_id')
                    <label id="store_view_id-error" class="error" for="store_view_id">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Name Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Slide Name" 
                    value="{{ old('name', $slide->name ?? '') }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Description Textarea -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" placeholder="Slide Description">{{ old('description', $slide->description ?? '') }}</textarea>
                @error('description')
                    <label id="description-error" class="error" for="description">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Primary Button Text -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="primary_button_text" class="form-label">Primary Button Text</label>
                <input type="text" class="form-control" name="primary_button_text" id="primary_button_text" 
                    placeholder="Primary Button Text" value="{{ old('primary_button_text', $slide->primary_button_text ?? '') }}" />
                @error('primary_button_text')
                    <label id="primary_button_text-error" class="error" for="primary_button_text">{{ $message }}</label>
                @enderror
            </div>
        </div>
    
        <!-- Primary Button Link -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="primary_button_link" class="form-label">Primary Button Link</label>
                <input type="url" class="form-control" name="primary_button_link" id="primary_button_link" 
                    placeholder="Primary Button URL" value="{{ old('primary_button_link', $slide->primary_button_link ?? '') }}" />
                @error('primary_button_link')
                    <label id="primary_button_link-error" class="error" for="primary_button_link">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>
    
    <div class="row">
        <!-- Secondary Button Text -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="secondary_button_text" class="form-label">Secondary Button Text</label>
                <input type="text" class="form-control" name="secondary_button_text" id="secondary_button_text" 
                    placeholder="Secondary Button Text" value="{{ old('secondary_button_text', $slide->secondary_button_text ?? '') }}" />
                @error('secondary_button_text')
                    <label id="secondary_button_text-error" class="error" for="secondary_button_text">{{ $message }}</label>
                @enderror
            </div>
        </div>
    
        <!-- Secondary Button Link -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="secondary_button_link" class="form-label">Secondary Button Link</label>
                <input type="url" class="form-control" name="secondary_button_link" id="secondary_button_link" 
                    placeholder="Secondary Button URL" value="{{ old('secondary_button_link', $slide->secondary_button_link ?? '') }}" />
                @error('secondary_button_link')
                    <label id="secondary_button_link-error" class="error" for="secondary_button_link">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>
    

    <div class="row">
        <!-- Web Image Upload -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="web_image" class="form-label">Web Image <span class="text-red">*</span></label>
                <input type="file" class="form-control" name="web_image" id="web_image" accept="image/*" {{(isset($slide) ? "" : "required")}} />
                @error('web_image')
                    <label id="web_image-error" class="error" for="web_image">{{ $message }}</label>
                @enderror
                @if(isset($slide) && $slide->web_image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$slide->web_image) }}" alt="Web Image" class="img-thumbnail" width="150" />
                    </div>
                @endif
            </div>
        </div>

        <!-- Mobile Image Upload -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="mobile_image" class="form-label">Mobile Image <span class="text-red">*</span></label>
                <input type="file" class="form-control" name="mobile_image" id="mobile_image" accept="image/*" {{(isset($slide) ? "" : "required")}} />
                @error('mobile_image')
                    <label id="mobile_image-error" class="error" for="mobile_image">{{ $message }}</label>
                @enderror
                @if(isset($slide) && $slide->mobile_image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$slide->mobile_image) }}" alt="Mobile Image" class="img-thumbnail" width="150" />
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('slide.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
