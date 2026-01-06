<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, add the category_id column without the foreign key constraint
        Schema::table('products', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('description');
        });

        // Migrate data: look up category IDs by name and update products
        $categories = DB::table('categories')->pluck('id', 'name');
        $products = DB::table('products')->whereNotNull('category')->get();
        
        foreach ($products as $product) {
            $categoryId = $categories[$product->category] ?? null;
            DB::table('products')
                ->where('id', $product->id)
                ->update(['category_id' => $categoryId]);
        }

        // Remove the old category column and add foreign key constraint
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('category');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Add the category string column back
        Schema::table('products', function (Blueprint $table) {
            $table->string('category')->nullable()->after('description');
        });

        // Migrate data back: look up category names by ID and update products
        $categories = DB::table('categories')->pluck('name', 'id');
        $products = DB::table('products')->whereNotNull('category_id')->get();
        
        foreach ($products as $product) {
            $categoryName = $categories[$product->category_id] ?? null;
            DB::table('products')
                ->where('id', $product->id)
                ->update(['category' => $categoryName]);
        }

        // Drop foreign key and category_id column
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropColumn('category_id');
        });
    }
};
