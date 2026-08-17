<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ActivityEvent;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\CustomerContact;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShareLink;
use App\Models\User;
use App\Models\VisitorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperAdminDashboardController extends Controller
{
    public function dashboard(Request $request)
    {
        $days = $request->integer('days', 30);
        $since = $days > 0 ? now()->subDays($days) : now()->subCentury();

        $totalUsers = User::count();
        $totalCustomers = User::where('role', 'customer')->count();
        $totalShopOwners = User::where('role', User::ROLE_SHOP_OWNER)->count();
        $totalShopAdmins = User::where('role', User::ROLE_SHOP_ADMIN)->count();

        $totalShops = Business::count();
        $activeShops = Business::where('is_active', true)->count();
        $inactiveShops = Business::where('is_active', false)->count();
        $verifiedShops = Business::where('is_verified', true)->count();
        $featuredShops = Business::where('is_featured', true)->count();

        $totalProducts = Product::count();
        $totalCategories = DB::table('categories')->whereNull('deleted_at')->count();
        $totalLocations = DB::table('marketplace_locations')->count();

        // Activity metrics
        $eventsQuery = ActivityEvent::where('created_at', '>=', $since);
        $totalShopViews = (clone $eventsQuery)->whereIn('event_type', ['shop_view', 'marketplace_view'])->count();
        $totalShowroomViews = (clone $eventsQuery)->where('event_type', 'showroom_view')->count();
        $totalProductViews = (clone $eventsQuery)->where('event_type', 'product_view')->count();
        $totalWhatsappClicks = (clone $eventsQuery)->where('event_type', 'whatsapp_click')->count();
        $totalPhoneClicks = (clone $eventsQuery)->where('event_type', 'phone_click')->count();
        $totalShares = (clone $eventsQuery)->whereIn('event_type', ['shop_share', 'product_share', 'owner_share'])->count();
        $totalEnquiries = Inquiry::where('created_at', '>=', $since)->count();
        $totalOrders = Order::where('created_at', '>=', $since)->count();

        $uniqueVisitors = (clone $eventsQuery)->whereNotNull('visitor_uuid')->distinct('visitor_uuid')->count('visitor_uuid');
        $loggedInCustomers = (clone $eventsQuery)->whereNotNull('user_id')->distinct('user_id')->count('user_id');
        $guestVisitors = max(0, $uniqueVisitors - $loggedInCustomers);

        // Conversion Funnel
        $impressions = max(1, $totalShopViews + $totalShowroomViews);
        $funnel = [
            'shop_impressions' => $impressions,
            'showroom_visits' => $totalShowroomViews,
            'product_views' => $totalProductViews,
            'whatsapp_clicks' => $totalWhatsappClicks,
            'enquiries' => $totalEnquiries,
            'orders' => $totalOrders,
            'conversion_rates' => [
                'visit_to_product_percent' => round(($totalProductViews / $impressions) * 100, 1),
                'product_to_lead_percent' => round(($totalWhatsappClicks / max(1, $totalProductViews)) * 100, 1),
                'lead_to_enquiry_percent' => round(($totalEnquiries / max(1, $totalWhatsappClicks)) * 100, 1),
                'overall_conversion_percent' => round(($totalOrders / $impressions) * 100, 2),
            ],
        ];

        // Today stats
        $todaySince = now()->startOfDay();
        $todayEvents = ActivityEvent::where('created_at', '>=', $todaySince);
        $todayStats = [
            'views' => (clone $todayEvents)->whereIn('event_type', ['shop_view', 'showroom_view', 'product_view'])->count(),
            'whatsapp_clicks' => (clone $todayEvents)->where('event_type', 'whatsapp_click')->count(),
            'enquiries' => Inquiry::where('created_at', '>=', $todaySince)->count(),
            'orders' => Order::where('created_at', '>=', $todaySince)->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => $totalUsers,
                'total_customers' => $totalCustomers,
                'total_shop_owners' => $totalShopOwners,
                'total_shop_admins' => $totalShopAdmins,
                'total_shops' => $totalShops,
                'active_shops' => $activeShops,
                'inactive_shops' => $inactiveShops,
                'verified_shops' => $verifiedShops,
                'featured_shops' => $featuredShops,
                'total_products' => $totalProducts,
                'total_categories' => $totalCategories,
                'total_locations' => $totalLocations,
                'total_shop_views' => $totalShopViews,
                'total_showroom_views' => $totalShowroomViews,
                'total_product_views' => $totalProductViews,
                'total_whatsapp_clicks' => $totalWhatsappClicks,
                'total_phone_clicks' => $totalPhoneClicks,
                'total_shares' => $totalShares,
                'total_enquiries' => $totalEnquiries,
                'total_orders' => $totalOrders,
                'unique_visitors' => $uniqueVisitors,
                'logged_in_customers' => $loggedInCustomers,
                'guest_visitors' => $guestVisitors,
                'conversion_funnel' => $funnel,
                'today_stats' => $todayStats,
            ],
        ]);
    }

    public function owners(Request $request)
    {
        $q = User::where('role', User::ROLE_SHOP_OWNER)
            ->with(['business' => fn($b) => $b->withCount(['products', 'inquiries', 'orders'])])
            ->latest('id');

        if ($request->filled('search')) {
            $s = trim((string)$request->search);
            $q->where(fn($x) => $x->where('name', 'like', "%$s%")->orWhere('email', 'like', "%$s%")->orWhere('phone', 'like', "%$s%"));
        }

        $owners = $q->paginate(25);
        return response()->json([
            'success' => true,
            'data' => $owners,
        ]);
    }

    public function owner(int $id)
    {
        $owner = User::with(['business' => fn($b) => $b->withCount(['products', 'inquiries', 'orders', 'visitorSessions'])])
            ->findOrFail($id);

        $businessId = $owner->business?->id;
        $stats = [
            'total_shops' => $owner->business ? 1 : 0,
            'total_products' => $owner->business?->products_count ?? 0,
            'total_visitors' => $owner->business?->visitor_sessions_count ?? 0,
            'total_views' => $businessId ? ActivityEvent::where('business_id', $businessId)->count() : 0,
            'total_whatsapp_clicks' => $businessId ? ActivityEvent::where('business_id', $businessId)->where('event_type', 'whatsapp_click')->count() : 0,
            'total_enquiries' => $owner->business?->inquiries_count ?? 0,
            'total_orders' => $owner->business?->orders_count ?? 0,
        ];

        return response()->json([
            'success' => true,
            'owner' => $owner,
            'stats' => $stats,
            'shops' => $owner->business ? [$owner->business] : [],
        ]);
    }

    public function shops(Request $request)
    {
        $q = Business::with(['user:id,name,email,phone', 'marketplaceCategory:id,name', 'marketplaceLocation:id,name,state'])
            ->withCount(['products', 'inquiries', 'orders', 'visitorSessions']);

        if ($request->filled('search')) {
            $s = trim((string)$request->search);
            $q->where(fn($x) => $x->where('name', 'like', "%$s%")->orWhere('city', 'like', "%$s%")->orWhere('slug', 'like', "%$s%"));
        }

        if ($request->filled('city')) {
            $q->where('city', 'like', '%' . trim((string)$request->city) . '%');
        }

        if ($request->has('is_verified')) {
            $q->where('is_verified', $request->boolean('is_verified'));
        }

        if ($request->has('is_featured')) {
            $q->where('is_featured', $request->boolean('is_featured'));
        }

        return response()->json([
            'success' => true,
            'data' => $q->latest('id')->paginate(25),
        ]);
    }

    public function shop(int $id)
    {
        $shop = Business::with(['user', 'marketplaceCategory', 'marketplaceLocation', 'categories'])
            ->withCount(['products', 'inquiries', 'orders', 'visitorSessions'])
            ->findOrFail($id);

        $events = ActivityEvent::where('business_id', $shop->id);
        $views = (clone $events)->whereIn('event_type', ['shop_view', 'showroom_view'])->count();
        $whatsapp = (clone $events)->where('event_type', 'whatsapp_click')->count();
        $calls = (clone $events)->where('event_type', 'phone_click')->count();
        $shares = (clone $events)->whereIn('event_type', ['shop_share', 'product_share'])->count();

        return response()->json([
            'success' => true,
            'shop' => $shop,
            'metrics' => [
                'views' => $views,
                'whatsapp_clicks' => $whatsapp,
                'phone_calls' => $calls,
                'shares' => $shares,
                'visitors' => $shop->visitor_sessions_count,
                'enquiries' => $shop->inquiries_count,
                'orders' => $shop->orders_count,
            ],
            'products' => $shop->products()->with(['category', 'images'])->latest('id')->limit(50)->get(),
        ]);
    }

    public function toggleVerify(int $id)
    {
        $shop = Business::findOrFail($id);
        $old = ['is_verified' => $shop->is_verified];
        $shop->is_verified = !$shop->is_verified;
        $shop->save();

        AuditLog::log($shop->is_verified ? 'verify_shop' : 'unverify_shop', 'Business', $shop->id, $old, ['is_verified' => $shop->is_verified]);

        return response()->json([
            'success' => true,
            'message' => $shop->is_verified ? 'Shop verified.' : 'Shop verification removed.',
            'shop' => $shop,
        ]);
    }

    public function toggleFeature(int $id)
    {
        $shop = Business::findOrFail($id);
        $old = ['is_featured' => $shop->is_featured];
        $shop->is_featured = !$shop->is_featured;
        $shop->save();

        AuditLog::log($shop->is_featured ? 'feature_shop' : 'unfeature_shop', 'Business', $shop->id, $old, ['is_featured' => $shop->is_featured]);

        return response()->json([
            'success' => true,
            'message' => $shop->is_featured ? 'Shop featured.' : 'Shop unfeatured.',
            'shop' => $shop,
        ]);
    }

    public function toggleActive(int $id)
    {
        $shop = Business::findOrFail($id);
        $old = ['is_active' => $shop->is_active];
        $shop->is_active = !$shop->is_active;
        $shop->save();

        AuditLog::log($shop->is_active ? 'activate_shop' : 'suspend_shop', 'Business', $shop->id, $old, ['is_active' => $shop->is_active]);

        return response()->json([
            'success' => true,
            'message' => $shop->is_active ? 'Shop activated.' : 'Shop suspended.',
            'shop' => $shop,
        ]);
    }

    public function products(Request $request)
    {
        $q = Product::with(['business:id,name,slug,city,user_id', 'business.user:id,name,phone', 'category:id,name', 'images'])
            ->withCount(['inquiries']);

        if ($request->filled('business_id')) {
            $q->where('business_id', $request->integer('business_id'));
        }
        if ($request->filled('search')) {
            $s = trim((string)$request->search);
            $q->where(fn($x) => $x->where('name', 'like', "%$s%")->orWhere('description', 'like', "%$s%"));
        }
        if ($request->filled('city')) {
            $city = trim((string)$request->city);
            $q->whereHas('business', fn($b) => $b->where('city', 'like', "%$city%"));
        }

        return response()->json([
            'success' => true,
            'data' => $q->latest('id')->paginate(30),
        ]);
    }

    public function customers(Request $request)
    {
        $customers = User::where('role', 'customer')
            ->latest('id')
            ->paginate(30);

        return response()->json([
            'success' => true,
            'data' => $customers,
        ]);
    }

    public function auditLogs(Request $request)
    {
        $logs = AuditLog::with('actor:id,name,email,role')
            ->latest('id')
            ->paginate(50);

        return response()->json([
            'success' => true,
            'data' => $logs,
        ]);
    }
}
