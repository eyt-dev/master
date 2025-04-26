<form 
    action="{{ isset($setting) && $setting->id ? route('setting.update', $setting->id) : route('setting.store') }}" 
    method="POST" 
    id="setting_form"
    novalidate=""
    class="needs-validation" 
    enctype="multipart/form-data">

    @csrf

    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="store_view" class="form-label">Store View <span class="text-red">*</span></label>
                <select class="form-control" name="store_view_id" id="store_view" required="">
                    <option value="">Select Store View</option>
                    @foreach($store_views as $storeView)
                        <option value="{{ $storeView->id }}" {{ old('store_view', $setting->store_view_id ?? '') == $storeView->id ? 'selected' : '' }}>
                            {{ $storeView->region }} - {{ $storeView->language }}
                        </option>
                    @endforeach
                </select>
                @error('store_view')
                    <label id="store_view-error" class="error" for="store_view">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Domain Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="domain" class="form-label">Domain <span class="text-red">*</span></label>
                <input type="url" class="form-control" name="domain" id="domain" placeholder="Website Domain"
                    value="{{ old('domain', $setting->domain ?? '') }}" required="" />
                @error('domain')
                    <label id="domain-error" class="error" for="domain">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Title -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="title" class="form-label">Title <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="title" id="title" placeholder="Website Title" 
                    value="{{ old('title', $setting->title ?? '') }}" required="" />
                @error('title')
                    <label id="title-error" class="error" for="title">{{ $message }}</label>
                @enderror
            </div>
        </div>
    
        <!-- Sub Title -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="sub_title" class="form-label">Sub Title</label>
                <input type="text" class="form-control" name="sub_title" id="sub_title" placeholder="Website Sub Title"
                    value="{{ old('sub_title', $setting->sub_title ?? '') }}" />
                @error('sub_title')
                    <label id="sub_title-error" class="error" for="sub_title">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Primary Text Color -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="primary_text_color" class="form-label">Primary Text Color</label>
                <input type="text" class="form-control" name="primary_text_color" id="primary_text_color" placeholder="#000000"
                    value="{{ old('primary_text_color', $setting->primary_text_color ?? '') }}" />
                @error('primary_text_color')
                    <label id="primary_text_color-error" class="error" for="primary_text_color">{{ $message }}</label>
                @enderror
            </div>
        </div>
   
        <!-- Secondary Text Color -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="secondary_text_color" class="form-label">Secondary Text Color</label>
                <input type="text" class="form-control" name="secondary_text_color" id="secondary_text_color" placeholder="#ffffff"
                    value="{{ old('secondary_text_color', $setting->secondary_text_color ?? '') }}" />
                @error('secondary_text_color')
                    <label id="secondary_text_color-error" class="error" for="secondary_text_color">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Primary Button Background -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="primary_button_background" class="form-label">Primary Button Background</label>
                <input type="text" class="form-control" name="primary_button_background" id="primary_button_background" placeholder="#007bff"
                    value="{{ old('primary_button_background', $setting->primary_button_background ?? '') }}" />
                @error('primary_button_background')
                    <label id="primary_button_background-error" class="error" for="primary_button_background">{{ $message }}</label>
                @enderror
            </div>
        </div>
    
        <!-- Secondary Button Background -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="secondary_button_background" class="form-label">Secondary Button Background</label>
                <input type="text" class="form-control" name="secondary_button_background" id="secondary_button_background" placeholder="#6c757d"
                    value="{{ old('secondary_button_background', $setting->secondary_button_background ?? '') }}" />
                @error('secondary_button_background')
                    <label id="secondary_button_background-error" class="error" for="secondary_button_background">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Primary Button Text Color -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="primary_button_text_color" class="form-label">Primary Button Text Color</label>
                <input type="text" class="form-control" name="primary_button_text_color" id="primary_button_text_color" placeholder="#ffffff"
                    value="{{ old('primary_button_text_color', $setting->primary_button_text_color ?? '') }}" />
                @error('primary_button_text_color')
                    <label id="primary_button_text_color-error" class="error" for="primary_button_text_color">{{ $message }}</label>
                @enderror
            </div>
        </div>
    
        <!-- Secondary Button Text Color -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="secondary_button_text_color" class="form-label">Secondary Button Text Color</label>
                <input type="text" class="form-control" name="secondary_button_text_color" id="secondary_button_text_color" placeholder="#000000"
                    value="{{ old('secondary_button_text_color', $setting->secondary_button_text_color ?? '') }}" />
                @error('secondary_button_text_color')
                    <label id="secondary_button_text_color-error" class="error" for="secondary_button_text_color">{{ $message }}</label>
                @enderror
            </div>
        </div>
   
        <!-- Description Textarea -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" placeholder="Website Description">{{ old('description', $setting->description ?? '') }}</textarea>
                @error('description')
                    <label id="description-error" class="error" for="description">{{ $message }}</label>
                @enderror
            </div>
        </div>
    
        <!-- Logo Uploads -->
        @foreach (['logo', 'dark_logo', 'light_logo', 'footer_logo', 'favicon'] as $logoType)
            <div class="col-sm-6 col-md-6">
                <div class="form-group">
                    <label for="{{ $logoType }}" class="form-label">{{ ucwords(str_replace('_', ' ', $logoType)) }}</label>
                    <input type="file" class="form-control" name="{{ $logoType }}" id="{{ $logoType }}" accept="image/*" />
                    @error($logoType)
                        <label id="{{ $logoType }}-error" class="error" for="{{ $logoType }}">{{ $message }}</label>
                    @enderror
                    @if(isset($setting) && $setting->$logoType)
                        <div class="mt-2">
                            <img src="{{ asset('storage/'.$setting->$logoType) }}" alt="{{ ucwords(str_replace('_', ' ', $logoType)) }}" class="img-thumbnail" width="150" />
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('setting.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
