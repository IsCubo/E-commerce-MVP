<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin User
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@admin.com',
            'password' => Hash::make('password'),
        ]);

        // Default Settings
        $settings = [
            ['key' => 'whatsapp_number', 'value' => '1234567890'],
            ['key' => 'welcome_message', 'value' => 'Hola, me interesa este producto: '],
            ['key' => 'brand_name', 'value' => 'Beauty Shop'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }
    }
}
