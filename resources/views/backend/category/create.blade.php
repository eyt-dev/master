<form 
    action="{{ isset($category) && $category->id ? route('category.update', ['site' => $siteSlug, 'category' => $category->id]) : route('category.store', ['site' => $siteSlug]) }}" 
    method="POST" 
    id="category_form"
    novalidate=""
    class="needs-validation" 
    enctype="multipart/form-data">

    @csrf
    
    <div class="row">
        <!-- Store View Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="store_view" class="form-label">Store View <span class="text-red">*</span></label>
                <select class="form-control" name="store_view" id="store_view" required="">
                    <option value="">Select Store View</option>
                    @foreach($store_views as $storeView)
                        <option value="{{ $storeView->id }}" {{ old('store_view', $category->store_view_id ?? '') == $storeView->id ? 'selected' : '' }}>
                            {{ $storeView->region }} - {{ $storeView->language }}
                        </option>
                    @endforeach
                </select>
                @error('store_view')
                    <label id="store_view-error" class="error" for="store_view">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Title Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="title" class="form-label">Title <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="title" id="title" placeholder="Category Title" 
                    value="{{ old('title', $category->title ?? '') }}" required="" />
                @error('title')
                    <label id="title-error" class="error" for="title">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Description Textarea -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="description" class="form-label">Description <span class="text-red">*</span></label>
                <textarea class="form-control" name="description" id="description" placeholder="Category Description" required="">{{ old('description', $category->description ?? '') }}</textarea>
                @error('description')
                    <label id="description-error" class="error" for="description">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Image Upload -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="image" class="form-label">Category Image</label>
                <input type="file" class="form-control" name="image" id="image" accept="image/*" />
                @error('image')
                    <label id="image-error" class="error" for="image">{{ $message }}</label>
                @enderror
                @if(isset($category) && $category->image)
                    <div class="mt-2">
                        <img src="{{ asset('storage/'.$category->image) }}" alt="Category Image" class="img-thumbnail" width="150" />
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('category.index', ['site' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
