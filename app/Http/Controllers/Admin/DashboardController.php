<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Combo;
use App\Models\Product;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        // Stats for dashboard
        $stats = [
            'products' => Product::count(),
            'offers' => Product::where('is_offer', true)->count(),
            'combos' => Combo::where('is_active', true)->count(),
            'categories' => Category::count(),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
