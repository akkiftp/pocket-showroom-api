<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessContext;
use App\Models\Business;
use App\Models\Product;
use App\Services\ActivityTracker;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    private function business(string $slug): Business
    {
        return Business::where('slug', $slug)->where('is_active', true)->firstOrFail();
    }

    public function publicEvent(Request $request, string $slug)
    {
        $data = $request->validate([
            'event_type' => ['required','string','max:50'],
            'product_id' => ['nullable','integer'],
            'visitor_token' => ['nullable','uuid'],
            'customer_name' => ['nullable','string','max:120'],
            'customer_phone' => ['nullable','string','max:30'],
            'customer_email' => ['nullable','email','max:150'],
            'source' => ['nullable','string','max:40'],
            'referrer' => ['nullable','string','max:1000'],
            'metadata' => ['nullable','array'],
        ]);

        $business = $this->business($slug);
        $product = null;
        if (!empty($data['product_id'])) {
            $product = Product::where('business_id', $business->id)->findOrFail($data['product_id']);
        }

        $event = ActivityTracker::record(
            $request,
            $business,
            $data['event_type'],
            $product,
            $data['metadata'] ?? [],
            $data['visitor_token'] ?? null,
            $data['customer_name'] ?? null,
            $data['customer_phone'] ?? null,
            $data['customer_email'] ?? null,
            $data['source'] ?? 'web'
        );

        return response()->json([
            'success' => true,
            'visitor_token' => $event->visitor?->visitor_token,
        ], 201);
    }

    public function ownerEvent(Request $request)
    {
        $data = $request->validate([
            'event_type' => ['required','in:owner_share,share,qr_open'],
            'product_id' => ['nullable','integer'],
            'metadata' => ['nullable','array'],
        ]);
        $business = BusinessContext::require($request);
        $product = !empty($data['product_id']) ? Product::where('business_id',$business->id)->findOrFail($data['product_id']) : null;
        ActivityTracker::record($request,$business,$data['event_type'],$product,$data['metadata'] ?? [], source:'owner_app');
        return response()->json(['success'=>true]);
    }
}
