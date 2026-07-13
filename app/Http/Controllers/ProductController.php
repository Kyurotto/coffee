<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProductController extends Controller
{
    /**
     * Return all products as JSON for the employee POS polling.
     * Products created within the last 24 hours are flagged as "new".
     */
    public function index(): JsonResponse
    {
        $products = Product::orderBy('created_at', 'desc')->get()->map(function ($p) {
            return [
                'id'          => $p->id,
                'name'        => $p->name,
                'description' => $p->description ?? '',
                'price'       => $p->price,
                'category'    => $p->category,
                'image'       => $p->image_path ? Storage::url($p->image_path) : null,
                'is_new'      => $p->created_at->gt(now()->subHours(24)),
            ];
        });

        return response()->json(['products' => $products]);
    }

    /**
     * Store a new product (multipart/form-data).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price'       => ['required', 'numeric', 'min:0.01'],
            'category'    => ['required', Rule::in(['milktea', 'snack'])],
            'image'       => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required'     => 'Product name is required.',
            'price.required'    => 'Price is required.',
            'price.numeric'     => 'Price must be a number.',
            'price.min'         => 'Price must be greater than 0.',
            'category.required' => 'Please select a category.',
            'category.in'       => 'Category must be Milk Tea or Snack.',
            'image.image'       => 'File must be an image.',
            'image.max'         => 'Image may not be larger than 2MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'category'    => $validated['category'],
            'image_path'  => $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'product' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description ?? '',
                'price'       => $product->price,
                'category'    => $product->category,
                'image'       => $imagePath ? Storage::url($imagePath) : null,
            ],
        ], 201);
    }

    /**
     * Update an existing product.
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'price'       => ['required', 'numeric', 'min:0.01'],
            'category'    => ['required', Rule::in(['milktea', 'snack'])],
            'image'       => ['nullable', 'image', 'max:2048'],
        ], [
            'name.required'     => 'Product name is required.',
            'price.required'    => 'Price is required.',
            'price.numeric'     => 'Price must be a number.',
            'price.min'         => 'Price must be greater than 0.',
            'category.required' => 'Please select a category.',
            'category.in'       => 'Category must be Milk Tea or Snack.',
            'image.image'       => 'File must be an image.',
            'image.max'         => 'Image may not be larger than 2MB.',
        ]);

        if ($request->hasFile('image')) {
            // Delete old image
            if ($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update([
            'name'        => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price'       => $validated['price'],
            'category'    => $validated['category'],
            'image_path'  => $validated['image_path'] ?? $product->image_path,
        ]);

        return response()->json([
            'success' => true,
            'product' => [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description ?? '',
                'price'       => $product->price,
                'category'    => $product->category,
                'image'       => $product->image_path ? Storage::url($product->image_path) : null,
            ],
        ]);
    }

    /**
     * Delete a product.
     */
    public function destroy(Product $product): JsonResponse
    {
        if ($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }
        $product->delete();

        return response()->json(['success' => true]);
    }
}
