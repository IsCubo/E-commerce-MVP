<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ComboRequest;
use App\Models\Combo;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ComboController extends Controller
{
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
            $path = $request->file('image')->store('combos', 'public');
            $combo->update(['image_path' => $path]);
        }

        // Sync products
        $syncData = [];
        foreach ($request->products as $product) {
            $syncData[$product['id']] = ['quantity' => $product['quantity']];
        }
        $combo->products()->sync($syncData);

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
            if ($combo->image_path) {
                Storage::disk('public')->delete($combo->image_path);
            }
            $path = $request->file('image')->store('combos', 'public');
            $combo->update(['image_path' => $path]);
        }

        // Sync products
        $syncData = [];
        foreach ($request->products as $product) {
            $syncData[$product['id']] = ['quantity' => $product['quantity']];
        }
        $combo->products()->sync($syncData);

        return redirect()->route('combos.index')
            ->with('success', 'Combo actualizado correctamente.');
    }

    public function destroy(Combo $combo)
    {
        if ($combo->image_path) {
            Storage::disk('public')->delete($combo->image_path);
        }
        $combo->delete();

        return redirect()->route('combos.index')
            ->with('success', 'Combo eliminado correctamente.');
    }
}
