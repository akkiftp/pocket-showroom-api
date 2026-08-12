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
        try {
            $business = Business::where('slug', $slug)->first();

            if (!$business) {
                return response()->view('showroom_not_found', ['slug' => $slug], 404);
            }

            $categories = $business->categories()->get();

            $products = $business->products()
                ->with(['category:id,name', 'images'])
                ->latest('id')
                ->get();

            return view('showroom', [
                'business' => $business,
                'categories' => $categories,
                'products' => $products,
            ]);
        } catch (\Throwable $e) {
            return response()->view('showroom_not_found', ['slug' => $slug], 404);
        }
    }

    public function showOrApi(Request $request, string $slug)
    {
        if ($request->wantsJson() || ($request->is('api/*') && !str_contains($request->header('Accept', ''), 'text/html'))) {
            return app(PublicShowroomController::class)->show($slug);
        }

        return $this->show($request, $slug);
    }
}
