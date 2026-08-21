<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Combo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Clave de caché del home. Pública para que los controladores de admin
     * que tocan productos/combos/categorías puedan invalidarla al guardar.
     */
    public const CACHE_KEY = 'home.index';

    public function index(): View
    {
        // Estas 4 queries se repetían en cada visita al home; se cachean
        // unos minutos (no son datos que cambien segundo a segundo) para no
        // pegarle a la BD en cada request. Los controladores de admin que
        // crean/editan/borran productos o combos invalidan esta clave.
        $data = Cache::remember(self::CACHE_KEY, now()->addMinutes(10), function () {
            return [
                'offers' => Product::where('is_offer', true)
                    ->where('is_active', true)
                    ->with('images')
                    ->latest()
                    ->take(6)
                    ->get(),

                'latestProducts' => Product::where('is_active', true)
                    ->with('images', 'category')
                    ->latest()
                    ->take(8)
                    ->get(),

                'combos' => Combo::where('is_active', true)
                    ->latest()
                    ->take(3)
                    ->get(),

                'categories' => Category::has('products')->get(),
            ];
        });

        return view('home', $data);
    }
}
