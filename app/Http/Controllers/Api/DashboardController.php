<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $business = Business::where('user_id', $request->user()->id)->firstOrFail();

        return response()->json([
            'business' => $business,
            'stats' => [
                'products' => $business->products()->count(),
                'active_products' => $business->products()->where('is_active', true)->where('in_stock', true)->count(),
                'featured_products' => $business->products()->where('is_active', true)->where('featured', true)->count(),
                'categories' => $business->categories()->count(),
                'pending_inquiries' => $business->inquiries()->where('status', 'pending')->count(),
                'handled_inquiries' => $business->inquiries()->where('status', 'handled')->count(),
                'total_product_views' => (int) $business->products()->sum('views_count'),
            ],
            'recent_inquiries' => $business->inquiries()
                ->with('product:id,name')
                ->latest()
                ->limit(5)
                ->get(),
            'recent_products' => $business->products()
                ->with(['category:id,name', 'images'])
                ->latest()
                ->limit(5)
                ->get(),
            'showroom_url' => url('/showroom/'.$business->slug),
            'public_api_url' => url('/api/public/showrooms/'.$business->slug),
        ]);
    }
}
