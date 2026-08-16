<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessContext;
use App\Models\ActivityEvent;
use App\Models\Business;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalyticsController extends Controller
{
    private function business(Request $request): Business { return BusinessContext::require($request); }

    public function overview(Request $request)
    {
        return response()->json($this->buildBusinessAnalytics($this->business($request), (int) $request->integer('days', 30)));
    }

    public function customers(Request $request)
    {
        $business = $this->business($request);
        $visitors = $business->visitorSessions()
            ->withCount([
                'events as product_views' => fn ($q) => $q->where('event_type','product_view'),
                'events as whatsapp_clicks' => fn ($q) => $q->where('event_type','whatsapp_click'),
                'events as shares' => fn ($q) => $q->whereIn('event_type',['share','owner_share']),
                'events as favorites' => fn ($q) => $q->where('event_type','favorite'),
                'events as cart_adds' => fn ($q) => $q->where('event_type','add_to_cart'),
                'events as inquiries' => fn ($q) => $q->where('event_type','inquiry'),
            ])
            ->orderByDesc('last_seen_at')
            ->paginate(min(max($request->integer('per_page',30),1),100));
        return response()->json($visitors);
    }

    public function products(Request $request)
    {
        $business = $this->business($request);
        $rows = $business->products()->withCount([
            'activityEvents as tracked_views' => fn($q)=>$q->where('event_type','product_view'),
            'activityEvents as shares' => fn($q)=>$q->where('event_type','share'),
            'activityEvents as whatsapp_clicks' => fn($q)=>$q->where('event_type','whatsapp_click'),
            'activityEvents as favorites' => fn($q)=>$q->where('event_type','favorite'),
            'activityEvents as cart_adds' => fn($q)=>$q->where('event_type','add_to_cart'),
            'activityEvents as inquiries' => fn($q)=>$q->where('event_type','inquiry'),
        ])->orderByDesc('tracked_views')->get();
        return response()->json(['data'=>$rows]);
    }

    public static function buildBusinessAnalytics(Business $business, int $days = 30): array
    {
        $days = min(max($days, 1), 365);
        $from = now()->subDays($days - 1)->startOfDay();
        $events = ActivityEvent::where('business_id',$business->id)->where('created_at','>=',$from);
        $count = fn(string $type) => (clone $events)->where('event_type',$type)->count();

        $uniqueVisitors = $business->visitorSessions()->where('last_seen_at','>=',$from)->count();
        $identifiedVisitors = $business->visitorSessions()->where('last_seen_at','>=',$from)
            ->where(fn($q)=>$q->whereNotNull('phone')->orWhereNotNull('email'))->count();

        $daily = ActivityEvent::selectRaw("date(created_at) as day, count(*) as total")
            ->where('business_id',$business->id)->where('created_at','>=',$from)
            ->groupBy('day')->orderBy('day')->get()->map(fn($r)=>['day'=>$r->day,'total'=>(int)$r->total]);

        $topProducts = $business->products()->with(['images'])->withCount([
            'activityEvents as tracked_views'=>fn($q)=>$q->where('event_type','product_view')->where('created_at','>=',$from),
            'activityEvents as whatsapp_clicks'=>fn($q)=>$q->where('event_type','whatsapp_click')->where('created_at','>=',$from),
            'activityEvents as favorites'=>fn($q)=>$q->where('event_type','favorite')->where('created_at','>=',$from),
            'activityEvents as cart_adds'=>fn($q)=>$q->where('event_type','add_to_cart')->where('created_at','>=',$from),
        ])->orderByDesc('tracked_views')->limit(8)->get();

        $recentEvents = ActivityEvent::where('business_id',$business->id)
            ->with(['visitor:id,customer_name,phone,email,visitor_token,last_seen_at','product:id,name'])
            ->latest()->limit(25)->get();

        $orders = Order::where('business_id',$business->id)->where('created_at','>=',$from);
        $orderCount = (clone $orders)->count();
        $orderValue = (float) (clone $orders)->sum('total');
        $inquiries = $count('inquiry');
        $whatsappClicks = $count('whatsapp_click');
        $productViews = $count('product_view');

        return [
            'business' => $business,
            'period_days' => $days,
            'stats' => [
                'products' => $business->products()->count(),
                'unique_visitors' => $uniqueVisitors,
                'identified_visitors' => $identifiedVisitors,
                'showroom_views' => $count('showroom_view'),
                'product_views' => $productViews,
                'shares' => $count('share') + $count('owner_share'),
                'whatsapp_clicks' => $whatsappClicks,
                'favorites' => $count('favorite'),
                'cart_adds' => $count('add_to_cart'),
                'inquiries' => $inquiries,
                'orders' => $orderCount,
                'order_value' => $orderValue,
                'view_to_whatsapp_rate' => $productViews > 0 ? round(($whatsappClicks/$productViews)*100,1) : 0,
                'view_to_inquiry_rate' => $productViews > 0 ? round(($inquiries/$productViews)*100,1) : 0,
            ],
            'daily_activity' => $daily,
            'top_products' => $topProducts,
            'recent_events' => $recentEvents,
            'tracking_note' => 'WhatsApp metrics represent button clicks/open intents. Actual WhatsApp message delivery/replies require WhatsApp Business API webhooks.',
        ];
    }
}
