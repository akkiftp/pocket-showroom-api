<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>{{ $business->name ?? 'Showroom' }} — Exclusive Live Showroom</title>
    <meta name="description" content="Explore live exclusive collections from {{ $business->name ?? 'our showroom' }}. Transparent pricing, genuine items, and direct WhatsApp order.">
    <meta name="theme-color" content="#3B091E">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', '-apple-system', 'BlinkMacSystemFont', 'sans-serif'],
                        serif: ['"Cinzel"', 'Georgia', 'serif'],
                    },
                    colors: {
                        brand: {
                            50: '#FDF2F7',
                            100: '#FCE7F1',
                            500: '#D6227B',
                            700: '{{ $business->theme_primary ?? "#8C174A" }}',
                            800: '#6B0F37',
                            900: '#3B091E',
                            950: '#1C040E',
                        },
                        gold: {
                            400: '#E7CE75',
                            500: '{{ $business->theme_secondary ?? "#D4AF37" }}',
                            600: '#B89420',
                        }
                    },
                    boxShadow: {
                        'mobile-card': '0 4px 20px -2px rgba(140, 23, 74, 0.08), 0 2px 6px -1px rgba(0, 0, 0, 0.04)',
                        'bottom-dock': '0 -4px 25px rgba(0, 0, 0, 0.08)',
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Mobile Performance & Smooth Scrolling */
        * { -webkit-tap-highlight-color: transparent; }
        
        .glass-nav {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
        }
        
        .mobile-hero-bg {
            background: radial-gradient(circle at 90% 10%, rgba(212, 175, 55, 0.22) 0%, transparent 45%),
                        radial-gradient(circle at 10% 90%, rgba(214, 34, 123, 0.25) 0%, transparent 50%),
                        linear-gradient(150deg, #1C040E 0%, #3B091E 50%, #6B0F37 100%);
        }
        
        /* Shimmering Text */
        .gold-shimmer {
            background: linear-gradient(90deg, #F3E2A3 0%, #D4AF37 50%, #F3E2A3 100%);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: shimmerText 3s linear infinite;
        }
        @keyframes shimmerText {
            to { background-position: 200% center; }
        }
        
        /* Custom Native Scrollbar */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        
        /* Smooth Bottom Sheet Animation */
        .slide-up {
            animation: slideUp 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        @keyframes slideUp {
            from { transform: translateY(100%); }
            to { transform: translateY(0); }
        }
    </style>
</head>
<body class="bg-[#F8F9FA] text-gray-900 font-sans antialiased selection:bg-brand-500 selection:text-white pb-28 sm:pb-16">

    <!-- Sticky Mobile Header -->
    <header class="sticky top-0 z-40 glass-nav border-b border-gray-200/70 shadow-sm">
        <div class="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-3">
            
            <!-- Left: Logo & Verified Title -->
            <div class="flex items-center gap-2.5 min-w-0">
                <div class="relative flex-shrink-0">
                    @if(!empty($business->logo_url))
                        <img src="{{ $business->logo_url }}" alt="{{ $business->name }}" class="w-11 h-11 rounded-xl object-cover ring-1.5 ring-gold-400/40 shadow-md">
                    @else
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-tr from-brand-900 to-brand-700 text-white flex items-center justify-center font-serif font-black text-lg shadow-md shadow-brand-900/20 ring-1.5 ring-gold-400/40">
                            {{ strtoupper(substr($business->name ?? 'S', 0, 1)) }}
                        </div>
                    @endif
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-500 ring-2 ring-white flex items-center justify-center">
                        <span class="w-1 h-1 rounded-full bg-white animate-ping"></span>
                    </span>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1">
                        <h1 class="font-extrabold text-gray-950 text-sm sm:text-lg leading-tight truncate tracking-tight">
                            {{ $business->name ?? 'Showroom' }}
                        </h1>
                        <svg class="w-3.5 h-3.5 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex items-center gap-1.5 text-[11px] font-semibold text-gray-500">
                        <span class="text-brand-700 font-bold uppercase tracking-wider text-[9px] bg-brand-50 px-1.5 py-0.2 rounded border border-brand-100/70">
                            {{ $business->business_type ?? 'BOUTIQUE' }}
                        </span>
                        <span>•</span>
                        <span class="truncate">{{ $business->locality ?? $business->city ?? 'Verified Showroom' }}</span>
                    </div>
                </div>
            </div>

            <!-- Right: Actions -->
            <div class="flex items-center gap-1.5 sm:gap-2.5 flex-shrink-0">
                <!-- Share Button -->
                <button onclick="shareShowroom()" class="p-2 sm:p-2.5 rounded-xl bg-white border border-gray-200 text-gray-700 hover:text-brand-700 transition shadow-sm" title="Share Showroom">
                    <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                </button>

                <!-- WhatsApp Connect -->
                @if(!empty($business->whatsapp))
                <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}?text={{ urlencode('Hi '.$business->name.', I am viewing your live showroom catalogue.') }}" target="_blank" class="p-2 sm:px-3.5 sm:py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold text-xs shadow-md shadow-emerald-500/20 transition flex items-center gap-1.5">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.698.058-2.146-.541-1.849-.766-3.037-2.65-3.13-2.774-.093-.124-.741-.986-.741-1.88 0-.894.469-1.333.636-1.514.167-.181.365-.227.486-.227.121 0 .243.001.35.006.113.005.263-.043.411.312.155.372.529 1.288.575 1.381.046.093.077.202.015.325-.062.124-.093.202-.185.31-.093.109-.196.243-.28.326-.093.093-.19.194-.082.38.108.186.482.795 1.034 1.288.71.636 1.309.833 1.495.926.186.093.295.078.404-.047.109-.124.465-.541.589-.727.124-.186.248-.155.419-.093.17.062 1.082.51 1.268.603.186.093.31.14.356.217.046.078.046.45-.098.855z"/>
                    </svg>
                    <span class="hidden sm:inline">WhatsApp</span>
                </a>
                @endif
            </div>

        </div>
    </header>

    <!-- Mobile-First Luxury Hero Card -->
    <div class="px-3.5 sm:px-6 lg:px-8 pt-3 sm:pt-6">
        <div class="max-w-7xl mx-auto rounded-3xl mobile-hero-bg text-white p-5 sm:p-10 shadow-xl relative overflow-hidden @if(!empty($business->banner_url)) bg-cover bg-center @endif" @if(!empty($business->banner_url)) style="background-image: linear-gradient(150deg, rgba(28, 4, 14, 0.85) 0%, rgba(59, 9, 30, 0.85) 50%, rgba(107, 15, 55, 0.85) 100%), url('{{ $business->banner_url }}');" @endif>
            <!-- Background Glows -->
            <div class="absolute -top-12 -right-12 w-48 h-48 rounded-full bg-gold-400/15 blur-2xl pointer-events-none"></div>
            
            <div class="relative z-10 max-w-2xl">
                <!-- Badge -->
                <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 backdrop-blur-md border border-white/15 text-[10px] sm:text-xs font-black uppercase tracking-wider mb-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-gold-400 animate-pulse"></span>
                    <span class="gold-shimmer font-serif">{{ $business->business_type ?? 'EXCLUSIVE LUXURY BOUTIQUE' }}</span>
                </div>

                <!-- Headline -->
                <h2 class="text-2xl sm:text-4xl lg:text-5xl font-serif font-black tracking-tight text-white leading-tight mb-2">
                    {{ $business->name ?? 'Welcome to Showroom' }}
                </h2>

                <p class="text-pink-100/85 text-xs sm:text-base leading-relaxed mb-5 line-clamp-2 sm:line-clamp-none font-normal">
                    {{ $business->about ?? 'Explore our latest verified collections, live transparent prices, and order directly on WhatsApp.' }}
                </p>

                <!-- Search Input Box -->
                <div class="relative">
                    <input type="text" id="searchInput" onkeyup="filterProductsLive()" placeholder="Search jewelry, rings, necklaces, price..." class="w-full pl-11 pr-10 py-3 sm:py-3.5 rounded-2xl bg-white text-gray-900 placeholder-gray-400 font-semibold text-xs sm:text-sm shadow-xl focus:outline-none focus:ring-3 focus:ring-brand-500 transition">
                    <svg class="w-5 h-5 text-brand-700 absolute left-3.5 top-1/2 -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                    <button onclick="clearSearchInput()" id="clearBtn" class="hidden absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Pills Slider (Smooth Horizontal Scroll) -->
    <div class="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 mt-4">
        <div class="flex items-center gap-2 overflow-x-auto no-scrollbar py-1">
            <button onclick="selectCategoryFilter('all', this)" class="cat-pill active-pill px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-gradient-to-r from-brand-900 to-brand-700 text-white font-black text-xs sm:text-sm whitespace-nowrap shadow-sm transition-all flex-shrink-0">
                ✨ All ({{ count($products ?? []) }})
            </button>
            @foreach($categories ?? [] as $cat)
            @php $cname = is_string($cat) ? $cat : ($cat->name ?? 'Category'); @endphp
            <button onclick="selectCategoryFilter('{{ strtolower($cname) }}', this)" class="cat-pill px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-white border border-gray-200/80 text-gray-700 font-bold text-xs sm:text-sm whitespace-nowrap transition-all shadow-sm flex-shrink-0 hover:border-brand-200">
                {{ $cname }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- Live Catalogue Grid Section -->
    <main class="max-w-7xl mx-auto px-3.5 sm:px-6 lg:px-8 mt-5 sm:mt-8">
        
        <!-- Section Header -->
        <div class="flex items-center justify-between mb-3.5 sm:mb-6">
            <h3 class="font-serif font-black text-lg sm:text-2xl text-gray-950 tracking-tight">
                Live Collections
            </h3>
            <span id="grid-count" class="text-[11px] sm:text-xs font-black text-brand-800 bg-brand-50 border border-brand-100 px-2.5 py-1 rounded-lg">
                {{ count($products ?? []) }} items
            </span>
        </div>

        <!-- 2-Column Mobile Grid, 3-4 Column Desktop -->
        <div id="catalog-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6">
            @forelse($products ?? [] as $product)
            @php
                $img = !empty($product->images) && count($product->images) > 0 ? $product->images[0]->url : null;
                $hasOffer = !empty($product->offer_price) && $product->offer_price < $product->price;
                $selling = $hasOffer ? $product->offer_price : $product->price;
                $discount = $hasOffer ? round((($product->price - $product->offer_price) / $product->price) * 100) : 0;
                $catName = $product->category->name ?? 'Collection';
            @endphp

            <div class="prod-item bg-white rounded-2xl sm:rounded-3xl border border-gray-200/80 shadow-mobile-card overflow-hidden flex flex-col justify-between"
                 data-id="{{ $product->id }}"
                 data-name="{{ strtolower($product->name) }}"
                 data-category="{{ strtolower($catName) }}"
                 data-price="{{ $selling }}"
                 data-img="{{ $img ?? '' }}"
                 data-desc="{{ $product->description ?? '' }}"
                 data-orig-price="{{ $product->price }}">

                <!-- Image Box -->
                <div class="relative w-full aspect-square bg-gradient-to-br from-brand-50/50 to-pink-50/40 overflow-hidden cursor-pointer" onclick="showProductDetails({{ $product->id }})">
                    @if($img)
                        <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-300 active:scale-105" onerror="this.style.display='none'; if(this.nextElementSibling) this.nextElementSibling.style.display='flex';">
                        <div style="display:none;" class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-brand-50 to-pink-50 text-brand-700">
                            <svg class="w-8 h-8 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span class="text-[10px] font-black uppercase mt-1">Showroom Item</span>
                        </div>
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-brand-50 to-pink-50 text-brand-700">
                            <svg class="w-8 h-8 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                            <span class="text-[10px] font-black uppercase mt-1">Showroom Item</span>
                        </div>
                    @endif

                    @if($hasOffer)
                    <div class="absolute top-2 left-2 bg-gradient-to-r from-rose-600 to-pink-600 text-white text-[9px] sm:text-[11px] font-black px-2 py-0.5 rounded-lg shadow-sm">
                        {{ $discount }}% OFF
                    </div>
                    @endif
                </div>

                <!-- Product Info & Action Buttons -->
                <div class="p-3 sm:p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <span class="text-[9px] sm:text-[10px] font-black text-brand-700 tracking-wider uppercase">
                            {{ $catName }}
                        </span>
                        <h4 class="font-extrabold text-gray-950 text-xs sm:text-base line-clamp-1 mt-0.5" title="{{ $product->name }}">
                            {{ $product->name }}
                        </h4>
                    </div>

                    <div class="mt-2.5">
                        <!-- Pricing -->
                        <div class="flex items-baseline gap-1.5 mb-2.5">
                            <span class="text-sm sm:text-lg font-black text-gray-950">
                                ₹{{ number_format($selling) }}
                            </span>
                            @if($hasOffer)
                            <span class="text-[10px] sm:text-xs text-gray-400 line-through font-bold">
                                ₹{{ number_format($product->price) }}
                            </span>
                            @endif
                        </div>

                        <!-- Buttons: + Bag & Buy Now -->
                        <div class="grid grid-cols-2 gap-1.5">
                            <button onclick="addToCustomerBag({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $selling }}, '{{ $img ?? '' }}')" class="py-2 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-800 font-black text-[11px] sm:text-xs transition active:scale-95 flex items-center justify-center">
                                + Bag
                            </button>
                            <button onclick="orderSingleWhatsApp('{{ addslashes($product->name) }}', {{ $selling }})" class="py-2 rounded-xl bg-brand-800 hover:bg-brand-900 text-white font-black text-[11px] sm:text-xs shadow-sm transition active:scale-95 flex items-center justify-center">
                                Buy Now
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full py-16 text-center bg-white rounded-2xl border border-gray-100 shadow-sm">
                <p class="font-serif font-black text-gray-900 text-lg">No collections available</p>
                <p class="text-xs text-gray-500 mt-1">Please check back soon.</p>
            </div>
            @endforelse
        </div>

    </main>

    <!-- Mobile App-Like Bottom Navigation Dock -->
    <nav class="fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-lg border-t border-gray-200 shadow-bottom-dock px-4 py-2 sm:hidden flex items-center justify-around">
        <!-- Home -->
        <button onclick="window.scrollTo({top: 0, behavior: 'smooth'})" class="flex flex-col items-center gap-0.5 text-brand-700 font-extrabold text-[10px]">
            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"/></svg>
            <span>Home</span>
        </button>

        <!-- Search -->
        <button onclick="document.getElementById('searchInput').focus(); window.scrollTo({top: 100, behavior: 'smooth'})" class="flex flex-col items-center gap-0.5 text-gray-500 hover:text-brand-700 font-bold text-[10px]">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
            <span>Search</span>
        </button>

        <!-- Shopping Bag -->
        <button onclick="toggleBagDrawer(true)" class="relative flex flex-col items-center gap-0.5 text-gray-900 font-extrabold text-[10px]">
            <div class="relative">
                <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span id="mobile-dock-badge" class="hidden absolute -top-1.5 -right-2 bg-brand-700 text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center ring-2 ring-white">0</span>
            </div>
            <span>Bag</span>
        </button>

        <!-- WhatsApp -->
        @if(!empty($business->whatsapp))
        <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}" target="_blank" class="flex flex-col items-center gap-0.5 text-emerald-600 font-extrabold text-[10px]">
            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.698.058-2.146-.541-1.849-.766-3.037-2.65-3.13-2.774-.093-.124-.741-.986-.741-1.88 0-.894.469-1.333.636-1.514.167-.181.365-.227.486-.227.121 0 .243.001.35.006.113.005.263-.043.411.312.155.372.529 1.288.575 1.381.046.093.077.202.015.325-.062.124-.093.202-.185.31-.093.109-.196.243-.28.326-.093.093-.19.194-.082.38.108.186.482.795 1.034 1.288.71.636 1.309.833 1.495.926.186.093.295.078.404-.047.109-.124.465-.541.589-.727.124-.186.248-.155.419-.093.17.062 1.082.51 1.268.603.186.093.31.14.356.217.046.078.046.45-.098.855z"/>
            </svg>
            <span>WhatsApp</span>
        </a>
        @endif
    </nav>

    <!-- Slide-Up / Slide-Over Bag Drawer -->
    <div id="drawer-backdrop" onclick="toggleBagDrawer(false)" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300"></div>
    
    <aside id="bag-drawer" class="fixed bottom-0 sm:top-0 right-0 w-full sm:max-w-md max-h-[85vh] sm:max-h-full bg-white z-50 rounded-t-3xl sm:rounded-none shadow-2xl transform translate-y-full sm:translate-y-0 sm:translate-x-full transition-transform duration-300 ease-out flex flex-col">
        <!-- Header -->
        <div class="p-4 sm:p-5 border-b border-gray-100 flex items-center justify-between bg-gray-50 rounded-t-3xl sm:rounded-none">
            <div>
                <h3 class="font-serif font-black text-base sm:text-lg text-gray-950">Shopping Bag</h3>
                <p class="text-[11px] text-gray-500 font-semibold" id="bag-item-count-text">0 items added</p>
            </div>
            <button onclick="toggleBagDrawer(false)" class="w-8 h-8 rounded-full bg-gray-200/80 flex items-center justify-center text-gray-600 font-bold">
                ✕
            </button>
        </div>

        <!-- Items -->
        <div id="bag-items-list" class="p-4 flex-1 overflow-y-auto space-y-3">
            <!-- Rendered by JS -->
        </div>

        <!-- Order Summary & WhatsApp Direct Button -->
        <div class="p-4 border-t border-gray-100 bg-gray-50 space-y-3">
            <div class="flex justify-between items-center text-base font-black text-gray-950">
                <span>Total Amount:</span>
                <span id="bag-total-amount" class="text-brand-700 text-xl font-black">₹0</span>
            </div>

            <div class="space-y-2">
                <input type="text" id="orderCustomerName" placeholder="Your Name *" class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                <input type="tel" id="orderCustomerPhone" placeholder="Your WhatsApp Mobile Number *" class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <button onclick="submitBagOrderToWhatsApp()" class="w-full py-3.5 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-black text-sm shadow-lg shadow-emerald-500/25 flex items-center justify-center gap-2 transition active:scale-95">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.698.058-2.146-.541-1.849-.766-3.037-2.65-3.13-2.774-.093-.124-.741-.986-.741-1.88 0-.894.469-1.333.636-1.514.167-.181.365-.227.486-.227.121 0 .243.001.35.006.113.005.263-.043.411.312.155.372.529 1.288.575 1.381.046.093.077.202.015.325-.062.124-.093.202-.185.31-.093.109-.196.243-.28.326-.093.093-.19.194-.082.38.108.186.482.795 1.034 1.288.71.636 1.309.833 1.495.926.186.093.295.078.404-.047.109-.124.465-.541.589-.727.124-.186.248-.155.419-.093.17.062 1.082.51 1.268.603.186.093.31.14.356.217.046.078.046.45-.098.855z"/>
                </svg>
                <span>Send Order on WhatsApp</span>
            </button>
        </div>
    </aside>

    <!-- Product Quick View Bottom Sheet / Modal -->
    <div id="modal-quickview" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-end sm:items-center justify-center p-0 sm:p-4">
        <div class="bg-white rounded-t-3xl sm:rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl relative slide-up">
            <button onclick="closeProductDetails()" class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/90 shadow-md flex items-center justify-center text-gray-500 font-bold">
                ✕
            </button>
            <div class="w-full aspect-square bg-gray-100">
                <img id="modal-img" src="" alt="" class="w-full h-full object-cover">
            </div>
            <div class="p-5">
                <span id="modal-cat" class="text-[9px] font-black text-brand-700 tracking-wider uppercase bg-brand-50 px-2 py-0.5 rounded"></span>
                <h3 id="modal-title" class="font-serif font-black text-base sm:text-xl text-gray-950 mt-1"></h3>
                <p id="modal-desc" class="text-gray-600 text-xs sm:text-sm mt-1.5 leading-relaxed font-normal"></p>
                <div class="mt-3 flex items-baseline gap-2">
                    <span id="modal-price" class="text-xl sm:text-2xl font-black text-gray-950"></span>
                    <span id="modal-orig" class="text-xs text-gray-400 line-through font-bold"></span>
                </div>
                <div class="grid grid-cols-2 gap-2 mt-4">
                    <button id="modal-add-btn" class="py-3 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-800 font-black text-xs transition">
                        + Add to Bag
                    </button>
                    <button id="modal-buy-btn" class="py-3 rounded-xl bg-emerald-500 text-white font-black text-xs shadow-md transition">
                        WhatsApp Order
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Core Interactive Client Script -->
    <script>
        const SHOP_NAME = "{{ addslashes($business->name ?? 'Showroom') }}";
        const SHOP_WHATSAPP = "{{ preg_replace('/[^0-9]/', '', $business->whatsapp ?? '') }}";
        let customerBag = JSON.parse(localStorage.getItem('ps_user_bag') || '[]');

        function updateBagDisplay() {
            const count = customerBag.reduce((a, b) => a + b.qty, 0);
            const total = customerBag.reduce((a, b) => a + (b.price * b.qty), 0);

            // Dock & Header badges
            const dockBadge = document.getElementById('mobile-dock-badge');
            if (count > 0) {
                dockBadge.innerText = count;
                dockBadge.classList.remove('hidden');
            } else {
                dockBadge.classList.add('hidden');
            }

            document.getElementById('bag-item-count-text').innerText = `${count} items in your bag`;
            document.getElementById('bag-total-amount').innerText = '₹' + total.toLocaleString('en-IN');

            const container = document.getElementById('bag-items-list');
            if (customerBag.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-8">
                        <p class="text-sm font-extrabold text-gray-900">Your bag is empty</p>
                        <p class="text-xs text-gray-400 mt-1">Tap "+ Bag" on any product.</p>
                    </div>
                `;
            } else {
                container.innerHTML = customerBag.map(item => `
                    <div class="flex items-center gap-3 p-2.5 rounded-xl bg-gray-50 border border-gray-100">
                        <img src="${item.img || ''}" class="w-12 h-12 rounded-lg object-cover bg-white" onerror="this.src='https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=200'">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-extrabold text-gray-950 text-xs truncate">${item.name}</h4>
                            <p class="text-xs font-black text-brand-700">₹${item.price.toLocaleString('en-IN')}</p>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button onclick="updateItemQty(${item.id}, -1)" class="w-6 h-6 rounded bg-white border text-xs font-bold">-</button>
                            <span class="text-xs font-black px-1">${item.qty}</span>
                            <button onclick="updateItemQty(${item.id}, 1)" class="w-6 h-6 rounded bg-white border text-xs font-bold">+</button>
                        </div>
                    </div>
                `).join('');
            }

            localStorage.setItem('ps_user_bag', JSON.stringify(customerBag));
        }

        function addToCustomerBag(id, name, price, img) {
            const existing = customerBag.find(x => x.id === id);
            if (existing) {
                existing.qty++;
            } else {
                customerBag.push({ id, name, price, img, qty: 1 });
            }
            updateBagDisplay();
            toggleBagDrawer(true);
        }

        function updateItemQty(id, delta) {
            const item = customerBag.find(x => x.id === id);
            if (!item) return;
            item.qty += delta;
            if (item.qty <= 0) customerBag = customerBag.filter(x => x.id !== id);
            updateBagDisplay();
        }

        function toggleBagDrawer(open) {
            const backdrop = document.getElementById('drawer-backdrop');
            const drawer = document.getElementById('bag-drawer');
            if (open) {
                backdrop.classList.remove('opacity-0', 'pointer-events-none');
                backdrop.classList.add('opacity-100');
                drawer.classList.remove('translate-y-full', 'sm:translate-x-full');
            } else {
                backdrop.classList.add('opacity-0', 'pointer-events-none');
                backdrop.classList.remove('opacity-100');
                drawer.classList.add('translate-y-full', 'sm:translate-x-full');
            }
        }

        function orderSingleWhatsApp(name, price) {
            const msg = `💎 *DIRECT ORDER - ${SHOP_NAME}*\n\n` +
                        `✨ Product: *${name}*\n` +
                        `💰 Price: ₹${price.toLocaleString('en-IN')}\n\n` +
                        `Please confirm availability & delivery time.`;
            window.open(`https://wa.me/91${SHOP_WHATSAPP}?text=${encodeURIComponent(msg)}`, '_blank');
        }

        function submitBagOrderToWhatsApp() {
            if (customerBag.length === 0) {
                alert('Your bag is empty!');
                return;
            }
            const name = document.getElementById('orderCustomerName').value.trim() || 'Customer';
            const phone = document.getElementById('orderCustomerPhone').value.trim();
            const total = customerBag.reduce((a, b) => a + (b.price * b.qty), 0);

            let msg = `🛍️ *NEW SHOWROOM ORDER from ${name}*\n` +
                      (phone ? `📞 Phone: +91 ${phone}\n` : '') +
                      `🏬 Showroom: ${SHOP_NAME}\n\n` +
                      `*ORDERED ITEMS:*\n`;

            customerBag.forEach((item, i) => {
                msg += `${i+1}. *${item.name}* (x${item.qty}) — ₹${(item.price * item.qty).toLocaleString('en-IN')}\n`;
            });

            msg += `\n💵 *TOTAL AMOUNT:* ₹${total.toLocaleString('en-IN')}\n\n` +
                   `Please confirm this order.`;

            window.open(`https://wa.me/91${SHOP_WHATSAPP}?text=${encodeURIComponent(msg)}`, '_blank');
        }

        function selectCategoryFilter(category, btn) {
            document.querySelectorAll('.cat-pill').forEach(b => {
                b.className = 'cat-pill px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-white border border-gray-200/80 text-gray-700 font-bold text-xs sm:text-sm whitespace-nowrap transition-all shadow-sm flex-shrink-0';
            });
            btn.className = 'cat-pill active-pill px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl bg-gradient-to-r from-brand-900 to-brand-700 text-white font-black text-xs sm:text-sm whitespace-nowrap shadow-sm transition-all flex-shrink-0';

            const cards = document.querySelectorAll('.prod-item');
            let count = 0;
            cards.forEach(c => {
                const cat = c.getAttribute('data-category');
                const show = (category === 'all' || cat.includes(category));
                c.style.display = show ? '' : 'none';
                if (show) count++;
            });
            document.getElementById('grid-count').innerText = `${count} items`;
        }

        function filterProductsLive() {
            const q = document.getElementById('searchInput').value.toLowerCase().trim();
            document.getElementById('clearBtn').classList.toggle('hidden', q.length === 0);

            const cards = document.querySelectorAll('.prod-item');
            let count = 0;
            cards.forEach(c => {
                const name = c.getAttribute('data-name');
                const cat = c.getAttribute('data-category');
                const show = name.includes(q) || cat.includes(q);
                c.style.display = show ? '' : 'none';
                if (show) count++;
            });
            document.getElementById('grid-count').innerText = `${count} items`;
        }

        function clearSearchInput() {
            document.getElementById('searchInput').value = '';
            filterProductsLive();
        }

        function shareShowroom() {
            if (navigator.share) {
                navigator.share({ title: SHOP_NAME, url: window.location.href });
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Link copied to clipboard!');
            }
        }

        function showProductDetails(id) {
            const card = document.querySelector(`.prod-item[data-id="${id}"]`);
            if (!card) return;
            const name = card.querySelector('h4').innerText;
            const cat = card.getAttribute('data-category');
            const price = card.getAttribute('data-price');
            const orig = card.getAttribute('data-orig-price');
            const desc = card.getAttribute('data-desc');
            const img = card.getAttribute('data-img');

            const modalImg = document.getElementById('modal-img');
            if (img && img.trim() !== '') {
                modalImg.src = img;
                modalImg.style.display = 'block';
            } else {
                modalImg.style.display = 'none';
            }
            document.getElementById('modal-cat').innerText = cat.toUpperCase();
            document.getElementById('modal-title').innerText = name;
            document.getElementById('modal-desc').innerText = desc || 'Exclusive genuine item from our verified showroom collection.';
            document.getElementById('modal-price').innerText = '₹' + Number(price).toLocaleString('en-IN');
            
            const origEl = document.getElementById('modal-orig');
            if (orig && Number(orig) > Number(price)) {
                origEl.innerText = '₹' + Number(orig).toLocaleString('en-IN');
                origEl.classList.remove('hidden');
            } else {
                origEl.classList.add('hidden');
            }

            document.getElementById('modal-add-btn').onclick = () => {
                addToCustomerBag(id, name, Number(price), img);
                closeProductDetails();
            };
            document.getElementById('modal-buy-btn').onclick = () => {
                orderSingleWhatsApp(name, Number(price));
                closeProductDetails();
            };

            document.getElementById('modal-quickview').classList.remove('hidden');
        }

        function closeProductDetails() {
            document.getElementById('modal-quickview').classList.add('hidden');
        }

        // Init
        updateBagDisplay();
    </script>
</body>
</html>
