<form 
    method="POST"
    action="{{ isset($contact) && $contact->id 
        ? route('contacts.update', ['username' => $siteSlug, 'mycontact' => $contact->id]) 
        : route('contacts.store', ['username' => $siteSlug]) }}" 
    id="mycontact_form" 
    enctype="multipart/form-data" 
    novalidate 
    class="needs-validation">

    @csrf

    @if(isset($contact) && $contact->id)
        @method('PUT')
    @endif

    <div class="row">

        <div class="col-sm-6">
            <div class="form-group" style="position:relative;">
                <label>Name <span class="text-red">*</span></label>
                <input id="contact_name" type="text" name="name" class="form-control" placeholder="Name" 
                    value="{{ old('name', $contact->name ?? '') }}" required>
                <div id="name_suggestions" class="list-group" style="position:absolute; left:0; right:0; width:100%; z-index:9999; display:none;"></div>
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
                            @php $iso = $country->iso2 ?? strtoupper(substr($country->name,0,2)); @endphp
                            <option value="{{ $iso }}" data-iso2="{{ $iso }}" {{ old('vat_country_code', $contact->vat_country_code ?? '') == $iso ? 'selected' : '' }}>
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
                    value="{{ old('vat_country_code', $contact->vat_country_code ?? '') }}" readonly>
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
                <div id="selected_contact_image_preview">
                @if(!empty($contact->image))
                    <div class="mt-2"><img src="{{ asset('storage/my_contacts/' . $contact->image) }}" alt="image" style="max-height:70px"></div>
                @endif
                </div>
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
            const selectedOption = select.options[select.selectedIndex];
            const vatCode = (selectedOption && selectedOption.dataset && selectedOption.dataset.iso2) ? selectedOption.dataset.iso2 : select.value;
            codeInput.value = vatCode || '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            // auto-set code on load if selection exists
            updateVatCode();
            const select = document.getElementById('vat_country_select');
            if (select) select.addEventListener('change', updateVatCode);
        });

        // Autocomplete for contacts
        (function(){
            var $search = $('#contact_name');
            if (!$search.length) return;
            var $suggestions = $('#name_suggestions');
            var contactImageBase = "{{ asset('storage/contacts') }}";

            $search.on('input', function() {
                var q = $(this).val();
                if (!q || q.length < 2) { $suggestions.empty().hide(); return; }

                $.get("{{ route('global_contacts.search', ['username' => $siteSlug]) }}", { q: q }, function(data) {
                    $suggestions.empty();
                    if (!data || !data.length) { $suggestions.hide(); return; }

                    data.forEach(function(item) {
                        var label = item.name + (item.email ? ' — ' + item.email : '') + (item.phone ? ' — ' + item.phone : '');
                        var safe = $('<div>').text(label).html();
                        var a = $('<a href="#" class="list-group-item list-group-item-action contact-suggestion">').html(safe);
                        a.data('item', item);
                        $suggestions.append(a);
                    });

                    $suggestions.show();
                });
            });

            $(document).on('click', '.contact-suggestion', function(e) {
                e.preventDefault();
                var item = $(this).data('item');

                $('input[name="name"]').val(item.name || '');
                $('input[name="formal_name"]').val(item.formal_name || '');
                $('#vat_code_input').val(item.vat_country_code || '');
                $('input[name="vat_number"]').val(item.vat_number || '');
                $('input[name="email"]').val(item.email || '');
                $('input[name="phone"]').val(item.phone || '');
                $('input[name="address1"]').val(item.address1 || '');
                $('input[name="address2"]').val(item.address2 || '');
                $('input[name="postal_code"]').val(item.postal_code || '');
                $('input[name="city"]').val(item.city || '');

                if (item.image) {
                    $('#selected_contact_image_preview').html('<div class="mt-2"><img src="' + contactImageBase + '/' + item.image + '" alt="image" style="max-height:70px"></div>');
                } else {
                    $('#selected_contact_image_preview').empty();
                }

                $suggestions.empty().hide();
            });

            // Hide suggestions when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('#contact_name, #name_suggestions').length) {
                    $suggestions.hide();
                }
            });

            // Handle keyboard navigation
            $search.on('keydown', function(e) {
                if (e.key === 'Escape') {
                    $suggestions.hide();
                } else if (e.key === 'ArrowDown' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    var $items = $suggestions.find('.contact-suggestion');
                    if (!$items.length) return;
                    
                    var $active = $suggestions.find('.active');
                    if (e.key === 'ArrowDown') {
                        if (!$active.length) {
                            $items.first().addClass('active');
                        } else {
                            $active.removeClass('active').next('.contact-suggestion').addClass('active');
                        }
                    } else {
                        if (!$active.length) {
                            $items.last().addClass('active');
                        } else {
                            $active.removeClass('active').prev('.contact-suggestion').addClass('active');
                        }
                    }
                } else if (e.key === 'Enter') {
                    var $active = $suggestions.find('.contact-suggestion.active');
                    if ($active.length) {
                        e.preventDefault();
                        $active.trigger('click');
                    }
                }
            });
        })();
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
        <a href="{{ route('contacts.index', ['username' => $siteSlug]) }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>