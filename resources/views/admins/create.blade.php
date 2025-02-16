<form 
    action="{{ isset($admin) && $admin->id ? route('admins.update', $admin->id) : route('admins.store') }}" 
    method="POST" 
    id="admin_form"
    novalidate=""
    class="needs-validation" 
    enctype="multipart/form-data">

    @csrf
    
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <input type="hidden" name="type" value="{{ request('type', 1) }}">

                <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name', $admin->name) }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="username" class="form-label">Username <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="username" id="username" placeholder="Username" value="{{ old('username', $admin->username) }}" required="" />
                @error('username')
                    <label id="username-error" class="error" for="username">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="email" class="form-label">Email <span class="text-red">*</span></label>
                <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $admin->email) }}" {{ !isset($admin->id) ? '' : 'readonly' }} required="" />
                @error('email')
                    <label id="email-error" class="error" for="email">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" value="{{ old('password') }}" />
                @error('password')
                    <label id="password-error" class="error" for="password">{{ $message }}</label>
                @enderror
            </div>
        </div>
        

    </div>
    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('admins.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>