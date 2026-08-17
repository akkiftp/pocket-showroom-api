<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'path',
        'is_primary',
        'sort_order',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];

    protected $appends = ['url'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->path, 'http://') || str_starts_with($this->path, 'https://')) {
            return str_replace('http://', 'https://', $this->path);
        }
        $appUrl = rtrim(config('app.url') ?? 'https://pocket-showroom-api.onrender.com', '/');
        $appUrl = str_replace('http://', 'https://', $appUrl);
        return $appUrl . '/storage/' . ltrim($this->path, '/');
    }
}
