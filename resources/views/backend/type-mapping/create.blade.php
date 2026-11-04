<form
    action="{{ isset($typeMapping) && $typeMapping->id ? route('type-mapping.update', ['username' => $siteSlug, 'type_mapping' => $typeMapping->id]) : route('type-mapping.store', ['username' => $siteSlug]) }}"
    method="POST"
    id="type_mapping_form"
    novalidate=""
    class="needs-validation"
    enctype="multipart/form-data">

    @csrf

    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="project_id" class="form-label">Project <span class="text-red">*</span></label>
                <select class="form-control" name="project_id" id="project_id" required="">
                    <option value="">Select Project</option>
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $typeMapping->project_id ?? '') == $project->id ? 'selected' : '' }}>
                            {{ $project->project_name }}
                        </option>
                    @endforeach
                </select>
                @error('project_id')
                    <label id="project_id-error" class="error" for="project_id">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="type" class="form-label">Type <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="type" id="type" placeholder="Type"
                    value="{{ old('type', $typeMapping->type ?? '') }}" required="" />
                @error('type')
                    <label id="type-error" class="error" for="type">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="url" class="form-label">URL <span class="text-red">*</span></label>
                <input type="url" class="form-control" name="url" id="url" placeholder="Type URL"
                    value="{{ old('url', $typeMapping->url ?? '') }}" required="" />
                @error('url')
                    <label id="url-error" class="error" for="url">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="parent_type" class="form-label">Parent Type <span class="text-red">*</span></label>
                <select class="form-control" name="parent_type" id="parent_type" required="">
                    <option value="">Select Parent Type</option>
                    @foreach(\App\Models\TypeMapping::PARENT_TYPES as $parentType)
                        <option value="{{ $parentType }}" {{ old('parent_type', $typeMapping->parent_type ?? '') == $parentType ? 'selected' : '' }}>
                            {{ $parentType }}
                        </option>
                    @endforeach
                </select>
                @error('parent_type')
                    <label id="parent_type-error" class="error" for="parent_type">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('type-mapping.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
