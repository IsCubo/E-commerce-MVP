<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $query = Product::where('is_active', true)->with('category', 'images');

        if ($request->has('category') && $request->category != '') {
            $category = Category::where('slug', $request->category)->first();
            if ($category) {
                $query->where('category_id', $category->id);
            }
        }

        if ($request->has('search') && $request->search != '') {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $products = $query->latest()->paginate(12)->withQueryString();
        $categories = Category::has('products')->get();

        return view('shop.index', compact('products', 'categories'));
    }

    public function show($slug): View
    {
        $product = Product::where('slug', $slug)
                          ->where('is_active', true)
                          ->with(['category', 'images'])
                          ->firstOrFail();

        $relatedProducts = Product::where('category_id', $product->category_id)
                                  ->where('id', '!=', $product->id)
                                  ->where('is_active', true)
                                  ->with('images', 'category')
                                  ->take(4)
                                  ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}
