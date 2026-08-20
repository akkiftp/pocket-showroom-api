<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'business_id',
        'category_id',
        'name',
        'slug',
        'sku',
        'price',
        'offer_price',
        'description',
        'video_url',
        'video_type',
        'in_stock',
        'featured',
        'is_promoted',
        'is_active',
        'sort_order',
        'views_count',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'offer_price' => 'decimal:2',
        'in_stock' => 'boolean',
        'featured' => 'boolean',
        'is_promoted' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderByDesc('is_primary')->orderBy('sort_order')->orderBy('id');
    }

    public function activityEvents()
    {
        return $this->hasMany(ActivityEvent::class);
    }

    public function getSellingPriceAttribute()
    {
        return $this->offer_price ?: $this->price;
    }

    public function hasVideo(): bool
    {
        return !empty($this->video_url);
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        if (!$this->video_url) return null;
        $url = $this->video_url;

        // YouTube Shorts & standard YouTube URLs
        if (preg_match('/(?:youtube\.com\/(?:shorts\/|watch\?v=)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $videoId = $matches[1];
            return "https://www.youtube.com/embed/{$videoId}?autoplay=1&mute=0&controls=1&rel=0&playsinline=1";
        }

        // Instagram Reels
        if (preg_match('/instagram\.com\/(?:reel|p)\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
            $reelId = $matches[1];
            return "https://www.instagram.com/reel/{$reelId}/embed";
        }

        return $url;
    }
}
