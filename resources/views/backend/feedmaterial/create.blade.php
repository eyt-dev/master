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
                <label class="form-label">Type <span class="text-red">*</span></label>
                <div class="custom-control custom-radio">
                    <input type="radio" class="custom-control-input" id="type_pelleted" name="type" value="Pelleted feed"
                        {{ old('type', $feedmaterial->type ?? '') == 'Pelleted feed' ? 'checked' : '' }} required="">
                    <label class="custom-control-label" for="type_pelleted">Pelleted feed</label>
                </div>
                <div class="custom-control custom-radio">
                    <input type="radio" class="custom-control-input" id="type_feedstuff" name="type" value="Feed Stuff"
                        {{ old('type', $feedmaterial->type ?? '') == 'Feed Stuff' ? 'checked' : '' }} required="">
                    <label class="custom-control-label" for="type_feedstuff">Feed Stuff</label>
                </div>
                @error('type')
                    <label id="type-error" class="error" for="type">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('feedmaterial.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
