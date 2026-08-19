@props(['product'])

<div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-full">
    <a href="{{ route('shop.show', $product->slug) }}" class="block relative aspect-[3/4] bg-gray-50 dark:bg-gray-900 overflow-hidden">
        @if($product->images->isNotEmpty())
            <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105"
                 src="{{ asset('storage/' . $product->images->first()->image_path) }}"
                 alt="{{ $product->name }}" loading="lazy">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-900">
                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        @endif

        @if($product->category)
            <span class="absolute top-3 left-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-gray-200 border border-gray-200/50 dark:border-gray-700 shadow">
                {{ $product->category->name }}
            </span>
        @endif

        @if($product->is_offer)
            <span class="absolute top-3 right-3 inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-dark text-white shadow">
                OFERTA
            </span>
        @endif
    </a>

    <div class="p-5 flex flex-col flex-grow gap-3">
        <a href="{{ route('shop.show', $product->slug) }}" class="block">
            <h5 class="text-lg font-serif font-bold text-gray-900 dark:text-white truncate group-hover:text-primary transition-colors duration-300">
                {{ $product->name }}
            </h5>
        </a>

        <div>
            <div class="flex items-baseline gap-2">
                @if($product->discount_price)
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($product->discount_price, 2) }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400 line-through">${{ number_format($product->price, 2) }}</span>
                @else
                    <span class="text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</span>
                @endif
            </div>
            @if($product->discount_price)
                <span class="block text-xs font-semibold text-green-700 dark:text-green-400 mt-0.5">
                    Ahorra ${{ number_format($product->price - $product->discount_price, 2) }}
                </span>
            @endif
        </div>

        <div class="mt-auto flex gap-2 pt-1">
            <a href="{{ route('shop.show', $product->slug) }}"
               class="flex-1 inline-flex items-center justify-center py-2.5 px-3 text-sm font-semibold text-white bg-dark hover:bg-black rounded-lg transition-colors">
                Ver Detalles
            </a>
            <button type="button"
                    data-cart-add
                    data-id="product-{{ $product->id }}"
                    data-slug="{{ $product->slug }}"
                    data-name="{{ $product->name }}"
                    data-price="{{ $product->discount_price ?? $product->price }}"
                    data-image="{{ $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->image_path) : '' }}"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 py-2.5 px-3 text-sm font-semibold text-white bg-green-700 hover:bg-green-800 rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Añadir
            </button>
        </div>
    </div>
</div>
