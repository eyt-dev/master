<form action="{{ isset($user) ? route('users.update', $user->id) : route('users.store') }}" method="POST" id="mailboxForm" enctype="multipart/form-data">
    @csrf
    
    <div class="row">
        <div class="col-sm-4 col-md-4">
            <div class="form-group">
                <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name', $user->name) }}" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-4 col-md-4">
            <div class="form-group">
                <label for="email" class="form-label">email</label>
                <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $user->email) }}" {{ !isset($user->id) ? '' : 'readonly' }} />
                @error('email')
                    <label id="email-error" class="error" for="email">{{ $message }}</label>
                @enderror
            </div>
        </div>
        <div class="col-sm-4 col-md-4">
            <div class="form-group">
                <label for="name" class="form-label">password</label>
                <input type="password" class="form-control" name="password" id="password" value="{{ old('password') }}" />
                @error('password')
                    <label id="password-error" class="error" for="password">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>