<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessContext;
use App\Models\Business;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Cloudinary\Cloudinary;

class ProductController extends Controller
{
    private function business(Request $request): Business { return BusinessContext::require($request); }

    private function owned(Request $request, Product $product): Product
    {
        $business = $this->business($request);
        abort_unless($product->business_id === $business->id, 404);
        return $product;
    }

    private function validatePayload(Request $request, ?Product $product = null): array
    {
        return $request->validate([
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'name' => [$product ? 'sometimes' : 'required', 'string', 'max:180'],
            'sku' => ['nullable', 'string', 'max:100'],
            'price' => [$product ? 'sometimes' : 'required', 'numeric', 'min:0'],
            'offer_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string', 'max:10000'],
            'in_stock' => ['nullable', 'boolean'],
            'featured' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array', 'max:8'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
        ]);
    }

    public function index(Request $request)
    {
        $business = $this->business($request);

        $query = $business->products()
            ->with(['category:id,name', 'images']);

        if ($request->filled('search')) {
            $search = trim((string) $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }

        if ($request->filled('in_stock')) {
            $query->where('in_stock', $request->boolean('in_stock'));
        }

        if ($request->filled('featured')) {
            $query->where('featured', $request->boolean('featured'));
        }

        $perPage = min(max($request->integer('per_page', 20), 1), 100);

        return response()->json(
            $query->latest('id')->paginate($perPage)->withQueryString()
        );
    }

    public function store(Request $request)
    {
        $business = $this->business($request);
        $data = $this->validatePayload($request);

        if (!empty($data['category_id'])) {
            $validCategory = Category::where('business_id', $business->id)
                ->whereKey($data['category_id'])
                ->exists();

            if (!$validCategory) {
                return response()->json(['message' => 'Invalid category for this business.'], 422);
            }
        }

        return DB::transaction(function () use ($request, $business, $data) {
            $base = Str::slug($data['name']) ?: 'product';
            $slug = $base;
            $i = 2;
            while (Product::where('business_id', $business->id)->where('slug', $slug)->withTrashed()->exists()) {
                $slug = $base.'-'.$i++;
            }

            $product = $business->products()->create([
                'category_id' => $data['category_id'] ?? null,
                'name' => $data['name'],
                'slug' => $slug,
                'sku' => $data['sku'] ?? null,
                'price' => $data['price'],
                'offer_price' => $data['offer_price'] ?? null,
                'description' => $data['description'] ?? null,
                'in_stock' => $data['in_stock'] ?? true,
                'featured' => $data['featured'] ?? false,
                'is_active' => $data['is_active'] ?? true,
                'sort_order' => $data['sort_order'] ?? 0,
            ]);

            $this->storeImages($request, $product);

            return response()->json([
                'message' => 'Product created.',
                'product' => $product->fresh()->load(['category:id,name', 'images']),
            ], 201);
        });
    }

    public function show(Request $request, Product $product)
    {
        $product = $this->owned($request, $product);

        return response()->json([
            'product' => $product->load(['category:id,name', 'images']),
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $product = $this->owned($request, $product);
        $business = $this->business($request);
        $data = $this->validatePayload($request, $product);

        if (!empty($data['category_id'])) {
            $validCategory = Category::where('business_id', $business->id)
                ->whereKey($data['category_id'])
                ->exists();

            if (!$validCategory) {
                return response()->json(['message' => 'Invalid category for this business.'], 422);
            }
        }

        return DB::transaction(function () use ($request, $product, $data) {
            $update = collect($data)->except('images')->all();

            if (isset($update['name']) && $update['name'] !== $product->name) {
                $base = Str::slug($update['name']) ?: 'product';
                $slug = $base;
                $i = 2;
                while (Product::where('business_id', $product->business_id)
                    ->where('id', '!=', $product->id)
                    ->where('slug', $slug)
                    ->withTrashed()
                    ->exists()) {
                    $slug = $base.'-'.$i++;
                }
                $update['slug'] = $slug;
            }

            $product->update($update);
            $this->storeImages($request, $product);

            return response()->json([
                'message' => 'Product updated.',
                'product' => $product->fresh()->load(['category:id,name', 'images']),
            ]);
        });
    }

    public function destroy(Request $request, Product $product)
    {
        $product = $this->owned($request, $product);

        foreach ($product->images as $image) {
            if (Str::startsWith($image->path, 'http')) {
                // If it's a Cloudinary URL, we can delete by public_id if needed, but for now we skip local deletion
                try {
                    $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
                    // Extract public ID from URL (basic extraction, optional step)
                    // We'll leave it in Cloudinary for safety or implement proper public_id extraction later
                } catch (\Exception $e) {}
            } else {
                Storage::disk('public')->delete($image->path);
            }
        }

        $product->delete();

        return response()->json([
            'message' => 'Product deleted.',
        ]);
    }

    public function toggleStock(Request $request, Product $product)
    {
        $product = $this->owned($request, $product);
        $product->update(['in_stock' => !$product->in_stock]);

        return response()->json([
            'message' => 'Stock status updated.',
            'product' => $product->fresh()->load('images'),
        ]);
    }

    public function toggleFeatured(Request $request, Product $product)
    {
        $product = $this->owned($request, $product);
        $product->update(['featured' => !$product->featured]);

        return response()->json([
            'message' => 'Featured status updated.',
            'product' => $product->fresh()->load('images'),
        ]);
    }

    public function destroyImage(Request $request, Product $product, ProductImage $image)
    {
        $product = $this->owned($request, $product);
        abort_unless($image->product_id === $product->id, 404);

        if (Str::startsWith($image->path, 'http')) {
            try {
                $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
                // Extraction of public ID could be done here if strictly needed
            } catch (\Exception $e) {}
        } else {
            Storage::disk('public')->delete($image->path);
        }
        $image->delete();

        $first = $product->images()->first();
        if ($first && !$product->images()->where('is_primary', true)->exists()) {
            $first->update(['is_primary' => true]);
        }

        return response()->json([
            'message' => 'Image deleted.',
            'product' => $product->fresh()->load('images'),
        ]);
    }

    private function storeImages(Request $request, Product $product): void
    {
        if (!$request->hasFile('images')) {
            return;
        }

        $existingCount = $product->images()->count();
        $sort = $existingCount;

        foreach ($request->file('images', []) as $file) {
            try {
                if (env('CLOUDINARY_URL')) {
                    $cloudinary = new Cloudinary(env('CLOUDINARY_URL'));
                    $upload = $cloudinary->uploadApi()->upload($file->getRealPath(), [
                        'folder' => "businesses/{$product->business_id}/products/{$product->id}",
                    ]);
                    $path = $upload['secure_url'];
                } else {
                    $path = $file->store("businesses/{$product->business_id}/products/{$product->id}", 'public');
                    // Store absolute URL even for local fallback
                    $path = url(Storage::url($path));
                }
            } catch (\Exception $e) {
                // Fallback to local if Cloudinary fails
                $path = $file->store("businesses/{$product->business_id}/products/{$product->id}", 'public');
                $path = url(Storage::url($path));
            }

            $product->images()->create([
                'path' => $path,
                'is_primary' => $existingCount === 0 && $sort === 0,
                'sort_order' => $sort++,
            ]);
        }
    }
}
