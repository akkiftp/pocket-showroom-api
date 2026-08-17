<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SHOWMORA – Super Admin Dashboard</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 min-h-screen">

    <!-- Top Navigation Header -->
    <header class="border-b border-slate-800 bg-slate-900/80 backdrop-blur sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-indigo-600 via-purple-600 to-pink-500 flex items-center justify-center font-black text-xl text-white shadow-lg shadow-indigo-500/30">
                    S
                </div>
                <div>
                    <span class="text-xl font-black tracking-tight text-white">SHOWMORA</span>
                    <span class="ml-2 px-2 py-0.5 text-xs font-bold rounded-full bg-indigo-500/20 text-indigo-400 border border-indigo-500/30">Super Admin</span>
                </div>
            </div>

            <!-- Time Filter & Links -->
            <div class="flex items-center gap-3">
                <form method="GET" action="/admin" class="flex items-center gap-1 bg-slate-800 p-1 rounded-xl border border-slate-700 text-xs font-bold">
                    <button type="submit" name="days" value="1" class="px-3 py-1.5 rounded-lg {{ $days == 1 ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">Today</button>
                    <button type="submit" name="days" value="7" class="px-3 py-1.5 rounded-lg {{ $days == 7 ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">7 Days</button>
                    <button type="submit" name="days" value="30" class="px-3 py-1.5 rounded-lg {{ $days == 30 ? 'bg-indigo-600 text-white shadow' : 'text-slate-400 hover:text-white' }}">30 Days</button>
                </form>
                <a href="https://pocket-showroom-api.onrender.com/api/marketplace/home" target="_blank" class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-xs font-bold text-slate-300 border border-slate-700 transition">
                    API Explorer ↗
                </a>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">

        <!-- Top Welcome & Status Banner -->
        <div class="bg-gradient-to-r from-indigo-900/60 via-purple-900/40 to-slate-900 border border-indigo-800/40 rounded-3xl p-6 sm:p-8 flex flex-col md:flex-row items-start md:items-center justify-between gap-6 relative overflow-hidden shadow-2xl">
            <div class="space-y-2 z-10">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 text-xs font-extrabold uppercase tracking-wider">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Live Platform Control Center
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white">Platform Health & Growth Dashboard</h1>
                <p class="text-slate-400 text-sm max-w-xl">Super Admin access to all shop owners, showrooms, live activity tracking, and conversions.</p>
            </div>
            <div class="flex items-center gap-3 z-10">
                <a href="#shops" class="px-5 py-2.5 rounded-2xl bg-indigo-600 hover:bg-indigo-500 text-white font-extrabold text-sm shadow-lg shadow-indigo-600/30 transition">
                    Manage Shops ({{ $totalShops }})
                </a>
                <a href="#owners" class="px-5 py-2.5 rounded-2xl bg-slate-800 hover:bg-slate-700 text-slate-200 font-extrabold text-sm border border-slate-700 transition">
                    Owners ({{ $totalOwners }})
                </a>
            </div>
        </div>

        <!-- 8 Key Metric KPI Cards -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Total Shops</div>
                <div class="text-3xl font-black text-white">{{ $totalShops }}</div>
                <div class="text-xs text-emerald-400 font-semibold">{{ $activeShops }} Active • {{ $verifiedShops }} Verified</div>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Shop Owners</div>
                <div class="text-3xl font-black text-indigo-400">{{ $totalOwners }}</div>
                <div class="text-xs text-slate-400 font-semibold">{{ $totalUsers }} Total Accounts</div>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Active Products</div>
                <div class="text-3xl font-black text-pink-400">{{ $totalProducts }}</div>
                <div class="text-xs text-slate-400 font-semibold">{{ $productViews }} Product Views</div>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Unique Visitors</div>
                <div class="text-3xl font-black text-cyan-400">{{ $uniqueVisitors }}</div>
                <div class="text-xs text-slate-400 font-semibold">{{ $totalViews }} Total Visits</div>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">WhatsApp Leads</div>
                <div class="text-3xl font-black text-emerald-400">{{ $whatsappClicks }}</div>
                <div class="text-xs text-emerald-400/80 font-semibold">Direct click intents</div>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Shares & QR</div>
                <div class="text-3xl font-black text-purple-400">{{ $totalShares }}</div>
                <div class="text-xs text-slate-400 font-semibold">Tracked viral links</div>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Enquiries</div>
                <div class="text-3xl font-black text-amber-400">{{ $totalEnquiries }}</div>
                <div class="text-xs text-slate-400 font-semibold">Customer queries</div>
            </div>
            <div class="bg-slate-900/90 border border-slate-800 rounded-2xl p-5 space-y-2">
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider">Orders Generated</div>
                <div class="text-3xl font-black text-teal-400">{{ $totalOrders }}</div>
                <div class="text-xs text-slate-400 font-semibold">Direct orders</div>
            </div>
        </div>

        <!-- Conversion Funnel -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <h2 class="text-lg font-black text-white">Platform Conversion Funnel</h2>
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4 pt-2">
                <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 space-y-1">
                    <div class="text-xs font-bold text-slate-400">1. Showroom Visits</div>
                    <div class="text-2xl font-black text-white">{{ $totalViews }}</div>
                    <div class="text-xs text-indigo-400 font-semibold">100% Traffic Base</div>
                </div>
                <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 space-y-1">
                    <div class="text-xs font-bold text-slate-400">2. Product Views</div>
                    <div class="text-2xl font-black text-white">{{ $productViews }}</div>
                    <div class="text-xs text-pink-400 font-semibold">{{ $totalViews > 0 ? round(($productViews / $totalViews) * 100, 1) : 0 }}% Engagement</div>
                </div>
                <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 space-y-1">
                    <div class="text-xs font-bold text-slate-400">3. WhatsApp Leads</div>
                    <div class="text-2xl font-black text-white">{{ $whatsappClicks }}</div>
                    <div class="text-xs text-emerald-400 font-semibold">{{ $productViews > 0 ? round(($whatsappClicks / max(1, $productViews)) * 100, 1) : 0 }}% Lead Rate</div>
                </div>
                <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 space-y-1">
                    <div class="text-xs font-bold text-slate-400">4. Enquiries & Orders</div>
                    <div class="text-2xl font-black text-white">{{ $totalEnquiries + $totalOrders }}</div>
                    <div class="text-xs text-teal-400 font-semibold">{{ $totalViews > 0 ? round((($totalEnquiries + $totalOrders) / $totalViews) * 100, 2) : 0 }}% Final Conversion</div>
                </div>
            </div>
        </div>

        <!-- Section: All Shops & Moderation -->
        <div id="shops" class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-black text-white">Registered Shops Directory</h2>
                    <p class="text-xs text-slate-400">Verify, Feature, or Inspect any shop on the SHOWMORA network.</p>
                </div>
                <span class="px-3 py-1 text-xs font-bold rounded-full bg-slate-800 text-slate-300 border border-slate-700">
                    {{ $shops->total() }} Total
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-300">
                    <thead class="text-xs uppercase bg-slate-950/80 text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-3.5 px-4 font-extrabold">Shop</th>
                            <th class="py-3.5 px-4 font-extrabold">Owner</th>
                            <th class="py-3.5 px-4 font-extrabold">City / Locality</th>
                            <th class="py-3.5 px-4 font-extrabold">Products</th>
                            <th class="py-3.5 px-4 font-extrabold">Visits</th>
                            <th class="py-3.5 px-4 font-extrabold">Status</th>
                            <th class="py-3.5 px-4 font-extrabold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @foreach($shops as $shop)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="py-4 px-4">
                                <div class="font-extrabold text-white">{{ $shop->name }}</div>
                                <a href="https://pocket-showroom-api.onrender.com/showrooms/{{ $shop->slug }}" target="_blank" class="text-xs text-indigo-400 hover:underline">
                                    showrooms/{{ $shop->slug }} ↗
                                </a>
                            </td>
                            <td class="py-4 px-4 text-xs">
                                <div class="font-bold text-slate-200">{{ $shop->user?->name ?? 'Owner #' . $shop->user_id }}</div>
                                <div class="text-slate-400">+91 {{ $shop->whatsapp ?? $shop->phone ?? 'N/A' }}</div>
                            </td>
                            <td class="py-4 px-4 text-xs font-semibold">
                                {{ ucfirst($shop->locality ?? '') }} {{ ucfirst($shop->city ?? 'Fatehpur') }}
                            </td>
                            <td class="py-4 px-4 font-extrabold text-slate-200">
                                {{ $shop->products_count }}
                            </td>
                            <td class="py-4 px-4 font-extrabold text-cyan-400">
                                {{ $shop->marketplace_views ?? $shop->visitor_sessions_count }}
                            </td>
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-1.5 flex-wrap">
                                    @if($shop->is_verified)
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-blue-500/20 text-blue-400 border border-blue-500/30">Verified</span>
                                    @endif
                                    @if($shop->is_featured)
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-amber-500/20 text-amber-400 border border-amber-500/30">Featured</span>
                                    @endif
                                    @if($shop->is_active)
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">Active</span>
                                    @else
                                        <span class="px-2 py-0.5 text-[10px] font-extrabold rounded-md bg-red-500/20 text-red-400 border border-red-500/30">Suspended</span>
                                    @endif
                                </div>
                            </td>
                            <td class="py-4 px-4 text-right">
                                <div class="inline-flex items-center gap-2">
                                    <form method="POST" action="/admin/shops/{{ $shop->id }}/verify">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg {{ $shop->is_verified ? 'bg-blue-600/30 text-blue-300 hover:bg-blue-600/50' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }} border border-slate-700 transition">
                                            {{ $shop->is_verified ? 'Unverify' : 'Verify' }}
                                        </button>
                                    </form>
                                    <form method="POST" action="/admin/shops/{{ $shop->id }}/feature">
                                        @csrf
                                        <button type="submit" class="px-2.5 py-1 text-xs font-bold rounded-lg {{ $shop->is_featured ? 'bg-amber-600/30 text-amber-300 hover:bg-amber-600/50' : 'bg-slate-800 text-slate-300 hover:bg-slate-700' }} border border-slate-700 transition">
                                            {{ $shop->is_featured ? 'Unfeature' : 'Feature' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pt-4">
                {{ $shops->links() }}
            </div>
        </div>

        <!-- Section: Live Activity Stream -->
        <div class="bg-slate-900 border border-slate-800 rounded-3xl p-6 sm:p-8 space-y-4">
            <h2 class="text-xl font-black text-white">Live Platform Activity Stream</h2>
            <div class="divide-y divide-slate-800/60 max-h-96 overflow-y-auto pr-2">
                @foreach($recentActivity as $act)
                <div class="py-3 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 rounded-lg font-bold bg-slate-800 text-indigo-400 border border-slate-700">
                            {{ strtoupper(str_replace('_', ' ', $act->event_type)) }}
                        </span>
                        <span class="text-slate-300 font-semibold">
                            {{ $act->business?->name ?? 'Marketplace' }}
                            @if($act->product)
                                • <span class="text-pink-400 font-bold">{{ $act->product->name }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="text-slate-500 font-mono">
                        {{ $act->created_at->diffForHumans() }}
                    </div>
                </div>
                @endforeach
            </div>
        </div>

    </main>

    <footer class="border-t border-slate-800/80 bg-slate-950 py-8 text-center text-xs text-slate-500">
        SHOWMORA Platform & Pocket Showroom API • Production Super Admin Control Center
    </footer>

</body>
</html>
