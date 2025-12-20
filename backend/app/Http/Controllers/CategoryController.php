<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Category::all());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:50|unique:categories,name',
        ]);

        $slug = \Str::slug($validated['name']);
        $validated['slug'] = $slug;

        $category = Category::create($validated);

        return response()->json($category, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return response()->json($category);
    }

    /**
     * Delete the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        // Set all products with this category to null (Other)
        $category->products()->update(['category_id' => null]);
        
        $category->delete();
        
        return response()->json(null, 204);
    }
}
