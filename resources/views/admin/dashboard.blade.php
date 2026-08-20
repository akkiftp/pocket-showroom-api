<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHOWMORA – Super Admin Command Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            900: '#312e81',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-panel {
            background: rgba(15, 23, 42, 0.75);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.5);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.05);
        }
        .glass-card:hover {
            border-color: rgba(99, 102, 241, 0.3);
        }
        /* Custom scrollbar */
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #0b0f19; }
        ::-webkit-scrollbar-thumb { background: #1e293b; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #334155; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen antialiased flex flex-col selection:bg-indigo-500 selection:text-white">

    <!-- Top Navigation Bar -->
    <header class="border-b border-slate-800/80 bg-slate-950/90 backdrop-blur-md sticky top-0 z-50 transition-all">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="h-16 flex items-center justify-between gap-4">
                
                <!-- Logo & Badges -->
                <div class="flex items-center gap-3">
                    <a href="/admin" class="flex items-center gap-3 group">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 flex items-center justify-center font-black text-xl text-white shadow-lg shadow-indigo-500/25 group-hover:scale-105 transition">
                            S
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-xl font-black tracking-tight text-white group-hover:text-indigo-400 transition">SHOWMORA</span>
                                <span class="px-2 py-0.5 text-[10px] font-extrabold uppercase tracking-wider rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/30">Super Admin</span>
                            </div>
                            <p class="text-[11px] text-slate-400 font-medium hidden sm:block">Pocket Showroom Management Portal</p>
                        </div>
                    </a>
                </div>

                <!-- Global Range Filters & Quick Links -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <form method="GET" action="/admin" class="flex items-center bg-slate-900/90 p-1 rounded-xl border border-slate-800 text-xs font-semibold">
                        <input type="hidden" name="tab" value="{{ $tab }}">
                        @if($search)<input type="hidden" name="search" value="{{ $search }}">@endif
                        @if($statusFilter)<input type="hidden" name="status" value="{{ $statusFilter }}">@endif
                        
                        <button type="submit" name="days" value="1" class="px-2.5 sm:px-3 py-1.5 rounded-lg transition {{ $days == 1 ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white' }}">Today</button>
                        <button type="submit" name="days" value="7" class="px-2.5 sm:px-3 py-1.5 rounded-lg transition {{ $days == 7 ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white' }}">7D</button>
                        <button type="submit" name="days" value="30" class="px-2.5 sm:px-3 py-1.5 rounded-lg transition {{ $days == 30 ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white' }}">30D</button>
                        <button type="submit" name="days" value="90" class="px-2.5 sm:px-3 py-1.5 rounded-lg transition {{ $days == 90 ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white' }}">90D</button>
                        <button type="submit" name="days" value="0" class="px-2.5 sm:px-3 py-1.5 rounded-lg transition {{ $days == 0 ? 'bg-indigo-600 text-white font-bold shadow-md shadow-indigo-600/30' : 'text-slate-400 hover:text-white' }}">All</button>
                    </form>

                    <a href="/api/marketplace/home" target="_blank" class="hidden md:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-xs font-bold text-slate-300 border border-slate-800 transition hover:border-slate-700">
                        <span>Marketplace API</span>
                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-800/50">
            <nav class="flex space-x-1 sm:space-x-2 overflow-x-auto py-2.5 scrollbar-none" aria-label="Tabs">
                <a href="/admin?tab=overview&days={{ $days }}" class="px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap transition {{ $tab === 'overview' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                    <span>📊 Overview & Funnel</span>
                </a>
                <a href="/admin?tab=shops&days={{ $days }}" class="px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap transition {{ $tab === 'shops' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                    <span>🏪 Shops Directory</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'shops' ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $totalShops }}</span>
                </a>
                <a href="/admin?tab=owners&days={{ $days }}" class="px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap transition {{ $tab === 'owners' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                    <span>👥 Shop Owners & Users</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'owners' ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $totalUsers }}</span>
                </a>
                <a href="/admin?tab=products&days={{ $days }}" class="px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap transition {{ $tab === 'products' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                    <span>📦 Products Catalog</span>
                    <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $tab === 'products' ? 'bg-white/20 text-white' : 'bg-slate-800 text-slate-400' }}">{{ $totalProducts }}</span>
                </a>
                <a href="/admin?tab=customers&days={{ $days }}" class="px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap transition {{ $tab === 'customers' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                    <span>🎯 Customer Leads</span>
                </a>
                <a href="/admin?tab=orders&days={{ $days }}" class="px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap transition {{ $tab === 'orders' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                    <span>🛍️ Orders & Inquiries</span>
                </a>
                <a href="/admin?tab=categories&days={{ $days }}" class="px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap transition {{ $tab === 'categories' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                    <span>🏷️ Categories & Locations</span>
                </a>
                <a href="/admin?tab=activity&days={{ $days }}" class="px-3.5 py-1.5 rounded-xl text-xs sm:text-sm font-bold flex items-center gap-2 whitespace-nowrap transition {{ $tab === 'activity' ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/25' : 'text-slate-400 hover:text-slate-200 hover:bg-slate-900' }}">
                    <span>⚡ Live Stream & Audit</span>
                </a>
            </nav>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-1 max-w-7xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        <!-- Flash Messages & Toasts -->
        @if(session('success'))
            <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 flex items-center justify-between shadow-lg shadow-emerald-950/50 animate-fade-in">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center font-bold">✓</div>
                    <div>
                        <h4 class="font-bold text-sm text-emerald-200">Action Executed Successfully</h4>
                        <p class="text-xs text-emerald-400/90">{{ session('success') }}</p>
                    </div>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400/70 hover:text-emerald-200 text-lg font-bold px-2">&times;</button>
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/30 text-rose-300 flex items-center justify-between shadow-lg shadow-rose-950/50">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-rose-500/20 text-rose-400 flex items-center justify-center font-bold">!</div>
                    <div>
                        <h4 class="font-bold text-sm text-rose-200">Action Failed</h4>
                        <p class="text-xs text-rose-400/90">{{ session('error') }}</p>
                    </div>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-400/70 hover:text-rose-200 text-lg font-bold px-2">&times;</button>
            </div>
        @endif

        @if(isset($errors) && $errors->any())
            <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/30 text-rose-300">
                <div class="font-bold text-sm mb-1 text-rose-200">Validation Notice:</div>
                <ul class="text-xs list-disc list-inside space-y-0.5 text-rose-400">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- =================================================================== -->
        <!-- TAB 1: OVERVIEW & FUNNEL -->
        <!-- =================================================================== -->
        @if($tab === 'overview')
            
            <!-- Hero Metric Banner -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-indigo-950 via-slate-900 to-purple-950 border border-indigo-900/40 p-6 sm:p-8 shadow-2xl">
                <div class="absolute -right-10 -bottom-10 w-80 h-80 bg-indigo-600/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs font-bold uppercase tracking-wider">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span> Production Core Active
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Super Admin Operations Center</h1>
                        <p class="text-slate-400 text-sm max-w-xl">
                            Real-time platform telemetry across 
                            <span class="text-indigo-400 font-semibold">{{ $totalShops }} registered shops</span>, 
                            <span class="text-indigo-400 font-semibold">{{ $totalProducts }} products</span>, and 
                            <span class="text-indigo-400 font-semibold">{{ $uniqueVisitors }} active visitors</span> 
                            in the last {{ $days > 0 ? $days . ' days' : 'all recorded time' }}.
                        </p>
                    </div>
                    <div class="flex flex-wrap items-center gap-3">
                        <a href="/admin?tab=shops" class="px-4 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs sm:text-sm shadow-lg shadow-indigo-600/30 transition">
                            Manage Shops ({{ $totalShops }}) →
                        </a>
                        <a href="/admin?tab=orders" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold text-xs sm:text-sm border border-slate-700 transition">
                            View Orders ({{ $totalOrders }})
                        </a>
                    </div>
                </div>
            </div>

            <!-- 8 KPI Metric Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                
                <!-- Card 1: Total Shops -->
                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Shops</span>
                        <span class="text-lg">🏪</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-white">{{ number_format($totalShops) }}</span>
                        <span class="text-xs text-emerald-400 font-bold">{{ $activeShops }} active</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-400 flex items-center justify-between">
                        <span>{{ $verifiedShops }} verified</span>
                        <span>{{ $featuredShops }} featured</span>
                    </div>
                </div>

                <!-- Card 2: Owners & Users -->
                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Shop Owners</span>
                        <span class="text-lg">👥</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-white">{{ number_format($totalOwners) }}</span>
                        <span class="text-xs text-slate-400">/ {{ $totalUsers }} users</span>
                    </div>
                    <div class="mt-2 text-[11px] text-indigo-400 font-semibold">
                        Owner accounts registered
                    </div>
                </div>

                <!-- Card 3: Products Catalog -->
                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Products</span>
                        <span class="text-lg">📦</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-white">{{ number_format($totalProducts) }}</span>
                        <span class="text-xs text-emerald-400 font-bold">{{ $inStockProducts }} in stock</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-400">
                        {{ $totalProducts > 0 ? round(($inStockProducts / $totalProducts) * 100) : 0 }}% inventory readiness
                    </div>
                </div>

                <!-- Card 4: Visitors -->
                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Unique Visitors</span>
                        <span class="text-lg">👀</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-white">{{ number_format($uniqueVisitors) }}</span>
                        <span class="text-xs text-indigo-400 font-bold">{{ number_format($totalViews) }} views</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-400">
                        Showrooms & Marketplace
                    </div>
                </div>

                <!-- Card 5: Product Views -->
                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Product Views</span>
                        <span class="text-lg">🏷️</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-white">{{ number_format($productViews) }}</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-400">
                        Detailed catalog interactions
                    </div>
                </div>

                <!-- Card 6: WhatsApp Clicks -->
                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">WhatsApp Clicks</span>
                        <span class="text-lg">💬</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-emerald-400">{{ number_format($whatsappClicks) }}</span>
                        <span class="text-xs text-slate-400">leads</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-400">
                        Direct buyer-to-seller chats
                    </div>
                </div>

                <!-- Card 7: Total Enquiries -->
                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Customer Enquiries</span>
                        <span class="text-lg">📩</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-purple-400">{{ number_format($totalEnquiries) }}</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-400">
                        Formal lead submissions
                    </div>
                </div>

                <!-- Card 8: Total Orders & GMV -->
                <div class="glass-card rounded-2xl p-5 relative overflow-hidden group">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Orders Generated</span>
                        <span class="text-lg">💰</span>
                    </div>
                    <div class="mt-3 flex items-baseline gap-2">
                        <span class="text-3xl font-black text-pink-400">{{ number_format($totalOrders) }}</span>
                        <span class="text-xs text-slate-300 font-bold">₹{{ number_format($orderVolume, 2) }}</span>
                    </div>
                    <div class="mt-2 text-[11px] text-slate-400">
                        Marketplace Gross Volume
                    </div>
                </div>

            </div>

            <!-- Conversion Funnel Section -->
            <div class="glass-panel rounded-3xl p-6 sm:p-8 space-y-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg sm:text-xl font-black text-white">Live Platform Conversion Funnel</h2>
                        <p class="text-xs text-slate-400">Telemetry tracking visitors from showroom discovery to direct orders</p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        Last {{ $days > 0 ? $days . ' Days' : 'All Time' }}
                    </span>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4">
                    
                    <div class="bg-slate-900/80 rounded-2xl p-4 border border-slate-800 text-center relative">
                        <div class="text-xs font-bold text-slate-400 uppercase">1. Visitors</div>
                        <div class="text-2xl font-black text-white mt-1">{{ number_format($uniqueVisitors) }}</div>
                        <div class="text-[11px] text-slate-400 mt-1">100% baseline</div>
                        <div class="hidden lg:block absolute -right-3 top-1/2 -translate-y-1/2 z-10 text-slate-600 font-bold text-lg">→</div>
                    </div>

                    <div class="bg-slate-900/80 rounded-2xl p-4 border border-slate-800 text-center relative">
                        <div class="text-xs font-bold text-slate-400 uppercase">2. Product Views</div>
                        <div class="text-2xl font-black text-indigo-400 mt-1">{{ number_format($productViews) }}</div>
                        <div class="text-[11px] text-indigo-300 mt-1">
                            {{ $uniqueVisitors > 0 ? round(($productViews / $uniqueVisitors) * 100) : 0 }}% engagement
                        </div>
                        <div class="hidden lg:block absolute -right-3 top-1/2 -translate-y-1/2 z-10 text-slate-600 font-bold text-lg">→</div>
                    </div>

                    <div class="bg-slate-900/80 rounded-2xl p-4 border border-slate-800 text-center relative">
                        <div class="text-xs font-bold text-slate-400 uppercase">3. WhatsApp Clicks</div>
                        <div class="text-2xl font-black text-emerald-400 mt-1">{{ number_format($whatsappClicks) }}</div>
                        <div class="text-[11px] text-emerald-300 mt-1">
                            {{ $productViews > 0 ? round(($whatsappClicks / $productViews) * 100) : 0 }}% of views
                        </div>
                        <div class="hidden lg:block absolute -right-3 top-1/2 -translate-y-1/2 z-10 text-slate-600 font-bold text-lg">→</div>
                    </div>

                    <div class="bg-slate-900/80 rounded-2xl p-4 border border-slate-800 text-center relative">
                        <div class="text-xs font-bold text-slate-400 uppercase">4. Inquiries</div>
                        <div class="text-2xl font-black text-purple-400 mt-1">{{ number_format($totalEnquiries) }}</div>
                        <div class="text-[11px] text-purple-300 mt-1">
                            {{ $uniqueVisitors > 0 ? round(($totalEnquiries / $uniqueVisitors) * 100, 1) : 0 }}% of visitors
                        </div>
                        <div class="hidden lg:block absolute -right-3 top-1/2 -translate-y-1/2 z-10 text-slate-600 font-bold text-lg">→</div>
                    </div>

                    <div class="bg-slate-900/80 rounded-2xl p-4 border border-indigo-500/30 text-center bg-indigo-950/30">
                        <div class="text-xs font-bold text-pink-400 uppercase">5. Orders</div>
                        <div class="text-2xl font-black text-pink-300 mt-1">{{ number_format($totalOrders) }}</div>
                        <div class="text-[11px] text-pink-400 mt-1 font-bold">
                            {{ $uniqueVisitors > 0 ? round(($totalOrders / $uniqueVisitors) * 100, 2) : 0 }}% conversion
                        </div>
                    </div>

                </div>
            </div>

            <!-- Two Column Section: Live Stream + Recent Audit -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Live Activity Feed -->
                <div class="glass-panel rounded-3xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-white text-base flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-ping"></span>
                            Real-time Activity Stream
                        </h3>
                        <a href="/admin?tab=activity" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">View All →</a>
                    </div>
                    <div class="space-y-2.5 max-h-96 overflow-y-auto pr-1">
                        @forelse($recentActivity as $event)
                            <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between text-xs hover:border-slate-700 transition">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center font-bold text-sm
                                        {{ $event->event_type === 'whatsapp_click' ? 'bg-emerald-500/20 text-emerald-400' : '' }}
                                        {{ $event->event_type === 'product_view' ? 'bg-indigo-500/20 text-indigo-400' : '' }}
                                        {{ $event->event_type === 'shop_view' || $event->event_type === 'showroom_view' ? 'bg-purple-500/20 text-purple-400' : '' }}
                                        {{ $event->event_type === 'shop_share' || $event->event_type === 'product_share' ? 'bg-amber-500/20 text-amber-400' : 'bg-slate-800 text-slate-300' }}">
                                        @if($event->event_type === 'whatsapp_click') 💬
                                        @elseif($event->event_type === 'product_view') 🏷️
                                        @elseif(str_contains($event->event_type, 'share')) 🔗
                                        @else 👁️
                                        @endif
                                    </div>
                                    <div>
                                        <div class="font-semibold text-slate-200">
                                            <span class="capitalize">{{ str_replace('_', ' ', $event->event_type) }}</span>
                                            @if($event->business)
                                                on <span class="text-indigo-400 font-bold">{{ $event->business->name }}</span>
                                            @endif
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            @if($event->product) Product: {{ $event->product->name }} • @endif
                                            {{ $event->city ?: 'Unknown city' }}
                                        </div>
                                    </div>
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $event->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <div class="p-6 text-center text-slate-400 text-xs">No activity recorded for this period.</div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Admin Audit Actions -->
                <div class="glass-panel rounded-3xl p-6 space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-white text-base flex items-center gap-2">
                            <span>🛡️</span>
                            Recent Admin Actions Log
                        </h3>
                        <a href="/admin?tab=activity" class="text-xs text-indigo-400 hover:text-indigo-300 font-semibold">View All →</a>
                    </div>
                    <div class="space-y-2.5 max-h-96 overflow-y-auto pr-1">
                        @forelse($auditLogs as $log)
                            <div class="p-3 rounded-xl bg-slate-900/60 border border-slate-800 flex items-center justify-between text-xs hover:border-slate-700 transition">
                                <div>
                                    <div class="font-semibold text-slate-200">
                                        <span class="text-indigo-400 font-mono font-bold">{{ $log->action }}</span>
                                        on <span class="text-slate-300">{{ $log->entity_type }} #{{ $log->entity_id }}</span>
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        Actor: {{ $log->actor ? $log->actor->name : 'Super Admin' }} • IP: {{ $log->ip_address }}
                                    </div>
                                </div>
                                <span class="text-[11px] text-slate-400 font-mono">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        @empty
                            <div class="p-6 text-center text-slate-400 text-xs">No administrative actions logged yet.</div>
                        @endforelse
                    </div>
                </div>

            </div>

        @endif

        <!-- =================================================================== -->
        <!-- TAB 2: SHOPS DIRECTORY & MODERATION -->
        <!-- =================================================================== -->
        @if($tab === 'shops')
            
            <div class="glass-panel rounded-3xl p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-white">Shops Directory & Moderation</h2>
                        <p class="text-xs text-slate-400">Manage showroom statuses, verification badges, and discovery visibility</p>
                    </div>

                    <!-- Search & Filters -->
                    <form method="GET" action="/admin" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="tab" value="shops">
                        <input type="hidden" name="days" value="{{ $days }}">
                        
                        <div class="relative">
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search shop, slug, city, phone..." class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 w-56 sm:w-64">
                        </div>

                        <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="">All Statuses</option>
                            <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active Only</option>
                            <option value="suspended" {{ $statusFilter === 'suspended' ? 'selected' : '' }}>Suspended Only</option>
                            <option value="verified" {{ $statusFilter === 'verified' ? 'selected' : '' }}>Verified Only</option>
                            <option value="featured" {{ $statusFilter === 'featured' ? 'selected' : '' }}>Featured Only</option>
                        </select>

                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">Search</button>
                        @if($search || $statusFilter)
                            <a href="/admin?tab=shops&days={{ $days }}" class="px-2.5 py-1.5 rounded-xl bg-slate-800 text-slate-400 hover:text-white text-xs">Reset</a>
                        @endif
                    </form>
                </div>

                <!-- Shops Table -->
                <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/90 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 text-[11px]">
                            <tr>
                                <th class="p-3.5">Shop / Showroom</th>
                                <th class="p-3.5">Owner & Contact</th>
                                <th class="p-3.5">Category & City</th>
                                <th class="p-3.5 text-center">Metrics</th>
                                <th class="p-3.5 text-center">Status</th>
                                <th class="p-3.5 text-right">Moderation Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                            @forelse($shops as $shop)
                                <tr class="hover:bg-slate-900/50 transition">
                                    
                                    <!-- Shop Name & Showroom Link -->
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-3">
                                            @if($shop->logo_url)
                                                <img src="{{ $shop->logo_url }}" alt="{{ $shop->name }}" class="w-10 h-10 rounded-xl object-cover border border-slate-700">
                                            @else
                                                <div class="w-10 h-10 rounded-xl bg-slate-800 text-slate-300 flex items-center justify-center font-bold text-sm border border-slate-700">
                                                    {{ strtoupper(substr($shop->name, 0, 2)) }}
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-white flex items-center gap-1.5">
                                                    <span>{{ $shop->name }}</span>
                                                    @if($shop->is_verified)
                                                        <span class="inline-flex items-center text-blue-400" title="Verified Showroom">
                                                            <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                        </span>
                                                    @endif
                                                    @if($shop->is_featured)
                                                        <span class="px-1.5 py-0.2 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30 text-[10px] font-bold">Featured</span>
                                                    @endif
                                                </div>
                                                <a href="/showrooms/{{ $shop->slug }}" target="_blank" class="text-[11px] text-indigo-400 hover:text-indigo-300 flex items-center gap-1">
                                                    <span>/showrooms/{{ $shop->slug }}</span>
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                                </a>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Owner & Contact -->
                                    <td class="p-3.5">
                                        <div class="font-semibold text-slate-200">{{ $shop->user ? $shop->user->name : 'No Owner Assigned' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $shop->phone ?: ($shop->user ? $shop->user->phone : '—') }}</div>
                                    </td>

                                    <!-- Category & Location -->
                                    <td class="p-3.5">
                                        <div class="font-medium text-slate-200">
                                            {{ $shop->marketplaceCategory ? $shop->marketplaceCategory->name : 'General' }}
                                        </div>
                                        <div class="text-[11px] text-slate-400">
                                            {{ $shop->city ?: ($shop->marketplaceLocation ? $shop->marketplaceLocation->name : 'National') }}
                                        </div>
                                    </td>

                                    <!-- Metrics -->
                                    <td class="p-3.5 text-center">
                                        <div class="inline-flex items-center gap-2 bg-slate-900 px-2.5 py-1 rounded-xl border border-slate-800 text-[11px]">
                                            <span title="Products">📦 {{ $shop->products_count }}</span>
                                            <span title="Inquiries">💬 {{ $shop->inquiries_count }}</span>
                                            <span title="Orders">🛍️ {{ $shop->orders_count }}</span>
                                        </div>
                                    </td>

                                    <!-- Status -->
                                    <td class="p-3.5 text-center">
                                        @if($shop->is_active)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Active</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">Suspended</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            
                                            <!-- Verify Toggle Form -->
                                            <form method="POST" action="/admin/shops/{{ $shop->id }}/verify">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $shop->is_verified ? 'bg-blue-600/20 text-blue-300 border border-blue-500/40 hover:bg-blue-600/40' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:text-white' }}" title="{{ $shop->is_verified ? 'Remove verification' : 'Verify shop' }}">
                                                    {{ $shop->is_verified ? '✓ Verified' : '+ Verify' }}
                                                </button>
                                            </form>

                                            <!-- Feature Toggle Form -->
                                            <form method="POST" action="/admin/shops/{{ $shop->id }}/feature">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $shop->is_featured ? 'bg-amber-600/20 text-amber-300 border border-amber-500/40 hover:bg-amber-600/40' : 'bg-slate-800 text-slate-400 border border-slate-700 hover:text-white' }}" title="{{ $shop->is_featured ? 'Remove featured' : 'Feature shop' }}">
                                                    {{ $shop->is_featured ? '★ Featured' : '☆ Feature' }}
                                                </button>
                                            </form>

                                            <!-- Suspend / Activate Form -->
                                            <form method="POST" action="/admin/shops/{{ $shop->id }}/toggle-active">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $shop->is_active ? 'bg-rose-600/20 text-rose-300 border border-rose-500/40 hover:bg-rose-600/40' : 'bg-emerald-600/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-600/40' }}">
                                                    {{ $shop->is_active ? 'Suspend' : 'Activate' }}
                                                </button>
                                            </form>

                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                        No shops match the selected criteria.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($shops->hasPages())
                    <div class="pt-2">
                        {{ $shops->links() }}
                    </div>
                @endif

            </div>

        @endif

        <!-- =================================================================== -->
        <!-- TAB 3: SHOP OWNERS & USER ACCOUNTS -->
        <!-- =================================================================== -->
        @if($tab === 'owners')
            
            <div class="glass-panel rounded-3xl p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-white">Shop Owners & User Accounts</h2>
                        <p class="text-xs text-slate-400">Manage user credentials, trial extensions, role authorizations, and account status</p>
                    </div>

                    <!-- Search & Filters -->
                    <form method="GET" action="/admin" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="tab" value="owners">
                        <input type="hidden" name="days" value="{{ $days }}">
                        
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search name, phone, email, shop..." class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 w-56 sm:w-64">

                        <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="">All Roles & Status</option>
                            <option value="owner" {{ $statusFilter === 'owner' ? 'selected' : '' }}>Shop Owners</option>
                            <option value="admin" {{ $statusFilter === 'admin' ? 'selected' : '' }}>Super Admins</option>
                            <option value="active" {{ $statusFilter === 'active' ? 'selected' : '' }}>Active Users</option>
                            <option value="blocked" {{ $statusFilter === 'blocked' ? 'selected' : '' }}>Blocked Users</option>
                        </select>

                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">Search</button>
                        @if($search || $statusFilter)
                            <a href="/admin?tab=owners&days={{ $days }}" class="px-2.5 py-1.5 rounded-xl bg-slate-800 text-slate-400 hover:text-white text-xs">Reset</a>
                        @endif
                    </form>
                </div>

                <!-- Users Table -->
                <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/90 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 text-[11px]">
                            <tr>
                                <th class="p-3.5">User Identity</th>
                                <th class="p-3.5">Role</th>
                                <th class="p-3.5">Associated Shop</th>
                                <th class="p-3.5">Trial / Subscription Status</th>
                                <th class="p-3.5 text-center">Account</th>
                                <th class="p-3.5 text-right">Owner Management</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                            @forelse($users as $user)
                                <tr class="hover:bg-slate-900/50 transition">
                                    
                                    <!-- User Identity -->
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-xl bg-indigo-900/50 border border-indigo-700/50 text-indigo-300 flex items-center justify-center font-bold text-sm">
                                                {{ strtoupper(substr($user->name ?: 'U', 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-white">{{ $user->name ?: 'Unnamed User' }}</div>
                                                <div class="text-[11px] text-slate-400 font-mono">{{ $user->phone ?: $user->email ?: 'No contact info' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Role -->
                                    <td class="p-3.5">
                                        @if($user->role === 'super_admin' || $user->is_admin)
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-extrabold bg-purple-500/20 text-purple-300 border border-purple-500/30">SUPER ADMIN</span>
                                        @elseif($user->role === 'shop_admin')
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-300 border border-blue-500/30">Shop Admin</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">Shop Owner</span>
                                        @endif
                                    </td>

                                    <!-- Shop -->
                                    <td class="p-3.5">
                                        @if($user->business)
                                            <div class="font-semibold text-slate-200">{{ $user->business->name }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $user->business->products_count }} products • {{ $user->business->orders_count }} orders</div>
                                        @else
                                            <span class="text-slate-500 italic">No shop linked</span>
                                        @endif
                                    </td>

                                    <!-- Trial / Expiry -->
                                    <td class="p-3.5">
                                        <div class="space-y-1">
                                            @if($user->subscription_status === 'active' || $user->subscription_status === 'pro')
                                                <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Active Subscription</span>
                                            @elseif($user->trial_expires_at)
                                                @if($user->trial_expires_at->isFuture())
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                                                        Trial: {{ $user->days_remaining_in_trial }}d remaining
                                                    </span>
                                                @else
                                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                                        Trial Expired ({{ $user->trial_expires_at->format('M d') }})
                                                    </span>
                                                @endif
                                            @else
                                                <span class="text-slate-500 text-[11px]">Free Tier</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Account Status -->
                                    <td class="p-3.5 text-center">
                                        @if($user->is_active)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Active</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">Blocked</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5 flex-wrap">
                                            
                                            <!-- Extend Trial Dropdown Form -->
                                            <form method="POST" action="/admin/users/{{ $user->id }}/extend-trial" class="inline-flex items-center gap-1">
                                                @csrf
                                                <input type="hidden" name="days" value="30">
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-indigo-600/20 text-indigo-300 border border-indigo-500/40 hover:bg-indigo-600/40 transition" title="Add 30 days trial">
                                                    +30d Trial
                                                </button>
                                            </form>

                                            <!-- Activate / Block Form -->
                                            @if($user->is_active)
                                                <form method="POST" action="/admin/users/{{ $user->id }}/block">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-600/20 text-rose-300 border border-rose-500/40 hover:bg-rose-600/40 transition">
                                                        Block
                                                    </button>
                                                </form>
                                            @else
                                                <form method="POST" action="/admin/users/{{ $user->id }}/activate">
                                                    @csrf
                                                    <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-600/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-600/40 transition">
                                                        Activate
                                                    </button>
                                                </form>
                                            @endif

                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($users->hasPages())
                    <div class="pt-2">
                        {{ $users->links() }}
                    </div>
                @endif

            </div>

        @endif

        <!-- =================================================================== -->
        <!-- TAB 4: PRODUCTS CATALOG & STOCK -->
        <!-- =================================================================== -->
        @if($tab === 'products')
            
            <div class="glass-panel rounded-3xl p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-white">Global Products Catalog</h2>
                        <p class="text-xs text-slate-400">Inventory control, price checks, and 1-click stock status moderation</p>
                    </div>

                    <!-- Search & Filters -->
                    <form method="GET" action="/admin" class="flex flex-wrap items-center gap-2">
                        <input type="hidden" name="tab" value="products">
                        <input type="hidden" name="days" value="{{ $days }}">
                        
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search product, SKU, shop..." class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 w-56 sm:w-64">

                        <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                            <option value="">All Stock Status</option>
                            <option value="instock" {{ $statusFilter === 'instock' ? 'selected' : '' }}>In Stock</option>
                            <option value="outofstock" {{ $statusFilter === 'outofstock' ? 'selected' : '' }}>Out of Stock</option>
                        </select>

                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">Search</button>
                        @if($search || $statusFilter)
                            <a href="/admin?tab=products&days={{ $days }}" class="px-2.5 py-1.5 rounded-xl bg-slate-800 text-slate-400 hover:text-white text-xs">Reset</a>
                        @endif
                    </form>
                </div>

                <!-- Products Table -->
                <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/90 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 text-[11px]">
                            <tr>
                                <th class="p-3.5">Product</th>
                                <th class="p-3.5">Shop & Category</th>
                                <th class="p-3.5">Pricing</th>
                                <th class="p-3.5 text-center">Views</th>
                                <th class="p-3.5 text-center">Stock</th>
                                <th class="p-3.5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                            @forelse($products as $product)
                                <tr class="hover:bg-slate-900/50 transition">
                                    
                                    <!-- Product Info & Image -->
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-3">
                                            @php
                                                $img = $product->images->first();
                                                $imgUrl = $img ? $img->image_url : null;
                                            @endphp
                                            @if($imgUrl)
                                                <img src="{{ $imgUrl }}" alt="{{ $product->name }}" class="w-12 h-12 rounded-xl object-cover border border-slate-700">
                                            @else
                                                <div class="w-12 h-12 rounded-xl bg-slate-800 text-slate-400 flex items-center justify-center font-bold text-base border border-slate-700">
                                                    📦
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-bold text-white">{{ $product->name }}</div>
                                                <div class="text-[11px] text-slate-400">SKU: {{ $product->sku ?: 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Shop & Category -->
                                    <td class="p-3.5">
                                        <div class="font-semibold text-slate-200">{{ $product->business ? $product->business->name : 'Unassigned' }}</div>
                                        <div class="text-[11px] text-slate-400">{{ $product->category ? $product->category->name : 'General' }}</div>
                                    </td>

                                    <!-- Pricing -->
                                    <td class="p-3.5">
                                        <div class="font-bold text-white">₹{{ number_format($product->price, 2) }}</div>
                                        @if($product->offer_price && $product->offer_price < $product->price)
                                            <div class="text-[10px] text-emerald-400 font-bold">Offer: ₹{{ number_format($product->offer_price, 2) }}</div>
                                        @endif
                                    </td>

                                    <!-- Views -->
                                    <td class="p-3.5 text-center font-mono">
                                        {{ number_format($product->views_count) }}
                                    </td>

                                    <!-- Stock Status -->
                                    <td class="p-3.5 text-center">
                                        @if($product->in_stock)
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">In Stock</span>
                                        @else
                                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">Out of Stock</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-3.5 text-right">
                                        <div class="flex items-center justify-end gap-1.5">
                                            
                                            <!-- Toggle Stock Form -->
                                            <form method="POST" action="/admin/products/{{ $product->id }}/toggle-stock">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $product->in_stock ? 'bg-amber-600/20 text-amber-300 border border-amber-500/40 hover:bg-amber-600/40' : 'bg-emerald-600/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-600/40' }}">
                                                    {{ $product->in_stock ? 'Mark Out of Stock' : 'Mark In Stock' }}
                                                </button>
                                            </form>

                                            <!-- Delete Product Form -->
                                            <form method="POST" action="/admin/products/{{ $product->id }}/delete" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-rose-600/20 text-rose-300 border border-rose-500/40 hover:bg-rose-600/40 transition">
                                                    Delete
                                                </button>
                                            </form>

                                        </div>
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                        No products match the filter.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($products->hasPages())
                    <div class="pt-2">
                        {{ $products->links() }}
                    </div>
                @endif

            </div>

        @endif

        <!-- =================================================================== -->
        <!-- TAB 5: CUSTOMER LEADS DIRECTORY -->
        <!-- =================================================================== -->
        @if($tab === 'customers')
            
            <div class="glass-panel rounded-3xl p-6 space-y-6">
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-xl font-black text-white">Customer Leads & Engagements</h2>
                        <p class="text-xs text-slate-400">Captured buyer inquiries, WhatsApp prospects, and order contacts across all showrooms</p>
                    </div>

                    <!-- Search Form -->
                    <form method="GET" action="/admin" class="flex items-center gap-2">
                        <input type="hidden" name="tab" value="customers">
                        <input type="hidden" name="days" value="{{ $days }}">
                        
                        <input type="text" name="search" value="{{ $search }}" placeholder="Search customer name, phone, email..." class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 w-64">
                        <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">Search</button>
                        @if($search)
                            <a href="/admin?tab=customers&days={{ $days }}" class="px-2.5 py-1.5 rounded-xl bg-slate-800 text-slate-400 hover:text-white text-xs">Reset</a>
                        @endif
                    </form>
                </div>

                <!-- Customer Leads Table -->
                <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
                    <table class="w-full text-left text-xs text-slate-300">
                        <thead class="bg-slate-900/90 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 text-[11px]">
                            <tr>
                                <th class="p-3.5">Customer</th>
                                <th class="p-3.5">Direct WhatsApp / Contact</th>
                                <th class="p-3.5">Showroom Engaged</th>
                                <th class="p-3.5">Notes / Context</th>
                                <th class="p-3.5">Last Active</th>
                                <th class="p-3.5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                            @forelse($customerLeads as $lead)
                                <tr class="hover:bg-slate-900/50 transition">
                                    
                                    <!-- Customer Name -->
                                    <td class="p-3.5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-indigo-900/60 text-indigo-300 flex items-center justify-center font-bold text-xs border border-indigo-700/40">
                                                {{ strtoupper(substr($lead->name ?: 'C', 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="font-bold text-white">{{ $lead->name ?: 'Lead #' . $lead->id }}</div>
                                                <div class="text-[11px] text-slate-400">{{ $lead->email ?: 'No email registered' }}</div>
                                            </div>
                                        </div>
                                    </td>

                                    <!-- Phone & Direct WhatsApp -->
                                    <td class="p-3.5">
                                        <div class="font-mono text-slate-200 font-semibold">{{ $lead->phone }}</div>
                                    </td>

                                    <!-- Showroom -->
                                    <td class="p-3.5">
                                        <span class="font-semibold text-slate-200">{{ $lead->business ? $lead->business->name : 'Marketplace General' }}</span>
                                    </td>

                                    <!-- Notes -->
                                    <td class="p-3.5 text-slate-400">
                                        {{ $lead->notes ?: 'Direct showroom visitor contact' }}
                                    </td>

                                    <!-- Last Activity -->
                                    <td class="p-3.5 text-slate-400 font-mono">
                                        {{ $lead->last_activity_at ? $lead->last_activity_at->diffForHumans() : $lead->created_at->diffForHumans() }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-3.5 text-right">
                                        @if($lead->phone)
                                            @php
                                                $cleanPhone = preg_replace('/[^0-9]/', '', $lead->phone);
                                                if (strlen($cleanPhone) === 10) { $cleanPhone = '91' . $cleanPhone; }
                                            @endphp
                                            <a href="https://wa.me/{{ $cleanPhone }}?text=Hello%20{{ urlencode($lead->name) }}%2C%20thank%20you%20for%20visiting%20SHOWMORA!" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-600/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-600/40 text-xs font-bold transition">
                                                <span>💬 WhatsApp</span>
                                            </a>
                                        @else
                                            <span class="text-slate-500 text-xs">No Phone</span>
                                        @endif
                                    </td>

                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                        No customer leads captured yet.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($customerLeads->hasPages())
                    <div class="pt-2">
                        {{ $customerLeads->links() }}
                    </div>
                @endif

            </div>

        @endif

        <!-- =================================================================== -->
        <!-- TAB 6: ORDERS & INQUIRIES FULFILLMENT -->
        <!-- =================================================================== -->
        @if($tab === 'orders')
            
            <div class="space-y-6">
                
                <!-- Orders Table Section -->
                <div class="glass-panel rounded-3xl p-6 space-y-6">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div>
                            <h2 class="text-xl font-black text-white">Marketplace & Showroom Orders</h2>
                            <p class="text-xs text-slate-400">Track and update order fulfillment lifecycle across all shops</p>
                        </div>

                        <!-- Orders Search & Filter -->
                        <form method="GET" action="/admin" class="flex flex-wrap items-center gap-2">
                            <input type="hidden" name="tab" value="orders">
                            <input type="hidden" name="days" value="{{ $days }}">
                            
                            <input type="text" name="search" value="{{ $search }}" placeholder="Search customer, phone, shop..." class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 w-56">

                            <select name="status" onchange="this.form.submit()" class="bg-slate-900 border border-slate-800 rounded-xl px-3 py-1.5 text-xs text-white focus:outline-none focus:border-indigo-500">
                                <option value="">All Order Statuses</option>
                                <option value="pending" {{ $statusFilter === 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="confirmed" {{ $statusFilter === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                <option value="processing" {{ $statusFilter === 'processing' ? 'selected' : '' }}>Processing</option>
                                <option value="completed" {{ $statusFilter === 'completed' ? 'selected' : '' }}>Completed</option>
                                <option value="cancelled" {{ $statusFilter === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                            </select>

                            <button type="submit" class="px-3 py-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">Filter</button>
                        </form>
                    </div>

                    <!-- Orders Table -->
                    <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-900/90 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 text-[11px]">
                                <tr>
                                    <th class="p-3.5">Order ID</th>
                                    <th class="p-3.5">Customer & Delivery</th>
                                    <th class="p-3.5">Shop</th>
                                    <th class="p-3.5">Amount</th>
                                    <th class="p-3.5 text-center">Status</th>
                                    <th class="p-3.5 text-right">Update Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                                @forelse($orders as $order)
                                    <tr class="hover:bg-slate-900/50 transition">
                                        
                                        <!-- ID & Date -->
                                        <td class="p-3.5">
                                            <div class="font-black text-white">#ORD-{{ str_pad($order->id, 5, '0', STR_PAD_LEFT) }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $order->created_at->format('M d, Y h:ia') }}</div>
                                        </td>

                                        <!-- Customer -->
                                        <td class="p-3.5">
                                            <div class="font-bold text-white">{{ $order->customer_name ?: 'Guest Buyer' }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $order->phone }}</div>
                                            @if($order->address)
                                                <div class="text-[11px] text-slate-400 line-clamp-1">{{ $order->address }}</div>
                                            @endif
                                        </td>

                                        <!-- Shop -->
                                        <td class="p-3.5">
                                            <div class="font-semibold text-slate-200">{{ $order->business ? $order->business->name : 'N/A' }}</div>
                                            <div class="text-[11px] text-slate-400">{{ $order->items->count() }} item(s)</div>
                                        </td>

                                        <!-- Amount -->
                                        <td class="p-3.5 font-bold text-white">
                                            ₹{{ number_format($order->total, 2) }}
                                        </td>

                                        <!-- Status Badge -->
                                        <td class="p-3.5 text-center">
                                            @if($order->status === 'completed')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Completed</span>
                                            @elseif($order->status === 'processing' || $order->status === 'confirmed')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-500/20 text-blue-400 border border-blue-500/30">{{ ucfirst($order->status) }}</span>
                                            @elseif($order->status === 'cancelled')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">Cancelled</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">Pending</span>
                                            @endif
                                        </td>

                                        <!-- Actions: Status Select -->
                                        <td class="p-3.5 text-right">
                                            <form method="POST" action="/admin/orders/{{ $order->id }}/status" class="inline-flex items-center gap-1.5">
                                                @csrf
                                                <select name="status" class="bg-slate-900 border border-slate-700 rounded-lg px-2 py-1 text-xs text-white focus:outline-none focus:border-indigo-500">
                                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                                                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                                                    <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                </select>
                                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs transition">
                                                    Update
                                                </button>
                                            </form>
                                        </td>

                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                            No orders placed yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($orders->hasPages())
                        <div class="pt-2">{{ $orders->links() }}</div>
                    @endif
                </div>

                <!-- Inquiries Section -->
                <div class="glass-panel rounded-3xl p-6 space-y-6">
                    <div>
                        <h2 class="text-xl font-black text-white">Product & Showroom Inquiries</h2>
                        <p class="text-xs text-slate-400">Formal inquiries submitted by customers</p>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-900/90 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 text-[11px]">
                                <tr>
                                    <th class="p-3.5">Customer</th>
                                    <th class="p-3.5">Target Product / Shop</th>
                                    <th class="p-3.5">Customer Message</th>
                                    <th class="p-3.5 text-center">Status</th>
                                    <th class="p-3.5 text-right">Moderation</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40">
                                @forelse($inquiries as $inquiry)
                                    <tr class="hover:bg-slate-900/50 transition">
                                        <td class="p-3.5">
                                            <div class="font-bold text-white">{{ $inquiry->customer_name ?: 'Buyer' }}</div>
                                            <div class="text-[11px] text-slate-400 font-mono">{{ $inquiry->phone }}</div>
                                        </td>
                                        <td class="p-3.5">
                                            <div class="font-semibold text-slate-200">{{ $inquiry->business ? $inquiry->business->name : 'N/A' }}</div>
                                            @if($inquiry->product)
                                                <div class="text-[11px] text-indigo-400 font-medium">📦 {{ $inquiry->product->name }}</div>
                                            @endif
                                        </td>
                                        <td class="p-3.5 max-w-xs text-slate-300">
                                            <p class="line-clamp-2">{{ $inquiry->message ?: 'No additional message provided.' }}</p>
                                        </td>
                                        <td class="p-3.5 text-center">
                                            @if($inquiry->status === 'handled' || $inquiry->status === 'replied')
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Handled</span>
                                            @else
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">Pending</span>
                                            @endif
                                        </td>
                                        <td class="p-3.5 text-right">
                                            <form method="POST" action="/admin/inquiries/{{ $inquiry->id }}/handled">
                                                @csrf
                                                <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-semibold transition {{ $inquiry->status === 'handled' ? 'bg-slate-800 text-slate-400 border border-slate-700 hover:text-white' : 'bg-emerald-600/20 text-emerald-300 border border-emerald-500/40 hover:bg-emerald-600/40' }}">
                                                    {{ $inquiry->status === 'handled' ? 'Mark Pending' : '✓ Mark Handled' }}
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-400 text-xs">
                                            No inquiries recorded.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($inquiries->hasPages())
                        <div class="pt-2">{{ $inquiries->links() }}</div>
                    @endif
                </div>

            </div>

        @endif

        <!-- =================================================================== -->
        <!-- TAB 7: CATEGORIES & LOCATIONS MANAGEMENT -->
        <!-- =================================================================== -->
        @if($tab === 'categories')
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Categories Column -->
                <div class="space-y-6">
                    
                    <!-- Add Category Form -->
                    <div class="glass-panel rounded-3xl p-6 space-y-4">
                        <div>
                            <h3 class="text-lg font-black text-white">Add Marketplace Category</h3>
                            <p class="text-xs text-slate-400">Organize showrooms into searchable industry classifications</p>
                        </div>

                        <form method="POST" action="/admin/categories/store" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Category Name *</label>
                                    <input type="text" name="name" required placeholder="e.g. Jewellery, Fashion" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Icon / Emoji</label>
                                    <input type="text" name="icon" placeholder="e.g. 💍, 👗, 📱" value="🏪" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Parent Category</label>
                                    <select name="parent_id" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                                        <option value="">None (Top Level Category)</option>
                                        @foreach($parentCategories as $pCat)
                                            <option value="{{ $pCat->id }}">{{ $pCat->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Sort Priority</label>
                                    <input type="number" name="sort_order" value="0" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>

                            <button type="submit" class="w-full py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white font-bold text-xs shadow-lg shadow-indigo-600/30 transition">
                                + Create Marketplace Category
                            </button>
                        </form>
                    </div>

                    <!-- Categories List -->
                    <div class="glass-panel rounded-3xl p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-white">Active Categories ({{ $categories->count() }})</h3>
                        </div>

                        <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                            @forelse($categories as $cat)
                                <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center justify-between hover:border-slate-700 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-800 text-lg flex items-center justify-center border border-slate-700">
                                            {{ $cat->icon ?: '🏪' }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-white flex items-center gap-2">
                                                <span>{{ $cat->name }}</span>
                                                @if($cat->parent)
                                                    <span class="text-[10px] px-2 py-0.2 rounded-full bg-slate-800 text-slate-400">under {{ $cat->parent->name }}</span>
                                                @endif
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-mono">
                                                slug: {{ $cat->slug }} • {{ $cat->businesses_count }} shop(s)
                                            </div>
                                        </div>
                                    </div>

                                    <form method="POST" action="/admin/categories/{{ $cat->id }}/delete" onsubmit="return confirm('Delete category {{ $cat->name }}?');">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-600/20 text-rose-300 hover:bg-rose-600/40 flex items-center justify-center font-bold text-xs border border-rose-500/30 transition" title="Delete category">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="p-6 text-center text-slate-400 text-xs">No categories created yet.</div>
                            @endforelse
                        </div>
                    </div>

                </div>

                <!-- Locations Column -->
                <div class="space-y-6">
                    
                    <!-- Add Location Form -->
                    <div class="glass-panel rounded-3xl p-6 space-y-4">
                        <div>
                            <h3 class="text-lg font-black text-white">Add Marketplace Location</h3>
                            <p class="text-xs text-slate-400">Expand local discovery coverage for cities and commercial hubs</p>
                        </div>

                        <form method="POST" action="/admin/locations/store" class="space-y-3">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">City / Hub Name *</label>
                                    <input type="text" name="name" required placeholder="e.g. Mumbai, Bengaluru, Surat" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">State *</label>
                                    <input type="text" name="state" required placeholder="e.g. Maharashtra, Gujarat" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">District / Region</label>
                                    <input type="text" name="district" placeholder="e.g. Central Mumbai" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-300 uppercase mb-1">Pincode</label>
                                    <input type="text" name="pincode" placeholder="e.g. 400001" class="w-full bg-slate-900 border border-slate-800 rounded-xl px-3 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500">
                                </div>
                            </div>

                            <button type="submit" class="w-full py-2.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold text-xs shadow-lg shadow-purple-600/30 transition">
                                + Create Marketplace Location
                            </button>
                        </form>
                    </div>

                    <!-- Locations List -->
                    <div class="glass-panel rounded-3xl p-6 space-y-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-base font-bold text-white">Registered Locations ({{ $locations->count() }})</h3>
                        </div>

                        <div class="space-y-2 max-h-[500px] overflow-y-auto pr-1">
                            @forelse($locations as $loc)
                                <div class="p-3 rounded-2xl bg-slate-900/60 border border-slate-800 flex items-center justify-between hover:border-slate-700 transition">
                                    <div class="flex items-center gap-3">
                                        <div class="w-9 h-9 rounded-xl bg-slate-800 text-base flex items-center justify-center border border-slate-700">
                                            📍
                                        </div>
                                        <div>
                                            <div class="font-bold text-white flex items-center gap-1.5">
                                                <span>{{ $loc->name }}</span>
                                                <span class="text-slate-400 font-normal">({{ $loc->state }})</span>
                                            </div>
                                            <div class="text-[11px] text-slate-400 font-mono">
                                                Pincode: {{ $loc->pincode ?: '—' }} • {{ $loc->businesses_count }} shop(s)
                                            </div>
                                        </div>
                                    </div>

                                    <form method="POST" action="/admin/locations/{{ $loc->id }}/delete" onsubmit="return confirm('Delete location {{ $loc->name }}?');">
                                        @csrf
                                        <button type="submit" class="w-8 h-8 rounded-lg bg-rose-600/20 text-rose-300 hover:bg-rose-600/40 flex items-center justify-center font-bold text-xs border border-rose-500/30 transition" title="Delete location">
                                            ✕
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <div class="p-6 text-center text-slate-400 text-xs">No locations added yet.</div>
                            @endforelse
                        </div>
                    </div>

                </div>

            </div>

        @endif

        <!-- =================================================================== -->
        <!-- TAB 8: LIVE STREAM & AUDIT LOGS -->
        <!-- =================================================================== -->
        @if($tab === 'activity')
            
            <div class="space-y-6">
                
                <!-- Live Activity Feed Table -->
                <div class="glass-panel rounded-3xl p-6 space-y-6">
                    <div>
                        <h2 class="text-xl font-black text-white">Platform Activity Stream</h2>
                        <p class="text-xs text-slate-400">Complete raw event logs (Showroom visits, Product impressions, WhatsApp clicks, and Shares)</p>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-900/90 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 text-[11px]">
                                <tr>
                                    <th class="p-3.5">Event Type</th>
                                    <th class="p-3.5">Business / Target</th>
                                    <th class="p-3.5">Product</th>
                                    <th class="p-3.5">Visitor Identifier</th>
                                    <th class="p-3.5">Location / City</th>
                                    <th class="p-3.5 text-right">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40 font-mono text-[11px]">
                                @forelse($recentActivity as $event)
                                    <tr class="hover:bg-slate-900/50 transition">
                                        <td class="p-3.5 font-sans font-bold text-white">
                                            <span class="px-2 py-0.5 rounded-md
                                                {{ $event->event_type === 'whatsapp_click' ? 'bg-emerald-500/20 text-emerald-400' : '' }}
                                                {{ $event->event_type === 'product_view' ? 'bg-indigo-500/20 text-indigo-400' : '' }}
                                                {{ $event->event_type === 'shop_view' || $event->event_type === 'showroom_view' ? 'bg-purple-500/20 text-purple-400' : 'bg-slate-800 text-slate-300' }}">
                                                {{ $event->event_type }}
                                            </span>
                                        </td>
                                        <td class="p-3.5 font-sans text-slate-200">
                                            {{ $event->business ? $event->business->name : 'Platform Home' }}
                                        </td>
                                        <td class="p-3.5 font-sans text-slate-300">
                                            {{ $event->product ? $event->product->name : '—' }}
                                        </td>
                                        <td class="p-3.5 text-slate-400 truncate max-w-xs">
                                            {{ $event->visitor_uuid ?: '—' }}
                                        </td>
                                        <td class="p-3.5 font-sans text-slate-400">
                                            {{ $event->city ?: 'Unknown' }}
                                        </td>
                                        <td class="p-3.5 text-right text-slate-400">
                                            {{ $event->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-8 text-center text-slate-400 text-xs">
                                            No events recorded.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Admin Audit Logs -->
                <div class="glass-panel rounded-3xl p-6 space-y-6">
                    <div>
                        <h2 class="text-xl font-black text-white">Administrative Audit Trail</h2>
                        <p class="text-xs text-slate-400">Immutable ledger of all administrative status changes and moderation commands</p>
                    </div>

                    <div class="overflow-x-auto rounded-2xl border border-slate-800/80">
                        <table class="w-full text-left text-xs text-slate-300">
                            <thead class="bg-slate-900/90 text-slate-400 font-bold uppercase tracking-wider border-b border-slate-800 text-[11px]">
                                <tr>
                                    <th class="p-3.5">Action Executed</th>
                                    <th class="p-3.5">Target Entity</th>
                                    <th class="p-3.5">Authorized Actor</th>
                                    <th class="p-3.5">IP Address</th>
                                    <th class="p-3.5 text-right">Timestamp</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/60 bg-slate-950/40 font-mono text-[11px]">
                                @forelse($auditLogs as $log)
                                    <tr class="hover:bg-slate-900/50 transition">
                                        <td class="p-3.5 font-bold text-indigo-400">
                                            {{ $log->action }}
                                        </td>
                                        <td class="p-3.5 text-slate-200">
                                            {{ $log->entity_type }} #{{ $log->entity_id }}
                                        </td>
                                        <td class="p-3.5 text-slate-300 font-sans">
                                            {{ $log->actor ? $log->actor->name : 'Super Admin' }}
                                        </td>
                                        <td class="p-3.5 text-slate-400">
                                            {{ $log->ip_address }}
                                        </td>
                                        <td class="p-3.5 text-right text-slate-400">
                                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="p-8 text-center text-slate-400 text-xs">
                                            No audit records found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

        @endif

    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-800/80 bg-slate-950 py-6 mt-12 text-center text-xs text-slate-500">
        <div class="max-w-7xl mx-auto px-4 flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-indigo-500"></span>
                <span class="font-bold text-slate-400">SHOWMORA Engine</span>
                <span>• v2.5.0 Production</span>
            </div>
            <div>
                Built for High Performance Multitenant Pocket Showroom Ecosystems
            </div>
        </div>
    </footer>

</body>
</html>
