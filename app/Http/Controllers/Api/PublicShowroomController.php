<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use App\Services\ActivityTracker;
use Illuminate\Http\Request;

class PublicShowroomController extends Controller
{
    private function business(string $slug): Business { return Business::where('slug',$slug)->where('is_active',true)->firstOrFail(); }

    public function show(Request $request,string $slug)
    {
        $business=$this->business($slug);
        $products=$business->products()->where('is_active',true)->where('in_stock',true)->with(['category:id,name','images'])->orderBy('sort_order')->latest('id')->limit(60)->get();
        if ($request->filled('visitor_token')) ActivityTracker::record($request,$business,'showroom_view',visitorToken:$request->string('visitor_token')->toString(),source:'app');
        $featured=$products->where('featured',true)->values();
        return response()->json(['success'=>true,'business'=>$business,'categories'=>$business->categories()->where('is_active',true)->withCount(['products'=>fn($q)=>$q->where('is_active',true)->where('in_stock',true)])->get(),'products'=>$products,'featured_products'=>$featured->isNotEmpty()?$featured:$products->take(12)]);
    }

    public function products(Request $request,string $slug)
    {
        $business=$this->business($slug);$q=$business->products()->where('is_active',true)->where('in_stock',true)->with(['category:id,name','images']);
        if($request->filled('category_id'))$q->where('category_id',$request->integer('category_id'));
        if($request->filled('search')){$s=trim((string)$request->search);$q->where(fn($x)=>$x->where('name','like',"%$s%")->orWhere('description','like',"%$s%"));}
        return response()->json($q->orderBy('sort_order')->latest('id')->paginate(min(max($request->integer('per_page',20),1),60)));
    }

    public function product(Request $request,string $slug,Product $product)
    {
        $business=$this->business($slug);abort_unless($product->business_id===$business->id&&$product->is_active&&$product->in_stock,404);
        ActivityTracker::record($request,$business,'product_view',$product,visitorToken:$request->input('visitor_token'),source:'app');
        return response()->json(['product'=>$product->fresh()->load(['category:id,name','images'])]);
    }

    public function inquiry(Request $request,string $slug)
    {
        $business=$this->business($slug);$data=$request->validate(['product_id'=>['nullable','integer'],'customer_name'=>['required','string','max:120'],'phone'=>['required','string','max:20'],'email'=>['nullable','email','max:150'],'message'=>['nullable','string','max:2000'],'source'=>['nullable','string','max:30'],'visitor_token'=>['nullable','uuid']]);
        $product=null;if(!empty($data['product_id'])){$product=$business->products()->whereKey($data['product_id'])->where('is_active',true)->first();if(!$product)return response()->json(['message'=>'Invalid product.'],422);}
        $inquiryData=$data; unset($inquiryData['visitor_token']);
        $inquiry=$business->inquiries()->create([...$inquiryData,'status'=>'pending','source'=>$data['source']??'showroom']);
        ActivityTracker::record($request,$business,'inquiry',$product,['inquiry_id'=>$inquiry->id],$data['visitor_token']??null,$data['customer_name'],$data['phone'],$data['email']??null,$data['source']??'showroom');
        return response()->json(['message'=>'Enquiry sent successfully.','inquiry'=>$inquiry->load('product:id,name')],201);
    }
}
