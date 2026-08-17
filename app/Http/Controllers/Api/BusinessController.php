<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\User;
use App\Services\BusinessContext;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
class BusinessController extends Controller
{
    private function business(Request $request): Business { return BusinessContext::require($request); }
    public function show(Request $request){ return response()->json(['business'=>$this->business($request)]); }
    public function store(Request $request)
    {
        abort_unless($request->user()->isShopOwner(),403,'Only a Shop Owner can create a showroom.');
        abort_if(Business::where('user_id',$request->user()->id)->exists(),422,'This owner already has a showroom.');
        $data=$request->validate([
            'name'=>['required','string','max:150'],'business_type'=>['nullable','string','max:100'],
            'marketplace_category_id'=>['nullable','integer','exists:marketplace_categories,id'],
            'city'=>['nullable','string','max:100'],'location_id'=>['nullable','integer','exists:marketplace_locations,id'],
            'locality'=>['nullable','string','max:150'],'pincode'=>['nullable','string','max:12'],
            'latitude'=>['nullable','numeric','between:-90,90'],'longitude'=>['nullable','numeric','between:-180,180'],
            'opening_time'=>['nullable','date_format:H:i'],'closing_time'=>['nullable','date_format:H:i'],
            'delivery_available'=>['nullable','boolean'],'accepts_orders'=>['nullable','boolean'],
            'address'=>['nullable','string','max:1000'],'whatsapp'=>['nullable','string','max:20'],'phone'=>['nullable','string','max:20'],
            'email'=>['nullable','email','max:190'],'about'=>['nullable','string','max:3000'],
        ]);
        $base=Str::slug($data['name']) ?: 'showroom'; $slug=$base; $i=2;
        while(Business::withTrashed()->where('slug',$slug)->exists()) $slug=$base.'-'.$i++;
        $business=Business::create($data+['user_id'=>$request->user()->id,'slug'=>$slug]);
        return response()->json(['message'=>'Showroom created.','business'=>$business],201);
    }
    public function update(Request $request)
    {
        abort_unless($request->user()->canDo('business.update'),403);
        $business=$this->business($request);
        $data=$request->validate([
            'name'=>['sometimes','required','string','max:150'],'business_type'=>['nullable','string','max:100'],
            'marketplace_category_id'=>['nullable','integer','exists:marketplace_categories,id'],
            'city'=>['nullable','string','max:100'],'location_id'=>['nullable','integer','exists:marketplace_locations,id'],
            'locality'=>['nullable','string','max:150'],'pincode'=>['nullable','string','max:12'],
            'latitude'=>['nullable','numeric','between:-90,90'],'longitude'=>['nullable','numeric','between:-180,180'],
            'opening_time'=>['nullable','date_format:H:i'],'closing_time'=>['nullable','date_format:H:i'],
            'delivery_available'=>['nullable','boolean'],'accepts_orders'=>['nullable','boolean'],
            'address'=>['nullable','string','max:1000'],'whatsapp'=>['nullable','string','max:20'],'phone'=>['nullable','string','max:20'],
            'email'=>['nullable','email','max:190'],'about'=>['nullable','string','max:3000'],'is_active'=>['sometimes','boolean'],
        ]);
        if(!$request->user()->isSuperAdmin()) unset($data['is_active']);
        $business->update($data); return response()->json(['message'=>'Business updated.','business'=>$business->fresh()]);
    }
    public function uploadLogo(Request $request){ return $this->upload($request,'logo','logo_path'); }
    public function uploadBanner(Request $request){ return $this->upload($request,'banner','banner_path'); }
    private function upload(Request $request,string $field,string $column){
        abort_unless($request->user()->canDo('business.update'),403); $business=$this->business($request);
        $request->validate([$field=>['required','image','max:8192']]);
        if($business->{$column} && !Str::startsWith($business->{$column},'http')) Storage::disk('public')->delete($business->{$column});
        $path=$request->file($field)->store('businesses/'.$business->id,'public'); $business->update([$column=>$path]);
        return response()->json(['message'=>ucfirst($field).' updated.','business'=>$business->fresh()]);
    }
    public function theme(Request $request){ abort_unless($request->user()->canDo('business.update'),403); $business=$this->business($request); $data=$request->validate(['theme_primary'=>['required','regex:/^#[0-9A-Fa-f]{6}$/'],'theme_secondary'=>['required','regex:/^#[0-9A-Fa-f]{6}$/']]); $business->update($data); return response()->json(['message'=>'Theme updated.','business'=>$business->fresh()]); }
}
