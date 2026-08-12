<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\Product;
use App\Http\Controllers\Api\PublicShowroomController;
use Illuminate\Http\Request;

class WebShowroomController extends Controller
{
    public function show(Request $request, string $slug)
    {
        $business = Business::where('slug', $slug)
            ->where('is_active', true)
            ->first();

        if (!$business) {
            return response()->view('showroom_not_found', ['slug' => $slug], 404);
        }

        $categories = $business->categories()
            ->where('is_active', true)
            ->withCount(['products' => fn ($q) => $q->where('is_active', true)->where('in_stock', true)])
            ->get();

        $products = $business->products()
            ->where('is_active', true)
            ->where('in_stock', true)
            ->with(['category:id,name', 'images'])
            ->orderBy('sort_order')
            ->latest('id')
            ->get();

        return view('showroom', [
            'business' => $business,
            'categories' => $categories,
            'products' => $products,
        ]);
    }

    public function showOrApi(Request $request, string $slug)
    {
        if ($request->wantsJson() || ($request->is('api/*') && !str_contains($request->header('Accept', ''), 'text/html'))) {
            return app(PublicShowroomController::class)->show($slug);
        }

        return $this->show($request, $slug);
    }
}
