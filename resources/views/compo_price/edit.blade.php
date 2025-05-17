<form
    action="{{ isset($compoPrice) && $compoPrice->id ? route('compo_price.update', $compoPrice->id) : route('compo_price.store') }}"
    method="POST"
    id="compo_price_form_edit"
    novalidate
    class="needs-validation"
    enctype="multipart/form-data">

    @csrf

    <div class="row row-cols-1 row-cols-md-2 g-3 pb-5">
        <!-- Component Select -->
        <div class="col">
            <label for="component" class="form-label fw-bold">{{ __('Component') }} <span
                    class="text-danger">*</span></label>
            <select name="component" id="component"
                    class="form-select select2 @error('component') is-invalid @enderror" required="">
                @php
                    $selectedComponent = old('component', isset($compoPrice) ? $compoPrice->component_id . '_' . $compoPrice->element_id : '');
                @endphp
                {{-- Debug --}}
                <small>🔍 Selected: {{ $selectedComponent }}</small>

                <option value="">{{ __('Select an option') }}</option>
                @foreach($components as $component)
                    @foreach($component->elements as $element)
                        <option value="{{ $component->id }}_{{ $element->id }}"
                            {{ $selectedComponent == $component->id . '_' . $element->id ? 'selected' : '' }}>
                            {{ $component->name }} - {{ $element->name }}
                        </option>
                    @endforeach
                @endforeach
            </select>
            @error('component')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Pricing Date -->
        <div class="col">
            <label for="pricing_date" class="form-label fw-bold">{{ __('Pricing Date') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="pricing_date" id="pricing_date"
                   class="form-control datepicker @error('pricing_date') is-invalid @enderror"
                   value="{{ old('pricing_date', isset($compoPrice->pricing_date) ? $compoPrice->pricing_date : '') }}"
                   required="">
            @error('pricing_date')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Price -->
        <div class="col">
            <label for="price" class="form-label fw-bold">{{ __('Price') }} <span
                    class="text-danger">*</span></label>
            <input type="text" name="price" id="price"
                   class="form-control @error('price') is-invalid @enderror"
                   placeholder="{{ __('Enter Price') }}"
                   value="{{ old('price', $compoPrice->price ?? '') }}"
                   required="">
            @error('price')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Description -->
        <div class="col">
            <label for="description" class="form-label fw-bold">{{ __('Description') }}</label>
            <input type="text" name="description" id="description"
                   class="form-control @error('description') is-invalid @enderror"
                   placeholder="{{ __('Enter Description') }}"
                   value="{{ old('description', $component->description ?? '') }}">
            @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" id="update_btn" type="submit">Save</button>
        <a href="{{ route('compo_price.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<script>
    $(document).ready(function () {
        // ===== Select2 init =====
        $('.select2').select2({
            width: '100%',
            allowClear: true,
            placeholder: "Select an option"
        });

        $('.datepicker').datepicker({
            startDate: '-3d',
            format: "yyyy-m-d",
            orientation: 'bottom',
            autoclose: true,
            todayHighlight: true
        });

    })
</script>
