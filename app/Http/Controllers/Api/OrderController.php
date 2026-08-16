<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessContext;
use App\Models\Business;
use App\Models\Order;
use App\Models\Product;
use App\Models\VisitorSession;
use App\Services\ActivityTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    private function business(Request $request): Business { return BusinessContext::require($request); }

    public function index(Request $request)
    {
        $business=$this->business($request);
        return response()->json($business->orders()->with('items')->latest()->paginate(min(max($request->integer('per_page',30),1),100)));
    }

    public function status(Request $request, Order $order)
    {
        $business=$this->business($request); abort_unless($order->business_id===$business->id,404);
        $data=$request->validate(['status'=>['required','in:new,confirmed,packed,processing,completed,cancelled']]);
        $order->update($data); return response()->json(['order'=>$order->fresh()->load('items')]);
    }

    public function publicStore(Request $request, string $slug)
    {
        $business=Business::where('slug',$slug)->where('is_active',true)->firstOrFail();
        $data=$request->validate([
            'customer_name'=>['required','string','max:120'],'phone'=>['required','string','max:30'],'address'=>['nullable','string','max:2000'],
            'visitor_token'=>['nullable','uuid'],'items'=>['required','array','min:1','max:50'],'items.*.product_id'=>['required','integer'],'items.*.qty'=>['required','integer','min:1','max:99'],
        ]);
        return DB::transaction(function() use($request,$business,$data){
            $visitor=null;
            if (!empty($data['visitor_token'])) $visitor=VisitorSession::where('business_id',$business->id)->where('visitor_token',$data['visitor_token'])->first();
            $total=0; $prepared=[];
            foreach($data['items'] as $row){
                $product=Product::where('business_id',$business->id)->where('is_active',true)->findOrFail($row['product_id']);
                $price=(float)($product->offer_price ?: $product->price); $qty=(int)$row['qty'];
                $prepared[]=[$product,$price,$qty]; $total += $price*$qty;
            }
            $order=$business->orders()->create(['visitor_session_id'=>$visitor?->id,'customer_name'=>$data['customer_name'],'phone'=>preg_replace('/\D+/','',$data['phone']),'address'=>$data['address']??null,'total'=>$total,'status'=>'new','source'=>'showroom']);
            foreach($prepared as [$product,$price,$qty]) $order->items()->create(['product_id'=>$product->id,'product_name'=>$product->name,'price'=>$price,'qty'=>$qty,'line_total'=>$price*$qty]);
            ActivityTracker::record($request,$business,'order',null,['order_id'=>$order->id,'total'=>$total],$data['visitor_token']??null,$data['customer_name'],$data['phone'],null,'showroom');
            return response()->json(['message'=>'Order created.','order'=>$order->load('items')],201);
        });
    }
}
