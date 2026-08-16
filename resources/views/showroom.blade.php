<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $business->name ?? 'Showroom' }} — Premium Exclusive Showroom</title>
    <meta name="description" content="Explore live exclusive collections from {{ $business->name ?? 'our showroom' }}. Browse genuine items, live prices, and order directly on WhatsApp.">
    
    <!-- Modern Luxury Typography -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@500;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS with custom plugins -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                        serif: ['"Cinzel"', 'serif'],
                    },
                    colors: {
                        brand: {
                            50: '#FDF2F7',
                            100: '#FCE7F1',
                            200: '#FBCFE5',
                            500: '#D6227B',
                            600: '#BD1567',
                            700: '{{ $business->theme_primary ?? "#8C174A" }}',
                            800: '#6B0F37',
                            900: '#4A0825',
                            950: '#260312',
                        },
                        gold: {
                            300: '#F3E2A3',
                            400: '#E7CE75',
                            500: '{{ $business->theme_secondary ?? "#D4AF37" }}',
                            600: '#B89420',
                            700: '#947413',
                        }
                    },
                    boxShadow: {
                        'luxury': '0 20px 40px -15px rgba(140, 23, 74, 0.15), 0 0 20px 0 rgba(0, 0, 0, 0.04)',
                        'card-hover': '0 25px 50px -12px rgba(140, 23, 74, 0.22), 0 0 0 1px rgba(212, 175, 55, 0.3)',
                        'glow': '0 0 35px rgba(212, 175, 55, 0.35)',
                    }
                }
            }
        }
    </script>
    
    <style>
        /* Glassmorphism */
        .glass-nav {
            background: rgba(255, 255, 255, 0.88);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.92);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        
        /* Ultra Luxury Dark Gradient */
        .luxury-hero {
            background: radial-gradient(circle at 80% 20%, rgba(212, 175, 55, 0.18) 0%, transparent 40%),
                        radial-gradient(circle at 20% 80%, rgba(189, 21, 103, 0.3) 0%, transparent 50%),
                        linear-gradient(145deg, #1C040E 0%, #3B091E 45%, #630F32 80%, #8C174A 100%);
        }
        
        /* Card Hover Animations */
        .product-card {
            transition: all 0.35s cubic-bezier(0.34, 1.56, 0.64, 1);
        }
        .product-card:hover {
            transform: translateY(-8px) scale(1.015);
        }
        .product-card:hover .product-img {
            transform: scale(1.08);
        }
        .product-img {
            transition: transform 0.55s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Gold Shimmer Text */
        .gold-shimmer {
            background: linear-gradient(90deg, #F3E2A3 0%, #D4AF37 50%, #F3E2A3 100%);
            background-size: 200% auto;
            color: transparent;
            -webkit-background-clip: text;
            background-clip: text;
            animation: shine 4s linear infinite;
        }
        @keyframes shine {
            to { background-position: 200% center; }
        }
        
        /* Custom Scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #f8fafc; }
        ::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
        
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="bg-[#FAF8F9] text-gray-900 font-sans antialiased selection:bg-brand-500 selection:text-white pb-24 md:pb-12">

    <!-- Top Luxury Navigation Bar -->
    <header class="sticky top-0 z-50 glass-nav border-b border-gray-200/80 shadow-sm transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between gap-4">
            
            <!-- Brand Logo & Showroom Title -->
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="relative group cursor-pointer">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-brand-900 via-brand-700 to-brand-500 text-white flex items-center justify-center font-black text-xl shadow-lg shadow-brand-900/25 ring-2 ring-gold-400/40">
                        {{ strtoupper(substr($business->name ?? 'S', 0, 1)) }}
                    </div>
                    <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full bg-emerald-500 ring-2 ring-white flex items-center justify-center" title="Showroom is Open & Online">
                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-ping"></span>
                    </span>
                </div>
                <div class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <h1 class="font-extrabold text-gray-950 text-base sm:text-xl leading-tight truncate tracking-tight">
                            {{ $business->name ?? 'Showroom' }}
                        </h1>
                        <svg class="w-4 h-4 text-emerald-600 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="flex items-center gap-2 text-xs font-semibold text-gray-500 mt-0.5">
                        <span class="text-brand-700 font-bold uppercase tracking-wider text-[10px] bg-brand-50 px-2 py-0.5 rounded-full border border-brand-100">
                            {{ $business->business_type ?? 'Exclusive Boutique' }}
                        </span>
                        <span class="hidden sm:inline text-gray-400">•</span>
                        <span class="truncate">{{ $business->city ?? 'Direct Showroom Collection' }}</span>
                    </div>
                </div>
            </div>

            <!-- Header Action Center -->
            <div class="flex items-center gap-2.5 flex-shrink-0">
                <!-- Search Button (Mobile trigger) -->
                <button onclick="document.getElementById('searchInput').focus(); window.scrollTo({top: 280, behavior: 'smooth'});" class="p-2.5 rounded-xl bg-white border border-gray-200/80 text-gray-600 hover:text-brand-700 hover:border-brand-200 transition shadow-sm md:hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </button>

                <!-- Share Button -->
                <button onclick="shareShowroom()" class="p-2.5 rounded-xl bg-white border border-gray-200/80 text-gray-600 hover:text-brand-700 hover:border-brand-200 transition shadow-sm flex items-center gap-1.5 text-xs font-bold" title="Share Showroom with Friends">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/>
                    </svg>
                    <span class="hidden lg:inline">Share</span>
                </button>

                <!-- Cart Drawer Trigger -->
                <button onclick="toggleCartDrawer(true)" class="relative p-2.5 sm:px-4 rounded-xl bg-white border border-gray-200/90 text-gray-900 hover:border-brand-300 hover:text-brand-700 transition shadow-sm flex items-center gap-2 text-sm font-extrabold group">
                    <div class="relative">
                        <svg class="w-5 h-5 text-gray-700 group-hover:text-brand-700 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                        <span id="cart-badge-count" class="hidden absolute -top-2.5 -right-2.5 bg-brand-700 text-white text-[10px] font-black w-5 h-5 rounded-full flex items-center justify-center ring-2 ring-white shadow-md">
                            0
                        </span>
                    </div>
                    <span class="hidden sm:inline">Bag</span>
                    <span id="cart-header-total" class="hidden sm:inline text-xs text-brand-700 font-bold bg-brand-50 px-2 py-0.5 rounded-md">₹0</span>
                </button>

                <!-- WhatsApp Direct Connect -->
                @if(!empty($business->whatsapp))
                <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}?text={{ urlencode('Hi '.$business->name.', I am viewing your online showroom catalogue.') }}" target="_blank" class="p-2.5 sm:px-4 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-extrabold text-sm shadow-md shadow-emerald-500/25 transition-all flex items-center gap-2 hover:scale-[1.02] active:scale-95">
                    <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                        <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.698.058-2.146-.541-1.849-.766-3.037-2.65-3.13-2.774-.093-.124-.741-.986-.741-1.88 0-.894.469-1.333.636-1.514.167-.181.365-.227.486-.227.121 0 .243.001.35.006.113.005.263-.043.411.312.155.372.529 1.288.575 1.381.046.093.077.202.015.325-.062.124-.093.202-.185.31-.093.109-.196.243-.28.326-.093.093-.19.194-.082.38.108.186.482.795 1.034 1.288.71.636 1.309.833 1.495.926.186.093.295.078.404-.047.109-.124.465-.541.589-.727.124-.186.248-.155.419-.093.17.062 1.082.51 1.268.603.186.093.31.14.356.217.046.078.046.45-.098.855z"/>
                    </svg>
                    <span class="hidden md:inline">Contact</span>
                </a>
                @endif
            </div>

        </div>
    </header>

    <!-- Ultra Luxury Hero Section -->
    <section class="luxury-hero text-white pt-14 pb-24 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
        <!-- Ambient Decorative Rings -->
        <div class="absolute -top-24 -left-24 w-96 h-96 rounded-full bg-gold-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -right-24 w-96 h-96 rounded-full bg-brand-500/20 blur-3xl pointer-events-none"></div>

        <div class="max-w-4xl mx-auto text-center relative z-10">
            <!-- Luxury Pill Badge -->
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-xl border border-white/20 text-xs font-black uppercase tracking-widest mb-5 shadow-inner">
                <span class="w-2 h-2 rounded-full bg-gold-400 animate-pulse"></span>
                <span class="gold-shimmer font-serif tracking-widest">{{ $business->business_type ?? 'EXCLUSIVE LUXURY SHOWROOM' }}</span>
            </div>

            <!-- Hero Headline -->
            <h2 class="text-3xl sm:text-5xl lg:text-6xl font-serif font-black tracking-tight text-white mb-4 leading-tight">
                {{ $business->name ?? 'Pocket Showroom' }}
            </h2>

            <p class="text-pink-100/90 text-sm sm:text-lg max-w-2xl mx-auto leading-relaxed mb-8 font-medium">
                {{ $business->about ?? 'Explore our hand-crafted masterpieces, verified collections, and place instant orders directly with the showroom owner on WhatsApp.' }}
            </p>

            <!-- Search & Live Filter Bar -->
            <div class="max-w-2xl mx-auto relative group">
                <div class="absolute -inset-1 bg-gradient-to-r from-gold-400 via-pink-500 to-brand-500 rounded-3xl blur opacity-30 group-hover:opacity-60 transition duration-300"></div>
                <div class="relative flex items-center bg-white rounded-2xl shadow-2xl p-1.5">
                    <div class="pl-3.5 pr-2 text-gray-400">
                        <svg class="w-6 h-6 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <input type="text" id="searchInput" onkeyup="filterLiveCatalog()" placeholder="Search jewelry, diamonds, collections, or price..." class="w-full py-3.5 text-gray-900 placeholder-gray-400 font-semibold text-sm sm:text-base focus:outline-none bg-transparent">
                    <button onclick="clearSearch()" id="clearSearchBtn" class="hidden pr-3 text-gray-400 hover:text-gray-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
            </div>

            <!-- Features Quick Badges -->
            <div class="flex flex-wrap items-center justify-center gap-3 sm:gap-6 mt-8 text-xs font-bold text-pink-200/90">
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                    <span>100% Genuine Certified</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-gold-400" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v1h16V6a2 2 0 00-2-2H4z"/><path fill-rule="evenodd" d="M18 9H2v5a2 2 0 002 2h12a2 2 0 002-2V9zM4 13a1 1 0 011-1h1a1 1 0 110 2H5a1 1 0 01-1-1zm5-1a1 1 0 100 2h1a1 1 0 100-2H9z" clip-rule="evenodd"/></svg>
                    <span>Transparent Pricing</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg>
                    <span>Instant WhatsApp Booking</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Interactive Category Slider -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-9 relative z-20">
        <div class="bg-white/95 backdrop-blur-xl p-3 rounded-2xl shadow-luxury border border-gray-200/90 flex items-center gap-2.5 overflow-x-auto no-scrollbar">
            <button onclick="filterCategory('all', this)" class="cat-pill active-cat px-6 py-3 rounded-xl bg-gradient-to-r from-brand-900 to-brand-700 text-white font-extrabold text-xs sm:text-sm whitespace-nowrap shadow-md shadow-brand-900/20 transition-all">
                ✨ All Items ({{ count($products ?? []) }})
            </button>
            @foreach($categories ?? [] as $cat)
            @php $cname = is_string($cat) ? $cat : ($cat->name ?? 'Category'); @endphp
            <button onclick="filterCategory('{{ strtolower($cname) }}', this)" class="cat-pill px-5 py-3 rounded-xl bg-gray-100/80 hover:bg-gray-200/80 text-gray-700 font-bold text-xs sm:text-sm whitespace-nowrap transition-all">
                {{ $cname }}
            </button>
            @endforeach
        </div>
    </div>

    <!-- Live Catalogue Main Section -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-12">
        
        <!-- Header Bar with Count -->
        <div class="flex items-center justify-between mb-8">
            <div>
                <h3 id="catalog-title" class="font-serif font-black text-2xl sm:text-3xl text-gray-950 tracking-tight">
                    Exclusive Collection
                </h3>
                <p class="text-xs sm:text-sm text-gray-500 font-medium mt-1">Showing all live verified items available for order</p>
            </div>
            <span id="product-count-badge" class="bg-brand-50 border border-brand-100 text-brand-800 text-xs sm:text-sm font-extrabold px-3.5 py-1.5 rounded-xl">
                {{ count($products ?? []) }} items
            </span>
        </div>

        <!-- Luxury Product Grid -->
        <div id="product-grid" class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @forelse($products ?? [] as $product)
            @php
                $img = !empty($product->images) && count($product->images) > 0 ? $product->images[0]->path : null;
                if ($img && !str_starts_with($img, 'http')) {
                    $img = asset('storage/'.$img);
                }
                $hasOffer = !empty($product->offer_price) && $product->offer_price < $product->price;
                $selling = $hasOffer ? $product->offer_price : $product->price;
                $discount = $hasOffer ? round((($product->price - $product->offer_price) / $product->price) * 100) : 0;
                $catName = $product->category->name ?? 'Exclusive';
            @endphp
            
            <div class="product-card group bg-white rounded-3xl border border-gray-200/90 shadow-sm hover:shadow-card-hover overflow-hidden flex flex-col justify-between"
                 data-id="{{ $product->id }}"
                 data-name="{{ strtolower($product->name) }}"
                 data-category="{{ strtolower($catName) }}"
                 data-price="{{ $selling }}"
                 data-img="{{ $img ?? '' }}"
                 data-desc="{{ $product->description ?? '' }}"
                 data-original-price="{{ $product->price }}">
                
                <!-- Image Container with Aspect Ratio & Badges -->
                <div class="relative w-full aspect-[4/4.2] bg-gradient-to-br from-gray-50 to-pink-50/40 overflow-hidden cursor-pointer" onclick="openQuickView({{ $product->id }})">
                    @if($img)
                        <img src="{{ $img }}" alt="{{ $product->name }}" loading="lazy" class="product-img w-full h-full object-cover" onerror="this.onerror=null; this.src='https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=600&auto=format&fit=crop&q=80';">
                    @else
                        <div class="w-full h-full flex flex-col items-center justify-center text-brand-700 bg-gradient-to-br from-brand-50/60 to-gold-50/40">
                            <div class="w-14 h-14 rounded-2xl bg-brand-100/60 flex items-center justify-center shadow-inner">
                                <svg class="w-7 h-7 text-brand-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <span class="text-xs font-extrabold mt-2 tracking-wider uppercase text-brand-900 font-serif">Showroom Item</span>
                        </div>
                    @endif

                    <!-- Floating Discount Badge -->
                    @if($hasOffer)
                    <div class="absolute top-3 left-3 bg-gradient-to-r from-rose-600 to-pink-600 text-white text-[10px] sm:text-xs font-black px-2.5 py-1 rounded-xl shadow-md flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M11.3 1.046A1 1 0 0112 2v5h4a1 1 0 01.82 1.573l-7 10A1 1 0 018 18v-5H4a1 1 0 01-.82-1.573l7-10a1 1 0 011.12-.38z" clip-rule="evenodd"/></svg>
                        <span>{{ $discount }}% OFF</span>
                    </div>
                    @endif

                    <!-- Featured Star Badge -->
                    @if(!empty($product->featured))
                    <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-md p-1.5 rounded-xl shadow-sm text-gold-500">
                        <svg class="w-4 h-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    </div>
                    @endif

                    <!-- Quick View Overlay on Hover -->
                    <div class="absolute inset-0 bg-black/25 opacity-0 group-hover:opacity-100 transition-opacity duration-200 flex items-center justify-center">
                        <span class="bg-white/95 backdrop-blur-md text-gray-900 text-xs font-black px-4 py-2 rounded-xl shadow-lg transform translate-y-2 group-hover:translate-y-0 transition">
                            🔍 Quick View
                        </span>
                    </div>
                </div>

                <!-- Product Details Body -->
                <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-black text-brand-700 tracking-wider uppercase bg-brand-50 px-2 py-0.5 rounded-md border border-brand-100/60">
                            {{ $catName }}
                        </span>
                        <h4 class="font-extrabold text-gray-950 text-sm sm:text-base line-clamp-1 mt-2 group-hover:text-brand-700 transition" title="{{ $product->name }}">
                            {{ $product->name }}
                        </h4>
                        @if(!empty($product->description))
                        <p class="text-gray-500 text-xs line-clamp-2 mt-1 leading-relaxed font-medium">
                            {{ $product->description }}
                        </p>
                        @endif
                    </div>

                    <div class="mt-4 pt-3 border-t border-gray-100">
                        <!-- Pricing -->
                        <div class="flex items-baseline gap-2 mb-3.5">
                            <span class="text-lg sm:text-xl font-black text-gray-950">
                                ₹{{ number_format($selling) }}
                            </span>
                            @if($hasOffer)
                            <span class="text-xs text-gray-400 line-through font-bold">
                                ₹{{ number_format($product->price) }}
                            </span>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="grid grid-cols-2 gap-2">
                            <button onclick="addToBag({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $selling }}, '{{ $img ?? '' }}')" class="py-2.5 px-2 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-800 font-extrabold text-xs transition active:scale-95 flex items-center justify-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                                <span>Add Bag</span>
                            </button>
                            <button onclick="instantBuyWhatsApp('{{ addslashes($product->name) }}', {{ $selling }}, '{{ $img ?? '' }}')" class="py-2.5 px-2 rounded-xl bg-gradient-to-r from-brand-900 to-brand-700 hover:from-brand-950 hover:to-brand-800 text-white font-extrabold text-xs shadow-md shadow-brand-900/20 transition active:scale-95 flex items-center justify-center gap-1">
                                <span>Buy Now</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
            @empty
            <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-gray-100 shadow-sm">
                <div class="w-20 h-20 bg-brand-50 text-brand-700 rounded-3xl flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h4 class="font-serif font-black text-gray-950 text-xl">Showroom Catalogue Updating</h4>
                <p class="text-sm text-gray-500 max-w-md mx-auto mt-2 font-medium">
                    The owner is currently uploading fresh collections. Please check back in a moment or chat directly on WhatsApp.
                </p>
                @if(!empty($business->whatsapp))
                <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}" target="_blank" class="inline-flex items-center gap-2 mt-6 px-6 py-3 rounded-2xl bg-emerald-500 text-white font-extrabold text-sm shadow-lg shadow-emerald-500/25 hover:bg-emerald-600 transition">
                    💬 WhatsApp Showroom
                </a>
                @endif
            </div>
            @endforelse
        </div>

    </main>

    <!-- Slide-Over Cart Drawer -->
    <div id="cart-overlay" onclick="toggleCartDrawer(false)" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 opacity-0 pointer-events-none transition-opacity duration-300"></div>
    
    <aside id="cart-drawer" class="fixed top-0 right-0 bottom-0 w-full max-w-md bg-white z-50 shadow-2xl transform translate-x-full transition-transform duration-300 ease-out flex flex-col">
        <!-- Drawer Header -->
        <div class="p-6 border-b border-gray-100 flex items-center justify-between bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center gap-2.5">
                <div class="w-10 h-10 rounded-xl bg-brand-100 text-brand-700 flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                </div>
                <div>
                    <h3 class="font-serif font-black text-lg text-gray-950">Shopping Bag</h3>
                    <p class="text-xs text-gray-500 font-semibold" id="cart-drawer-subtitle">0 items added</p>
                </div>
            </div>
            <button onclick="toggleCartDrawer(false)" class="p-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Drawer Item List -->
        <div id="cart-items-container" class="p-6 flex-1 overflow-y-auto space-y-4">
            <!-- Rendered by JavaScript -->
        </div>

        <!-- Drawer Footer with Instant WhatsApp Checkout -->
        <div class="p-6 border-t border-gray-100 bg-gray-50 space-y-4">
            <div class="space-y-2">
                <div class="flex justify-between text-xs font-semibold text-gray-500">
                    <span>Subtotal</span>
                    <span id="cart-drawer-subtotal">₹0</span>
                </div>
                <div class="flex justify-between text-xs font-semibold text-emerald-600">
                    <span>Showroom WhatsApp Booking</span>
                    <span>FREE</span>
                </div>
                <div class="border-t border-gray-200 pt-2 flex justify-between text-base font-black text-gray-950">
                    <span>Total Amount</span>
                    <span id="cart-drawer-total" class="text-brand-700 text-xl">₹0</span>
                </div>
            </div>

            <!-- Customer Details Input -->
            <div class="space-y-2.5 pt-2">
                <input type="text" id="custName" placeholder="Your Name (e.g. Rahul Sharma)" class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
                <input type="tel" id="custPhone" placeholder="Your WhatsApp Mobile Number" class="w-full px-3.5 py-2.5 text-xs font-semibold bg-white border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-brand-500">
            </div>

            <button onclick="submitCartOrderWhatsApp()" class="w-full py-4 rounded-2xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-extrabold text-sm shadow-xl shadow-emerald-500/25 flex items-center justify-center gap-2 transition active:scale-95">
                <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.698.058-2.146-.541-1.849-.766-3.037-2.65-3.13-2.774-.093-.124-.741-.986-.741-1.88 0-.894.469-1.333.636-1.514.167-.181.365-.227.486-.227.121 0 .243.001.35.006.113.005.263-.043.411.312.155.372.529 1.288.575 1.381.046.093.077.202.015.325-.062.124-.093.202-.185.31-.093.109-.196.243-.28.326-.093.093-.19.194-.082.38.108.186.482.795 1.034 1.288.71.636 1.309.833 1.495.926.186.093.295.078.404-.047.109-.124.465-.541.589-.727.124-.186.248-.155.419-.093.17.062 1.082.51 1.268.603.186.093.31.14.356.217.046.078.046.45-.098.855z"/>
                </svg>
                <span>Order on WhatsApp Now</span>
            </button>
        </div>
    </aside>

    <!-- Quick View Product Modal -->
    <div id="quickview-modal" class="fixed inset-0 bg-black/70 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-lg w-full overflow-hidden shadow-2xl relative animate-in fade-in zoom-in-95 duration-200">
            <button onclick="closeQuickView()" class="absolute top-4 right-4 z-10 w-9 h-9 rounded-full bg-white/90 backdrop-blur-md shadow-md flex items-center justify-center text-gray-500 hover:text-gray-900 transition">
                ✕
            </button>
            <div class="relative w-full aspect-square bg-gray-100">
                <img id="qv-img" src="" alt="" class="w-full h-full object-cover">
            </div>
            <div class="p-6">
                <span id="qv-cat" class="text-[10px] font-black text-brand-700 tracking-wider uppercase bg-brand-50 px-2.5 py-1 rounded-md"></span>
                <h3 id="qv-title" class="font-serif font-black text-xl text-gray-950 mt-2"></h3>
                <p id="qv-desc" class="text-gray-600 text-xs sm:text-sm mt-2 leading-relaxed font-medium"></p>
                <div class="mt-4 flex items-baseline gap-2">
                    <span id="qv-price" class="text-2xl font-black text-gray-950"></span>
                    <span id="qv-orig-price" class="text-sm text-gray-400 line-through font-bold"></span>
                </div>
                <div class="grid grid-cols-2 gap-3 mt-6">
                    <button id="qv-add-btn" class="py-3.5 rounded-xl bg-brand-50 hover:bg-brand-100 text-brand-800 font-extrabold text-sm transition">
                        + Add to Bag
                    </button>
                    <button id="qv-buy-btn" class="py-3.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 text-white font-extrabold text-sm shadow-md transition">
                        Order on WhatsApp
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating WhatsApp Quick Button (Mobile) -->
    @if(!empty($business->whatsapp))
    <aside class="fixed bottom-5 right-5 z-40 md:hidden">
        <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}" target="_blank" class="w-14 h-14 rounded-full bg-gradient-to-tr from-emerald-600 to-emerald-400 text-white flex items-center justify-center shadow-xl shadow-emerald-600/40 ring-4 ring-white animate-bounce">
            <svg class="w-7 h-7 fill-current" viewBox="0 0 24 24">
                <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.582 2.128 2.182-.573c.978.58 1.911.928 3.145.929 3.178 0 5.767-2.587 5.768-5.766.001-3.187-2.575-5.771-5.764-5.771zm3.392 8.244c-.144.405-.837.774-1.17.824-.312.045-.698.058-2.146-.541-1.849-.766-3.037-2.65-3.13-2.774-.093-.124-.741-.986-.741-1.88 0-.894.469-1.333.636-1.514.167-.181.365-.227.486-.227.121 0 .243.001.35.006.113.005.263-.043.411.312.155.372.529 1.288.575 1.381.046.093.077.202.015.325-.062.124-.093.202-.185.31-.093.109-.196.243-.28.326-.093.093-.19.194-.082.38.108.186.482.795 1.034 1.288.71.636 1.309.833 1.495.926.186.093.295.078.404-.047.109-.124.465-.541.589-.727.124-.186.248-.155.419-.093.17.062 1.082.51 1.268.603.186.093.31.14.356.217.046.078.046.45-.098.855z"/>
            </svg>
        </a>
    </aside>
    @endif

    <!-- Core Interactive Client JavaScript -->
    <script>
        const SHOP_NAME = "{{ addslashes($business->name ?? 'Showroom') }}";
        const SHOP_WHATSAPP = "{{ preg_replace('/[^0-9]/', '', $business->whatsapp ?? '') }}";
        let bag = JSON.parse(localStorage.getItem('ps_bag_items') || '[]');

        function updateBagUI() {
            const count = bag.reduce((sum, item) => sum + item.qty, 0);
            const total = bag.reduce((sum, item) => sum + (item.price * item.qty), 0);

            // Badges
            const badge = document.getElementById('cart-badge-count');
            const totalLabel = document.getElementById('cart-header-total');
            if (count > 0) {
                badge.innerText = count;
                badge.classList.remove('hidden');
                totalLabel.innerText = '₹' + total.toLocaleString('en-IN');
                totalLabel.classList.remove('hidden');
            } else {
                badge.classList.add('hidden');
                totalLabel.classList.add('hidden');
            }

            // Drawer Items
            document.getElementById('cart-drawer-subtitle').innerText = `${count} items in your bag`;
            document.getElementById('cart-drawer-subtotal').innerText = '₹' + total.toLocaleString('en-IN');
            document.getElementById('cart-drawer-total').innerText = '₹' + total.toLocaleString('en-IN');

            const container = document.getElementById('cart-items-container');
            if (bag.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto mb-3">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                        </div>
                        <h4 class="font-extrabold text-gray-900">Your bag is empty</h4>
                        <p class="text-xs text-gray-500 mt-1">Explore our collections and add items.</p>
                    </div>
                `;
            } else {
                container.innerHTML = bag.map(item => `
                    <div class="flex items-center gap-3.5 p-3 rounded-2xl border border-gray-100 bg-gray-50/50">
                        <img src="${item.img || 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=200'}" class="w-16 h-16 rounded-xl object-cover flex-shrink-0 bg-white" onerror="this.src='https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=200'">
                        <div class="flex-1 min-w-0">
                            <h4 class="font-extrabold text-gray-900 text-xs sm:text-sm truncate">${item.name}</h4>
                            <p class="text-xs font-black text-brand-700 mt-0.5">₹${item.price.toLocaleString('en-IN')}</p>
                            <div class="flex items-center gap-2 mt-2">
                                <button onclick="changeQty(${item.id}, -1)" class="w-6 h-6 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-bold text-xs hover:bg-gray-100">-</button>
                                <span class="text-xs font-black">${item.qty}</span>
                                <button onclick="changeQty(${item.id}, 1)" class="w-6 h-6 rounded-lg bg-white border border-gray-200 flex items-center justify-center font-bold text-xs hover:bg-gray-100">+</button>
                            </div>
                        </div>
                        <button onclick="removeFromBag(${item.id})" class="text-gray-400 hover:text-rose-600 p-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                `).join('');
            }

            localStorage.setItem('ps_bag_items', JSON.stringify(bag));
        }

        function addToBag(id, name, price, img) {
            const found = bag.find(x => x.id === id);
            if (found) {
                found.qty++;
            } else {
                bag.push({ id, name, price, img, qty: 1 });
            }
            updateBagUI();
            toggleCartDrawer(true);
        }

        function changeQty(id, delta) {
            const found = bag.find(x => x.id === id);
            if (!found) return;
            found.qty += delta;
            if (found.qty <= 0) {
                bag = bag.filter(x => x.id !== id);
            }
            updateBagUI();
        }

        function removeFromBag(id) {
            bag = bag.filter(x => x.id !== id);
            updateBagUI();
        }

        function toggleCartDrawer(open) {
            const overlay = document.getElementById('cart-overlay');
            const drawer = document.getElementById('cart-drawer');
            if (open) {
                overlay.classList.remove('opacity-0', 'pointer-events-none');
                overlay.classList.add('opacity-100');
                drawer.classList.remove('translate-x-full');
            } else {
                overlay.classList.add('opacity-0', 'pointer-events-none');
                overlay.classList.remove('opacity-100');
                drawer.classList.add('translate-x-full');
            }
        }

        function instantBuyWhatsApp(name, price, img) {
            const msg = `💎 *ORDER INQUIRY - ${SHOP_NAME}*\n\n` +
                        `I want to buy:\n` +
                        `✨ *${name}*\n` +
                        `💰 *Price:* ₹${price.toLocaleString('en-IN')}\n\n` +
                        `Please share stock availability and payment details.`;
            window.open(`https://wa.me/91${SHOP_WHATSAPP}?text=${encodeURIComponent(msg)}`, '_blank');
        }

        function submitCartOrderWhatsApp() {
            if (bag.length === 0) {
                alert('Your bag is empty! Please add items first.');
                return;
            }
            const name = document.getElementById('custName').value.trim() || 'Customer';
            const phone = document.getElementById('custPhone').value.trim();
            const total = bag.reduce((sum, item) => sum + (item.price * item.qty), 0);

            let msg = `🛍️ *NEW SHOWROOM ORDER from ${name}*\n` +
                      (phone ? `📞 Phone: +91 ${phone}\n` : '') +
                      `🏬 Showroom: ${SHOP_NAME}\n\n` +
                      `*ORDERED ITEMS:*\n`;

            bag.forEach((item, i) => {
                msg += `${i+1}. *${item.name}* (x${item.qty}) — ₹${(item.price * item.qty).toLocaleString('en-IN')}\n`;
            });

            msg += `\n💵 *TOTAL AMOUNT:* ₹${total.toLocaleString('en-IN')}\n\n` +
                   `Please confirm this order and dispatch details.`;

            window.open(`https://wa.me/91${SHOP_WHATSAPP}?text=${encodeURIComponent(msg)}`, '_blank');
        }

        function shareShowroom() {
            if (navigator.share) {
                navigator.share({
                    title: SHOP_NAME + ' Live Showroom',
                    text: `Explore exclusive collections from ${SHOP_NAME} online!`,
                    url: window.location.href,
                });
            } else {
                navigator.clipboard.writeText(window.location.href);
                alert('Showroom link copied to clipboard!');
            }
        }

        function filterCategory(cat, btn) {
            document.querySelectorAll('.cat-pill').forEach(b => {
                b.className = 'cat-pill px-5 py-3 rounded-xl bg-gray-100/80 hover:bg-gray-200/80 text-gray-700 font-bold text-xs sm:text-sm whitespace-nowrap transition-all';
            });
            btn.className = 'cat-pill active-cat px-6 py-3 rounded-xl bg-gradient-to-r from-brand-900 to-brand-700 text-white font-extrabold text-xs sm:text-sm whitespace-nowrap shadow-md shadow-brand-900/20 transition-all';

            const cards = document.querySelectorAll('.product-card');
            let visibleCount = 0;
            cards.forEach(c => {
                const itemCat = c.getAttribute('data-category');
                const show = (cat === 'all' || itemCat.includes(cat));
                c.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });
            document.getElementById('product-count-badge').innerText = `${visibleCount} items`;
        }

        function filterLiveCatalog() {
            const query = document.getElementById('searchInput').value.toLowerCase().trim();
            const clearBtn = document.getElementById('clearSearchBtn');
            clearBtn.classList.toggle('hidden', query.length === 0);

            const cards = document.querySelectorAll('.product-card');
            let visibleCount = 0;
            cards.forEach(c => {
                const name = c.getAttribute('data-name');
                const cat = c.getAttribute('data-category');
                const show = name.includes(query) || cat.includes(query);
                c.style.display = show ? '' : 'none';
                if (show) visibleCount++;
            });
            document.getElementById('product-count-badge').innerText = `${visibleCount} items`;
        }

        function clearSearch() {
            document.getElementById('searchInput').value = '';
            filterLiveCatalog();
        }

        function openQuickView(productId) {
            const card = document.querySelector(`.product-card[data-id="${productId}"]`);
            if (!card) return;
            const name = card.querySelector('h4').innerText;
            const cat = card.getAttribute('data-category');
            const price = card.getAttribute('data-price');
            const origPrice = card.getAttribute('data-original-price');
            const desc = card.getAttribute('data-desc');
            const img = card.getAttribute('data-img');

            document.getElementById('qv-img').src = img || 'https://images.unsplash.com/photo-1515562141207-7a88fb7ce338?w=600';
            document.getElementById('qv-cat').innerText = cat.toUpperCase();
            document.getElementById('qv-title').innerText = name;
            document.getElementById('qv-desc').innerText = desc || 'Exclusive genuine item from our verified live showroom collection.';
            document.getElementById('qv-price').innerText = '₹' + Number(price).toLocaleString('en-IN');
            
            const origEl = document.getElementById('qv-orig-price');
            if (origPrice && Number(origPrice) > Number(price)) {
                origEl.innerText = '₹' + Number(origPrice).toLocaleString('en-IN');
                origEl.classList.remove('hidden');
            } else {
                origEl.classList.add('hidden');
            }

            document.getElementById('qv-add-btn').onclick = () => {
                addToBag(productId, name, Number(price), img);
                closeQuickView();
            };
            document.getElementById('qv-buy-btn').onclick = () => {
                instantBuyWhatsApp(name, Number(price), img);
                closeQuickView();
            };

            document.getElementById('quickview-modal').classList.remove('hidden');
        }

        function closeQuickView() {
            document.getElementById('quickview-modal').classList.add('hidden');
        }

        // Initialize UI
        updateBagUI();
    </script>
</body>
</html>
