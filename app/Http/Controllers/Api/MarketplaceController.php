<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class MarketplaceController extends Controller
{
    public function home(Request $request)
    {
        $categories = [
            ['id' => 1, 'name' => 'Mobiles & Tablets', 'icon' => 'smartphone'],
            ['id' => 2, 'name' => 'Electronics & Laptops', 'icon' => 'devices'],
            ['id' => 3, 'name' => 'Fashion & Clothes', 'icon' => 'spa'],
            ['id' => 4, 'name' => 'Jewellery & Watches', 'icon' => 'watch'],
            ['id' => 5, 'name' => 'Furniture & Home', 'icon' => 'chair'],
            ['id' => 6, 'name' => 'Bakery & Sweets', 'icon' => 'cake'],
            ['id' => 7, 'name' => 'Pharmacy & Health', 'icon' => 'local_pharmacy'],
            ['id' => 8, 'name' => 'Hardware & Tools', 'icon' => 'hardware'],
            ['id' => 9, 'name' => 'Solar & Electric', 'icon' => 'solar_power'],
            ['id' => 10, 'name' => 'Agriculture & Seeds', 'icon' => 'agriculture'],
            ['id' => 11, 'name' => 'Pets & Vet', 'icon' => 'pets'],
        ];

        $cities = Business::where('is_active', true)
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->distinct()
            ->pluck('city')
            ->map(function ($city, $index) {
                return ['id' => $index + 1, 'name' => $city];
            })
            ->values();

        return response()->json([
            'categories' => $categories,
            'locations' => $cities,
        ]);
    }

    public function shops(Request $request)
    {
        $query = Business::where('is_active', true)->withCount([
            'products' => function ($q) {
                $q->where('is_active', true);
            }
        ]);

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . trim($request->city) . '%');
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('business_type', 'like', "%{$search}%")
                  ->orWhere('city', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('products', function ($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%")
                         ->orWhere('description', 'like', "%{$search}%");
                  });
            });
        }

        $shops = $query->latest('id')->paginate(min(max((int)$request->input('per_page', 30), 1), 60));

        $shopsData = collect($shops->items())->map(function ($b) {
            return [
                'id' => $b->id,
                'name' => $b->name,
                'slug' => $b->slug,
                'business_type' => $b->business_type ?? 'Local Shop',
                'locality' => $b->address,
                'city' => $b->city,
                'is_verified' => true,
                'products_count' => $b->products_count,
                'logo_url' => $b->logo_url,
                'marketplace_category' => [
                    'name' => $b->business_type ?? 'Retail',
                ],
            ];
        });

        return response()->json([
            'data' => $shopsData,
            'current_page' => $shops->currentPage(),
            'last_page' => $shops->lastPage(),
            'total' => $shops->total(),
        ]);
    }
}
