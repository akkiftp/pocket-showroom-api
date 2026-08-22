<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BusinessReel;
use App\Services\BusinessContext;
use App\Services\MediaStorage;
use Illuminate\Http\Request;

class ReelController extends Controller
{
    public function feed(Request $r)
    {
        $q = BusinessReel::query()
            ->where('is_active', true)
            ->whereHas('business', fn($x) => $x->where('is_active', true))
            ->with([
                'business:id,name,slug,logo_path,city,locality,latitude,longitude',
                'product:id,name,slug,price,offer_price',
                'service:id,name,price,price_type',
            ]);

        if ($r->filled('city')) {
            $q->whereHas('business', fn($x) => $x->where('city', 'like', '%' . trim($r->city) . '%'));
        }
        if ($r->filled('business_id')) $q->where('business_id', $r->integer('business_id'));

        return $q->orderByDesc('is_promoted')->latest('id')
            ->paginate(min(max($r->integer('per_page', 10), 1), 30));
    }

    public function view(BusinessReel $reel)
    {
        if ($reel->is_active) $reel->increment('views_count');
        return response()->noContent();
    }

    public function index(Request $r)
    {
        $b = BusinessContext::require($r);
        return $b->reels()->latest('id')->paginate(30);
    }

    public function store(Request $r, MediaStorage $media)
    {
        $b = BusinessContext::require($r);

        $d = $r->validate([
            'video' => 'nullable|required_without:video_url|file|mimetypes:video/mp4,video/quicktime,video/x-m4v,video/webm|max:102400',
            'video_url' => 'nullable|required_without:video|url|max:1000',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:8192',
            'thumbnail_url' => 'nullable|url|max:1000',
            'caption' => 'nullable|string|max:1000',
            'product_id' => 'nullable|integer|exists:products,id',
            'service_id' => 'nullable|integer|exists:services,id',
            'duration_seconds' => 'nullable|integer|min:1|max:600',
            'is_promoted' => 'nullable|boolean',
        ]);

        if (!empty($d['product_id'])) {
            abort_unless($b->products()->whereKey($d['product_id'])->exists(), 422, 'Product does not belong to this business.');
        }
        if (!empty($d['service_id'])) {
            abort_unless($b->services()->whereKey($d['service_id'])->exists(), 422, 'Service does not belong to this business.');
        }

        if ($r->hasFile('video')) {
            $d['video_url'] = $media->uploadVideo($r->file('video'), 'businesses/' . $b->id . '/reels');
        }
        if ($r->hasFile('thumbnail')) {
            $d['thumbnail_url'] = $media->uploadImage($r->file('thumbnail'), 'businesses/' . $b->id . '/reel-thumbnails');
        }

        unset($d['video'], $d['thumbnail']);
        $d['is_active'] = true;

        $reel = $b->reels()->create($d);
        return response()->json(['success' => true, 'reel' => $reel], 201);
    }

    public function destroy(Request $r, BusinessReel $reel, MediaStorage $media)
    {
        $b = BusinessContext::require($r);
        abort_unless($reel->business_id === $b->id, 404);
        $media->delete($reel->video_url);
        $media->delete($reel->thumbnail_url);
        $reel->delete();
        return ['success' => true];
    }
}
