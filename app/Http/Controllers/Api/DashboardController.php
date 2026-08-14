<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $business=Business::where('user_id',$request->user()->id)->firstOrFail();
        $events=$business->activityEvents();
        return response()->json([
            'business'=>$business,
            'stats'=>[
                'products'=>$business->products()->count(),'active_products'=>$business->products()->where('is_active',true)->where('in_stock',true)->count(),
                'featured_products'=>$business->products()->where('is_active',true)->where('featured',true)->count(),'categories'=>$business->categories()->count(),
                'pending_inquiries'=>$business->inquiries()->where('status','pending')->count(),'handled_inquiries'=>$business->inquiries()->where('status','handled')->count(),
                'orders'=>$business->orders()->count(),'new_orders'=>$business->orders()->where('status','new')->count(),
                'unique_visitors'=>$business->visitorSessions()->count(),'showroom_views'=>(clone $events)->where('event_type','showroom_view')->count(),
                'total_product_views'=>(clone $events)->where('event_type','product_view')->count(),'shares'=>(clone $events)->whereIn('event_type',['share','owner_share'])->count(),
                'whatsapp_clicks'=>(clone $events)->where('event_type','whatsapp_click')->count(),'favorites'=>(clone $events)->where('event_type','favorite')->count(),
            ],
            'recent_inquiries'=>$business->inquiries()->with('product:id,name')->latest()->limit(5)->get(),
            'recent_products'=>$business->products()->with(['category:id,name','images'])->latest()->limit(5)->get(),
            'recent_activity'=>$business->activityEvents()->with(['visitor:id,customer_name,phone,email,visitor_token','product:id,name'])->latest()->limit(8)->get(),
            'showroom_url'=>url('/showrooms/'.$business->slug),'public_api_url'=>url('/api/public/showrooms/'.$business->slug),
        ]);
    }
}
