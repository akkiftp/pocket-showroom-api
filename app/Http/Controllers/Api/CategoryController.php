<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BusinessContext;
use App\Models\Business;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    private function business(Request $request): Business { return BusinessContext::require($request); }

    private function owned(Request $request, Category $category): Category
    {
        $business = $this->business($request);
        abort_unless($category->business_id === $business->id, 404);
        return $category;
    }

    public function index(Request $request)
    {
        $business = $this->business($request);

        return response()->json([
            'categories' => $business->categories()
                ->withCount('products')
                ->get(),
        ]);
    }

    public function store(Request $request)
    {
        $business = $this->business($request);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $base = Str::slug($data['name']) ?: 'category';
        $slug = $base;
        $i = 2;
        while (Category::where('business_id', $business->id)->where('slug', $slug)->exists()) {
            $slug = $base.'-'.$i++;
        }

        $category = $business->categories()->create([
            ...$data,
            'slug' => $slug,
            'sort_order' => $data['sort_order'] ?? 0,
            'is_active' => $data['is_active'] ?? true,
        ]);

        return response()->json([
            'message' => 'Category created.',
            'category' => $category,
        ], 201);
    }

    public function update(Request $request, Category $category)
    {
        $category = $this->owned($request, $category);

        $data = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:100'],
            'sort_order' => ['sometimes', 'integer', 'min:0'],
            'is_active' => ['sometimes', 'boolean'],
        ]);

        if (array_key_exists('name', $data) && $data['name'] !== $category->name) {
            $base = Str::slug($data['name']) ?: 'category';
            $slug = $base;
            $i = 2;
            while (Category::where('business_id', $category->business_id)
                ->where('id', '!=', $category->id)
                ->where('slug', $slug)
                ->exists()) {
                $slug = $base.'-'.$i++;
            }
            $data['slug'] = $slug;
        }

        $category->update($data);

        return response()->json([
            'message' => 'Category updated.',
            'category' => $category->fresh()->loadCount('products'),
        ]);
    }

    public function destroy(Request $request, Category $category)
    {
        $category = $this->owned($request, $category);

        if ($category->products()->exists()) {
            return response()->json([
                'message' => 'Category has products. Move/delete those products first.',
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted.',
        ]);
    }
}
