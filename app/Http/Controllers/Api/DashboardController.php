<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessContext;
use App\Models\Business;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $business=BusinessContext::require($request);
        $events=$business->activityEvents();
        return response()->json([
            'business'=>$business,
            'stats'=>[
                'products_count'=>$business->products()->count(),
                'in_stock_count'=>$business->products()->where('in_stock',true)->count(),
                'enquiries_count'=>$business->inquiries()->count(),
                'orders_count'=>$business->orders()->count(),
                'unique_visitors'=>$business->visitorSessions()->count(),
                'showroom_views'=>(clone $events)->where('event_type','showroom_view')->count(),
                'total_product_views'=>(clone $events)->where('event_type','product_view')->count(),
                'shares'=>(clone $events)->whereIn('event_type',['share','owner_share'])->count(),
                'whatsapp_clicks'=>(clone $events)->where('event_type','whatsapp_click')->count(),
                'favorites'=>(clone $events)->where('event_type','favorite')->count(),
            ],
            'recent_inquiries'=>$business->inquiries()->with('product:id,name')->latest()->limit(5)->get(),
            'recent_products'=>$business->products()->with(['category:id,name','images'])->latest()->limit(5)->get(),
            'recent_activity'=>$business->activityEvents()->with(['visitor:id,customer_name,phone,email,visitor_token','product:id,name'])->latest()->limit(8)->get(),
            'showroom_url'=>url('/showrooms/'.$business->slug),'public_api_url'=>url('/api/public/showrooms/'.$business->slug),
        ]);
    }
}
