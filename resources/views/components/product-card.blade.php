@props(['product'])

<div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-700 overflow-hidden flex flex-col h-full">
    {{-- Everything here (image, badges, name, price) is one click target to the
         product page. The quantity selector + add-to-cart button live outside
         this anchor as a sibling, so they never end up nested inside an <a>
         (invalid HTML) and clicking them doesn't also trigger navigation. --}}
    <a href="{{ route('shop.show', $product->slug) }}" class="block">
        <div class="relative aspect-[3/4] bg-gray-50 dark:bg-gray-900 overflow-hidden">
            @if($product->images->isNotEmpty())
                <img class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105 {{ $product->inStock() ? '' : 'opacity-60' }}"
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
                <span class="absolute top-2 left-2 sm:top-3 sm:left-3 inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-semibold bg-white/90 dark:bg-gray-800/90 text-gray-800 dark:text-gray-200 border border-gray-200/50 dark:border-gray-700 shadow">
                    {{ $product->category->name }}
                </span>
            @endif

            @if(!$product->inStock())
                <span class="absolute top-2 right-2 sm:top-3 sm:right-3 inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-red-600 text-white shadow">
                    AGOTADO
                </span>
            @elseif($product->is_offer)
                <span class="absolute top-2 right-2 sm:top-3 sm:right-3 inline-flex items-center px-2 sm:px-3 py-0.5 sm:py-1 rounded-full text-[10px] sm:text-xs font-bold bg-dark text-white shadow">
                    OFERTA
                </span>
            @endif
        </div>

        <div class="p-3 sm:p-5 pb-0">
            <h5 class="text-sm sm:text-lg font-serif font-bold text-gray-900 dark:text-white truncate group-hover:text-primary dark:group-hover:text-yellow-400 transition-colors duration-300">
                {{ $product->name }}
            </h5>

            <div class="mt-1 sm:mt-2">
                <div class="flex flex-wrap items-baseline gap-1 sm:gap-2">
                    @if($product->discount_price)
                        <span class="text-base sm:text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($product->discount_price, 2) }}</span>
                        <span class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 line-through">${{ number_format($product->price, 2) }}</span>
                    @else
                        <span class="text-base sm:text-2xl font-bold text-gray-900 dark:text-white">${{ number_format($product->price, 2) }}</span>
                    @endif
                </div>
                @if($product->discount_price)
                    <span class="block text-[11px] sm:text-xs font-semibold text-green-700 dark:text-green-400 mt-0.5">
                        Ahorra ${{ number_format($product->price - $product->discount_price, 2) }}
                    </span>
                @endif
                @if($product->inStock() && $product->stock <= 5)
                    <span class="block text-[11px] sm:text-xs font-semibold text-amber-600 dark:text-amber-400 mt-0.5">
                        ¡Últimas {{ $product->stock }} unidades!
                    </span>
                @endif
            </div>
        </div>
    </a>

    <div class="p-3 sm:p-5 pt-2 sm:pt-3 mt-auto" data-product-qty>
        @if($product->inStock())
            <div class="flex items-stretch gap-1 sm:gap-2">
                <div class="flex-[3] sm:flex-[2] flex items-center justify-center gap-0.5 border border-gray-300 dark:border-gray-600 rounded-lg overflow-hidden">
                    <button type="button" data-qty-decrement aria-label="Disminuir cantidad"
                            class="w-4 h-4 sm:w-7 sm:h-7 flex-shrink-0 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        &minus;
                    </button>
                    <span data-qty-value class="text-[11px] sm:text-sm font-semibold text-gray-900 dark:text-white text-center" data-max="{{ $product->stock }}">1</span>
                    <button type="button" data-qty-increment aria-label="Aumentar cantidad"
                            class="w-4 h-4 sm:w-7 sm:h-7 flex-shrink-0 flex items-center justify-center text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                        +
                    </button>
                </div>
                <button type="button"
                        data-cart-add
                        data-id="product-{{ $product->id }}"
                        data-slug="{{ $product->slug }}"
                        data-name="{{ $product->name }}"
                        data-price="{{ $product->discount_price ?? $product->price }}"
                        data-image="{{ $product->images->isNotEmpty() ? asset('storage/' . $product->images->first()->image_path) : '' }}"
                        class="flex-[7] sm:flex-[3] inline-flex items-center justify-center gap-1 sm:gap-1.5 py-2 sm:py-2.5 px-1 sm:px-2 text-xs sm:text-sm font-semibold text-white bg-green-700 hover:bg-green-800 rounded-lg transition-colors">
                    <svg class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span class="hidden sm:inline">Añadir</span>
                </button>
            </div>
        @else
            <span class="block text-center text-xs sm:text-sm font-semibold text-gray-500 dark:text-gray-400 py-2 sm:py-2.5 border border-gray-200 dark:border-gray-700 rounded-lg">
                Agotado
            </span>
        @endif
    </div>
</div>
