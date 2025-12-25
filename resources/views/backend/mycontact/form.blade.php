@extends('layouts.backend')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-6">
            <h1>@if($contact->exists) Edit My Contact @else Create My Contact @endif</h1>
        </div>
        <div class="col-md-6 text-right">
            <a href="{{ route('gmycontact.index') }}" class="btn btn-secondary">Back</a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            @if ($contact->exists)
                <form method="POST" action="{{ route('gmycontact.update', $contact->id) }}" enctype="multipart/form-data">
            @else
                <form method="POST" action="{{ route('gmycontact.store') }}" enctype="multipart/form-data">
            @endif
                @csrf

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="name">Name *</label>
                            <input name="name" value="{{ old('name', $contact->name) }}" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label for="formal_name">Formal Name</label>
                            <input name="formal_name" value="{{ old('formal_name', $contact->formal_name) }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="vat_country_code">VAT Country</label>
                            <select id="vat_country" name="vat_country_code" class="form-control">
                                <option value="">Select one</option>
                                @foreach ($countries as $country)
                                <option value="{{ $country->iso2 }}" @if(old('vat_country_code', $contact->vat_country_code) == $country->iso2) selected @endif>
                                    {{ $country->name }} ({{ $country->iso2 }})
                                </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="vat_number">VAT Number</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span id="vatPrefix" class="input-group-text">{{ old('vat_country_code', $contact->vat_country_code) }}</span>
                                </div>
                                <input name="vat_number" value="{{ old('vat_number', $contact->vat_number) }}" class="form-control" id="vat_number" placeholder="e.g. 123456789">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input name="email" value="{{ old('email', $contact->email) }}" class="form-control" type="email">
                        </div>

                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input name="phone" value="{{ old('phone', $contact->phone) }}" class="form-control">
                        </div>

                        <div class="form-group">
                            <label for="image">Image</label>
                            <input type="file" name="image" class="form-control-file">
                            @if($contact->image)
                                <div class="mt-2"><img src="{{ asset('storage/my_contacts/' . $contact->image) }}" alt="image" style="max-height:70px"></div>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="address1">Address 1</label>
                            <input name="address1" value="{{ old('address1', $contact->address1) }}" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="address2">Address 2</label>
                            <input name="address2" value="{{ old('address2', $contact->address2) }}" class="form-control">
                        </div>
                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="postal_code">Postal Code</label>
                                <input name="postal_code" value="{{ old('postal_code', $contact->postal_code) }}" class="form-control">
                            </div>
                            <div class="form-group col-md-6">
                                <label for="city">City</label>
                                <input name="city" value="{{ old('city', $contact->city) }}" class="form-control">
                            </div>
                        </div>

                    </div>
                </div>


                <div class="form-group mt-3">
                    <button class="btn btn-success">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('footer_js')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var countrySelect = document.getElementById('vat_country');
        var vatPrefix = document.getElementById('vatPrefix');
        if (!countrySelect) return;
        countrySelect.addEventListener('change', function () {
            vatPrefix.textContent = this.value;
        });
    });
</script>
@endsection