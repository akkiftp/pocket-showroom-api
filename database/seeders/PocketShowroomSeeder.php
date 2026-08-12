<?php

namespace Database\Seeders;

use App\Models\Business;
use App\Models\Category;
use App\Models\Inquiry;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PocketShowroomSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $user = User::firstOrCreate(
                ['phone' => '9999999999'],
                ['name' => 'Riya Jewels Owner']
            );

            $business = Business::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'name' => 'Riya Jewels',
                    'slug' => 'riya-jewels',
                    'business_type' => 'Jewellery',
                    'city' => 'Varanasi',
                    'whatsapp' => '9999999999',
                    'phone' => '9999999999',
                    'about' => 'Welcome to our official digital showroom. Explore our exclusive collection and place inquiries directly on WhatsApp.',
                ]
            );

            $categoryNames = ['Rings', 'Necklaces', 'Earrings', 'Bangles'];
            $categories = [];

            foreach ($categoryNames as $index => $name) {
                $category = Category::firstOrCreate(
                    ['business_id' => $business->id, 'slug' => strtolower($name)],
                    ['name' => $name, 'sort_order' => $index]
                );
                $categories[$name] = $category;
            }

            $products = [
                ['Classic Diamond Ring', 'Rings', 45999, 42999, true, true],
                ['Royal Gold Necklace', 'Necklaces', 89999, null, true, true],
                ['Pearl Drop Earrings', 'Earrings', 18999, null, true, false],
                ['Traditional Bangles', 'Bangles', 52999, 49999, true, false],
                ['Minimal Gold Ring', 'Rings', 21999, null, true, false],
                ['Bridal Necklace Set', 'Necklaces', 129999, null, false, false],
            ];

            foreach ($products as $index => [$name, $cat, $price, $offer, $stock, $featured]) {
                Product::firstOrCreate(
                    ['business_id' => $business->id, 'slug' => str($name)->slug()->toString()],
                    [
                        'category_id' => $categories[$cat]->id,
                        'name' => $name,
                        'price' => $price,
                        'offer_price' => $offer,
                        'description' => 'Exclusive handcrafted jewellery collection.',
                        'in_stock' => $stock,
                        'featured' => $featured,
                        'is_active' => true,
                        'sort_order' => $index,
                    ]
                );
            }

            $firstProduct = $business->products()->first();

            Inquiry::firstOrCreate(
                [
                    'business_id' => $business->id,
                    'phone' => '9876543210',
                    'customer_name' => 'Priya Sharma',
                ],
                [
                    'product_id' => $firstProduct?->id,
                    'message' => 'Please share final price and availability.',
                    'status' => 'pending',
                    'source' => 'showroom',
                ]
            );
        });
    }
}
