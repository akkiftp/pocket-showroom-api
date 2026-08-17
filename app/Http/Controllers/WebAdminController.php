<?php

namespace App\Http\Controllers;

use App\Models\ActivityEvent;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\Inquiry;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShareLink;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WebAdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $days = (int) $request->input('days', 30);
        $since = $days > 0 ? now()->subDays($days) : now()->subCentury();

        $totalUsers = User::count();
        $totalOwners = User::where('role', User::ROLE_SHOP_OWNER)->count();
        $totalShops = Business::count();
        $activeShops = Business::where('is_active', true)->count();
        $verifiedShops = Business::where('is_verified', true)->count();
        $featuredShops = Business::where('is_featured', true)->count();
        $totalProducts = Product::count();

        $eventsQuery = ActivityEvent::where('created_at', '>=', $since);
        $totalViews = (clone $eventsQuery)->whereIn('event_type', ['shop_view', 'showroom_view', 'marketplace_view'])->count();
        $productViews = (clone $eventsQuery)->where('event_type', 'product_view')->count();
        $whatsappClicks = (clone $eventsQuery)->where('event_type', 'whatsapp_click')->count();
        $totalShares = (clone $eventsQuery)->whereIn('event_type', ['shop_share', 'product_share', 'owner_share'])->count();
        $totalEnquiries = Inquiry::where('created_at', '>=', $since)->count();
        $totalOrders = Order::where('created_at', '>=', $since)->count();
        $uniqueVisitors = (clone $eventsQuery)->whereNotNull('visitor_uuid')->distinct('visitor_uuid')->count('visitor_uuid');

        $shops = Business::with(['user', 'marketplaceCategory'])
            ->withCount(['products', 'inquiries', 'orders', 'visitorSessions'])
            ->latest('id')
            ->paginate(20);

        $owners = User::where('role', User::ROLE_SHOP_OWNER)
            ->with(['business' => fn($b) => $b->withCount(['products', 'inquiries', 'orders'])])
            ->latest('id')
            ->limit(15)
            ->get();

        $products = Product::with(['business', 'category', 'images'])
            ->latest('id')
            ->limit(12)
            ->get();

        $recentActivity = ActivityEvent::with(['business', 'product'])
            ->latest('id')
            ->limit(20)
            ->get();

        $auditLogs = AuditLog::with('actor')
            ->latest('id')
            ->limit(15)
            ->get();

        return view('admin.dashboard', compact(
            'days',
            'totalUsers',
            'totalOwners',
            'totalShops',
            'activeShops',
            'verifiedShops',
            'featuredShops',
            'totalProducts',
            'totalViews',
            'productViews',
            'whatsappClicks',
            'totalShares',
            'totalEnquiries',
            'totalOrders',
            'uniqueVisitors',
            'shops',
            'owners',
            'products',
            'recentActivity',
            'auditLogs'
        ));
    }

    public function toggleVerify(Request $request, int $id)
    {
        $shop = Business::findOrFail($id);
        $old = ['is_verified' => $shop->is_verified];
        $shop->is_verified = !$shop->is_verified;
        $shop->save();

        AuditLog::log($shop->is_verified ? 'verify_shop' : 'unverify_shop', 'Business', $shop->id, $old, ['is_verified' => $shop->is_verified]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_verified' => $shop->is_verified]);
        }
        return back()->with('success', 'Shop verification status updated.');
    }

    public function toggleFeature(Request $request, int $id)
    {
        $shop = Business::findOrFail($id);
        $old = ['is_featured' => $shop->is_featured];
        $shop->is_featured = !$shop->is_featured;
        $shop->save();

        AuditLog::log($shop->is_featured ? 'feature_shop' : 'unfeature_shop', 'Business', $shop->id, $old, ['is_featured' => $shop->is_featured]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_featured' => $shop->is_featured]);
        }
        return back()->with('success', 'Shop featured status updated.');
    }

    public function toggleActive(Request $request, int $id)
    {
        $shop = Business::findOrFail($id);
        $old = ['is_active' => $shop->is_active];
        $shop->is_active = !$shop->is_active;
        $shop->save();

        AuditLog::log($shop->is_active ? 'activate_shop' : 'suspend_shop', 'Business', $shop->id, $old, ['is_active' => $shop->is_active]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_active' => $shop->is_active]);
        }
        return back()->with('success', 'Shop active status updated.');
    }
}
