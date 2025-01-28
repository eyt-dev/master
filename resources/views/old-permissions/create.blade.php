
<form action="{{ route('permissions.store') }}" method="POST" novalidate=""
class="needs-validation"  enctype="multipart/form-data">
    @csrf

    <div class="row">        
        <div class="col-sm-12 col-lg-12">
            <div class="form-group">
                <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                <input type="text" class="form-control permission permissionInput @error('name') is-invalid @enderror" name="name" id="name" placeholder="Enter permission name" value="{{ old('name', $permission->name) }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>         
        <input type="hidden" name="guard_name" value="web">
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
