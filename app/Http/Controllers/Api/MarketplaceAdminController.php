<?php
namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceLocation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class MarketplaceAdminController extends Controller
{
    public function categories(){return response()->json(['data'=>MarketplaceCategory::with('children')->orderBy('sort_order')->orderBy('name')->get()]);}
    public function storeCategory(Request $r){$d=$r->validate(['name'=>'required|string|max:100','parent_id'=>'nullable|exists:marketplace_categories,id','icon'=>'nullable|string|max:50','sort_order'=>'nullable|integer|min:0','is_active'=>'nullable|boolean']);$d['slug']=$this->uniqueSlug(MarketplaceCategory::class,$d['name']);$m=MarketplaceCategory::create($d);return response()->json(['message'=>'Marketplace category created.','data'=>$m],201);}
    public function updateCategory(Request $r,MarketplaceCategory $category){$d=$r->validate(['name'=>'sometimes|required|string|max:100','parent_id'=>['nullable','exists:marketplace_categories,id',Rule::notIn([$category->id])],'icon'=>'nullable|string|max:50','sort_order'=>'nullable|integer|min:0','is_active'=>'nullable|boolean']);if(isset($d['name'])&&$d['name']!==$category->name)$d['slug']=$this->uniqueSlug(MarketplaceCategory::class,$d['name'],$category->id);$category->update($d);return response()->json(['message'=>'Marketplace category updated.','data'=>$category->fresh()]);}
    public function destroyCategory(MarketplaceCategory $category){abort_if($category->businesses()->exists()||$category->children()->exists(),422,'Category is in use. Move shops/subcategories first.');$category->delete();return response()->json(['message'=>'Marketplace category deleted.']);}
    public function locations(){return response()->json(['data'=>MarketplaceLocation::withCount('businesses')->orderBy('name')->get()]);}
    public function storeLocation(Request $r){$d=$r->validate(['name'=>'required|string|max:100','type'=>['required',Rule::in(['city','town','village'])],'district'=>'nullable|string|max:100','state'=>'nullable|string|max:100','pincode'=>'nullable|string|max:12','latitude'=>'nullable|numeric|between:-90,90','longitude'=>'nullable|numeric|between:-180,180','is_active'=>'nullable|boolean']);$d['slug']=$this->uniqueSlug(MarketplaceLocation::class,implode('-',array_filter([$d['name'],$d['district']??null,$d['state']??null])));$m=MarketplaceLocation::create($d);return response()->json(['message'=>'Location created.','data'=>$m],201);}
    public function updateLocation(Request $r,MarketplaceLocation $location){$d=$r->validate(['name'=>'sometimes|required|string|max:100','type'=>["sometimes",Rule::in(['city','town','village'])],'district'=>'nullable|string|max:100','state'=>'nullable|string|max:100','pincode'=>'nullable|string|max:12','latitude'=>'nullable|numeric|between:-90,90','longitude'=>'nullable|numeric|between:-180,180','is_active'=>'nullable|boolean']);$location->update($d);return response()->json(['message'=>'Location updated.','data'=>$location->fresh()]);}
    public function destroyLocation(MarketplaceLocation $location){abort_if($location->businesses()->exists(),422,'Location is in use by shops.');$location->delete();return response()->json(['message'=>'Location deleted.']);}
    public function updateShop(Request $r,Business $business){$d=$r->validate(['marketplace_category_id'=>'nullable|exists:marketplace_categories,id','location_id'=>'nullable|exists:marketplace_locations,id','is_verified'=>'nullable|boolean','is_featured'=>'nullable|boolean','is_active'=>'nullable|boolean']);$business->update($d);return response()->json(['message'=>'Shop marketplace settings updated.','business'=>$business->fresh(['marketplaceCategory','marketplaceLocation'])]);}
    private function uniqueSlug(string $model,string $name,?int $ignore=null):string{$base=Str::slug($name)?:'item';$slug=$base;$i=2;while($model::where('slug',$slug)->when($ignore,fn($q)=>$q->whereKeyNot($ignore))->exists())$slug=$base.'-'.$i++;return $slug;}
}
