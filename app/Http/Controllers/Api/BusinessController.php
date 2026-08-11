<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class BusinessController extends Controller
{
    private function businessOrFail(Request $request): Business
    {
        return Business::where('user_id', $request->user()->id)->firstOrFail();
    }

    public function show(Request $request)
    {
        return response()->json([
            'business' => $request->user()->business,
        ]);
    }

    public function store(Request $request)
    {
        $existing = $request->user()->business;

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'business_type' => ['nullable', 'string', 'max:80'],
            'city' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string', 'max:1000'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'about' => ['nullable', 'string', 'max:3000'],
        ]);

        if ($existing) {
            $existing->update($data);
            return response()->json([
                'message' => 'Business updated.',
                'business' => $existing->fresh(),
            ]);
        }

        $baseSlug = Str::slug($data['name']) ?: 'showroom';
        $slug = $baseSlug;
        $i = 2;
        while (Business::where('slug', $slug)->exists()) {
            $slug = $baseSlug.'-'.$i++;
        }

        $business = Business::create([
            ...$data,
            'user_id' => $request->user()->id,
            'slug' => $slug,
        ]);

        return response()->json([
            'message' => 'Business created.',
            'business' => $business,
        ], 201);
    }

    public function uploadLogo(Request $request)
    {
        $business = $this->businessOrFail($request);

        $data = $request->validate([
            'logo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        if ($business->logo_path) {
            Storage::disk('public')->delete($business->logo_path);
        }

        $path = $data['logo']->store("businesses/{$business->id}/branding", 'public');
        $business->update(['logo_path' => $path]);

        return response()->json([
            'message' => 'Logo uploaded.',
            'business' => $business->fresh(),
        ]);
    }

    public function uploadBanner(Request $request)
    {
        $business = $this->businessOrFail($request);

        $data = $request->validate([
            'banner' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);

        if ($business->banner_path) {
            Storage::disk('public')->delete($business->banner_path);
        }

        $path = $data['banner']->store("businesses/{$business->id}/branding", 'public');
        $business->update(['banner_path' => $path]);

        return response()->json([
            'message' => 'Banner uploaded.',
            'business' => $business->fresh(),
        ]);
    }

    public function theme(Request $request)
    {
        $business = $this->businessOrFail($request);

        $data = $request->validate([
            'theme_primary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'theme_secondary' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $business->update($data);

        return response()->json([
            'message' => 'Theme updated.',
            'business' => $business->fresh(),
        ]);
    }
}
