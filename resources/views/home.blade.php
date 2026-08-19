@extends('layouts.app')

@section('meta_description', 'Descubre ofertas, categorías y los productos de belleza más nuevos en ' . ($globalSettings['brand_name'] ?? 'BeautyShop') . '.')

@section('content')
<!-- Hero Section / Offers Carousel -->
@if($offers->isNotEmpty())
<div id="offers-carousel" class="relative w-full" data-carousel="slide">
    <!-- Carousel wrapper -->
    <div class="relative h-[26rem] overflow-hidden rounded-lg md:h-96">
        @foreach($offers->take(5) as $offer)
            <div class="hidden duration-700 ease-in-out" data-carousel-item>
                <section class="bg-white dark:bg-gray-900 h-full flex items-center">
                    <div class="grid max-w-screen-xl px-4 py-8 mx-auto lg:gap-8 xl:gap-0 lg:py-16 lg:grid-cols-12 w-full">
                        <div class="mr-auto place-self-center lg:col-span-7">
                            <h1 class="max-w-2xl mb-4 text-4xl font-extrabold tracking-tight leading-none md:text-5xl xl:text-6xl dark:text-white font-serif">{{ $offer->name }}</h1>
                            <p class="max-w-2xl mb-6 font-light text-gray-500 lg:mb-8 md:text-lg lg:text-xl dark:text-gray-400">{{ Str::limit($offer->description, 100) }}</p>
                            <div class="flex items-center gap-4">
                                <span class="text-3xl font-bold text-green-600 dark:text-green-400">${{ number_format($offer->discount_price, 2) }}</span>
                                <span class="text-xl text-gray-500 dark:text-gray-400 line-through">${{ number_format($offer->price, 2) }}</span>
                            </div>
                            <div class="mt-6">
                                <a href="{{ route('shop.show', $offer->slug) }}" class="inline-flex items-center justify-center px-5 py-3 mr-3 text-base font-medium text-center text-white rounded-lg bg-dark hover:bg-black focus:ring-4 focus:ring-primary/40 transition-colors">
                                    Ver Oferta
                                    <svg class="w-5 h-5 ml-2 -mr-1" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M10.293 3.293a1 1 0 011.414 0l6 6a1 1 0 010 1.414l-6 6a1 1 0 01-1.414-1.414L14.586 11H3a1 1 0 110-2h11.586l-4.293-4.293a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                                </a>
                            </div>
                        </div>
                        <div class="hidden lg:mt-0 lg:col-span-5 lg:flex justify-center">
                             @if($offer->images->isNotEmpty())
                                <img src="{{ asset('storage/' . $offer->images->first()->image_path) }}" alt="{{ $offer->name }}" class="rounded-lg shadow-lg max-h-80 object-cover">
                             @else
                                <div class="w-full h-64 bg-gray-200 rounded-lg flex items-center justify-center text-gray-500">Sin Imagen</div>
                             @endif
                        </div>
                    </div>
                </section>
            </div>
        @endforeach
    </div>
    <!-- Slider indicators -->
    <div class="absolute z-30 flex -translate-x-1/2 bottom-5 left-1/2 space-x-3 rtl:space-x-reverse">
        @foreach($offers->take(5) as $index => $offer)
            <button type="button" class="w-3 h-3 rounded-full" aria-current="{{ $index === 0 ? 'true' : 'false' }}" aria-label="Diapositiva {{ $index + 1 }}" data-carousel-slide-to="{{ $index }}"></button>
        @endforeach
    </div>
    <!-- Slider controls -->
    <button type="button" class="absolute top-0 start-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-prev>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <svg class="w-4 h-4 text-gray-800 dark:text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 1 1 5l4 4"/>
            </svg>
            <span class="sr-only">Anterior</span>
        </span>
    </button>
    <button type="button" class="absolute top-0 end-0 z-30 flex items-center justify-center h-full px-4 cursor-pointer group focus:outline-none" data-carousel-next>
        <span class="inline-flex items-center justify-center w-10 h-10 rounded-full bg-white/30 dark:bg-gray-800/30 group-hover:bg-white/50 dark:group-hover:bg-gray-800/60 group-focus:ring-4 group-focus:ring-white dark:group-focus:ring-gray-800/70 group-focus:outline-none">
            <svg class="w-4 h-4 text-gray-800 dark:text-white rtl:rotate-180" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 6 10">
                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 9 4-4-4-4"/>
            </svg>
            <span class="sr-only">Siguiente</span>
        </span>
    </button>
</div>
@endif

