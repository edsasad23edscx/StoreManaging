<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing products
        Product::truncate();

        $products = [
            [
                'name' => 'Fresh Red Apple',
                'description' => 'Crisp and sweet red apple, locally sourced.',
                'category' => 'Fruit',
                'price' => 1.20,
                'stock_quantity' => 100,
                'image' => '/images/products/apple.png',
            ],
            [
                'name' => 'Swiss Cheese',
                'description' => 'Aged Swiss cheese with distinct holes and bold flavor.',
                'category' => 'Dairy',
                'price' => 5.50,
                'stock_quantity' => 50,
                'image' => '/images/products/cheese.png',
            ],
            [
                'name' => 'Premium Lager Beer',
                'description' => 'Refreshing lager beer, perfect for any occasion.',
                'category' => 'Beverages',
                'price' => 3.00,
                'stock_quantity' => 200,
                'image' => '/images/products/beer.png',
            ],
            [
                'name' => 'Sourdough Bread',
                'description' => 'Artisan sourdough bread with a crispy crust.',
                'category' => 'Bakery',
                'price' => 2.50,
                'stock_quantity' => 30,
                'image' => '/images/products/bread.png',
            ],
            [
                'name' => 'Whole Milk',
                'description' => 'Fresh whole milk from happy cows.',
                'category' => 'Dairy',
                'price' => 1.80,
                'stock_quantity' => 80,
                'image' => '/images/products/milk.png',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Generate 45 random products
        Product::factory()->count(45)->create();
    }
}
