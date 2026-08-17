<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use App\Services\ActivityTracker;
use App\Services\BusinessContext;
use Illuminate\Http\Request;

class TrackingController extends Controller
{
    public function publicEvent(Request $request, ?string $slug = null)
    {
        $data = $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'business_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'visitor_uuid' => ['nullable', 'string', 'max:64'],
            'visitor_token' => ['nullable', 'string', 'max:64'],
            'customer_name' => ['nullable', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:150'],
            'source' => ['nullable', 'string', 'max:40'],
            'platform' => ['nullable', 'string', 'max:40'],
            'share_code' => ['nullable', 'string', 'max:32'],
            'ref' => ['nullable', 'string', 'max:32'],
            'metadata' => ['nullable', 'array'],
        ]);

        $business = null;
        if ($slug) {
            $business = Business::where('slug', $slug)->first();
        } elseif (!empty($data['business_id'])) {
            $business = Business::find($data['business_id']);
        }

        $product = null;
        if (!empty($data['product_id'])) {
            $product = Product::find($data['product_id']);
            if ($product && !$business) {
                $business = $product->business;
            }
        }

        $event = ActivityTracker::record(
            $request,
            $business,
            $data['event_type'],
            $product,
            $data['metadata'] ?? [],
            $data['visitor_uuid'] ?? $data['visitor_token'] ?? null,
            $data['customer_name'] ?? null,
            $data['customer_phone'] ?? null,
            $data['customer_email'] ?? null,
            $data['source'] ?? 'showmora_app',
            $data['platform'] ?? 'app',
            $data['share_code'] ?? $data['ref'] ?? null
        );

        return response()->json([
            'success' => true,
            'event_id' => $event->id,
            'visitor_uuid' => $event->visitor_uuid,
        ], 201);
    }

    public function linkVisitor(Request $request)
    {
        $data = $request->validate([
            'visitor_uuid' => ['required', 'string', 'max:64'],
        ]);

        $user = $request->user();
        if ($user) {
            ActivityTracker::linkVisitorToUser($data['visitor_uuid'], $user->id);
        }

        return response()->json([
            'success' => true,
            'message' => 'Visitor session linked.',
        ]);
    }

    public function ownerEvent(Request $request)
    {
        $data = $request->validate([
            'event_type' => ['required', 'string', 'max:50'],
            'product_id' => ['nullable', 'integer'],
            'metadata' => ['nullable', 'array'],
        ]);
        $business = BusinessContext::require($request);
        $product = !empty($data['product_id']) ? Product::where('business_id', $business->id)->findOrFail($data['product_id']) : null;
        ActivityTracker::record($request, $business, $data['event_type'], $product, $data['metadata'] ?? [], source: 'owner_app');
        return response()->json(['success' => true]);
    }
}