<!-- Categories Section -->
<section class="relative bg-gradient-to-br from-secondary/30 via-white to-primary/10 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 py-16 overflow-hidden">
    <!-- Decorative background elements -->
    <div class="absolute inset-0 bg-[linear-gradient(rgba(197,160,89,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(197,160,89,0.03)_1px,transparent_1px)] bg-[size:4rem_4rem] [mask-image:radial-gradient(ellipse_80%_50%_at_50%_0%,#000,transparent)]"></div>
    
    <div class="max-w-screen-xl px-4 mx-auto relative z-10">
        <div class="text-center mb-12">
            <h2 class="text-3xl md:text-4xl font-serif font-bold text-gray-900 dark:text-white mb-4 inline-block relative">
                Nuestras Categorías
                <div class="absolute -bottom-2 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-primary to-transparent rounded-full"></div>
            </h2>
            <p class="text-gray-600 dark:text-gray-300 mt-6 max-w-2xl mx-auto">Explora nuestra selección curada de productos de belleza</p>
        </div>
        
        <div class="grid grid-cols-[repeat(auto-fit,minmax(140px,220px))] justify-center gap-6">
            @foreach($categories as $category)
                <a href="{{ route('shop.index', ['category' => $category->slug]) }}" 
                   class="group relative bg-white dark:bg-gray-800 rounded-2xl border-2 border-gray-100 dark:border-gray-700 shadow-lg hover:shadow-2xl transition-all duration-500 overflow-hidden transform hover:-translate-y-2 hover:scale-105 perspective-1000">
                    <!-- Gradient overlay -->
                    <div class="absolute inset-0 bg-gradient-to-br from-primary/0 to-primary/0 group-hover:from-primary/10 group-hover:to-primary/20 transition-all duration-500"></div>
                    
                    <!-- Content -->
                    <div class="relative p-6 text-center">
                        <!-- Icon placeholder - can be customized per category -->
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-gradient-to-br from-primary/20 to-primary/30 dark:from-primary/30 dark:to-primary/40 flex items-center justify-center transform group-hover:rotate-12 transition-transform duration-500">
                            <svg class="w-6 h-6 text-primary dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                            </svg>
                        </div>
                        
                        <h5 class="text-lg font-bold font-serif tracking-tight text-gray-900 dark:text-white group-hover:text-primary dark:group-hover:text-yellow-400 transition-colors duration-300">
                            {{ $category->name }}
                        </h5>
                        
                        <!-- Decorative arrow -->
                        <div class="mt-3 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            <svg class="w-4 h-4 mx-auto text-primary dark:text-yellow-400 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Top accent line -->
                    <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-transparent via-primary to-transparent transform scale-x-0 group-hover:scale-x-100 transition-transform duration-500"></div>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Latest Products Section -->
<section id="products" class="bg-white dark:bg-gray-900 py-12">
    <div class="max-w-screen-xl px-4 mx-auto">
        <h2 class="text-3xl md:text-4xl font-serif font-bold text-center text-gray-900 dark:text-white mb-8">Nuevos Productos</h2>
        <div class="grid grid-cols-[repeat(auto-fit,minmax(240px,300px))] justify-center gap-6">
            @foreach($latestProducts as $product)
                <x-product-card :product="$product" />
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('shop.index') }}" class="inline-flex items-center justify-center text-white bg-dark hover:bg-black focus:ring-4 focus:ring-primary/40 font-medium rounded-lg text-sm px-5 py-2.5 transition-colors">Ver Todos los Productos</a>
        </div>
    </div>
</section>

<!-- Combos Section -->
@if($combos->isNotEmpty())
<section class="bg-gray-50 dark:bg-gray-800 py-12">
    <div class="max-w-screen-xl px-4 mx-auto">
        <h2 class="text-3xl md:text-4xl font-serif font-bold text-center text-gray-900 dark:text-white mb-8">Combos Especiales</h2>
        <div class="grid grid-cols-[repeat(auto-fit,minmax(260px,340px))] justify-center gap-6">
            @foreach($combos as $combo)
                <div class="bg-white rounded-lg shadow-md border border-gray-200 dark:bg-gray-800 dark:border-gray-700">
                    @if($combo->image_path)
                        <img class="rounded-t-lg w-full h-48 object-cover" src="{{ asset('storage/' . $combo->image_path) }}" alt="{{ $combo->name }}">
                    @else
                        <div class="w-full h-48 bg-gray-200 rounded-t-lg flex items-center justify-center text-gray-500">Sin Imagen</div>
                    @endif
                    <div class="p-5">
                        <h5 class="mb-2 text-lg font-serif font-bold tracking-tight text-gray-900 dark:text-white">{{ $combo->name }}</h5>
                        <p class="mb-3 font-normal text-gray-700 dark:text-gray-400">{{ Str::limit($combo->description, 100) }}</p>
                         <span class="text-2xl font-bold text-gray-900 dark:text-white block mb-4">${{ number_format($combo->price, 2) }}</span>
                        <a href="{{ $combo->whatsapp_url }}" target="_blank" class="inline-flex items-center py-2 px-3 text-sm font-medium text-center text-white bg-green-700 rounded-lg hover:bg-green-800 focus:ring-4 focus:outline-none focus:ring-green-300 dark:focus:ring-green-800 w-full justify-center">
                            Pedir Combo
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
