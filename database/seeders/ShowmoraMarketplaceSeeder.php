<?php
namespace Database\Seeders;
use App\Models\MarketplaceCategory;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
class ShowmoraMarketplaceSeeder extends Seeder
{
    public function run(): void
    {
        $cats=['Jewellery & Gold/Silver'=>'diamond','Readymade Garments'=>'checkroom','Saree & Ladies Wear'=>'woman','Men’s Wear'=>'man','Kids Wear'=>'child_care','Footwear'=>'steps','Mobile & Accessories'=>'smartphone','Electronics'=>'devices','Computer & Laptop'=>'laptop','Furniture'=>'chair','Home Appliances'=>'kitchen','Cosmetics & Beauty'=>'spa','Gift Shop'=>'redeem','Grocery / Kirana'=>'local_grocery_store','Bakery & Sweets'=>'cake','Medical / Pharmacy'=>'local_pharmacy','Hardware'=>'hardware','Sanitary & Tiles'=>'bathroom','Electrical'=>'electrical_services','Solar Products'=>'solar_power','Automobile Parts'=>'directions_car','Bike Accessories'=>'two_wheeler','Agriculture Equipment'=>'agriculture','Seeds & Fertilizer'=>'grass','Optical & Eyewear'=>'visibility','Watches'=>'watch','Bags & Luggage'=>'luggage','Stationery'=>'edit','Sports'=>'sports_cricket','Home Decor'=>'home','Crockery'=>'dinner_dining','Toys'=>'toys','Pet Shop'=>'pets','Other Local Business'=>'storefront'];
        $i=0;foreach($cats as $name=>$icon){MarketplaceCategory::firstOrCreate(['slug'=>Str::slug($name)],['name'=>$name,'icon'=>$icon,'sort_order'=>$i++,'is_active'=>true]);}
    }
}
