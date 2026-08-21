<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Database\Seeders\Concerns\GeneratesPlaceholderImages;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    use GeneratesPlaceholderImages;

    /**
     * Pastel background per category for the generated placeholder images,
     * so products are visually grouped by category at a glance.
     */
    private const CATEGORY_COLORS = [
        'maquillaje' => '#FCE7EB',
        'skin-care' => '#E8F3EE',
        'cabello' => '#F5EFE0',
        'perfumeria' => '#EFE7F5',
        'cuidado-personal' => '#EAF1F7',
    ];

    /**
     * Seed a realistic catalog: a mix of products with a plain discount
     * (discount_price set, not featured as an "oferta"), products flagged
     * as an offer (discount_price + is_offer, shown in the home hero and
     * with the OFERTA badge), and regular full-price products.
     */
    public function run(): void
    {
        $products = [
            [
                'category' => 'maquillaje',
                'name' => 'Base de Maquillaje Líquida Matte',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: cobertura media a alta, acabado matte, larga duración.\nModo de uso: aplicar con esponja húmeda o brocha desde el centro del rostro hacia afuera.\nIngredientes: ácido hialurónico, vitamina E.",
                'price' => 45000,
                'discount_price' => 36000,
                'is_offer' => true,
                'stock' => 18,
            ],
            [
                'category' => 'maquillaje',
                'name' => 'Paleta de Sombras Nude',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: 12 tonos nude altamente pigmentados, acabados mate y shimmer.\nModo de uso: aplicar con brocha o dedos y difuminar para un degradado natural.\nIngredientes: talco cosmético, mica, pigmentos minerales.",
                'price' => 38000,
                'stock' => 25,
            ],
            [
                'category' => 'maquillaje',
                'name' => 'Delineador de Ojos Waterproof',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: trazo preciso, resistente al agua y de larga duración.\nModo de uso: aplicar sobre el párpado siguiendo la línea de las pestañas.\nIngredientes: cera de carnauba, pigmentos negros.",
                'price' => 15000,
                'discount_price' => 12000,
                'stock' => 4,
            ],
            [
                'category' => 'maquillaje',
                'name' => 'Rubor en Polvo Compacto',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: color buildable de acabado natural, ilumina el rostro.\nModo de uso: aplicar con brocha sobre los pómulos y difuminar hacia las sienes.\nIngredientes: talco cosmético, pigmentos minerales, vitamina E.",
                'price' => 22000,
                'stock' => 0,
            ],
            [
                'category' => 'skin-care',
                'name' => 'Sérum Facial de Ácido Hialurónico',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: hidratación profunda, reduce líneas de expresión, efecto plump.\nZonas de aplicación: rostro y cuello.\nIngredientes: ácido hialurónico, vitamina B5.",
                'price' => 55000,
                'discount_price' => 44000,
                'is_offer' => true,
                'stock' => 12,
            ],
            [
                'category' => 'skin-care',
                'name' => 'Crema Hidratante de Día SPF30',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: hidratación 24h, protección solar SPF30, textura ligera.\nZonas de aplicación: rostro.\nIngredientes: filtros solares, niacinamida, glicerina.",
                'price' => 42000,
                'stock' => 30,
            ],
            [
                'category' => 'skin-care',
                'name' => 'Tónico Facial Purificante',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: equilibra el pH, minimiza poros, refresca la piel.\nZonas de aplicación: rostro, después de la limpieza.\nIngredientes: agua de rosas, extracto de hamamelis.",
                'price' => 28000,
                'discount_price' => 23000,
                'stock' => 8,
            ],
            [
                'category' => 'cabello',
                'name' => 'Shampoo Reparador de Keratina',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: repara puntas abiertas, aporta brillo y suavidad.\nModo de uso: aplicar sobre cabello húmedo, masajear y enjuagar.\nIngredientes: keratina hidrolizada, aceite de argán.",
                'price' => 32000,
                'stock' => 15,
            ],
            [
                'category' => 'cabello',
                'name' => 'Aceite Capilar Nutritivo',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: nutre en profundidad, controla el frizz, aporta brillo.\nModo de uso: aplicar unas gotas en puntas húmedas o secas.\nIngredientes: aceite de argán, aceite de coco, vitamina E.",
                'price' => 26000,
                'discount_price' => 20000,
                'is_offer' => true,
                'stock' => 3,
            ],
            [
                'category' => 'perfumeria',
                'name' => 'Perfume Floral de Larga Duración',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: fragancia floral de larga duración, notas de jazmín y vainilla.\nModo de uso: aplicar en puntos de pulso (muñecas, cuello).\nIngredientes: alcohol desnaturalizado, fragancia, agua.",
                'price' => 65000,
                'stock' => 20,
            ],
            [
                'category' => 'cuidado-personal',
                'name' => 'Crema Corporal Hidratante',
                'description' => "Lo que tienes que saber de este producto\nBeneficios: hidratación profunda, piel suave hasta por 48h.\nZonas de aplicación: cuerpo.\nIngredientes: manteca de karité, glicerina, vitamina E.",
                'price' => 24000,
                'discount_price' => 19000,
                'stock' => 10,
            ],
        ];

        $categoryIds = Category::pluck('id', 'slug');

        foreach ($products as $data) {
            $slug = Str::slug($data['name']);
            $categoryId = $categoryIds[$data['category']] ?? null;

            $product = Product::updateOrCreate(
                ['slug' => $slug],
                [
                    'category_id' => $categoryId,
                    'name' => $data['name'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'discount_price' => $data['discount_price'] ?? null,
                    'stock' => $data['stock'] ?? 0,
                    'is_offer' => $data['is_offer'] ?? false,
                    'is_active' => true,
                ]
            );

            // Regenerate the placeholder image on every run so re-seeding
            // never leaves orphaned image rows or files behind.
            $product->images()->delete();

            $imagePath = 'products/' . $slug . '.svg';
            $bgColor = self::CATEGORY_COLORS[$data['category']] ?? '#F3F4F6';
            Storage::disk('public')->put($imagePath, $this->placeholderImage($data['name'], $bgColor));

            ProductImage::create([
                'product_id' => $product->id,
                'image_path' => $imagePath,
            ]);
        }

        // Limpia de storage/app/public/products cualquier imagen que ya no
        // esté referenciada por ningún producto (evita huérfanos si se
        // vuelve a sembrar tras renombrar/quitar productos del catálogo).
        $this->pruneOrphanedImages('products', ProductImage::pluck('image_path')->all());
    }
}
