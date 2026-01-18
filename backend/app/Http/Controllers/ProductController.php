<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::query()
            ->with('category')
            ->when($request->category_id, function ($query, $categoryId) {
                if ($categoryId === 'null') {
                    return $query->whereNull('category_id');
                }
                return $query->where('category_id', $categoryId);
            })
            ->when($request->search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($products);
    }

    public function store(Request $request)
    {
        if ($request->has('category_id')) {
            $categoryId = $request->input('category_id');
            if ($categoryId === '' || $categoryId === 'null') {
                $request->merge(['category_id' => null]);
            }
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'description' => 'nullable|string',
            'category_id' => 'nullable|integer|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock_quantity' => 'required|integer|min:0',
            'minimum_stock' => 'nullable|integer|min:0',
        ], [
            'barcode.unique' => 'Produkt z takim kodem kreskowym już istnieje.',
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('products', 'public');
            $validated['image'] = '/storage/' . $path;
        }

        $product = Product::create($validated);

        return response()->json($product->load('category'), 201);
    }

    public function show(Product $product)
    {
        return response()->json($product->load('category'));
    }

    public function update(Request $request, Product $product)
    {
        try {
            \Illuminate\Support\Facades\Log::info('Update Request Data:', $request->all());
            \Illuminate\Support\Facades\Log::info('Update Request Files:', $request->allFiles());

            if ($request->has('category_id')) {
                $categoryId = $request->input('category_id');
                if ($categoryId === '' || $categoryId === 'null') {
                    $request->merge(['category_id' => null]);
                }
            }
            
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $product->id,
                'description' => 'nullable|string',
                'category_id' => 'nullable|integer|exists:categories,id',
                'price' => 'sometimes|required|numeric|min:0',
                'stock_quantity' => 'sometimes|required|integer|min:0',
                'minimum_stock' => 'nullable|integer|min:0',
                'image' => 'nullable|image|max:2048',
            ], [
                'barcode.unique' => 'Produkt z takim kodem kreskowym już istnieje.',
            ]);

            if ($request->hasFile('image')) {
                $path = $request->file('image')->store('products', 'public');
                $validated['image'] = '/storage/' . $path;
            }

            $product->update($validated);

            return response()->json($product->load('category'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Product update failed: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update product', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Product $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}
