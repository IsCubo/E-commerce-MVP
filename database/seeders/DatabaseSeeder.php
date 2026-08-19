<?php

namespace Database\Seeders;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Safe to run more than once: every step here uses updateOrCreate
     * (keyed by a unique column) instead of create(), so re-seeding an
     * already-populated database won't throw duplicate-key errors.
     */
    public function run(): void
    {
        // Admin User
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Admin User',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // Default Settings
        $settings = [
            ['key' => 'whatsapp_number', 'value' => '1234567890'],
            ['key' => 'welcome_message', 'value' => 'Hola, me interesa este producto: '],
            ['key' => 'brand_name', 'value' => 'Beauty Shop'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }

        // Catalog: categories -> products (with placeholder images) -> combos
        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
            ComboSeeder::class,
        ]);
    }
}
