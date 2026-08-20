<?php

namespace App\Http\Controllers;

use App\Models\ActivityEvent;
use App\Models\AuditLog;
use App\Models\Business;
use App\Models\CustomerContact;
use App\Models\Inquiry;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceLocation;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WebAdminController extends Controller
{
    public function dashboard(Request $request)
    {
        $tab = $request->input('tab', 'overview');
        $days = (int) $request->input('days', 30);
        $search = trim((string) $request->input('search', ''));
        $statusFilter = $request->input('status', '');
        $since = $days > 0 ? now()->subDays($days) : now()->subCentury();

        // 1. High-level Platform Metrics
        $totalUsers = User::count();
        $totalOwners = User::where('role', User::ROLE_SHOP_OWNER)->count();
        $totalShops = Business::count();
        $activeShops = Business::where('is_active', true)->count();
        $verifiedShops = Business::where('is_verified', true)->count();
        $featuredShops = Business::where('is_featured', true)->count();
        $totalProducts = Product::count();
        $inStockProducts = Product::where('in_stock', true)->count();

        // 2. Range-based Engagement & Funnel Metrics
        $eventsQuery = ActivityEvent::where('created_at', '>=', $since);
        $totalViews = (clone $eventsQuery)->whereIn('event_type', ['shop_view', 'showroom_view', 'marketplace_view'])->count();
        $productViews = (clone $eventsQuery)->where('event_type', 'product_view')->count();
        $whatsappClicks = (clone $eventsQuery)->where('event_type', 'whatsapp_click')->count();
        $totalShares = (clone $eventsQuery)->whereIn('event_type', ['shop_share', 'product_share', 'owner_share'])->count();
        $totalEnquiries = Inquiry::where('created_at', '>=', $since)->count();
        $totalOrders = Order::where('created_at', '>=', $since)->count();
        $orderVolume = Order::where('created_at', '>=', $since)->sum('total');
        $uniqueVisitors = (clone $eventsQuery)->whereNotNull('visitor_uuid')->distinct('visitor_uuid')->count('visitor_uuid');

        // 3. Tab Specific Queries
        // Tab: Shops
        $shopsQuery = Business::with(['user', 'marketplaceCategory', 'marketplaceLocation'])
            ->withCount(['products', 'inquiries', 'orders', 'visitorSessions']);
        if ($search !== '') {
            $shopsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('slug', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        if ($statusFilter === 'active') {
            $shopsQuery->where('is_active', true);
        } elseif ($statusFilter === 'suspended') {
            $shopsQuery->where('is_active', false);
        } elseif ($statusFilter === 'verified') {
            $shopsQuery->where('is_verified', true);
        } elseif ($statusFilter === 'featured') {
            $shopsQuery->where('is_featured', true);
        }
        $shops = $shopsQuery->latest('id')->paginate(15, ['*'], 'shops_page')->withQueryString();

        // Tab: Users & Shop Owners
        $usersQuery = User::with(['business' => fn($b) => $b->withCount(['products', 'inquiries', 'orders'])]);
        if ($search !== '') {
            $usersQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhereHas('business', function ($bq) use ($search) {
                      $bq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if ($statusFilter === 'owner') {
            $usersQuery->where('role', User::ROLE_SHOP_OWNER);
        } elseif ($statusFilter === 'admin') {
            $usersQuery->where('role', User::ROLE_SUPER_ADMIN);
        } elseif ($statusFilter === 'active') {
            $usersQuery->where('is_active', true);
        } elseif ($statusFilter === 'blocked') {
            $usersQuery->where('is_active', false);
        }
        $users = $usersQuery->latest('id')->paginate(15, ['*'], 'users_page')->withQueryString();

        // Tab: Products Catalog
        $productsQuery = Product::with(['business', 'category', 'images']);
        if ($search !== '') {
            $productsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhereHas('business', function ($bq) use ($search) {
                      $bq->where('name', 'like', "%{$search}%");
                  });
            });
        }
        if ($statusFilter === 'instock') {
            $productsQuery->where('in_stock', true);
        } elseif ($statusFilter === 'outofstock') {
            $productsQuery->where('in_stock', false);
        }
        $products = $productsQuery->latest('id')->paginate(15, ['*'], 'products_page')->withQueryString();

        // Tab: Customer Leads (CustomerContacts + Inquiries + Orders)
        $contactsQuery = CustomerContact::with('business');
        if ($search !== '') {
            $contactsQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }
        $customerLeads = $contactsQuery->latest('last_activity_at')->paginate(20, ['*'], 'leads_page')->withQueryString();

        // Tab: Orders
        $ordersQuery = Order::with(['business', 'items.product']);
        if ($search !== '') {
            $ordersQuery->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('business', fn($bq) => $bq->where('name', 'like', "%{$search}%"));
            });
        }
        if ($statusFilter && in_array($statusFilter, ['pending', 'processing', 'completed', 'cancelled'])) {
            $ordersQuery->where('status', $statusFilter);
        }
        $orders = $ordersQuery->latest('id')->paginate(15, ['*'], 'orders_page')->withQueryString();

        // Tab: Inquiries
        $inquiriesQuery = Inquiry::with(['business', 'product']);
        if ($search !== '') {
            $inquiriesQuery->where(function ($q) use ($search) {
                $q->where('customer_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%")
                  ->orWhereHas('business', fn($bq) => $bq->where('name', 'like', "%{$search}%"));
            });
        }
        if ($statusFilter && in_array($statusFilter, ['pending', 'handled', 'replied'])) {
            $inquiriesQuery->where('status', $statusFilter);
        }
        $inquiries = $inquiriesQuery->latest('id')->paginate(15, ['*'], 'inquiries_page')->withQueryString();

        // Tab: Categories & Locations
        $categories = MarketplaceCategory::with(['parent'])->withCount('businesses')->orderBy('sort_order')->orderBy('name')->get();
        $parentCategories = MarketplaceCategory::whereNull('parent_id')->orderBy('name')->get();
        $locations = MarketplaceLocation::withCount('businesses')->orderBy('state')->orderBy('name')->get();

        // Overview / Live Stream Data
        $recentActivity = ActivityEvent::with(['business', 'product'])->latest('id')->limit(15)->get();
        $auditLogs = AuditLog::with('actor')->latest('id')->limit(15)->get();

        return view('admin.dashboard', compact(
            'tab',
            'days',
            'search',
            'statusFilter',
            'totalUsers',
            'totalOwners',
            'totalShops',
            'activeShops',
            'verifiedShops',
            'featuredShops',
            'totalProducts',
            'inStockProducts',
            'totalViews',
            'productViews',
            'whatsappClicks',
            'totalShares',
            'totalEnquiries',
            'totalOrders',
            'orderVolume',
            'uniqueVisitors',
            'shops',
            'users',
            'products',
            'customerLeads',
            'orders',
            'inquiries',
            'categories',
            'parentCategories',
            'locations',
            'recentActivity',
            'auditLogs'
        ));
    }

    /* =========================================================================
     * SHOP ACTIONS
     * ========================================================================= */
    public function toggleVerify(Request $request, int $id)
    {
        $shop = Business::findOrFail($id);
        $old = ['is_verified' => (bool)$shop->is_verified];
        $shop->is_verified = !$shop->is_verified;
        $shop->save();

        AuditLog::log($shop->is_verified ? 'verify_shop' : 'unverify_shop', 'Business', $shop->id, $old, ['is_verified' => $shop->is_verified]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_verified' => $shop->is_verified]);
        }
        return back()->with('success', "Shop '{$shop->name}' verification updated to " . ($shop->is_verified ? 'Verified' : 'Unverified') . ".");
    }

    public function toggleFeature(Request $request, int $id)
    {
        $shop = Business::findOrFail($id);
        $old = ['is_featured' => (bool)$shop->is_featured];
        $shop->is_featured = !$shop->is_featured;
        $shop->save();

        AuditLog::log($shop->is_featured ? 'feature_shop' : 'unfeature_shop', 'Business', $shop->id, $old, ['is_featured' => $shop->is_featured]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_featured' => $shop->is_featured]);
        }
        return back()->with('success', "Shop '{$shop->name}' featured status updated to " . ($shop->is_featured ? 'Featured' : 'Standard') . ".");
    }

    public function toggleActive(Request $request, int $id)
    {
        $shop = Business::findOrFail($id);
        $old = ['is_active' => (bool)$shop->is_active];
        $shop->is_active = !$shop->is_active;
        $shop->save();

        AuditLog::log($shop->is_active ? 'activate_shop' : 'suspend_shop', 'Business', $shop->id, $old, ['is_active' => $shop->is_active]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_active' => $shop->is_active]);
        }
        return back()->with('success', "Shop '{$shop->name}' is now " . ($shop->is_active ? 'Active' : 'Suspended') . ".");
    }

    /* =========================================================================
     * USER / OWNER ACTIONS
     * ========================================================================= */
    public function activateUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $old = ['is_active' => (bool)$user->is_active];
        $user->is_active = true;
        $user->save();

        AuditLog::log('activate_user', 'User', $user->id, $old, ['is_active' => true]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_active' => true]);
        }
        return back()->with('success', "User '{$user->name}' has been activated.");
    }

    public function blockUser(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $old = ['is_active' => (bool)$user->is_active];
        $user->is_active = false;
        $user->save();

        AuditLog::log('block_user', 'User', $user->id, $old, ['is_active' => false]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'is_active' => false]);
        }
        return back()->with('success', "User '{$user->name}' has been blocked.");
    }

    public function extendTrial(Request $request, int $id)
    {
        $user = User::findOrFail($id);
        $days = (int) $request->input('days', 30);
        $old = ['trial_expires_at' => $user->trial_expires_at, 'subscription_status' => $user->subscription_status];

        $currentExpiry = ($user->trial_expires_at && $user->trial_expires_at->isFuture())
            ? $user->trial_expires_at
            : now();

        $user->trial_expires_at = $currentExpiry->addDays($days);
        $user->subscription_status = 'trial';
        $user->save();

        AuditLog::log('extend_trial', 'User', $user->id, $old, [
            'trial_expires_at' => $user->trial_expires_at,
            'days_added' => $days,
        ]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'trial_expires_at' => $user->trial_expires_at->toIso8601String()]);
        }
        return back()->with('success', "Trial extended by {$days} days for '{$user->name}' (valid until {$user->trial_expires_at->format('M d, Y')}).");
    }

    /* =========================================================================
     * PRODUCT ACTIONS
     * ========================================================================= */
    public function toggleStock(Request $request, int $id)
    {
        $product = Product::findOrFail($id);
        $old = ['in_stock' => (bool)$product->in_stock];
        $product->in_stock = !$product->in_stock;
        $product->save();

        AuditLog::log($product->in_stock ? 'product_in_stock' : 'product_out_of_stock', 'Product', $product->id, $old, ['in_stock' => $product->in_stock]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'in_stock' => $product->in_stock]);
        }
        return back()->with('success', "Product '{$product->name}' stock marked as " . ($product->in_stock ? 'In Stock' : 'Out of Stock') . ".");
    }

    public function deleteProduct(Request $request, int $id)
    {
        $product = Product::findOrFail($id);
        $productName = $product->name;
        $product->delete();

        AuditLog::log('delete_product', 'Product', $id, ['name' => $productName], null);

        if ($request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', "Product '{$productName}' deleted successfully.");
    }

    /* =========================================================================
     * ORDER & INQUIRY ACTIONS
     * ========================================================================= */
    public function updateOrderStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,completed,cancelled',
        ]);

        $order = Order::findOrFail($id);
        $old = ['status' => $order->status];
        $order->status = $request->input('status');
        $order->save();

        AuditLog::log('update_order_status', 'Order', $order->id, $old, ['status' => $order->status]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $order->status]);
        }
        return back()->with('success', "Order #{$order->id} status updated to '{$order->status}'.");
    }

    public function toggleInquiryStatus(Request $request, int $id)
    {
        $inquiry = Inquiry::findOrFail($id);
        $old = ['status' => $inquiry->status];
        $newStatus = $request->input('status', ($inquiry->status === 'handled' ? 'pending' : 'handled'));
        $inquiry->status = $newStatus;
        $inquiry->save();

        AuditLog::log('update_inquiry_status', 'Inquiry', $inquiry->id, $old, ['status' => $inquiry->status]);

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'status' => $inquiry->status]);
        }
        return back()->with('success', "Inquiry #{$inquiry->id} marked as '{$inquiry->status}'.");
    }

    /* =========================================================================
     * CATEGORY & LOCATION ACTIONS
     * ========================================================================= */
    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'nullable|string|max:120',
            'icon' => 'nullable|string|max:50',
            'parent_id' => 'nullable|exists:marketplace_categories,id',
            'sort_order' => 'nullable|integer',
        ]);

        $slug = $request->input('slug') ? Str::slug($request->input('slug')) : Str::slug($request->input('name'));
        // Ensure uniqueness
        if (MarketplaceCategory::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }

        $cat = MarketplaceCategory::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'icon' => $request->input('icon', '🏪'),
            'parent_id' => $request->input('parent_id') ?: null,
            'sort_order' => (int) $request->input('sort_order', 0),
            'is_active' => true,
        ]);

        AuditLog::log('create_category', 'MarketplaceCategory', $cat->id, null, $cat->toArray());

        return back()->with('success', "Category '{$cat->name}' added successfully.");
    }

    public function deleteCategory(Request $request, int $id)
    {
        $cat = MarketplaceCategory::findOrFail($id);
        $name = $cat->name;
        $cat->delete();

        AuditLog::log('delete_category', 'MarketplaceCategory', $id, ['name' => $name], null);

        return back()->with('success', "Category '{$name}' deleted successfully.");
    }

    public function storeLocation(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'district' => 'nullable|string|max:100',
            'pincode' => 'nullable|string|max:10',
        ]);

        $slug = Str::slug($request->input('name') . '-' . $request->input('state'));
        if (MarketplaceLocation::where('slug', $slug)->exists()) {
            $slug .= '-' . Str::random(4);
        }

        $loc = MarketplaceLocation::create([
            'name' => $request->input('name'),
            'slug' => $slug,
            'state' => $request->input('state'),
            'district' => $request->input('district') ?: $request->input('name'),
            'pincode' => $request->input('pincode'),
            'type' => 'city',
            'is_active' => true,
        ]);

        AuditLog::log('create_location', 'MarketplaceLocation', $loc->id, null, $loc->toArray());

        return back()->with('success', "Location '{$loc->name}, {$loc->state}' added successfully.");
    }

    public function deleteLocation(Request $request, int $id)
    {
        $loc = MarketplaceLocation::findOrFail($id);
        $name = $loc->name;
        $loc->delete();

        AuditLog::log('delete_location', 'MarketplaceLocation', $id, ['name' => $name], null);

        return back()->with('success', "Location '{$name}' deleted successfully.");
    }
}
