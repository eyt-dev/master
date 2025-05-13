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
        Setting::create([
            'domain' => 'admin.eyt.app',
            'admin_domain' => 'https://admin.eyt.app/admin',

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
            'domain' => 'add2care.eyt.app',
            'admin_domain' => 'https://add2care.eyt.app/admin',

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
    }
}
