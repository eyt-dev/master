<form action="{{ $role->id == null ? route('roles.store') : route('roles.update', ['role' => $role->id]) }}" method="POST"
    id="role_form" novalidate="" class="needs-validation">
    <div class="card-body">
        @csrf
        <div class="row">
            <div class="col-sm-12 col-lg-12">
                <div class="form-group">
                    <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name', $role->name) }}" required="" />
                    @error('name')
                        <label id="name-error" class="error" for="name">{{ $message }}</label>
                    @enderror
                </div>
                <input type="hidden" value="web" name="guard_name">
            </div>
            <div class="form-group col-sm-12 col-lg-12">
                <div class="permission input-box">
                    <label for="permission" class="form-label">Permission:</label>
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <div class="row">
                            @foreach ($groupPermission as $key => $permissions)
                                <div class="col-sm-6 role-group">
                                    <div class="custom-checkbox permission  input-box">
                                        <input id="{{ $key }}" type="checkbox" class=" check-all"
                                            name="checkAll">
                                        <label for="{{ $key }}">
                                            <b>{{ Str::ucfirst(explode('.', $permissions[0]->name)[1]) }}</b></label>
                                    </div>

                                    @foreach ($permissions as $permission)
                                        <div class="custom-control custom-checkbox ms-3 row  input-box">
                                            <div class="col-md-5">
                                                <input id="{{ $permission->id }}" type="checkbox" class="check-one"
                                                    name="permission_data[]" value="{{ $permission->id }}"
                                                    {{ $role->id != null && count($role->permission_data) > 0 && isset($role->permission_data[$permission->id]) ? 'checked' : '' }}>
                                                <input id="{{ $permission->module }}" type="hidden"
                                                    name="permission_module[{{ $permission->id }}]"
                                                    value="{{ $permission->module }}">
                                                <label
                                                    for="{{ $permission->id }}">{{ Str::ucfirst($permission->name) }}</label>
                                            </div>
                                            <?php
                                            $edit_no = 0;
                                            $edit_type = '';
                                            $permission_id = 0;
                                            $scheduler_data = ['scheduler_no' => '', 'type' => ''];
                                            if ($role->scheduler->count() > 0) {
                                                $scheduler = $role->scheduler->toArray();
                                                if (array_search($permission->id, array_column($scheduler, 'permission_id')) !== false) {
                                                    $key = array_search($permission->id, array_column($scheduler, 'permission_id'));
                                                    $scheduler_data = $scheduler[$key];
                                                }
                                            
                                                // dump($scheduler[$key]);
                                            
                                                if (array_search($permission->id, array_column($scheduler, 'permission_id')) !== false) {
                                                    // $edit_no=$scheduler['scheduler_no'];
                                                    // $edit_type=$scheduler['type'];
                                                    $permission_id = $scheduler;
                                                }
                                                // echo $key;
                                            }
                                            // dump($scheduler_data);
                                            // echo $role->scheduler->count().$edit_no.$edit_type;
                                            ?>
                                            <div class="col-md-7">
                                                <div class="row">


                                                    @if (str_contains($permission->name, 'edit'))
                                                        <div class="col-md-6 select-box">

                                                            <select name="schedule_no_edit[{{ $permission->id }}]"
                                                                class="google-input" title="Number">
                                                                <option value="0"
                                                                    {{ $scheduler_data['scheduler_no'] == '0' || $scheduler_data['scheduler_no'] == null ? 'selected' : '' }}>
                                                                    0</option>
                                                                @for ($i = 1; $i <= 10; $i++)
                                                                    <option value="{{ $i }}"
                                                                        {{ $scheduler_data['scheduler_no'] == $i ? 'selected' : '' }}>
                                                                        {{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6  select-box">

                                                            <select name="schedule_time_edit[{{ $permission->id }}]"
                                                                class="google-input schedule_time_edit schedule_time"
                                                                title="Time">
                                                                <option value="day"
                                                                    {{ $scheduler_data['type'] == 'day' || $scheduler_data['type'] ==null ? 'selected' : '' }}>
                                                                    Days</option>
                                                                <option value="week"
                                                                    {{ $scheduler_data['type'] == 'week' ? 'selected' : '' }}>
                                                                    Weeks</option>
                                                                <option value="month"
                                                                    {{ $scheduler_data['type'] == 'month' ? 'selected' : '' }}>
                                                                    Months</option>
                                                                <option value="year"
                                                                    {{ $scheduler_data['type'] == '0' ? 'selected' : '' }}>
                                                                    Years</option>
                                                            </select>
                                                        </div>
                                                    @endif

                                                </div>

                                                @if (str_contains($permission->name, 'delete'))
                                                    <div class="row">
                                                        <div class="col-md-6  select-box">
                                                            <select name="schedule_no_delete[{{ $permission->id }}]"
                                                                class="google-input schedule_no_delete schedule_no"
                                                                title="Number">
                                                                <option value="0"
                                                                    {{ $scheduler_data['scheduler_no'] == '0' || $scheduler_data['scheduler_no'] == null ? 'selected' : '' }}>
                                                                    0</option>
                                                                @for ($i = 1; $i <= 10; $i++)
                                                                    <option value="{{ $i }}"
                                                                        {{ $scheduler_data['scheduler_no'] == $i ? 'selected' : '' }}>
                                                                        {{ $i }}</option>
                                                                @endfor
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6  select-box">
                                                            <select name="schedule_time_delete[{{ $permission->id }}]"
                                                                class="google-input schedule_time_delete schedule_time"
                                                                title="Time">
                                                                <option value="day"
                                                                    {{ $scheduler_data['type'] == 'day' || $scheduler_data['type'] == null ? 'selected' : '' }}>
                                                                    Days</option>
                                                                <option value="week"
                                                                    {{ $scheduler_data['type'] == 'week' ? 'selected' : '' }}>
                                                                    Weeks</option>
                                                                <option value="month"
                                                                    {{ $scheduler_data['type'] == 'month' ? 'selected' : '' }}>
                                                                    Months</option>
                                                                <option value="year"
                                                                    {{ $scheduler_data['type'] == '0' ? 'selected' : '' }}>
                                                                    Years</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>

                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        </div>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button class="btn btn-primary" type="submit">{{ $role->id == null ? 'Save' : 'Update' }}</button>
        <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>