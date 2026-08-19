@extends('layouts.app')

@section('meta_title', 'Tienda - ' . ($globalSettings['brand_name'] ?? 'BeautyShop'))
@section('meta_description', 'Explora el catálogo completo de productos de belleza de ' . ($globalSettings['brand_name'] ?? 'BeautyShop') . '.')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Mobile Filters Toggle -->
        <button type="button" data-collapse-toggle="shop-filters" aria-controls="shop-filters" aria-expanded="false"
                class="md:hidden flex items-center justify-between w-full p-4 text-left font-semibold text-gray-900 dark:text-white bg-white dark:bg-gray-800 rounded-lg shadow-md border border-gray-100 dark:border-gray-700">
            <span class="flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                </svg>
                Filtros
            </span>
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
        </button>

        <!-- Sidebar Filters -->
        <aside id="shop-filters" class="hidden md:block w-full md:w-1/4">
            <div class="bg-white rounded-lg shadow-md p-6 dark:bg-gray-800">
                <h3 class="text-xl font-bold mb-4 dark:text-white">Filtros</h3>
                
                <!-- Search -->
                <form action="{{ route('shop.index') }}" method="GET" class="mb-6 group">
                    @if(request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}" 
                               class="block w-full p-4 pr-12 text-sm text-gray-900 border-2 border-gray-200 rounded-2xl bg-white/80 backdrop-blur-md focus:ring-2 focus:ring-primary focus:border-primary transition-all duration-300 placeholder-gray-400 dark:bg-gray-700/80 dark:border-gray-600 dark:placeholder-gray-500 dark:text-white dark:focus:ring-primary dark:focus:border-primary shadow-sm hover:shadow-md" 
                               placeholder="Buscar productos...">
                        <button type="submit" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary dark:text-gray-500 dark:hover:text-primary transition-all duration-300 hover:scale-110">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </button>
                    </div>
                </form>

                <!-- Categories -->
                <h4 class="font-semibold mb-2 dark:text-white">Categorías</h4>
                <ul class="space-y-2">
                    <li>
                        <a href="{{ route('shop.index') }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ !request('category') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <span class="ml-3">Todas</span>
                        </a>
                    </li>
                    @foreach($categories as $category)
                        <li>
                            <a href="{{ route('shop.index', array_merge(request()->query(), ['category' => $category->slug])) }}" class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request('category') == $category->slug ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                                <span class="ml-3">{{ $category->name }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </aside>

        <!-- Product Grid -->
        <main class="w-full md:w-3/4">
            <h2 class="text-3xl md:text-4xl font-serif font-bold mb-8 dark:text-white text-gray-900">Catálogo de Productos</h2>
            
            @if($products->count() > 0)
                <div class="grid grid-cols-[repeat(auto-fit,minmax(240px,300px))] justify-center gap-8">
                    @foreach($products as $product)
                        <x-product-card :product="$product" />
                    @endforeach
                </div>
                <div class="mt-12">
                    {{ $products->links() }}
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 bg-gray-50 rounded-lg dark:bg-gray-800">
                    <svg class="w-16 h-16 text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <p class="text-xl text-gray-500 font-serif">No encontramos productos.</p>
                </div>
            @endif
        </main>
    </div>
</div>
@endsection
