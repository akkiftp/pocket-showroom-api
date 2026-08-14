<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function users(Request $request)
    {
        $users = User::with('business')->orderByDesc('id')->get();
        $data = $users->map(function (User $u) {
            $business = $u->business;
            $metrics = ['products'=>0,'views'=>0,'shares'=>0,'whatsapp_clicks'=>0,'inquiries'=>0,'orders'=>0,'unique_visitors'=>0];
            if ($business) {
                $metrics['products'] = $business->products()->count();
                $metrics['views'] = $business->activityEvents()->where('event_type','product_view')->count();
                $metrics['shares'] = $business->activityEvents()->whereIn('event_type',['share','owner_share'])->count();
                $metrics['whatsapp_clicks'] = $business->activityEvents()->where('event_type','whatsapp_click')->count();
                $metrics['inquiries'] = $business->inquiries()->count();
                $metrics['orders'] = $business->orders()->count();
                $metrics['unique_visitors'] = $business->visitorSessions()->count();
            }
            return [
                'id'=>(string)$u->id,'name'=>$u->name??'Shop Owner','phone'=>$u->phone??'','email'=>$u->email??'',
                'authProvider'=>$u->auth_provider??'','avatarUrl'=>$u->avatar_url,'businessName'=>$business?->name??($u->name.' Showroom'),
                'businessId'=>$business?->id,'showroomSlug'=>$business?->slug,'city'=>$business?->city??'',
                'registeredAt'=>$u->created_at?->toIso8601String()??now()->toIso8601String(),'subscriptionStatus'=>$u->subscription_status??'active',
                'trialExpiresAt'=>$u->trial_expires_at?->toIso8601String(),'subscriptionExpiresAt'=>$u->subscription_expires_at?->toIso8601String(),
                'isAdmin'=>(bool)$u->is_admin,'metrics'=>$metrics,
            ];
        });
        return response()->json(['status'=>true,'data'=>$data]);
    }

    public function overview(Request $request)
    {
        $from = now()->subDays(min(max($request->integer('days',30),1),365)-1)->startOfDay();
        $businesses = Business::count();
        $events = DB::table('activity_events')->where('created_at','>=',$from);
        $count = fn($type)=>(clone $events)->where('event_type',$type)->count();
        $topBusinesses = Business::query()->with('user:id,name,email')->withCount([
            'products','visitorSessions',
            'activityEvents as product_views'=>fn($q)=>$q->where('event_type','product_view')->where('created_at','>=',$from),
            'activityEvents as whatsapp_clicks'=>fn($q)=>$q->where('event_type','whatsapp_click')->where('created_at','>=',$from),
            'activityEvents as shares'=>fn($q)=>$q->whereIn('event_type',['share','owner_share'])->where('created_at','>=',$from),
            'inquiries as inquiries_count'=>fn($q)=>$q->where('created_at','>=',$from),
            'orders as orders_count'=>fn($q)=>$q->where('created_at','>=',$from),
        ])->orderByDesc('product_views')->limit(25)->get();

        return response()->json([
            'stats'=>[
                'owners'=>User::where('is_admin',false)->count(),'businesses'=>$businesses,'products'=>DB::table('products')->whereNull('deleted_at')->count(),
                'unique_visitors'=>DB::table('visitor_sessions')->where('last_seen_at','>=',$from)->count(),'showroom_views'=>$count('showroom_view'),'product_views'=>$count('product_view'),
                'shares'=>$count('share')+$count('owner_share'),'whatsapp_clicks'=>$count('whatsapp_click'),'inquiries'=>DB::table('inquiries')->where('created_at','>=',$from)->count(),
                'orders'=>DB::table('orders')->where('created_at','>=',$from)->count(),'order_value'=>(float)DB::table('orders')->where('created_at','>=',$from)->sum('total'),
            ],
            'top_businesses'=>$topBusinesses,
            'recent_events'=>\App\Models\ActivityEvent::with(['business:id,name,slug','product:id,name','visitor:id,customer_name,phone,email,visitor_token'])->latest()->limit(40)->get(),
        ]);
    }

    public function businessAnalytics(Request $request, Business $business)
    {
        return response()->json(AnalyticsController::buildBusinessAnalytics($business, (int)$request->integer('days',30)));
    }

    public function activate(Request $request,$id){$months=(int)($request->input('months')?:1);$u=User::findOrFail($id);$u->update(['subscription_status'=>'active','subscription_expires_at'=>now()->addDays(30*$months)]);return response()->json(['status'=>true,'message'=>'User activated.','user'=>$u]);}
    public function extendTrial(Request $request,$id){$days=(int)($request->input('days')?:7);$u=User::findOrFail($id);$u->update(['subscription_status'=>'trial','trial_expires_at'=>now()->addDays($days)]);return response()->json(['status'=>true,'message'=>'Trial extended.','user'=>$u]);}
    public function block(Request $request,$id){$u=User::findOrFail($id);$u->update(['subscription_status'=>'blocked']);return response()->json(['status'=>true,'message'=>'User blocked.','user'=>$u]);}
}
