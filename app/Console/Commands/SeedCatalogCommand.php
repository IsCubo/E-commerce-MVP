<?php

namespace App\Console\Commands;

use Database\Seeders\CategorySeeder;
use Database\Seeders\ComboSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Console\Command;

class SeedCatalogCommand extends Command
{
    /**
     * A single, specific command to (re)populate the demo catalog —
     * categories, products (with their placeholder images), and combos —
     * without touching the admin user or settings like a full `db:seed`
     * (via DatabaseSeeder) would.
     *
     * Safe to run any time, on any environment: every seeder underneath
     * uses updateOrCreate keyed by slug, so running this again just
     * refreshes the existing catalog instead of duplicating it.
     */
    protected $signature = 'catalog:seed';

    protected $description = 'Siembra (o actualiza) el catálogo de demo: categorías, productos con imágenes y combos';

    public function handle(): int
    {
        $this->call('db:seed', ['--class' => CategorySeeder::class]);
        $this->call('db:seed', ['--class' => ProductSeeder::class]);
        $this->call('db:seed', ['--class' => ComboSeeder::class]);

        $this->newLine();
        $this->info('Catálogo de demo listo: categorías, productos y combos sembrados/actualizados.');

        return self::SUCCESS;
    }
}
