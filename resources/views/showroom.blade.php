<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $business->name }} | Pocket Showroom</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; color: #0F172A; }
        .glass-header { background: rgba(255, 255, 255, 0.85); backdrop-filter: blur(12px); }
        .product-card { transition: transform 0.2s ease, box-shadow 0.2s ease; }
        .product-card:hover { transform: translateY(-4px); box-shadow: 0 12px 24px -6px rgba(0, 0, 0, 0.08); }
    </style>
</head>
<body class="min-h-screen flex flex-col">

    <!-- Header -->
    <header class="sticky top-0 z-50 glass-header border-b border-slate-200/80 shadow-sm">
        <div class="max-w-5xl mx-auto px-4 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-purple-500/20">
                    {{ strtoupper(substr($business->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="font-extrabold text-slate-900 text-lg leading-tight">{{ $business->name }}</h1>
                    <p class="text-xs font-semibold text-slate-500 flex items-center gap-1">
                        <span>{{ $business->business_type ?? 'Digital Showroom' }}</span>
                        @if($business->city) • <span>📍 {{ $business->city }}</span> @endif
                    </p>
                </div>
            </div>
            @if($business->whatsapp)
            <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}?text={{ urlencode('Hi '.$business->name.', I visited your showroom.') }}"
               target="_blank"
               class="inline-flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2 rounded-xl text-sm transition shadow-sm shadow-emerald-600/20">
                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                <span>WhatsApp</span>
            </a>
            @endif
        </div>
    </header>

    <!-- Banner Info -->
    <div class="bg-gradient-to-r from-purple-900 via-indigo-900 to-slate-900 text-white py-10 px-4">
        <div class="max-w-5xl mx-auto text-center">
            <h2 class="text-3xl font-black tracking-tight mb-2">Welcome to {{ $business->name }}</h2>
            <p class="text-purple-200 text-sm max-w-xl mx-auto font-medium">{{ $business->about ?? 'Explore our latest collection and place inquiries directly on WhatsApp.' }}</p>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 py-8 flex-1 w-full">

        <!-- Category Tabs -->
        @if($categories->count() > 0)
        <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6 no-scrollbar">
            <span class="bg-indigo-600 text-white font-bold text-xs px-4 py-2 rounded-full cursor-pointer shadow-sm">All Products</span>
            @foreach($categories as $cat)
            <span class="bg-white hover:bg-slate-100 text-slate-700 font-semibold text-xs px-4 py-2 rounded-full border border-slate-200 cursor-pointer transition shadow-xs">
                {{ $cat->name }}
            </span>
            @endforeach
        </div>
        @endif

        <!-- Product Grid -->
        @if($products->count() > 0)
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
            @foreach($products as $product)
            @php
                $img = $product->images->first()?->path;
                $imgUrl = $img ? (str_starts_with($img, 'http') ? $img : asset('storage/'.$img)) : 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600&auto=format&fit=crop&q=80';
                $waText = urlencode("Hi {$business->name}, I am interested in {$product->name} (Price: ₹".number_format($product->offer_price ?? $product->price)."). Please share details.");
            @endphp
            <div class="product-card bg-white rounded-2xl border border-slate-200/90 overflow-hidden flex flex-col">
                <div class="relative aspect-square bg-slate-100 overflow-hidden">
                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @if($product->offer_price && $product->offer_price < $product->price)
                    <span class="absolute top-2.5 left-2.5 bg-rose-500 text-white text-[10px] font-extrabold px-2 py-0.5 rounded-full shadow-sm">
                        OFFER
                    </span>
                    @endif
                </div>
                <div class="p-3.5 flex-1 flex flex-col justify-between">
                    <div>
                        <p class="text-[11px] font-bold text-purple-600 uppercase tracking-wider mb-0.5">{{ $product->category?->name ?? 'Catalogue' }}</p>
                        <h3 class="font-bold text-slate-800 text-sm line-clamp-1 mb-1.5">{{ $product->name }}</h3>
                        <p class="text-xs text-slate-500 line-clamp-2 mb-3">{{ $product->description }}</p>
                    </div>
                    <div>
                        <div class="flex items-baseline gap-2 mb-3">
                            <span class="text-base font-extrabold text-slate-900">₹{{ number_format($product->offer_price ?? $product->price) }}</span>
                            @if($product->offer_price && $product->offer_price < $product->price)
                            <span class="text-xs font-semibold text-slate-400 line-through">₹{{ number_format($product->price) }}</span>
                            @endif
                        </div>
                        @if($business->whatsapp)
                        <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}?text={{ $waText }}"
                           target="_blank"
                           class="w-full py-2 bg-slate-900 hover:bg-purple-700 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1.5 transition">
                            <span>Inquire / Buy</span>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="bg-white rounded-3xl p-12 text-center border border-slate-200 my-8">
            <div class="w-16 h-16 bg-slate-100 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">🛍️</div>
            <h3 class="text-lg font-bold text-slate-800 mb-1">No products added yet</h3>
            <p class="text-sm text-slate-500">Check back soon for latest additions from {{ $business->name }}.</p>
        </div>
        @endif

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-6 px-4 text-center mt-auto">
        <p class="text-xs font-semibold text-slate-500 mb-1">Powered by <strong class="text-indigo-600">Pocket Showroom</strong></p>
        <p class="text-[11px] text-slate-400">Your showroom in every pocket.</p>
    </footer>

</body>
</html>
