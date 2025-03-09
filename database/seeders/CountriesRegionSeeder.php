<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CountriesRegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countries = [
            // Asia
            ['name' => 'Afghanistan', 'region' => 'Asia'],
            ['name' => 'Armenia', 'region' => 'Asia'],
            ['name' => 'Azerbaijan', 'region' => 'Asia'],
            ['name' => 'Bahrain', 'region' => 'Asia'],
            ['name' => 'Bangladesh', 'region' => 'Asia'],
            ['name' => 'Bhutan', 'region' => 'Asia'],
            ['name' => 'Brunei', 'region' => 'Asia'],
            ['name' => 'Cambodia', 'region' => 'Asia'],
            ['name' => 'China', 'region' => 'Asia'],
            ['name' => 'India', 'region' => 'Asia'],
            ['name' => 'Indonesia', 'region' => 'Asia'],
            ['name' => 'Iran', 'region' => 'Asia'],
            ['name' => 'Iraq', 'region' => 'Asia'],
            ['name' => 'Israel', 'region' => 'Asia'],
            ['name' => 'Japan', 'region' => 'Asia'],
            ['name' => 'Jordan', 'region' => 'Asia'],
            ['name' => 'Kazakhstan', 'region' => 'Asia'],
            ['name' => 'Kuwait', 'region' => 'Asia'],
            ['name' => 'Kyrgyzstan', 'region' => 'Asia'],
            ['name' => 'Laos', 'region' => 'Asia'],
            ['name' => 'Lebanon', 'region' => 'Asia'],
            ['name' => 'Malaysia', 'region' => 'Asia'],
            ['name' => 'Maldives', 'region' => 'Asia'],
            ['name' => 'Mongolia', 'region' => 'Asia'],
            ['name' => 'Myanmar', 'region' => 'Asia'],
            ['name' => 'Nepal', 'region' => 'Asia'],
            ['name' => 'North Korea', 'region' => 'Asia'],
            ['name' => 'Oman', 'region' => 'Asia'],
            ['name' => 'Pakistan', 'region' => 'Asia'],
            ['name' => 'Philippines', 'region' => 'Asia'],
            ['name' => 'Qatar', 'region' => 'Asia'],
            ['name' => 'Saudi Arabia', 'region' => 'Asia'],
            ['name' => 'Singapore', 'region' => 'Asia'],
            ['name' => 'South Korea', 'region' => 'Asia'],
            ['name' => 'Sri Lanka', 'region' => 'Asia'],
            ['name' => 'Syria', 'region' => 'Asia'],
            ['name' => 'Taiwan', 'region' => 'Asia'],
            ['name' => 'Thailand', 'region' => 'Asia'],
            ['name' => 'Turkey', 'region' => 'Asia'],
            ['name' => 'United Arab Emirates', 'region' => 'Asia'],
            ['name' => 'Uzbekistan', 'region' => 'Asia'],
            ['name' => 'Vietnam', 'region' => 'Asia'],
            ['name' => 'Yemen', 'region' => 'Asia'],

            // Europe
            ['name' => 'United Kingdom', 'region' => 'Europe'],
            ['name' => 'Germany', 'region' => 'Europe'],
            ['name' => 'France', 'region' => 'Europe'],
            ['name' => 'Italy', 'region' => 'Europe'],
            ['name' => 'Spain', 'region' => 'Europe'],
            ['name' => 'Netherlands', 'region' => 'Europe'],
            ['name' => 'Sweden', 'region' => 'Europe'],
            ['name' => 'Switzerland', 'region' => 'Europe'],
            ['name' => 'Belgium', 'region' => 'Europe'],
            ['name' => 'Poland', 'region' => 'Europe'],
            ['name' => 'Norway', 'region' => 'Europe'],
            ['name' => 'Denmark', 'region' => 'Europe'],
            ['name' => 'Ireland', 'region' => 'Europe'],
            ['name' => 'Austria', 'region' => 'Europe'],
            ['name' => 'Finland', 'region' => 'Europe'],
            
            // North America
            ['name' => 'United States', 'region' => 'North America'],
            ['name' => 'Canada', 'region' => 'North America'],
            ['name' => 'Mexico', 'region' => 'North America'],
            ['name' => 'Guatemala', 'region' => 'North America'],
            ['name' => 'Cuba', 'region' => 'North America'],

            // South America
            ['name' => 'Brazil', 'region' => 'South America'],
            ['name' => 'Argentina', 'region' => 'South America'],
            ['name' => 'Colombia', 'region' => 'South America'],
            ['name' => 'Chile', 'region' => 'South America'],
            ['name' => 'Peru', 'region' => 'South America'],

            // Africa
            ['name' => 'South Africa', 'region' => 'Africa'],
            ['name' => 'Nigeria', 'region' => 'Africa'],
            ['name' => 'Egypt', 'region' => 'Africa'],
            ['name' => 'Kenya', 'region' => 'Africa'],
            ['name' => 'Ghana', 'region' => 'Africa'],

            // Oceania
            ['name' => 'Australia', 'region' => 'Oceania'],
            ['name' => 'New Zealand', 'region' => 'Oceania'],
            ['name' => 'Fiji', 'region' => 'Oceania'],
            ['name' => 'Papua New Guinea', 'region' => 'Oceania'],
            ['name' => 'Samoa', 'region' => 'Oceania']
        ];

        // Insert data into the database
        DB::table('countries_region')->insert($countries);
    }
}
