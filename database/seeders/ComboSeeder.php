<?php

namespace Database\Seeders;

use App\Models\Combo;
use App\Models\Product;
use Database\Seeders\Concerns\GeneratesPlaceholderImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ComboSeeder extends Seeder
{
    use GeneratesPlaceholderImages;

    private const IMAGE_BG = '#F6EFE3';

    /**
     * Seed a few bundled combos built from products created by ProductSeeder
     * (which must run first). Bundle price is set below the sum of the
     * individual product prices to make the combo a genuine deal.
     */
    public function run(): void
    {
        $combos = [
            [
                'name' => 'Ritual Facial Completo',
                'description' => 'Sérum, hidratante con SPF y tónico para una rutina facial completa a un precio especial.',
                'price' => 99000,
                'products' => [
                    'serum-facial-de-acido-hialuronico',
                    'crema-hidratante-de-dia-spf30',
                    'tonico-facial-purificante',
                ],
            ],
            [
                'name' => 'Set de Maquillaje Esencial',
                'description' => 'Base líquida matte, paleta de sombras nude y delineador waterproof para un look completo.',
                'price' => 79000,
                'products' => [
                    'base-de-maquillaje-liquida-matte',
                    'paleta-de-sombras-nude',
                    'delineador-de-ojos-waterproof',
                ],
            ],
            [
                'name' => 'Combo Cabello Nutrido',
                'description' => 'Shampoo reparador de keratina y aceite capilar nutritivo para un cabello sano y brillante.',
                'price' => 47000,
                'products' => [
                    'shampoo-reparador-de-keratina',
                    'aceite-capilar-nutritivo',
                ],
            ],
        ];

        foreach ($combos as $data) {
            $slug = Str::slug($data['name']);

            $combo = Combo::updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'is_active' => true,
                ]
            );

            $productIds = Product::whereIn('slug', $data['products'])->pluck('id');
            $combo->products()->sync($productIds->mapWithKeys(fn ($id) => [$id => ['quantity' => 1]]));

            $imagePath = 'combos/' . $slug . '.svg';
            Storage::disk('public')->put($imagePath, $this->placeholderImage($data['name'], self::IMAGE_BG));
            $combo->update(['image_path' => $imagePath]);
        }

        // Limpia de storage/app/public/combos cualquier imagen que ya no
        // esté referenciada por ningún combo (evita huérfanos si se vuelve
        // a sembrar tras renombrar/quitar combos del catálogo).
        $this->pruneOrphanedImages('combos', Combo::whereNotNull('image_path')->pluck('image_path')->all());
    }
}
