<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MarketplaceController extends Controller
{
    public function home(Request $request)
    {
        $locationId=$request->integer('location_id') ?: null;
        $city=trim((string)$request->query('city',''));
        $base=Business::query()->where('is_active',true)->with(['marketplaceCategory:id,name,slug,icon','marketplaceLocation:id,name,type,state,district,pincode']);
        if($locationId)$base->where('location_id',$locationId);
        elseif($city!=='')$base->where('city','like','%'.$city.'%');

        $featured=(clone $base)->where('is_featured',true)->withCount(['products'=>fn($q)=>$q->where('is_active',true)->where('in_stock',true)])->orderByDesc('is_verified')->latest('id')->limit(12)->get();
        if($featured->isEmpty()) $featured=(clone $base)->withCount(['products'=>fn($q)=>$q->where('is_active',true)->where('in_stock',true)])->orderByDesc('marketplace_views')->latest('id')->limit(12)->get();

        return response()->json([
            'success'=>true,
            'categories'=>MarketplaceCategory::whereNull('parent_id')->where('is_active',true)->with(['children'=>fn($q)=>$q->where('is_active',true)])->withCount(['businesses'=>fn($q)=>$q->where('is_active',true)])->orderBy('sort_order')->orderBy('name')->get(),
            'locations'=>MarketplaceLocation::where('is_active',true)->withCount(['businesses'=>fn($q)=>$q->where('is_active',true)])->orderByDesc('businesses_count')->orderBy('name')->limit(100)->get(),
            'featured_shops'=>$featured,
            'stats'=>['shops'=>Business::where('is_active',true)->count(),'products'=>DB::table('products')->whereNull('deleted_at')->where('is_active',true)->count()],
        ]);
    }

    public function categories()
    {
        return response()->json(['data'=>MarketplaceCategory::whereNull('parent_id')->where('is_active',true)->with(['children'=>fn($q)=>$q->where('is_active',true)])->withCount(['businesses'=>fn($q)=>$q->where('is_active',true)])->orderBy('sort_order')->orderBy('name')->get()]);
    }

    public function locations(Request $request)
    {
        $q=MarketplaceLocation::query()->where('is_active',true);
        if($request->filled('search')){$s=trim((string)$request->search);$q->where(fn($x)=>$x->where('name','like',"%$s%")->orWhere('district','like',"%$s%")->orWhere('state','like',"%$s%")->orWhere('pincode','like',"%$s%"));}
        return response()->json(['data'=>$q->withCount(['businesses'=>fn($b)=>$b->where('is_active',true)])->orderBy('name')->limit(100)->get()]);
    }

    public function shops(Request $request)
    {
        $q=Business::query()->where('is_active',true)->with(['marketplaceCategory:id,name,slug,icon','marketplaceLocation:id,name,type,state,district,pincode'])->withCount(['products'=>fn($p)=>$p->where('is_active',true)->where('in_stock',true)]);
        if($request->integer('category_id'))$q->where('marketplace_category_id',$request->integer('category_id'));
        if($request->integer('location_id'))$q->where('location_id',$request->integer('location_id'));
        if($request->filled('city'))$q->where('city','like','%'.trim((string)$request->city).'%');
        if($request->filled('pincode'))$q->where('pincode',$request->string('pincode')->toString());
        if($request->boolean('featured'))$q->where('is_featured',true);
        if($request->filled('search')){$s=trim((string)$request->search);$q->where(fn($x)=>$x->where('name','like',"%$s%")->orWhere('about','like',"%$s%")->orWhereHas('products',fn($p)=>$p->where('name','like',"%$s%")->where('is_active',true)));}
        $lat=$request->input('lat');$lng=$request->input('lng');
        if(is_numeric($lat)&&is_numeric($lng)){
            $lat=(float)$lat;$lng=(float)$lng;
            $q->select('businesses.*')->selectRaw('(6371 * acos(cos(radians(?)) * cos(radians(latitude)) * cos(radians(longitude) - radians(?)) + sin(radians(?)) * sin(radians(latitude)))) AS distance_km',[$lat,$lng,$lat])->whereNotNull('latitude')->whereNotNull('longitude')->orderBy('distance_km');
        } else {
            $q->orderByDesc('is_featured')->orderByDesc('is_verified')->orderByDesc('marketplace_views')->latest('id');
        }
        return response()->json($q->paginate(min(max($request->integer('per_page',20),1),60)));
    }

    public function shop(Request $request,string $slug)
    {
        $business=Business::where('slug',$slug)->where('is_active',true)->with(['marketplaceCategory','marketplaceLocation'])->withCount(['products'=>fn($q)=>$q->where('is_active',true)->where('in_stock',true)])->firstOrFail();
        $business->increment('marketplace_views');
        return response()->json([
            'success'=>true,
            'business'=>$business->fresh(['marketplaceCategory','marketplaceLocation']),
            'categories'=>$business->categories()->where('is_active',true)->get(),
            'products'=>$business->products()->where('is_active',true)->where('in_stock',true)->with('images')->orderByDesc('is_featured')->latest('id')->limit(200)->get(),
        ]);
    }

    public function search(Request $request)
    {
        $request->validate(['q'=>['required','string','min:2','max:120']]);
        $s=trim((string)$request->q);
        $shops=Business::where('is_active',true)->where(fn($q)=>$q->where('name','like',"%$s%")->orWhere('business_type','like',"%$s%")->orWhere('city','like',"%$s%")->orWhereHas('products',fn($p)=>$p->where('name','like',"%$s%")))->with(['marketplaceCategory:id,name,slug,icon'])->withCount(['products'=>fn($q)=>$q->where('is_active',true)->where('in_stock',true)])->limit(20)->get();
        $products=DB::table('products')->join('businesses','businesses.id','=','products.business_id')->whereNull('products.deleted_at')->where('products.is_active',true)->where('businesses.is_active',true)->where(fn($q)=>$q->where('products.name','like',"%$s%")->orWhere('products.description','like',"%$s%"))->select('products.id','products.name','products.price','products.offer_price','businesses.name as business_name','businesses.slug as business_slug')->limit(30)->get();
        return response()->json(['shops'=>$shops,'products'=>$products]);
    }
}
