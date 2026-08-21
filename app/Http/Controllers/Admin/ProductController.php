<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    public function __construct(protected ImageUploadService $imageUploadService)
    {
    }

    public function index(): View
    {
        $products = Product::with(['category', 'images'])->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function create(): View
    {
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    public function store(ProductRequest $request)
    {
        $product = Product::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'is_offer' => $request->has('is_offer'),
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $this->imageUploadService->store($image, 'products');
                $product->images()->create(['image_path' => $path]);
            }
        }

        Cache::forget(HomeController::CACHE_KEY);

        return redirect()->route('products.index')
            ->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product): View
    {
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'discount_price' => $request->discount_price,
            'stock' => $request->stock,
            'is_offer' => $request->has('is_offer'),
            'is_active' => $request->has('is_active'),
        ]);

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $this->imageUploadService->store($image, 'products');
                $product->images()->create(['image_path' => $path]);
            }
        }

        // Handle deletion of images
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $imageId) {
                $image = ProductImage::find($imageId);
                if ($image && $image->product_id == $product->id) {
                    $this->imageUploadService->delete($image->image_path);
                    $image->delete();
                }
            }
        }

        Cache::forget(HomeController::CACHE_KEY);

        return redirect()->route('products.index')
            ->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        // Delete images from storage
        foreach ($product->images as $image) {
            $this->imageUploadService->delete($image->image_path);
        }
        
        $product->delete();

        Cache::forget(HomeController::CACHE_KEY);

        return redirect()->route('products.index')
            ->with('success', 'Producto eliminado correctamente.');
    }
}
