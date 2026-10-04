<div class="row mb-3">
    <div class="col-sm-12">
        <button type="button" class="btn btn-info" id="manage_material_types_btn" data-toggle="modal" data-target="#material_types_modal">
            <i class="fe fe-settings mr-1"></i> Manage Material Types
        </button>
    </div>
</div>

<form
    action="{{ isset($feedmaterial) && $feedmaterial->id ? route('feedmaterial.update', ['username' => $siteSlug, 'feedmaterial' => $feedmaterial->id]) : route('feedmaterial.store', ['username' => $siteSlug]) }}"
    method="POST"
    id="feedmaterial_form"
    novalidate=""
    class="needs-validation">

    @csrf

    <div class="row">
        <!-- Name Textbox -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Feed Material Name"
                    value="{{ old('name', $feedmaterial->name ?? '') }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Type Radio Buttons -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label class="form-label">Material Type <span class="text-red">*</span></label>
                <div id="material_types_radio_container">
                    <!-- Radio buttons will be generated dynamically here -->
                </div>
                @error('material_type_id')
                    <label id="material_type_id-error" class="error" for="material_type_id">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('feedmaterial.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<!-- Material Types Management Modal -->
<div class="modal fade" id="material_types_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Manage Material Types</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <button type="button" class="btn btn-success" id="add_new_material_type_btn">
                        <i class="fe fe-plus mr-1"></i> Add New Material Type
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" id="material_types_table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Hangar Allocation Required</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="material_types_tbody">
                            <!-- Rows will be loaded here -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add New Material Type Modal -->
<div class="modal fade" id="add_material_type_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Material Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="add_material_type_form">
                    <div class="form-group">
                        <label for="add_material_name">Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="add_material_name" placeholder="Material Type Name" required="">
                    </div>
                    <div class="form-group">
                        <label for="add_material_value">Value (auto-generated) <span class="text-muted small">(Read-only)</span></label>
                        <input type="text" class="form-control" id="add_material_value" placeholder="material_type" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hangar Allocation Required <span class="text-red">*</span></label>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="add_hangar_yes" name="add_hangar_allocation" value="1" required="">
                            <label class="custom-control-label" for="add_hangar_yes">Yes</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="add_hangar_no" name="add_hangar_allocation" value="0" required="">
                            <label class="custom-control-label" for="add_hangar_no">No</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_new_material_type_btn">Add Material Type</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Material Type Modal -->
<div class="modal fade" id="edit_material_type_modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Material Type</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="edit_material_type_form">
                    <input type="hidden" id="edit_material_type_id" />
                    <div class="form-group">
                        <label for="edit_material_name">Name <span class="text-red">*</span></label>
                        <input type="text" class="form-control" id="edit_material_name" placeholder="Material Type Name" required="">
                    </div>
                    <div class="form-group">
                        <label for="edit_material_value">Value (auto-generated) <span class="text-muted small">(Read-only)</span></label>
                        <input type="text" class="form-control" id="edit_material_value" placeholder="material_type" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Hangar Allocation Required <span class="text-red">*</span></label>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="edit_hangar_yes" name="edit_hangar_allocation" value="1" required="">
                            <label class="custom-control-label" for="edit_hangar_yes">Yes</label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" class="custom-control-input" id="edit_hangar_no" name="edit_hangar_allocation" value="0" required="">
                            <label class="custom-control-label" for="edit_hangar_no">No</label>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="save_material_type_btn">Save Changes</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var currentMaterialTypeId = '{{ old('material_type_id', $feedmaterial->material_type_id ?? '') }}';

        function loadMaterialTypes() {
            $.ajax({
                url: "{{ route('material-type.index', ['username' => $siteSlug]) }}",
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        loadRadioButtons(response.data);
                        loadTableRows(response.data);
                    }
                }
            });
        }

        function loadRadioButtons(types) {
            var container = $('#material_types_radio_container');
            container.html('');

            types.forEach(function(type, index) {
                var html = `
                    <div class="custom-control custom-radio">
                        <input type="radio" class="custom-control-input" id="type_${type.id}" name="material_type_id" value="${type.id}"
                            ${currentMaterialTypeId == type.id ? 'checked' : ''} required="">
                        <label class="custom-control-label" for="type_${type.id}">${type.name}</label>
                    </div>
                `;
                container.append(html);
            });
        }

        function loadTableRows(types) {
            var tbody = $('#material_types_tbody');
            tbody.html('');

            types.forEach(function(type) {
                var hangarStatus = type.hangar_allocation ? '<span class="badge badge-success">Yes</span>' : '<span class="badge badge-danger">No</span>';
                var html = `
                    <tr>
                        <td>${type.name}</td>
                        <td>${hangarStatus}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-warning edit-material-type-btn" data-id="${type.id}" title="Edit">
                                <i class="fa fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-material-type-btn" data-id="${type.id}" title="Delete">
                                <i class="fa fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                `;
                tbody.append(html);
            });
        }

        // Load material types on page load
        loadMaterialTypes();

        $(document).on('click', '#add_new_material_type_btn', function() {
            $('#add_material_name').val('');
            $('#add_material_value').val('');
            $('input[name="add_hangar_allocation"]').prop('checked', false);
            $('#add_material_type_modal').modal('show');
        });

        // Auto-generate snake_case value from name for add form
        $(document).on('keyup', '#add_material_name', function() {
            var name = $(this).val();
            var snakeCase = name.toLowerCase().replace(/\s+/g, '_');
            $('#add_material_value').val(snakeCase);
        });

        $('#save_new_material_type_btn').on('click', function() {
            var name = $('#add_material_name').val();
            var value = $('#add_material_value').val();
            var hangarAllocation = $('input[name="add_hangar_allocation"]:checked').val();

            if (!name) {
                swal({title: 'Error', text: 'Please enter material type name', icon: 'error'});
                return;
            }

            if (!value) {
                swal({title: 'Error', text: 'Value cannot be empty', icon: 'error'});
                return;
            }

            if (hangarAllocation === undefined) {
                swal({title: 'Error', text: 'Please select hangar allocation requirement', icon: 'error'});
                return;
            }

            var baseUrl = "{{ route('feedmaterial.create', ['username' => $siteSlug]) }}";
            var url = baseUrl.replace('/create', '') + '/material-type';

            $.ajax({
                url: url,
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    name: name,
                    value: value,
                    hangar_allocation: hangarAllocation
                },
                success: function(response) {
                    if (response.success) {
                        $('#add_material_type_modal').modal('hide');
                        loadMaterialTypes();
                        swal({title: 'Success', text: response.message, icon: 'success'});
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors || {};
                    var errorMsg = Object.values(errors).flat().join('\n');
                    swal({title: 'Error', text: errorMsg || 'Failed to create material type', icon: 'error'});
                }
            });
        });

        // Edit material type
        $(document).on('click', '.edit-material-type-btn', function() {
            var id = $(this).data('id');
            $.ajax({
                url: "{{ route('material-type.index', ['username' => $siteSlug]) }}",
                type: 'GET',
                success: function(response) {
                    var type = response.data.find(t => t.id == id);
                    if (type) {
                        $('#edit_material_type_id').val(type.id);
                        $('#edit_material_name').val(type.name);
                        $('#edit_material_value').val(type.value);
                        $('input[name="edit_hangar_allocation"][value="' + (type.hangar_allocation ? 1 : 0) + '"]').prop('checked', true);
                        $('#edit_material_type_modal').modal('show');
                    }
                }
            });
        });

        // Auto-generate snake_case value from name
        $(document).on('keyup', '#edit_material_name', function() {
            var name = $(this).val();
            var snakeCase = name.toLowerCase().replace(/\s+/g, '_');
            $('#edit_material_value').val(snakeCase);
        });

        // Save material type changes
        $('#save_material_type_btn').on('click', function() {
            var id = $('#edit_material_type_id').val();
            var name = $('#edit_material_name').val();
            var value = $('#edit_material_value').val();
            var hangarAllocation = $('input[name="edit_hangar_allocation"]:checked').val();

            if (!name) {
                alert('Please enter material type name');
                return;
            }

            if (!value) {
                alert('Value cannot be empty');
                return;
            }

            var baseUrl = "{{ route('feedmaterial.create', ['username' => $siteSlug]) }}";
            var url = baseUrl.replace('/create', '') + '/material-type/' + id;

            $.ajax({
                url: url,
                type: 'PUT',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {
                    name: name,
                    value: value,
                    hangar_allocation: hangarAllocation
                },
                success: function(response) {
                    if (response.success) {
                        $('#edit_material_type_modal').modal('hide');
                        loadMaterialTypes();
                        swal({title: 'Success', text: response.message, icon: 'success'});
                    }
                },
                error: function(xhr) {
                    var errors = xhr.responseJSON?.errors || {};
                    var errorMsg = Object.values(errors).flat().join('\n');
                    swal({title: 'Error', text: errorMsg || 'Failed to update material type', icon: 'error'});
                }
            });
        });

        // Delete material type
        $(document).on('click', '.delete-material-type-btn', function() {
            var id = $(this).data('id');
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this material type!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }, function(willDelete) {
                if (willDelete) {
                    var baseUrl = "{{ route('feedmaterial.create', ['username' => $siteSlug]) }}";
                    var url = baseUrl.replace('/create', '') + '/material-type/' + id;

                    $.ajax({
                        url: url,
                        type: 'DELETE',
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function(response) {
                            if (response.success) {
                                loadMaterialTypes();
                                swal({title: 'Success', text: response.message, icon: 'success'});
                            }
                        },
                        error: function() {
                            swal({title: 'Error', text: 'Failed to delete material type', icon: 'error'});
                        }
                    });
                }
            });
        });
    });
</script>
