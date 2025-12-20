<form 
    method="POST"
    action="{{ isset($contact) && $contact->id 
        ? route('gcontact.update', ['username' => $siteSlug, 'contact' => $contact->id]) 
        : route('gcontact.store', ['username' => $siteSlug]) }}" 
    id="contact_form" 
    enctype="multipart/form-data" 
    novalidate 
    class="needs-validation">

    @csrf

    @if(isset($contact) && $contact->id)
        @method('PUT')
    @endif

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Name <span class="text-red">*</span></label>
                <input type="text" name="name" class="form-control" placeholder="Name" 
                    value="{{ old('name', $contact->name ?? '') }}" required>
                @error('name')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Formal Name</label>
                <input type="text" name="formal_name" class="form-control" placeholder="Formal Name" 
                    value="{{ old('formal_name', $contact->formal_name ?? '') }}">
                @error('formal_name')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>VAT Country</label>
                <select class="form-control" id="vat_country_select" onchange="updateVatCode()">
                    <option value="">Select Country</option>
                    @if(isset($countries) && $countries)
                        @foreach ($countries as $country)
                            <option value="{{ $country->iso2 }}" data-iso2="{{ $country->iso2 }}" {{ old('vat_country_code', $contact->vat_country_code ?? '') == $country->iso2 ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    @endif
                </select>
                @error('vat_country_code')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                <label>VAT Code</label>
                <input type="text" id="vat_code_input" name="vat_country_code" class="form-control" placeholder="DE" maxlength="4"
                    value="{{ old('vat_country_code', $contact->vat_country_code ?? '') }}">
                @error('vat_country_code')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-3">
            <div class="form-group">
                <label>VAT Number</label>
                <input type="text" name="vat_number" class="form-control" placeholder="123456789" 
                    value="{{ old('vat_number', $contact->vat_number ?? '') }}">
                @error('vat_number')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control-file">
                @if(!empty($contact->image))
                    <div class="mt-2"><img src="{{ asset('storage/contacts/' . $contact->image) }}" alt="image" style="max-height:70px"></div>
                @endif
                @error('image')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <script>
        function updateVatCode() {
            const select = document.getElementById('vat_country_select');
            const codeInput = document.getElementById('vat_code_input');
            if (!select || !codeInput) return;
            codeInput.value = select.value || '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // auto-set code on load if selection exists
            updateVatCode();
            const select = document.getElementById('vat_country_select');
            if (select) select.addEventListener('change', updateVatCode);
        });
    </script>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" placeholder="Email" 
                    value="{{ old('email', $contact->email ?? '') }}">
                @error('email')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>Phone</label>
                <input type="text" name="phone" class="form-control" placeholder="Phone" 
                    value="{{ old('phone', $contact->phone ?? '') }}">
                @error('phone')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Address 1</label>
                <input type="text" name="address1" class="form-control" placeholder="Address 1" 
                    value="{{ old('address1', $contact->address1 ?? '') }}">
                @error('address1')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Address 2</label>
                <input type="text" name="address2" class="form-control" placeholder="Address 2" 
                    value="{{ old('address2', $contact->address2 ?? '') }}">
                @error('address2')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-6">
            <div class="form-group">
                <label>Postal Code</label>
                <input type="text" name="postal_code" class="form-control" placeholder="Postal Code" 
                    value="{{ old('postal_code', $contact->postal_code ?? '') }}">
                @error('postal_code')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>

        <div class="col-sm-6">
            <div class="form-group">
                <label>City</label>
                <input type="text" name="city" class="form-control" placeholder="City" 
                    value="{{ old('city', $contact->city ?? '') }}">
                @error('city')
                    <label class="error">{{ $message }}</label>
                @enderror
            </div>
        </div>
    </div>

    <div class="card-footer">
        <button class="btn btn-primary" type="submit">Save</button>
        <a href="{{ route('gcontact.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
