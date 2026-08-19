<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Seed the product categories.
     *
     * Uses updateOrCreate keyed by slug so this is safe to run more than
     * once against a database that already has data (e.g. a plain
     * `php artisan db:seed` on top of an existing install).
     */
    public function run(): void
    {
        $categories = [
            'Maquillaje',
            'Skin Care',
            'Cabello',
            'Perfumería',
            'Cuidado Personal',
        ];

        foreach ($categories as $name) {
            Category::updateOrCreate(
                ['slug' => Str::slug($name)],
                ['name' => $name]
            );
        }
    }
}
