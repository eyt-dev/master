<form
    action="{{ isset($country) && $country->id ? route('country.update', $country->id) : route('country.store') }}"
    method="POST"
    id="country_form"
    novalidate=""
    class="needs-validation"
    enctype="multipart/form-data">

    @csrf

    <div class="row">
        {{-- Image --}}
        <div class="col-md-6 mb-3">
            <label for="image" class="form-label fw-bold">{{ __('Image') }}</label>
            <input type="file" name="image" id="image"
                   class="form-control @error('image') is-invalid @enderror">
            @if(isset($country) && $country->image)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $country->image) }}" alt="image" class="img-fluid"
                         style="max-height: 150px;">
                </div>
            @endif
            @error('image')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Name Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">{{__('Name')}} <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="{{__('Enter Name')}}"
                       value="{{ old('name', $country->name ?? '') }}" required=""/>
                @error('name')
                <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Alpha-2 Code Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="alpha_2_code" class="form-label">{{__('Alpha-2 Code')}}<span
                        class="text-red">*</span></label>
                <input type="text" class="form-control" name="alpha_2_code" id="alpha_2_code"
                       placeholder="{{__('Enter Alpha-2 Code')}}"
                       value="{{ old('alpha_2_code', $country->alpha_2_code ?? '') }}" required=""/>
                @error('alpha_2_code')
                <label id="alpha_2_code-error" class="error" for="alpha_2_code">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Alpha-3 Code -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="alpha_3_code" class="form-label">{{__('Alpha-3 Code')}}<span
                        class="text-red">*</span></label>
                <input type="text" class="form-control" name="alpha_3_code" id="alpha_3_code"
                       placeholder="{{__('Enter Alpha-3 Code')}}"
                       value="{{ old('alpha_3_code', $country->alpha_3_code ?? '') }}" required=""/>
                @error('alpha_3_code')
                <label id="alpha_3_code-error" class="error" for="alpha_3_code">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Dial Code -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="dial_code" class="form-label">{{__('Dial Code')}}<span class="text-red">*</span></label>
                <input type="text" class="form-control" name="dial_code" id="dial_code"
                       placeholder="{{__('Enter Dial Code')}}"
                       value="{{ old('dial_code', $country->dial_code ?? '') }}" required=""/>
                @error('dial_code')
                <label id="dial_code-error" class="error" for="dial_code">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6 col-md-6">
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('country.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

</form>
