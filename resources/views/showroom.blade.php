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
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #F8FAFC; color: #0F172A; }
        .glass-header { background: rgba(255, 255, 255, 0.9); backdrop-filter: blur(14px); }
        .product-card { transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s ease; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 16px 30px -10px rgba(79, 70, 229, 0.12); }
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="min-h-screen flex flex-col relative pb-24"
      x-data="showroomData()"
      x-init="initCart(); initTracking()">

    <!-- Header -->
    <header class="sticky top-0 z-40 glass-header border-b border-slate-200/80 shadow-xs">
        <div class="max-w-5xl mx-auto px-4 py-3 flex items-center justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-purple-600 via-indigo-600 to-pink-500 flex items-center justify-center text-white font-extrabold text-xl shadow-md shadow-indigo-500/20">
                    {{ strtoupper(substr($business->name, 0, 1)) }}
                </div>
                <div>
                    <h1 class="font-extrabold text-slate-900 text-lg leading-tight">{{ $business->name }}</h1>
                    <p class="text-xs font-semibold text-slate-500 flex items-center gap-1.5">
                        <span class="bg-purple-100 text-purple-700 px-2 py-0.5 rounded-md text-[11px] font-bold">{{ $business->business_type ?? 'Digital Showroom' }}</span>
                        @if($business->city) • <span>📍 {{ $business->city }}</span> @endif
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-2">
                <!-- Cart Trigger Button -->
                <button @click="cartOpen = true"
                        class="relative p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl font-bold transition flex items-center gap-2 text-sm border border-slate-200/80">
                    <svg class="w-5 h-5 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z"/></svg>
                    <span class="hidden sm:inline font-bold">Cart</span>
                    <span x-show="totalCartItems > 0"
                          x-text="totalCartItems"
                          class="bg-indigo-600 text-white text-[11px] font-black px-2 py-0.5 rounded-full shadow-xs"></span>
                </button>

                @if($business->whatsapp)
                <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}?text={{ urlencode('Hi '.$business->name.', I visited your online showroom.') }}"
                   @click="track('whatsapp_click', null, {placement:'header'})"
                   target="_blank"
                   class="inline-flex items-center gap-1.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-4 py-2.5 rounded-xl text-sm transition shadow-sm shadow-emerald-600/20">
                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                    <span class="hidden xs:inline">WhatsApp</span>
                </a>
                @endif
            </div>
        </div>
    </header>

    <!-- Banner Info -->
    <div class="bg-gradient-to-r from-slate-900 via-purple-950 to-indigo-950 text-white py-12 px-4 relative overflow-hidden">
        <div class="absolute -right-10 -bottom-10 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="max-w-5xl mx-auto text-center relative z-10">
            <h2 class="text-3xl sm:text-4xl font-black tracking-tight mb-3">Welcome to {{ $business->name }}</h2>
            <p class="text-purple-200/90 text-sm sm:text-base max-w-xl mx-auto font-medium leading-relaxed mb-6">{{ $business->about ?? 'Explore our latest collection, add items to cart and order directly on WhatsApp.' }}</p>

            <!-- Search Bar -->
            <div class="max-w-md mx-auto relative">
                <input type="text"
                       x-model="searchQuery"
                       placeholder="Search products in {{ $business->name }}..."
                       class="w-full bg-white/10 backdrop-blur-md text-white placeholder-purple-200/60 border border-white/20 rounded-2xl px-4 py-3 pl-11 text-sm focus:outline-none focus:ring-2 focus:ring-purple-400/80 shadow-lg">
                <svg class="w-5 h-5 text-purple-200/70 absolute left-3.5 top-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                <button x-show="searchQuery" @click="searchQuery = ''" class="absolute right-3.5 top-3 text-purple-200 hover:text-white font-bold text-xs">✕ Clear</button>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main class="max-w-5xl mx-auto px-4 py-8 flex-1 w-full">

        <!-- Category Filter Tabs -->
        @if($categories->count() > 0)
        <div class="flex items-center gap-2.5 overflow-x-auto pb-4 mb-8 no-scrollbar">
            <button @click="activeCategory = 'all'"
                    :class="activeCategory === 'all' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25 border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'"
                    class="font-bold text-xs px-5 py-2.5 rounded-full border transition cursor-pointer whitespace-nowrap">
                ✨ All Products ({{ $products->count() }})
            </button>
            @foreach($categories as $cat)
            <button @click="activeCategory = '{{ $cat->id }}'"
                    :class="activeCategory === '{{ $cat->id }}' ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/25 border-indigo-600' : 'bg-white text-slate-700 border-slate-200 hover:bg-slate-100'"
                    class="font-bold text-xs px-5 py-2.5 rounded-full border transition cursor-pointer whitespace-nowrap">
                {{ $cat->name }}
            </button>
            @endforeach
        </div>
        @endif

        <!-- Product Grid -->
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 sm:gap-6">
            @foreach($products as $product)
            @php
                $img = $product->images->first()?->path;
                $imgUrl = $img ? (str_starts_with($img, 'http') ? $img : asset('storage/'.$img)) : 'https://images.unsplash.com/photo-1599643478518-a784e5dc4c8f?w=600&auto=format&fit=crop&q=80';
                $waText = urlencode("Hi {$business->name}, I am interested in {$product->name} (Price: ₹".number_format($product->offer_price ?? $product->price)."). Please share availability.");
            @endphp
            <div class="product-card bg-white rounded-3xl border border-slate-200/90 overflow-hidden flex flex-col"
                 x-show="matchesFilter('{{ $product->category_id }}', '{{ strtolower(addslashes($product->name)) }}')"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100">

                <div class="relative aspect-square bg-slate-100 overflow-hidden cursor-pointer"
                     @click="openProductModal({{ json_encode([
                         'id' => $product->id,
                         'name' => $product->name,
                         'category' => $product->category?->name ?? 'Catalogue',
                         'description' => $product->description,
                         'price' => $product->price,
                         'offer_price' => $product->offer_price,
                         'img' => $imgUrl,
                         'waText' => $waText
                     ]) }})">
                    <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                    @if($product->offer_price && $product->offer_price < $product->price)
                    <span class="absolute top-3 left-3 bg-rose-500 text-white text-[10px] font-black px-2.5 py-1 rounded-full shadow-md tracking-wider">
                        OFFER
                    </span>
                    @endif
                </div>

                <div class="p-4 flex-1 flex flex-col justify-between">
                    <div>
                        <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest block mb-1">{{ $product->category?->name ?? 'Catalogue' }}</span>
                        <h3 class="font-bold text-slate-900 text-sm sm:text-base line-clamp-1 mb-1">{{ $product->name }}</h3>
                        <p class="text-xs text-slate-500 line-clamp-2 mb-3 leading-relaxed">{{ $product->description }}</p>
                    </div>

                    <div>
                        <div class="flex items-baseline gap-2 mb-3">
                            <span class="text-lg font-black text-slate-900">₹{{ number_format($product->offer_price ?? $product->price) }}</span>
                            @if($product->offer_price && $product->offer_price < $product->price)
                            <span class="text-xs font-semibold text-slate-400 line-through">₹{{ number_format($product->price) }}</span>
                            @endif
                        </div>

                        <div class="grid grid-cols-2 gap-2">
                            <!-- Add to Cart -->
                            <button @click="addToCart({{ json_encode([
                                        'id' => $product->id,
                                        'name' => $product->name,
                                        'price' => $product->offer_price ?? $product->price,
                                        'img' => $imgUrl
                                    ]) }})"
                                    class="py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 text-xs font-bold rounded-xl flex items-center justify-center gap-1 transition border border-slate-200">
                                <span>+ Cart</span>
                            </button>

                            <!-- Buy Now / Inquire -->
                            @if($business->whatsapp)
                            <a href="https://wa.me/91{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}?text={{ $waText }}"
                               @click="track('whatsapp_click', {{ $product->id }}, {placement:'product_card'})"
                               target="_blank"
                               class="py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl flex items-center justify-center gap-1 transition shadow-sm shadow-indigo-600/20">
                                <span>Buy Now</span>
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

    </main>

    <!-- Floating Bottom Cart Bar -->
    <div x-show="totalCartItems > 0"
         x-cloak
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="translate-y-full opacity-0"
         x-transition:enter-end="translate-y-0 opacity-100"
         class="fixed bottom-4 left-4 right-4 max-w-xl mx-auto z-40 bg-slate-900 text-white rounded-2xl p-4 shadow-2xl flex items-center justify-between border border-slate-800">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center font-bold text-white text-sm">
                🛒 <span x-text="totalCartItems"></span>
            </div>
            <div>
                <p class="text-xs font-medium text-slate-400"><span x-text="totalCartItems"></span> Item(s) Selected</p>
                <p class="text-base font-black text-white">Total: ₹<span x-text="formattedCartTotal"></span></p>
            </div>
        </div>
        <button @click="cartOpen = true" class="bg-emerald-500 hover:bg-emerald-600 text-white font-extrabold px-5 py-2.5 rounded-xl text-sm transition shadow-md shadow-emerald-500/20">
            View Cart & Order ➔
        </button>
    </div>

    <!-- Cart Drawer Modal -->
    <div x-show="cartOpen" x-cloak class="fixed inset-0 z-50 overflow-hidden" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs transition-opacity" @click="cartOpen = false"></div>
        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
            <div class="pointer-events-auto w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between">
                
                <!-- Drawer Header -->
                <div class="p-5 border-b border-slate-200 flex items-center justify-between bg-slate-50">
                    <div class="flex items-center gap-2">
                        <span class="text-xl">🛍️</span>
                        <h3 class="text-lg font-black text-slate-900">Your Shopping Cart</h3>
                    </div>
                    <button @click="cartOpen = false" class="text-slate-400 hover:text-slate-600 font-bold p-1">✕</button>
                </div>

                <!-- Cart Items List -->
                <div class="p-5 flex-1 overflow-y-auto space-y-4">
                    <template x-if="cart.length === 0">
                        <div class="text-center py-12">
                            <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center text-3xl mx-auto mb-3">🛒</div>
                            <h4 class="font-bold text-slate-800">Your cart is empty</h4>
                            <p class="text-xs text-slate-500 mt-1">Browse products and tap "+ Add to Cart"</p>
                        </div>
                    </template>

                    <template x-for="item in cart" :key="item.id">
                        <div class="flex items-center justify-between gap-3 p-3 border border-slate-200 rounded-2xl bg-white shadow-2xs">
                            <img :src="item.img" :alt="item.name" class="w-14 h-14 rounded-xl object-cover bg-slate-100">
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-slate-900 text-sm truncate" x-text="item.name"></h4>
                                <p class="text-xs font-black text-indigo-600">₹<span x-text="Number(item.price).toLocaleString('en-IN')"></span></p>
                            </div>
                            <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl">
                                <button @click="updateQty(item.id, -1)" class="w-6 h-6 rounded-lg bg-white font-bold text-slate-700 flex items-center justify-center text-xs shadow-2xs">-</button>
                                <span class="text-xs font-black text-slate-900 w-4 text-center" x-text="item.qty"></span>
                                <button @click="updateQty(item.id, 1)" class="w-6 h-6 rounded-lg bg-white font-bold text-slate-700 flex items-center justify-center text-xs shadow-2xs">+</button>
                            </div>
                        </div>
                    </template>
                </div>

                <!-- Drawer Footer & WhatsApp Checkout -->
                <div class="p-5 border-t border-slate-200 bg-slate-50 space-y-4">
                    <div class="flex justify-between items-center text-base font-black text-slate-900">
                        <span>Grand Total:</span>
                        <span class="text-xl text-indigo-600">₹<span x-text="formattedCartTotal"></span></span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <input x-model="customerName" type="text" placeholder="Your name" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm bg-white">
                        <input x-model="customerPhone" type="tel" placeholder="Mobile number" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-sm bg-white">
                    </div>
                    <p class="text-[10px] text-slate-500">Name/mobile helps the shop owner identify your enquiry. Actual WhatsApp replies stay inside WhatsApp.</p>
                    @if($business->whatsapp)
                    <button @click="sendWhatsAppOrder('{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}', '{{ addslashes($business->name) }}')"
                            :disabled="cart.length === 0"
                            class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-50 text-white font-black text-sm rounded-xl transition flex items-center justify-center gap-2 shadow-md shadow-emerald-600/20">
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                        <span>Send Order on WhatsApp</span>
                    </button>
                    @endif
                </div>

            </div>
        </div>
    </div>

    <!-- Product Detail Modal -->
    <div x-show="modalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-xs" @click="modalOpen = false"></div>
        <div class="relative max-w-lg w-full bg-white rounded-3xl shadow-2xl overflow-hidden z-10 flex flex-col max-h-[90vh]">
            <button @click="modalOpen = false" class="absolute top-3 right-3 bg-slate-900/50 hover:bg-slate-900 text-white rounded-full w-8 h-8 flex items-center justify-center font-bold z-20">✕</button>
            <div class="aspect-square bg-slate-100 relative overflow-hidden">
                <img :src="selectedProduct.img" :alt="selectedProduct.name" class="w-full h-full object-cover">
            </div>
            <div class="p-6 overflow-y-auto flex-1">
                <span class="text-xs font-black text-indigo-600 uppercase tracking-widest" x-text="selectedProduct.category"></span>
                <h3 class="text-xl font-black text-slate-900 mt-1 mb-2" x-text="selectedProduct.name"></h3>
                <p class="text-xs text-slate-600 leading-relaxed mb-4" x-text="selectedProduct.description"></p>
                <div class="flex items-baseline gap-3 mb-6">
                    <span class="text-2xl font-black text-slate-900">₹<span x-text="Number(selectedProduct.offer_price || selectedProduct.price).toLocaleString('en-IN')"></span></span>
                    <span x-show="selectedProduct.offer_price" class="text-sm font-bold text-slate-400 line-through">₹<span x-text="Number(selectedProduct.price).toLocaleString('en-IN')"></span></span>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <button @click="addToCart(selectedProduct); modalOpen = false" class="py-3 bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold rounded-xl text-sm border border-slate-200">+ Add to Cart</button>
                    @if($business->whatsapp)
                    <a :href="'https://wa.me/91{{ preg_replace('/[^0-9]/', '', $business->whatsapp) }}?text=' + selectedProduct.waText" @click="track('whatsapp_click', selectedProduct.id, {placement:'product_modal'})" target="_blank" class="py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-sm flex items-center justify-center gap-1 shadow-sm">Buy Now</a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div x-show="toastShow"
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         class="fixed top-20 right-4 z-50 bg-slate-900 text-white font-bold text-xs px-4 py-3 rounded-2xl shadow-xl flex items-center gap-2 border border-slate-800">
        <span>✅</span> <span x-text="toastMessage"></span>
    </div>

    <!-- Alpine.js Application Script -->
    <script>
        function showroomData() {
            return {
                activeCategory: 'all',
                searchQuery: '',
                cartOpen: false,
                modalOpen: false,
                toastShow: false,
                toastMessage: '',
                cart: [],
                selectedProduct: {},
                visitorToken: '',
                trackingUrl: @json(url('/api/public/showrooms/'.$business->slug.'/events')),
                orderUrl: @json(url('/api/public/showrooms/'.$business->slug.'/orders')),
                customerName: '',
                customerPhone: '',

                initCart() {
                    const saved = localStorage.getItem('ps_cart_{{ $business->id }}');
                    if (saved) {
                        try { this.cart = JSON.parse(saved); } catch(e) {}
                    }
                },

                initTracking() {
                    const key = 'ps_visitor_{{ $business->id }}';
                    let token = localStorage.getItem(key);
                    if (!token) {
                        token = (window.crypto && crypto.randomUUID) ? crypto.randomUUID() : '00000000-0000-4000-8000-' + Math.random().toString(16).slice(2,14).padEnd(12,'0').slice(0,12);
                        localStorage.setItem(key, token);
                    }
                    this.visitorToken = token;
                    this.track('showroom_view', null, {path: location.pathname});
                },

                track(eventType, productId = null, metadata = {}) {
                    const payload = {
                        event_type: eventType,
                        visitor_token: this.visitorToken,
                        product_id: productId || null,
                        source: 'web',
                        referrer: document.referrer || null,
                        metadata: metadata
                    };
                    fetch(this.trackingUrl, {
                        method: 'POST',
                        headers: {'Content-Type':'application/json','Accept':'application/json'},
                        body: JSON.stringify(payload),
                        keepalive: true
                    }).catch(() => {});
                },

                saveCart() {
                    localStorage.setItem('ps_cart_{{ $business->id }}', JSON.stringify(this.cart));
                },

                matchesFilter(catId, name) {
                    const matchesCategory = (this.activeCategory === 'all' || String(this.activeCategory) === String(catId));
                    const matchesSearch = !this.searchQuery || name.includes(this.searchQuery.toLowerCase());
                    return matchesCategory && matchesSearch;
                },

                addToCart(product) {
                    this.track('add_to_cart', product.id, {qty:1});
                    const existing = this.cart.find(i => i.id === product.id);
                    if (existing) {
                        existing.qty++;
                    } else {
                        this.cart.push({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            img: product.img,
                            qty: 1
                        });
                    }
                    this.saveCart();
                    this.showToast('Added ' + product.name + ' to Cart!');
                },

                updateQty(id, delta) {
                    const item = this.cart.find(i => i.id === id);
                    if (item) {
                        item.qty += delta;
                        if (item.qty <= 0) {
                            this.cart = this.cart.filter(i => i.id !== id);
                        }
                    }
                    this.saveCart();
                },

                get totalCartItems() {
                    return this.cart.reduce((sum, item) => sum + item.qty, 0);
                },

                get cartTotal() {
                    return this.cart.reduce((sum, item) => sum + (item.price * item.qty), 0);
                },

                get formattedCartTotal() {
                    return Number(this.cartTotal).toLocaleString('en-IN');
                },

                openProductModal(product) {
                    this.track('product_view', product.id, {placement:'modal'});
                    this.selectedProduct = product;
                    this.modalOpen = true;
                },

                showToast(msg) {
                    this.toastMessage = msg;
                    this.toastShow = true;
                    setTimeout(() => { this.toastShow = false; }, 2500);
                },
                async sendWhatsAppOrder(whatsapp, shopName) {
                    if (this.cart.length === 0) return;
                    if (!this.customerName.trim() || this.customerPhone.replace(/\D/g,'').length < 10) {
                        this.showToast('Please enter your name and valid mobile number.');
                        return;
                    }
                    this.track('whatsapp_click', null, {placement:'cart_order', items:this.totalCartItems, total:this.cartTotal});
                    try {
                        await fetch(this.orderUrl, {
                            method:'POST', headers:{'Content-Type':'application/json','Accept':'application/json'},
                            body:JSON.stringify({
                                customer_name:this.customerName.trim(), phone:this.customerPhone.replace(/\D/g,''),
                                visitor_token:this.visitorToken,
                                items:this.cart.map(i=>({product_id:i.id, qty:i.qty}))
                            })
                        });
                    } catch(e) {}
                    let text = `Hi ${shopName}! I am ${this.customerName}. I would like to place an order:

`;
                    this.cart.forEach((item, index) => {
                        text += `${index + 1}. ${item.name} x ${item.qty} = ₹${(item.price * item.qty).toLocaleString('en-IN')}
`;
                    });
                    text += `
*Grand Total: ₹${this.formattedCartTotal}*

Please confirm availability and payment details.`;
                    const url = `https://wa.me/91${whatsapp}?text=${encodeURIComponent(text)}`;
                    window.open(url, '_blank');
                }
            }
        }
    </script>

</body>
</html>
