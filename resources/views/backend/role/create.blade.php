<form action="{{ $role->id == null ? route('role.store', ['username' => $siteSlug]) : route('role.update', ['username' => $siteSlug, 'role' => $role->id]) }}" method="POST"
    id="role_form" novalidate="" class="needs-validation">
    <div class="card-body">
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="form-group col-sm-6">
                    <label for="name">Role</label>
                    <input class="form-control @error('name') is-invalid @enderror" name="name" type="text"
                        value="{{ old('name', $role->name) }}" required="" placeholder="Role">
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <div class="valid-feedback">Looks good!</div>
                    <div class="invalid-feedback">Please enter role.</div>
                </div>
                <input type="hidden" value="admin" name="guard_name">
            </div>
            
            <div class="form-group col-sm-12 col-lg-12">
                <div class="permission">
                    <label for="permission">Permission:</label>
                    <div class="row" id="permission-modules">
                        @foreach ($groupPermission as $key => $permissions)
                        {{-- @dump($permissions[0]->permissionModule->name) --}}
                            <div class="col-sm-6 col-lg-4 mb-3" data-module="{{ $key }}">
                                <div class="border rounded p-3">
                                    <div class="custom-checkbox permission mb-2">
                                        <input id="module-{{ $key }}" type="checkbox" class="check-all form-check-input" data-module-id="{{ $key }}" name="checkAll">
                                        <label for="module-{{ $key }}" class="form-check-label"> <b>{{$permissions[0]->permissionModule->name}}</b></label>
                                    </div>
                                    <div class="module-permissions" data-module-id="{{ $key }}">
                                        @foreach ($permissions as $permission)
                                            <div class="custom-control custom-checkbox ms-2 mb-2">
                                                <input id="perm-{{ $permission->id }}" type="checkbox" class="check-one form-check-input"
                                                    data-module-id="{{ $key }}"
                                                    name="permission_data[]" value="{{ $permission->id }}"
                                                    {{ $role->id != null && count($role->permission_data) > 0 && isset($role->permission_data[$permission->id]) ? 'checked' : '' }}>
                                                <input id="module-hidden-{{ $permission->module }}" type="hidden"
                                                    name="permission_module[{{$permission->id}}]" value="{{ $permission->module }}">
                                                <label for="perm-{{ $permission->id }}" class="form-check-label">{{ Str::ucfirst($permission->name) }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit">{{ $role->id == null ? 'Save' : 'Update' }}</button>
        <a href="{{ route('role.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
