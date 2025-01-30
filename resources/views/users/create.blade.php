<form 
    action="{{ isset($user) && $user->id ? route('users.update', $user->id) : route('users.store') }}" 
    method="POST" 
    id="user_form"
    novalidate=""
    class="needs-validation" 
    enctype="multipart/form-data">

    @csrf
    
    <div class="row">
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name', $user->name) }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="email" class="form-label">Email <span class="text-red">*</span></label>
                <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $user->email) }}" {{ !isset($user->id) ? '' : 'readonly' }} required="" />
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
        <div class="form-group col-sm-6">
            <label for="role">Role <span class="text-red">*</span> </label>
            <select class="form-control custom-select @error('role') is-invalid @enderror" name="role"
                required="">
                <option value="" selected="selected" disabled>Select Role</option>
                @foreach ($roles as $role)
                    <option value="{{ $role->name }}"
                        {{ $user->getRoleNames()->contains($role->name) ? 'selected' : '' }}>
                        {{ $role->name }}</option>
                @endforeach
            </select>
            @error('role')
                <div class="invalid-feedback">{{ $message }}</div>
                {{-- <span class="error invalid-feedback">{{ $message }}</span> --}}
            @enderror
            <div class="valid-feedback">Looks good!</div>
            <div class="invalid-feedback">Please choose role.</div>
        </div>

    </div>
    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>