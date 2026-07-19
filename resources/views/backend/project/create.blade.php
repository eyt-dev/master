<form 
    action="{{ isset($project) && $project->id ? route('project.update', ['username' => $siteSlug, 'project' => $project->id]) : route('project.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="project_form"
    novalidate=""
    class="needs-validation" 
    enctype="multipart/form-data">

    @csrf
    
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="project_name" class="form-label">Project Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="project_name" id="project_name" placeholder="Project Name" 
                    value="{{ old('project_name', $project->project_name ?? '') }}" required="" />
                @error('project_name')
                    <label id="project_name-error" class="error" for="project_name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="url" class="form-label">URL</label>
                <input type="url" class="form-control" name="url" id="url" placeholder="Project URL" 
                    value="{{ old('url', $project->url ?? '') }}" required="" />
                @error('url')
                    <label id="url-error" class="error" for="url">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="description" class="form-label">Description</label>
                <textarea class="form-control" name="description" id="description" placeholder="Project Description">{{ old('description', $project->description ?? '') }}</textarea>
                @error('description')
                    <label id="description-error" class="error" for="description">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('project.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
