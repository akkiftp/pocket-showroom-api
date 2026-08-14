<?php

namespace App\Services;

use App\Models\ActivityEvent;
use App\Models\Business;
use App\Models\CustomerContact;
use App\Models\Product;
use App\Models\VisitorSession;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ActivityTracker
{
    public const TYPES = [
        'showroom_view','product_view','share','owner_share','favorite','add_to_cart',
        'buy_now','whatsapp_click','inquiry','order','qr_open','search','category_view'
    ];

    public static function record(
        Request $request,
        Business $business,
        string $eventType,
        ?Product $product = null,
        array $metadata = [],
        ?string $visitorToken = null,
        ?string $customerName = null,
        ?string $phone = null,
        ?string $email = null,
        string $source = 'web'
    ): ActivityEvent {
        abort_unless(in_array($eventType, self::TYPES, true), 422, 'Unsupported activity event.');

        if ($product && $product->business_id !== $business->id) {
            abort(422, 'Product does not belong to this showroom.');
        }

        $visitorToken = trim((string) ($visitorToken ?: $request->input('visitor_token')));
        if ($visitorToken === '' || !Str::isUuid($visitorToken)) {
            $visitorToken = (string) Str::uuid();
        }

        $phone = preg_replace('/\D+/', '', (string) ($phone ?: $request->input('customer_phone')));
        if ($phone === '') $phone = null;
        $email = strtolower(trim((string) ($email ?: $request->input('customer_email'))));
        if ($email === '') $email = null;
        $customerName = trim((string) ($customerName ?: $request->input('customer_name')));
        if ($customerName === '') $customerName = null;

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

        if ($phone) {
            CustomerContact::updateOrCreate(
                ['business_id' => $business->id, 'phone' => $phone],
                [
                    'name' => $customerName ?: 'Showroom customer',
                    'email' => $email,
                    'last_activity_at' => $now,
                ]
            );
        }

        $event = ActivityEvent::create([
            'business_id' => $business->id,
            'visitor_session_id' => $visitor->id,
            'product_id' => $product?->id,
            'event_type' => $eventType,
            'source' => $source,
            'metadata' => $metadata ?: null,
        ]);

        if ($eventType === 'product_view' && $product) {
            $product->increment('views_count');
        }

        return $event->load(['visitor:id,visitor_token,customer_name,phone,email,last_seen_at','product:id,name']);
    }
}
