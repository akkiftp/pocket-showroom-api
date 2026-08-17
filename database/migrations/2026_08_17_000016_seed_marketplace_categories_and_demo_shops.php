<?php

use App\Models\Business;
use App\Models\Category;
use App\Models\MarketplaceCategory;
use App\Models\MarketplaceLocation;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Seed standard locations
        $locFatehpur = MarketplaceLocation::firstOrCreate(
            ['name' => 'Fatehpur', 'district' => 'Fatehpur', 'state' => 'Uttar Pradesh'],
            ['type' => 'city', 'pincode' => '212601', 'is_active' => true, 'is_popular' => true]
        );
        $locKanpur = MarketplaceLocation::firstOrCreate(
            ['name' => 'Kanpur', 'district' => 'Kanpur Nagar', 'state' => 'Uttar Pradesh'],
            ['type' => 'city', 'pincode' => '208001', 'is_active' => true, 'is_popular' => true]
        );

        // 2. Seed standard marketplace categories
        $categoriesData = [
            ['name' => 'Grocery / Kirana', 'slug' => 'grocery-kirana', 'icon' => 'shopping_basket', 'sort_order' => 1],
            ['name' => 'Medical / Pharmacy', 'slug' => 'medical-pharmacy', 'icon' => 'medical_services', 'sort_order' => 2],
            ['name' => 'Hardware', 'slug' => 'hardware', 'icon' => 'hardware', 'sort_order' => 3],
            ['name' => 'Agriculture', 'slug' => 'agriculture', 'icon' => 'agriculture', 'sort_order' => 4],
            ['name' => 'Jewelry / Gold', 'slug' => 'jewelry-gold', 'icon' => 'diamond', 'sort_order' => 5],
            ['name' => 'Fashion / Clothing', 'slug' => 'fashion-clothing', 'icon' => 'checkroom', 'sort_order' => 6],
            ['name' => 'Electronics & Mobile', 'slug' => 'electronics-mobile', 'icon' => 'devices', 'sort_order' => 7],
            ['name' => 'Auto & Bikes', 'slug' => 'auto-bikes', 'icon' => 'two_wheeler', 'sort_order' => 8],
            ['name' => 'Restaurants / Food', 'slug' => 'restaurants-food', 'icon' => 'restaurant', 'sort_order' => 9],
            ['name' => 'Services / Others', 'slug' => 'services-others', 'icon' => 'build', 'sort_order' => 10],
        ];

        $catMap = [];
        foreach ($categoriesData as $c) {
            $model = MarketplaceCategory::firstOrCreate(
                ['slug' => $c['slug']],
                ['name' => $c['name'], 'icon' => $c['icon'], 'sort_order' => $c['sort_order'], 'is_active' => true]
            );
            $catMap[$c['slug']] = $model;
        }

        // 3. Map existing businesses to category & location
        $hardwareCat = $catMap['hardware'] ?? null;
        if ($hardwareCat) {
            Business::where('name', 'like', '%hardware%')
                ->orWhere('business_type', 'like', '%hardware%')
                ->update(['marketplace_category_id' => $hardwareCat->id, 'location_id' => $locKanpur->id]);
        }

        $electronicsCat = $catMap['electronics-mobile'] ?? null;
        if ($electronicsCat) {
            Business::where('name', 'like', '%technology%')
                ->orWhere('name', 'like', '%electronics%')
                ->update(['marketplace_category_id' => $electronicsCat->id, 'location_id' => $locFatehpur->id]);
        }

        $fashionCat = $catMap['fashion-clothing'] ?? null;
        if ($fashionCat) {
            Business::where('name', 'like', '%akki%')
                ->whereNull('marketplace_category_id')
                ->update(['marketplace_category_id' => $fashionCat->id, 'location_id' => $locFatehpur->id]);
        }

        // 4. Create 1 live verified demo shop for each category
        $demoShops = [
            [
                'cat_slug' => 'grocery-kirana',
                'name' => 'Shree Ganesh Super Kirana',
                'slug' => 'shree-ganesh-kirana',
                'type' => 'Grocery / Kirana',
                'city' => 'Fatehpur',
                'locality' => 'Chowk Bazar',
                'address' => 'Shop #12, Chowk Bazar, Main Market, Fatehpur',
                'pincode' => '212601',
                'phone' => '9838012341',
                'whatsapp' => '9838012341',
                'about' => 'All daily household groceries, pure mustard oils, premium pulses, spices and daily essentials at wholesale prices.',
                'products' => [
                    ['name' => 'Fortune Kachi Ghani Mustard Oil (1L)', 'price' => 165, 'offer_price' => 145, 'desc' => '100% pure cold pressed mustard oil for healthy everyday cooking.'],
                    ['name' => 'Aashirvaad Shudh Chakki Whole Wheat Atta (10kg)', 'price' => 450, 'offer_price' => 399, 'desc' => 'Rich in dietary fiber and vitamins with 0% maida.'],
                    ['name' => 'Tata Gold Premium Assam Tea (1kg)', 'price' => 520, 'offer_price' => 480, 'desc' => 'Exquisite blend of 15% gently rolled long tea leaves.'],
                ]
            ],
            [
                'cat_slug' => 'medical-pharmacy',
                'name' => 'Sanjivani Medical & Health Store',
                'slug' => 'sanjivani-medical',
                'type' => 'Medical / Pharmacy',
                'city' => 'Fatehpur',
                'locality' => 'Hospital Road',
                'address' => 'Opposite District Hospital Gate, Hospital Road, Fatehpur',
                'pincode' => '212601',
                'phone' => '9838012342',
                'whatsapp' => '9838012342',
                'about' => 'All branded genuine medicines, digital medical equipments, orthopedic supports and baby healthcare products available 24x7.',
                'products' => [
                    ['name' => 'Omron HEM 7120 Automatic Digital BP Monitor', 'price' => 2400, 'offer_price' => 1850, 'desc' => 'High precision Japanese sensor with IntelliSense technology.'],
                    ['name' => 'Dr. Morepen BG-03 Blood Glucose Monitor Kit', 'price' => 1100, 'offer_price' => 799, 'desc' => 'Comes with 25 test strips and fast 5-second result.'],
                    ['name' => 'Comprehensive Home First Aid Emergency Kit', 'price' => 650, 'offer_price' => 499, 'desc' => 'Sterile bandages, antiseptic creams, burn relief and thermometer.'],
                ]
            ],
            [
                'cat_slug' => 'agriculture',
                'name' => 'Kisan Seva Kendra & Agro Fertilizers',
                'slug' => 'kisan-seva-kendra',
                'type' => 'Agriculture',
                'city' => 'Fatehpur',
                'locality' => 'Kisan Mandi',
                'address' => 'Shop #4, Grain Mandi Gate, GT Road, Fatehpur',
                'pincode' => '212601',
                'phone' => '9838012343',
                'whatsapp' => '9838012343',
                'about' => 'Certified high-yield hybrid crop seeds, organic bio fertilizers, pesticides, sprayers and modern farming equipments.',
                'products' => [
                    ['name' => 'High-Yield Hybrid Wheat Seeds (40kg Bag)', 'price' => 1700, 'offer_price' => 1450, 'desc' => 'Disease resistant high production certified grain seeds.'],
                    ['name' => '16L 12V 12Ah Dual Motor Battery Knapsack Sprayer', 'price' => 3500, 'offer_price' => 2800, 'desc' => 'Heavy duty battery operated continuous farm spray pump with brass lance.'],
                    ['name' => 'Organic Neem Cake Bio Fertilizer (50kg)', 'price' => 1100, 'offer_price' => 850, 'desc' => 'Enhances soil fertility and protects roots from nematodes.'],
                ]
            ],
            [
                'cat_slug' => 'jewelry-gold',
                'name' => 'Kalyan Jewellers & Gold Showroom',
                'slug' => 'kalyan-jewellers-fatehpur',
                'type' => 'Jewelry / Gold',
                'city' => 'Fatehpur',
                'locality' => 'Sarafa Bazar',
                'address' => 'Sarafa Bazar, Main Market, Fatehpur',
                'pincode' => '212601',
                'phone' => '9838012344',
                'whatsapp' => '9838012344',
                'about' => '100% BIS Hallmarked 22K/18K Gold Jewellery, certified diamond ornaments and pure 925 sterling silver articles.',
                'products' => [
                    ['name' => '22K BIS Hallmarked Traditional Royal Necklace Set', 'price' => 165000, 'offer_price' => 145000, 'desc' => 'Exquisite handcrafted bridal jewellery set with matching earrings.'],
                    ['name' => '925 Pure Sterling Silver Antique Bridal Payal (Pair)', 'price' => 4500, 'offer_price' => 3800, 'desc' => 'Handmade fine silver anklets with high shine polish.'],
                    ['name' => 'Certified Real Diamond Solitaire Engagement Ring', 'price' => 35000, 'offer_price' => 28500, 'desc' => 'VVS-EF clarity natural diamond in 18K yellow gold prong setting.'],
                ]
            ],
            [
                'cat_slug' => 'auto-bikes',
                'name' => 'Sharma Hero & Honda Genuine Spare Parts',
                'slug' => 'sharma-auto-parts',
                'type' => 'Auto & Bikes',
                'city' => 'Kanpur',
                'locality' => 'Transport Nagar',
                'address' => 'Plot 45, Transport Nagar Main Road, Kanpur',
                'pincode' => '208001',
                'phone' => '9838012345',
                'whatsapp' => '9838012345',
                'about' => 'Genuine OEM spare parts, tubeless tyres, high performance lubricants and battery replacements for all 2-wheelers and 4-wheelers.',
                'products' => [
                    ['name' => 'Castrol Power1 4T 10W-30 Synthetic Engine Oil (1L)', 'price' => 490, 'offer_price' => 420, 'desc' => 'Engineered with Power Release Technology for optimal bike acceleration.'],
                    ['name' => 'MRF Nylogrip Zapper Front & Rear Tubeless Tyre Set', 'price' => 3100, 'offer_price' => 2650, 'desc' => 'Superior wet and dry grip with high tread longevity.'],
                    ['name' => 'Universal High-Beam LED Projector Fog Lights (Pair)', 'price' => 1600, 'offer_price' => 1200, 'desc' => 'Waterproof 6000K pure white auxiliary driving lights.'],
                ]
            ],
            [
                'cat_slug' => 'restaurants-food',
                'name' => 'Royal Treat Sweets & Bakers',
                'slug' => 'royal-treat-sweets',
                'type' => 'Restaurants / Food',
                'city' => 'Fatehpur',
                'locality' => 'Station Road',
                'address' => 'Near Railway Station Circle, Station Road, Fatehpur',
                'pincode' => '212601',
                'phone' => '9838012346',
                'whatsapp' => '9838012346',
                'about' => 'Pure Desi Ghee traditional Indian sweets, fresh baked celebration cakes, savouries and customized festival gift hampers.',
                'products' => [
                    ['name' => 'Pure Desi Ghee Kaju Katli Box (1kg)', 'price' => 1100, 'offer_price' => 950, 'desc' => 'Melt-in-mouth premium cashew fudge with genuine silver vark.'],
                    ['name' => 'Signature Rasgulla Tin Box (1kg / 16 Pcs)', 'price' => 320, 'offer_price' => 280, 'desc' => 'Soft, spongy cottage cheese dumplings soaked in aromatic saffron syrup.'],
                    ['name' => 'Celebration Deluxe Dry Fruit Gift Pack', 'price' => 1450, 'offer_price' => 1200, 'desc' => 'Almonds, Cashews, Pistachios and Kishmish in premium packaging.'],
                ]
            ],
        ];

        foreach ($demoShops as $shop) {
            $cat = $catMap[$shop['cat_slug']] ?? null;
            if (!$cat) continue;

            $existing = Business::where('slug', $shop['slug'])->first();
            if (!$existing) {
                $user = User::firstOrCreate(
                    ['phone' => $shop['phone']],
                    [
                        'name' => $shop['name'] . ' Owner',
                        'role' => User::ROLE_SHOP_OWNER,
                        'is_active' => true,
                    ]
                );

                $business = Business::create([
                    'user_id' => $user->id,
                    'name' => $shop['name'],
                    'slug' => $shop['slug'],
                    'business_type' => $shop['type'],
                    'marketplace_category_id' => $cat->id,
                    'location_id' => $locFatehpur->id,
                    'city' => $shop['city'],
                    'locality' => $shop['locality'],
                    'address' => $shop['address'],
                    'pincode' => $shop['pincode'],
                    'phone' => $shop['phone'],
                    'whatsapp' => $shop['whatsapp'],
                    'about' => $shop['about'],
                    'is_active' => true,
                    'is_verified' => true,
                    'is_featured' => true,
                    'marketplace_views' => rand(15, 60),
                ]);

                foreach ($shop['products'] as $prod) {
                    $slug = Str::slug($prod['name']) . '-' . rand(100, 999);
                    $product = Product::create([
                        'business_id' => $business->id,
                        'name' => $prod['name'],
                        'slug' => $slug,
                        'price' => $prod['price'],
                        'offer_price' => $prod['offer_price'],
                        'description' => $prod['desc'],
                        'in_stock' => true,
                        'featured' => true,
                        'is_active' => true,
                    ]);
                }
            } else {
                $existing->update([
                    'marketplace_category_id' => $cat->id,
                    'is_active' => true,
                    'is_verified' => true,
                ]);
            }
        }
    }

    public function down(): void
    {
    }
};
