@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="flex flex-col md:flex-row gap-8">
        <!-- Sidebar Filters -->
        <aside class="w-full md:w-1/4">
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
            <h2 class="text-3xl font-serif font-bold mb-8 dark:text-white text-gray-900">Catálogo de Productos</h2>
            
            @if($products->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($products as $product)
                        <div class="group relative bg-white rounded-2xl shadow-md hover:shadow-2xl transition-all duration-500 border border-gray-100 overflow-hidden dark:bg-gray-800 dark:border-gray-700 transform hover:-translate-y-2">
                            <!-- Product Image Container -->
                            <a href="{{ route('shop.show', $product->slug) }}" class="block relative overflow-hidden aspect-[3/4] bg-gray-50 dark:bg-gray-900">
                                @if($product->images->isNotEmpty())
                                    <img class="w-full h-full object-cover transition-transform duration-700 ease-out group-hover:scale-105" 
                                         src="{{ asset('storage/' . $product->images->first()->image_path) }}" 
                                         alt="{{ $product->name }}">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center text-gray-400 dark:from-gray-800 dark:to-gray-900">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                                
                                <!-- Category Badge -->
                                <div class="absolute top-3 left-3 z-10">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/90 backdrop-blur-md text-gray-800 border border-gray-200/50 shadow-lg transition-all duration-300 group-hover:scale-110 group-hover:bg-primary group-hover:text-white dark:bg-gray-800/90 dark:text-gray-200 dark:border-gray-700">
                                        {{ $product->category->name }}
                                    </span>
                                </div>
                                
                                <!-- Offer Badge -->
                                @if($product->is_offer)
                                    <div class="absolute top-3 right-3 z-10">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-500 text-white shadow-lg">
                                            OFERTA
                                        </span>
                                    </div>
                                @endif
                                
                                <!-- Hover Overlay - 45% height for better visibility -->
                                <div class="absolute inset-x-0 bottom-0 h-[45%] translate-y-full group-hover:translate-y-0 transition-transform duration-500 ease-out bg-gradient-to-t from-black/95 via-black/80 to-transparent backdrop-blur-xl">
                                    <div class="absolute inset-0 flex flex-col justify-end p-4 space-y-2">
                                        <h5 class="text-base font-serif font-bold text-white line-clamp-1">
                                            {{ $product->name }}
                                        </h5>
                                        @if($product->description)
                                            <p class="text-xs text-gray-200 line-clamp-3 leading-relaxed">
                                                {{ $product->description }}
                                            </p>
                                        @endif
                                        <button class="w-full py-2.5 px-4 text-xs font-bold uppercase tracking-widest text-gray-900 bg-white hover:bg-primary hover:text-white transition-all duration-300 rounded-lg shadow-lg transform hover:scale-105 flex items-center justify-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                            </svg>
                                            Ver Detalles
                                        </button>
                                    </div>
                                </div>
                            </a>
                            
                            <!-- Product Info -->
                            <div class="p-5 space-y-3">
                                <a href="{{ route('shop.show', $product->slug) }}" class="block">
                                    <h5 class="text-lg font-serif font-bold text-gray-900 dark:text-white truncate group-hover:text-primary transition-colors duration-300">
                                        {{ $product->name }}
                                    </h5>
                                </a>
                                
                                <!-- Price Section -->
                                <div class="flex items-baseline gap-2">
                                    @if($product->discount_price)
                                        <div class="flex flex-col">
                                            <div class="flex items-baseline gap-2">
                                                <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                                    ${{ number_format($product->discount_price, 2) }}
                                                </span>
                                                <span class="text-sm text-gray-400 line-through">
                                                    ${{ number_format($product->price, 2) }}
                                                </span>
                                            </div>
                                            <span class="text-xs font-semibold text-green-600 dark:text-green-400">
                                                Ahorra ${{ number_format($product->price - $product->discount_price, 2) }}
                                            </span>
                                        </div>
                                    @else
                                        <span class="text-2xl font-bold text-gray-900 dark:text-white">
                                            ${{ number_format($product->price, 2) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
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
