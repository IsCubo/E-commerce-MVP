@extends('layouts.app')

@section('meta_title', $product->name . ' - ' . ($globalSettings['brand_name'] ?? 'BeautyShop'))
@section('meta_description', Str::limit($product->description ?? 'Descubre ' . $product->name . ' y más productos de belleza.', 150))
@if($product->images->isNotEmpty())
    @section('meta_image', asset('storage/' . $product->images->first()->image_path))
@endif

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="bg-white rounded-lg shadow-lg dark:bg-gray-800 overflow-hidden">
        <div class="md:flex">
            <!-- Product Images -->
            <div class="md:w-1/2 p-4">
                @if($product->images->isNotEmpty())
                    <div class="mb-4">
                        <img id="mainImage" src="{{ asset('storage/' . $product->images->first()->image_path) }}" alt="{{ $product->name }}" class="w-full h-96 object-contain rounded-lg border border-gray-200 dark:border-gray-700">
                    </div>
                    @if($product->images->count() > 1)
                        <div class="flex gap-2 overflow-x-auto">
                            @foreach($product->images as $image)
                                <img src="{{ asset('storage/' . $image->image_path) }}" alt="{{ $product->name }}" class="w-20 h-20 object-cover rounded cursor-pointer border border-gray-300 hover:border-pink-500" onclick="changeImage(this.src)">
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="w-full h-96 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">Sin Imagen</div>
                @endif
            </div>

            <!-- Product Details -->
            <div class="md:w-1/2 p-8">
                <div class="mb-4">
                    <span class="bg-pink-100 text-pink-800 text-xs font-medium mr-2 px-2.5 py-0.5 rounded dark:bg-pink-900 dark:text-pink-300">{{ $product->category->name }}</span>
                    @if(!$product->inStock())
                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-red-900 dark:text-red-300">AGOTADO</span>
                    @elseif($product->is_offer)
                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded dark:bg-green-900 dark:text-green-300">OFERTA</span>
                    @endif
                </div>
                
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">{{ $product->name }}</h1>
                
                <div class="mb-6">
                    @if($product->discount_price)
                        <span class="text-3xl font-bold text-gray-900 dark:text-white mr-2">${{ number_format($product->discount_price, 2) }}</span>
                        <span class="text-xl text-gray-500 line-through">${{ number_format($product->price, 2) }}</span>
                    @else
                        <span class="text-3xl font-bold text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>

                <p class="text-gray-700 dark:text-gray-300 mb-6 leading-relaxed">
                    {{ $product->description ?? 'No hay descripción disponible para este producto.' }}
                </p>

                @if($product->inStock())
                    <div class="mb-8" data-product-qty>
                        <div class="flex items-center gap-3 mb-4">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Cantidad:</span>
                            <div class="flex items-center gap-2">
                                <button type="button" data-qty-decrement aria-label="Disminuir cantidad"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    &minus;
                                </button>
                                <span data-qty-value class="text-sm font-semibold text-gray-900 dark:text-white w-8 text-center" data-max="{{ $product->stock }}">1</span>
                                <button type="button" data-qty-increment aria-label="Aumentar cantidad"
                                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    +
                                </button>
                            </div>
                            <span class="text-xs text-gray-500 dark:text-gray-400">({{ $product->stock }} disponibles)</span>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="button"
                                    data-cart-add
                                    data-id="product-{{ $product->id }}"
                                    data-slug="{{ $product->slug }}"
                                    data-name="{{ $product->name }}"
                                    data-price="{{ $product->discount_price ?? $product->price }}"
                                    data-image="{{ $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->image_path) : '' }}"
                                    class="inline-flex items-center justify-center gap-2 w-full sm:w-auto px-8 py-3 text-base font-medium text-center text-white bg-dark hover:bg-black dark:hover:bg-gray-700 rounded-lg focus:ring-4 focus:ring-primary/40 transition duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                                Añadir al carrito
                            </button>
                            <a href="{{ $product->whatsapp_url }}" target="_blank" class="inline-flex items-center justify-center w-full sm:w-auto px-8 py-3 text-base font-medium text-center text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:ring-green-300 dark:focus:ring-green-800 transition duration-300">
                                <svg class="w-5 h-5 mr-2 -ml-1" fill="currentColor" viewBox="0 0 448 512" xmlns="http://www.w3.org/2000/svg"><path d="M380.9 97.1C339 55.1 283.2 32 223.9 32c-122.4 0-222 99.6-222 222 0 39.1 10.2 77.3 29.6 111L0 480l117.7-30.9c32.4 17.7 68.9 27 106.1 27h.1c122.3 0 224.1-99.6 224.1-222 0-59.3-25.2-115-67.1-157zm-157 341.6c-33.2 0-65.7-8.9-94-25.7l-6.7-4-69.8 18.3L72 359.2l-4.4-7c-18.5-29.4-28.2-63.3-28.2-98.2 0-101.7 82.8-184.5 184.6-184.5 49.3 0 95.6 19.2 130.4 54.1 34.8 34.9 56.2 81.2 56.1 130.5 0 101.8-84.9 184.6-186.6 184.6zm101.2-138.2c-5.5-2.8-32.8-16.2-37.9-18-5.1-1.9-8.8-2.8-12.5 2.8-3.7 5.6-14.3 18-17.6 21.8-3.2 3.7-6.5 4.2-12 1.4-32.6-16.3-54-29.1-75.5-66-5.7-9.8 5.7-9.1 16.3-30.3 1.8-3.7 .9-6.9-.5-9.7-1.4-2.8-12.5-30.1-17.1-41.2-4.5-10.8-9.1-9.3-12.5-9.5-3.2-.2-6.9-.2-10.6-.2-3.7 0-9.7 1.4-14.8 6.9-5.1 5.6-19.4 19-19.4 46.3 0 27.3 19.9 53.7 22.6 57.4 2.8 3.7 39.1 59.7 94.8 83.8 35.2 15.2 49 16.5 66.6 13.9 10.7-1.6 32.8-13.4 37.4-26.4 4.6-13 4.6-24.1 3.2-26.4-1.3-2.5-5-4-10.5-6.7z"/></svg>
                                Comprar en WhatsApp
                            </a>
                        </div>
                    </div>
                @else
                    <div class="mb-8">
                        <span class="inline-flex items-center px-4 py-3 text-sm font-semibold text-gray-600 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-lg">
                            Producto agotado por el momento
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-12">
            <h2 class="text-3xl md:text-4xl font-serif font-bold mb-6 dark:text-white text-gray-900">Productos Relacionados</h2>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
                 @foreach($relatedProducts as $related)
                    <x-product-card :product="$related" />
                @endforeach
            </div>
        </div>
    @endif
</div>

<script>
    function changeImage(src) {
        document.getElementById('mainImage').src = src;
    }
</script>
@endsection
