<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use App\Models\ShareLink;
use App\Services\ActivityTracker;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ShareController extends Controller
{
    public function create(Request $request)
    {
        $data = $request->validate([
            'business_id' => ['nullable', 'integer'],
            'product_id' => ['nullable', 'integer'],
            'platform' => ['nullable', 'string', 'max:40'],
            'source' => ['nullable', 'string', 'max:40'],
        ]);

        $businessId = $data['business_id'] ?? null;
        $product = null;
        if (!empty($data['product_id'])) {
            $product = Product::findOrFail($data['product_id']);
            $businessId = $product->business_id;
        }

        if (!$businessId) {
            $user = $request->user();
            if ($user && $user->business) {
                $businessId = $user->business->id;
            } else {
                return response()->json(['message' => 'Business ID required.'], 422);
            }
        }

        $business = Business::findOrFail($businessId);
        $code = strtoupper(Str::random(6));
        while (ShareLink::where('code', $code)->exists()) {
            $code = strtoupper(Str::random(6));
        }

        $share = ShareLink::create([
            'code' => $code,
            'user_id' => $request->user()?->id,
            'business_id' => $business->id,
            'product_id' => $product?->id,
            'platform' => $data['platform'] ?? 'whatsapp',
            'source' => $data['source'] ?? 'owner_share',
            'click_count' => 0,
        ]);

        $baseUrl = rtrim(config('app.url') ?? 'https://pocket-showroom-api.onrender.com', '/');
        $shareUrl = "$baseUrl/s/$code";
        $directShowroomUrl = $business->showroom_url . "?ref=$code";

        // Record the share action itself
        ActivityTracker::record(
            $request,
            $business,
            $product ? 'product_share' : 'shop_share',
            $product,
            ['share_code' => $code, 'platform' => $share->platform],
            source: $share->source,
            platform: $share->platform,
            shareCode: $code
        );

        return response()->json([
            'success' => true,
            'share_code' => $code,
            'share_url' => $shareUrl,
            'direct_url' => $directShowroomUrl,
            'share' => $share,
        ], 201);
    }

    public function resolve(Request $request, string $code)
    {
        $share = ShareLink::where('code', $code)->with(['business', 'product'])->firstOrFail();
        $share->increment('click_count');

        ActivityTracker::record(
            $request,
            $share->business,
            'share_visit',
            $share->product,
            ['share_code' => $code, 'platform' => $share->platform],
            source: 'share_link',
            platform: 'web',
            shareCode: $code
        );

        $targetUrl = $share->business->showroom_url . "?ref=$code";
        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'target_url' => $targetUrl,
                'business' => $share->business,
                'product' => $share->product,
                'share' => $share,
            ]);
        }

        return redirect()->away($targetUrl);
    }
}
