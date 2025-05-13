<form 
    action="{{ isset($page) ? route('page.update', $page->id) : route('page.store') }}" 
    method="POST" 
    id="page_form"
    class="needs-validation" 
    novalidate=""
    enctype="multipart/form-data">
    
    @csrf

    <div class="row">
        <!-- Category Select -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="category_id" class="form-label">Category <span class="text-red">*</span></label>
                <select class="form-control" name="category_id" id="category_id" required="">
                    <option value="">Select Category</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ old('category_id', $page->category_id ?? '') == $category->id ? 'selected' : '' }}>
                            {{ $category->title }}
                        </option>
                    @endforeach
                </select>
                @error('category_id')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Title -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="title" class="form-label">Title <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="title" id="title" placeholder="Page Title"
                    value="{{ old('title', $page->title ?? '') }}" required="" />
                @error('title')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Description -->
        <div class="col-md-12">
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description">{{ old('description', $page->description ?? '') }}</textarea>
            </div>
        </div>

        <!-- Meta Data -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="meta_data" class="form-label">Meta Data</label>
                <textarea class="form-control" name="meta_data" id="meta_data">{{ old('meta_data', $page->meta_data ?? '') }}</textarea>
            </div>
        </div>

        <!-- Meta Keywords -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="meta_keyword" class="form-label">Meta Keywords</label>
                <textarea class="form-control" name="meta_keyword" id="meta_keyword">{{ old('meta_keyword', $page->meta_keyword ?? '') }}</textarea>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Path -->
        <div class="col-md-6">
            <div class="form-group">
                <label for="path" class="form-label">Path (URL) <span class="text-red">*</span></label>
                <input type="url" class="form-control" name="path" id="path" placeholder="Enter URL"
                    value="{{ old('path', $page->path ?? '') }}" required="" />
                @error('path')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('page.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
