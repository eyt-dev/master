<form
    action="{{ isset($element) && $element->id ? route('element.update', $element->id) : route('element.store') }}"
    method="POST"
    id="element_form"
    novalidate=""
    class="needs-validation"
    enctype="multipart/form-data">

    @csrf

    <div class="row">

        <!-- Name Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="name" class="form-label">{{__('Name')}} <span class="text-red">*</span></label>
                <input type="text" class="form-control" name="name" id="name" placeholder="{{__('Enter Name')}}"
                       value="{{ old('name', $element->name ?? '') }}" required=""/>
                @error('name')
                <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Eu Code Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="eu_code" class="form-label">{{__('EU Code')}}</label>
                <input type="text" class="form-control" name="eu_code" id="eu_code"
                       placeholder="{{__('Enter EU Code')}}"
                       value="{{ old('eu_code', $element->eu_code ?? '') }}"/>
                @error('eu_code')
                <label id="eu_code-error" class="error" for="eu_code">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Synonym -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="synonym" class="form-label">{{__('Synonym')}}</label>
                <input type="text" class="form-control" name="synonym" id="synonym"
                       placeholder="{{__('Enter Synonym')}}"
                       value="{{ old('synonym', $element->synonym ?? '') }}"/>
                @error('synonym')
                <label id="alpha_3_code-error" class="error" for="alpha_3_code">{{ $message }}</label>
                @enderror
            </div>
        </div>

        {{-- Attachment --}}
        <div class="col-md-6 mb-3">
            <label for="image" class="form-label fw-bold">{{ __('Attachment') }}</label>
            <input type="file" name="attachment" id="attachment"
                   class="form-control
            @error('attachment') is-invalid @enderror">

            @if(isset($element) && $element->attachment)
                <div class="mt-2">
                    <p><strong>{{__('Current Attachment')}}</strong></p>
                    <p>{{__('Attachment Name')}} {{ basename($element->attachment) }}</p>
                    <p><a href="{{ \Illuminate\Support\Facades\Storage::url($element->attachment) }}"
                          target="_blank">{{__('Display File')}}</a></p>
                </div>
            @endif

            @error('attachment')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('element.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

</form>
