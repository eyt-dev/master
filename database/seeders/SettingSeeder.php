<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
use App\Models\Theme;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Theme::firstOrCreate(
            ['id' => 1],
            [
                'name' => 'theme1',
                'created_by' => 1
            ]
        );
        Theme::firstOrCreate(
            ['id' => 2],
            [
                'name' => 'theme2',
                'created_by' => 2
            ]
        );
        Setting::create([
            'domain' => config('domains.main_domain'),
            'admin_domain' => config('domains.admin_subdomain'),

            'dark_logo' => 'dark-logo.png',
            'light_logo' => 'light-logo.png',
            'footer_logo' => 'footer-logo.png',
            'favicon' => 'favicon.ico',

            'primary_text_color' => '#000000',
            'secondary_text_color' => '#666666',

            'primary_button_background' => '#007bff',
            'secondary_button_background' => '#6c757d',
            'primary_button_text_color' => '#ffffff',
            'secondary_button_text_color' => '#ffffff',

            'created_by' => 1,
            'theme' => 1,
        ]);

        Setting::create([
            'domain' => 'eyt.li',
            'admin_domain' => 'admin.eyt.li',

            'dark_logo' => 'dark-logo.png',
            'light_logo' => 'light-logo.png',
            'footer_logo' => 'footer-logo.png',
            'favicon' => 'favicon.ico',

            'primary_text_color' => '#000000',
            'secondary_text_color' => '#666666',

            'primary_button_background' => '#007bff',
            'secondary_button_background' => '#6c757d',
            'primary_button_text_color' => '#ffffff',
            'secondary_button_text_color' => '#ffffff',

            'created_by' => 2,
            'theme' => 2,
        ]);
    }
}
