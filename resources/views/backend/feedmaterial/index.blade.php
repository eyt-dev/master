@extends('layouts.master')
@section('css')
    <link href="{{ URL::asset('assets/plugins/datatable/css/dataTables.bootstrap4.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/select2/select2.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/sweet-alert/jquery.sweet-modal.min.css') }}" rel="stylesheet" />
    <link href="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.css') }}" rel="stylesheet" />
    <style>
        .hide{display: none;}
        label.error{font-size: 87.5%; color: #dc0441;}
    </style>
@endsection
@section('page-header')
    <div class="page-header">
        <div class="page-leftheader">
            <h4 class="page-title mb-0">Feed Materials</h4>
            <ol class="breadcrumb">
                <li class="breadcrumb-item">
                    <a href="#">
                        <i class="fe fe-layout mr-2 fs-14"></i>Feed Materials
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><a href="#">Listing</a></li>
            </ol>
        </div>
        <div class="page-rightheader">
            <div class="btn btn-list">
                <button id="breed_category_mapping_btn" class="btn btn-secondary" data-toggle="tooltip" title="Breed Category Material Mapping">
                    <i class="fe fe-map mr-1"></i> Breed Category Material Mapping
                </button>
                <button id="manage_types_btn" class="btn btn-warning" data-toggle="tooltip" title="Manage Material Types">
                    <i class="fe fe-settings mr-1"></i> Material Type Settings
                </button>
                <a id="add_new" class="btn btn-info" data-toggle="tooltip" title="Add new">
                    <i class="fe fe-plus mr-1"></i> Add new
                </a>
            </div>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-title">Feed Materials Data</div>
                </div>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered text-nowrap" id="feedmaterial_table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Type</th>
                                    <th>Created By</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="feedmaterial_form_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Add Feed Material</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">×</span> </button>
                </div>
                <div class="modal-body"></div>
            </div>
        </div>
    </div>

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

    <!-- Breed Category Material Mapping Modal -->
    <div class="modal fade" id="breed_category_mapping_modal" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Breed Category Material Mapping</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="breed_category_mapping_container">
                        <!-- Breed category checkboxes will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="save_breed_category_mapping_btn">Save</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ URL::asset('assets/plugins/datatable/js/jquery.dataTables.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/datatable/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/sweet-alert/sweetalert.min.js') }}"></script>
    <script src="{{ URL::asset('assets/plugins/select2/select2.full.min.js') }}"></script>
    <script>
        // Material Types Management
        function loadMaterialTypes() {
            $.ajax({
                url: "{{ route('material-type.index', ['username' => $siteSlug]) }}",
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        loadTableRows(response.data);
                    }
                }
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

        $(document).on('click', '#manage_types_btn', function() {
            $('#material_types_modal').modal('show');
            loadMaterialTypes();
        });

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

            var baseUrl = "{{ route('feedmaterial.index', ['username' => $siteSlug]) }}";
            var url = baseUrl.replace('/feed-material', '/material-type');

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

        $('#save_material_type_btn').on('click', function() {
            var id = $('#edit_material_type_id').val();
            var name = $('#edit_material_name').val();
            var value = $('#edit_material_value').val();
            var hangarAllocation = $('input[name="edit_hangar_allocation"]:checked').val();

            if (!name) {
                swal({title: 'Error', text: 'Please enter material type name', icon: 'error'});
                return;
            }

            if (!value) {
                swal({title: 'Error', text: 'Value cannot be empty', icon: 'error'});
                return;
            }

            var baseUrl = "{{ route('feedmaterial.index', ['username' => $siteSlug]) }}";
            var url = baseUrl.replace('/feed-material', '/material-type/' + id);

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
                    var baseUrl = "{{ route('feedmaterial.index', ['username' => $siteSlug]) }}";
                    var url = baseUrl.replace('/feed-material', '/material-type/' + id);

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

        $(document).on('click', '#add_new', function() {
            $.ajax({
                url: "{{ route('feedmaterial.create', ['username' => $siteSlug]) }}",
                type: "GET",
                success: function(response) {
                    $(".modal-body").html(response);
                    $(".modal-title").html("Add Feed Material");
                    $("#feedmaterial_form_modal").modal('show');
                    checkValidation();
                }
            });
        });

        $(document).on('click', '.edit-feed-material', function() {
            var id = $(this).data('id');
            $.ajax({
                url: $(this).data('path'),
                success: function(response) {
                    $(".modal-body").html(response);
                    $(".modal-title").html("Update Feed Material");
                    $("#feedmaterial_form_modal").modal('show');
                    checkValidation();
                }
            });
        });

        var table = $('#feedmaterial_table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            ajax: "{{ route('feedmaterial.index', ['username' => $siteSlug]) }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'type', name: 'type' },
                { data: 'creator' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('click', '.delete-feed-material', function() {
            var id = $(this).attr("data-id");
            swal({
                title: "Are you sure?",
                text: "Once deleted, you will not be able to recover this feed material!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                showCancelButton: true,
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "Cancel"
            }, function(willDelete) {
                if (willDelete) {
                    $.ajax({
                        type: "get",
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        url: "{{ route('feedmaterial.destroy', ['username' => $siteSlug, 'feedmaterial' => ':id']) }}".replace(':id', id),
                        success: function(response) {
                            swal({
                                title: response.msg
                            }, function(result) {
                                location.reload();
                            });
                        }
                    });
                }
            });
        });

        function checkValidation() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }

        // Breed Category Material Mapping
        function loadBreedCategoryMapping() {
            $.ajax({
                url: "{{ route('feedmaterial.breed-mapping-load', ['username' => $siteSlug]) }}",
                type: 'GET',
                success: function(response) {
                    if (response.success && response.data) {
                        renderBreedCategoryMapping(response.data);
                    }
                }
            });
        }

        function renderBreedCategoryMapping(data) {
            var container = $('#breed_category_mapping_container');
            container.html('');

            data.forEach(function(category) {
                var categoryHtml = `
                    <div class="card mb-3">
                        <div class="card-header">
                            <h5 class="mb-0">${category.breed_category}</h5>
                        </div>
                        <div class="card-body">
                            <div class="breed-category-materials" data-category="${category.breed_category}">
                `;

                category.material_types.forEach(function(materialType) {
                    var checkboxId = 'material_' + category.breed_category + '_' + materialType.id;
                    categoryHtml += `
                        <div class="custom-control custom-checkbox mb-2">
                            <input type="checkbox" class="custom-control-input material-type-checkbox" id="${checkboxId}"
                                data-category="${category.breed_category}" data-material-id="${materialType.id}"
                                ${materialType.checked ? 'checked' : ''}>
                            <label class="custom-control-label" for="${checkboxId}">${materialType.name}</label>
                        </div>
                    `;
                });

                categoryHtml += `
                            </div>
                        </div>
                    </div>
                `;

                container.append(categoryHtml);
            });
        }

        $(document).on('click', '#breed_category_mapping_btn', function() {
            $('#breed_category_mapping_modal').modal('show');
            loadBreedCategoryMapping();
        });

        $(document).on('click', '#save_breed_category_mapping_btn', function() {
            var mappings = [];
            var categories = ['Broiler', 'Layer'];

            categories.forEach(function(category) {
                var selectedMaterialIds = [];
                $('input[data-category="' + category + '"]:checked').each(function() {
                    selectedMaterialIds.push($(this).data('material-id'));
                });

                mappings.push({
                    breed_category: category,
                    material_type_ids: selectedMaterialIds
                });
            });

            $.ajax({
                url: "{{ route('feedmaterial.breed-mapping-save', ['username' => $siteSlug]) }}",
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: { mappings: mappings },
                success: function(response) {
                    if (response.success) {
                        $('#breed_category_mapping_modal').modal('hide');
                        swal({title: 'Success', text: response.message, icon: 'success'});
                    }
                },
                error: function(xhr) {
                    var errorMsg = xhr.responseJSON?.message || 'Failed to save mappings';
                    swal({title: 'Error', text: errorMsg, icon: 'error'});
                }
            });
        });
    </script>
@endsection
