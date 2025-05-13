<form 
    action="{{ isset($store_view) && $store_view->id ? route('store_view.update', $store_view->id) : route('store_view.store') }}" 
    method="POST" 
    id="store_view_form"
    novalidate=""
    class="needs-validation" 
    enctype="multipart/form-data">

    @csrf

    <div class="row">
        <!-- Region Select Box -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="region" class="form-label">Region <span class="text-red">*</span></label>
                <select class="form-control" name="region" id="region" required="">
                    <option value="">Select Region</option>
                    @foreach($regions as $region)
                        <option value="{{ $region }}" {{ old('region', $store_view->region ?? '') == $region ? 'selected' : '' }}>
                            {{ $region }}
                        </option>
                    @endforeach
                </select>
                @error('region')
                    <label id="region-error" class="error" for="region">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Language Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="language" class="form-label">Language <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="language" id="language" placeholder="Language" 
                    value="{{ old('language', $store_view->language ?? '') }}" required="" />
                @error('language')
                    <label id="language-error" class="error" for="language">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Slug Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="slug" class="form-label">Slug <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="slug" id="slug" placeholder="Slug" 
                    value="{{ old('slug', $store_view->slug ?? '') }}" required="" />
                @error('slug')
                    <label id="slug-error" class="error" for="slug">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Status Select Box -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="status" class="form-label">Status <span class="text-red">*</span></label>
                <select class="form-control" name="status" id="status" required="">
                    <option value="active" {{ old('status', $store_view->status ?? '') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status', $store_view->status ?? '') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
                @error('status')
                    <label id="status-error" class="error" for="status">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Description Textarea -->
        <div class="col-sm-12">
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" rows="3">{{ old('description', $store_view->description ?? '') }}</textarea>
                @error('description')
                    <label id="description-error" class="error" for="description">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Meta Data Textarea -->
        <div class="col-sm-12">
            <div class="form-group">
                <label for="meta_data" class="form-label">Meta Data</label>
                <textarea class="form-control" name="meta_data" id="meta_data" rows="3">{{ old('meta_data', $store_view->meta_data ?? '') }}</textarea>
                @error('meta_data')
                    <label id="meta_data-error" class="error" for="meta_data">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Meta Keywords Textarea -->
        <div class="col-sm-12">
            <div class="form-group">
                <label for="meta_keywords" class="form-label">Meta Keywords</label>
                <textarea class="form-control" name="meta_keywords" id="meta_keywords" rows="3">{{ old('meta_keywords', $store_view->meta_keywords ?? '') }}</textarea>
                @error('meta_keywords')
                    <label id="meta_keywords-error" class="error" for="meta_keywords">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('store_view.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
