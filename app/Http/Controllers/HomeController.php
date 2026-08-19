<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Combo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        // Fetch specific data for sections
        $offers = Product::where('is_offer', true)
                        ->where('is_active', true)
                        ->with('images')
                        ->latest()
                        ->take(6)
                        ->get();

        $latestProducts = Product::where('is_active', true)
                                ->with('images', 'category')
                                ->latest()
                                ->take(8)
                                ->get();

        $combos = Combo::where('is_active', true)
                       ->latest()
                       ->take(3)
                       ->get();

        $categories = Category::has('products')->get();

        return view('home', compact('offers', 'latestProducts', 'combos', 'categories'));
    }
}
