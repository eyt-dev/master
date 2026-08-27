<form 
    action="{{ isset($slaughter) && $slaughter->id ? route('slaughter.update', ['username' => $siteSlug, 'slaughter' => $slaughter->id]) : route('slaughter.store', ['username' => $siteSlug]) }}" 
    method="POST" 
    id="slaughter_form"
    novalidate=""
    class="needs-validation">

    @csrf
    
    <div class="row">
        <!-- Name Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">Name <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="Slaughter Name" 
                    value="{{ old('name', $slaughter->name ?? '') }}" required="" />
                @error('name')
                    <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Contact Person Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="contact_person" class="form-label">Contact Person <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="contact_person" id="contact_person" placeholder="Contact Person Name" 
                    value="{{ old('contact_person', $slaughter->contact_person ?? '') }}" required="" />
                @error('contact_person')
                    <label id="contact_person-error" class="error" for="contact_person">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Location Textarea -->
        <div class="col-sm-12 col-md-12">
            <div class="form-group">
                <label for="location" class="form-label">Location</label>
                <textarea class="form-control" name="location" id="location" placeholder="Slaughter Location">{{ old('location', $slaughter->location ?? '') }}</textarea>
                @error('location')
                    <label id="location-error" class="error" for="location">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Latitude Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="latitude" class="form-label">Latitude</label>
                <input type="number" class="form-control" name="latitude" id="latitude" placeholder="Latitude (-90 to 90)"
                    step="0.00000001" min="-90" max="90"
                    value="{{ old('latitude', $slaughter->latitude ?? '') }}" />
                @error('latitude')
                    <label id="latitude-error" class="error" for="latitude">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Longitude Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="longitude" class="form-label">Longitude</label>
                <input type="number" class="form-control" name="longitude" id="longitude" placeholder="Longitude (-180 to 180)"
                    step="0.00000001" min="-180" max="180"
                    value="{{ old('longitude', $slaughter->longitude ?? '') }}" />
                @error('longitude')
                    <label id="longitude-error" class="error" for="longitude">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Phone Code Dropdown -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="phone_code" class="form-label">Phone Code</label>
                <select class="form-control" name="phone_code" id="phone_code">
                    <option value="">Select Phone Code</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->dial_code_with_plus }}" {{ old('phone_code', $slaughter->phone_code ?? '') == $country->dial_code_with_plus ? 'selected' : '' }}>
                            {{ $country->name }} ({{ $country->dial_code_with_plus }})
                        </option>
                    @endforeach
                </select>
                @error('phone_code')
                    <label id="phone_code-error" class="error" for="phone_code">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Mobile Number Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="mobile_number" class="form-label">Mobile Number <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="mobile_number" id="mobile_number" placeholder="Mobile Number"
                    value="{{ old('mobile_number', $slaughter->mobile_number ?? '') }}" required="" maxlength="20" />
                @error('mobile_number')
                    <label id="mobile_number-error" class="error" for="mobile_number">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
    </div>
</form>

<script>
    $('#slaughter_form').on('submit', function(e) {
        e.preventDefault();

        let formData = new FormData(this);
        let url = this.action;

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response) {
                if (response.success) {
                    swal({
                        title: response.message
                    }, function() {
                        $('#slaughter_form_modal').modal('hide');
                        $('#slaughter_table').DataTable().ajax.reload();
                    });
                }
            },
            error: function(xhr) {
                if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    $.each(errors, function(field, messages) {
                        let fieldElement = $('[name="' + field + '"]');
                        fieldElement.addClass('is-invalid');
                        fieldElement.after('<label class="error">' + messages[0] + '</label>');
                    });
                } else {
                    swal({
                        title: 'Error',
                        text: 'Something went wrong!',
                        icon: 'error'
                    });
                }
            }
        });
    });
</script>
