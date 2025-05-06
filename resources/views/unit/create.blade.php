<form
    action="{{ isset($unit) && $unit->id ? route('unit.update', $unit->id) : route('unit.store') }}"
    method="POST"
    id="unit_form"
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
                       value="{{ old('name', $unit->name ?? '') }}" required=""/>
                @error('name')
                <label id="name-error" class="error" for="name">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <!-- Symbol Textbox -->
        <div class="col-sm-6 col-md-6">
            <div class="form-group">
                <label for="symbol" class="form-label">{{__('Symbol')}}<span
                        class="text-red">*</span></label>
                <input type="text" class="form-control" name="symbol" id="symbol"
                       placeholder="{{__('Enter Symbol')}}"
                       value="{{ old('symbol', $unit->symbol ?? '') }}" required=""/>
                @error('symbol')
                <label id="symbol" class="error" for="symbol">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('unit.index') }}" class="btn btn-secondary">Cancel</a>
    </div>

</form>
