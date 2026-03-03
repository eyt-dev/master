<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contact;
use App\Models\CountryRegion;

class ContactSeeder extends Seeder
{
    public function run()
    {
        $country = CountryRegion::first();
        Contact::create([
            'name' => 'ACME Ltd',
            'formal_name' => 'ACME Limited',
            'vat_country_code' => $country ? $country->iso2 : 'DE',
            'vat_number' => '123456789',
            'email' => 'info@acme.test',
            'phone' => '+49123456789',
            'address1' => 'Street 1',
            'address2' => 'Suite 10',
            'postal_code' => '12345',
            'city' => 'Berlin',
            'created_by' => null,
        ]);
    }
}
