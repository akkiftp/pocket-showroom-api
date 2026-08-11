<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use Illuminate\Http\Request;

class PublicShowroomController extends Controller
{
    private function business(string $slug): Business
    {
        return Business::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    public function show(string $slug)
    {
        $business = $this->business($slug);

        return response()->json([
            'business' => $business,
            'categories' => $business->categories()
                ->where('is_active', true)
                ->withCount(['products' => fn ($q) => $q->where('is_active', true)->where('in_stock', true)])
                ->get(),
            'featured_products' => $business->products()
                ->where('is_active', true)
                ->where('in_stock', true)
                ->where('featured', true)
                ->with(['category:id,name', 'images'])
                ->orderBy('sort_order')
                ->latest('id')
                ->limit(12)
                ->get(),
        ]);
    }

    public function products(Request $request, string $slug)
    {
        $business = $this->business($slug);

        $query = $business->products()
            ->where('is_active', true)
            ->where('in_stock', true)
            ->with(['category:id,name', 'images']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return response()->json(
            $query->orderBy('sort_order')->latest('id')
                ->paginate(min(max($request->integer('per_page', 20), 1), 60))
        );
    }

    public function product(string $slug, Product $product)
    {
        $business = $this->business($slug);

        abort_unless(
            $product->business_id === $business->id &&
            $product->is_active &&
            $product->in_stock,
            404
        );

        $product->increment('views_count');

        return response()->json([
            'product' => $product->fresh()->load(['category:id,name', 'images']),
        ]);
    }

    public function inquiry(Request $request, string $slug)
    {
        $business = $this->business($slug);

        $data = $request->validate([
            'product_id' => ['nullable', 'integer'],
            'customer_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:150'],
            'message' => ['nullable', 'string', 'max:2000'],
            'source' => ['nullable', 'string', 'max:30'],
        ]);

        if (!empty($data['product_id'])) {
            $valid = $business->products()
                ->whereKey($data['product_id'])
                ->where('is_active', true)
                ->exists();

            if (!$valid) {
                return response()->json(['message' => 'Invalid product.'], 422);
            }
        }

        $inquiry = $business->inquiries()->create([
            ...$data,
            'status' => 'pending',
            'source' => $data['source'] ?? 'showroom',
        ]);

        return response()->json([
            'message' => 'Enquiry sent successfully.',
            'inquiry' => $inquiry->load('product:id,name'),
        ], 201);
    }
}
