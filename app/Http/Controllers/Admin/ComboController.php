<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HomeController;
use App\Http\Requests\Admin\ComboRequest;
use App\Models\Combo;
use App\Models\Product;
use App\Services\ImageUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ComboController extends Controller
{
    public function __construct(protected ImageUploadService $imageUploadService)
    {
    }

    public function index(): View
    {
        $combos = Combo::latest()->paginate(10);
        return view('admin.combos.index', compact('combos'));
    }

    public function create(): View
    {
        $products = Product::where('is_active', true)->get();
        return view('admin.combos.create', compact('products'));
    }

    public function store(ComboRequest $request)
    {
        $combo = Combo::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->hasFile('image')) {
            $path = $this->imageUploadService->store($request->file('image'), 'combos');
            $combo->update(['image_path' => $path]);
        }

        // Sync products
        $syncData = [];
        foreach ($request->products as $product) {
            $syncData[$product['id']] = ['quantity' => $product['quantity']];
        }
        $combo->products()->sync($syncData);

        Cache::forget(HomeController::CACHE_KEY);

        return redirect()->route('combos.index')
            ->with('success', 'Combo creado correctamente.');
    }

    public function edit(Combo $combo): View
    {
        $products = Product::where('is_active', true)->get();
        $combo->load('products');
        return view('admin.combos.edit', compact('combo', 'products'));
    }

    public function update(ComboRequest $request, Combo $combo)
    {
        $combo->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'is_active' => $request->has('is_active'),
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            $this->imageUploadService->delete($combo->image_path);
            $path = $this->imageUploadService->store($request->file('image'), 'combos');
            $combo->update(['image_path' => $path]);
        }

        // Sync products
        $syncData = [];
        foreach ($request->products as $product) {
            $syncData[$product['id']] = ['quantity' => $product['quantity']];
        }
        $combo->products()->sync($syncData);

        Cache::forget(HomeController::CACHE_KEY);

        return redirect()->route('combos.index')
            ->with('success', 'Combo actualizado correctamente.');
    }

    public function destroy(Combo $combo)
    {
        $this->imageUploadService->delete($combo->image_path);
        $combo->delete();

        Cache::forget(HomeController::CACHE_KEY);

        return redirect()->route('combos.index')
            ->with('success', 'Combo eliminado correctamente.');
    }
}
