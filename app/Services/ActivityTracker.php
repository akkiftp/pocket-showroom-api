<?php

namespace App\Services;

use App\Models\ActivityEvent;
use App\Models\Business;
use App\Models\CustomerContact;
use App\Models\Product;
use App\Models\ShareLink;
use App\Models\VisitorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityTracker
{
    public const TYPES = [
        'marketplace_view',
        'category_view',
        'shop_impression',
        'shop_view',
        'showroom_view',
        'product_view',
        'whatsapp_click',
        'phone_click',
        'shop_share',
        'product_share',
        'share_visit',
        'favourite_added',
        'favourite_removed',
        'enquiry_created',
        'inquiry',
        'order_created',
        'order',
        'search',
        'owner_share',
        'qr_open',
        'add_to_cart',
        'buy_now',
    ];

    public static function record(
        Request $request,
        ?Business $business = null,
        string $eventType = 'shop_view',
        ?Product $product = null,
        array $metadata = [],
        ?string $visitorToken = null,
        ?string $customerName = null,
        ?string $phone = null,
        ?string $email = null,
        string $source = 'direct',
        ?string $platform = null,
        ?string $shareCode = null
    ): ActivityEvent {
        $visitorToken = trim((string) ($visitorToken ?: $request->input('visitor_uuid') ?: $request->input('visitor_token')));
        if ($visitorToken === '') {
            $visitorToken = (string) Str::uuid();
        }

        $userId = auth('sanctum')->id() ?? auth()->id();
        $shareId = null;
        if ($shareCode) {
            $shareObj = ShareLink::where('code', $shareCode)->first();
            if ($shareObj) {
                $shareId = $shareObj->id;
                $shareObj->increment('click_count');
            }
        } elseif ($request->filled('ref')) {
            $shareObj = ShareLink::where('code', $request->string('ref')->toString())->first();
            if ($shareObj) {
                $shareId = $shareObj->id;
                $shareObj->increment('click_count');
            }
        }

        $phone = preg_replace('/\D+/', '', (string) ($phone ?: $request->input('customer_phone') ?: $request->input('phone')));
        if ($phone === '') $phone = null;
        $email = strtolower(trim((string) ($email ?: $request->input('customer_email') ?: $request->input('email'))));
        if ($email === '') $email = null;
        $customerName = trim((string) ($customerName ?: $request->input('customer_name') ?: $request->input('name')));
        if ($customerName === '') $customerName = null;

        $visitorSessionId = null;
        if ($business) {
            $now = now();
            $visitor = VisitorSession::firstOrNew([
                'business_id' => $business->id,
                'visitor_token' => $visitorToken,
            ]);
            if (!$visitor->exists) {
                $visitor->first_seen_at = $now;
                $visitor->source = $source;
            }
            if ($customerName) $visitor->customer_name = $customerName;
            if ($phone) $visitor->phone = $phone;
            if ($email) $visitor->email = $email;
            $visitor->last_seen_at = $now;
            $visitor->referrer = mb_substr((string) ($request->input('referrer') ?: $request->headers->get('referer')), 0, 1000) ?: $visitor->referrer;
            $visitor->user_agent = mb_substr((string) $request->userAgent(), 0, 2000);
            $visitor->ip_hash = $request->ip() ? hash('sha256', $request->ip().'|'.config('app.key')) : null;
            $visitor->events_count = (int) $visitor->events_count + 1;
            $visitor->save();
            $visitorSessionId = $visitor->id;

            if ($phone) {
                CustomerContact::updateOrCreate(
                    ['business_id' => $business->id, 'phone' => $phone],
                    [
                        'name' => $customerName ?: 'Customer',
                        'email' => $email,
                        'last_activity_at' => $now,
                    ]
                );
            }
        }

        $event = ActivityEvent::create([
            'business_id' => $business?->id,
            'user_id' => $userId,
            'visitor_uuid' => $visitorToken,
            'visitor_session_id' => $visitorSessionId,
            'product_id' => $product?->id,
            'share_id' => $shareId,
            'event_type' => $eventType,
            'source' => $source,
            'platform' => $platform ?: ($request->header('X-Platform') ?: 'app'),
            'metadata' => $metadata ?: null,
            'ip_address' => $request->ip(),
            'user_agent' => mb_substr((string)$request->userAgent(), 0, 500),
        ]);

        if ($eventType === 'product_view' && $product) {
            $product->increment('views_count');
        }
        if (in_array($eventType, ['shop_view', 'showroom_view'], true) && $business) {
            try {
                $business->increment('marketplace_views');
            } catch (\Throwable $e) {}
        }

        return $event;
    }

    public static function linkVisitorToUser(string $visitorUuid, int $userId): void
    {
        if (empty($visitorUuid)) return;
        ActivityEvent::where('visitor_uuid', $visitorUuid)
            ->whereNull('user_id')
            ->update(['user_id' => $userId]);
    }
}
