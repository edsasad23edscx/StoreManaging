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
                'name' => 'Świeże Czerwone Jabłko',
                'description' => 'Chrupiące i słodkie czerwone jabłko z lokalnych sadów.',
                'category' => 'Owoce',
                'price' => 1.20,
                'stock_quantity' => 100,
                'image' => '/images/products/apple.png',
            ],
            [
                'name' => 'Ser Szwajcarski',
                'description' => 'Dojrzewający ser szwajcarski z charakterystycznymi dziurami i wyrazistym smakiem.',
                'category' => 'Nabiał',
                'price' => 5.50,
                'stock_quantity' => 50,
                'image' => '/images/products/cheese.png',
            ],
            [
                'name' => 'Piwo Lager Premium',
                'description' => 'Orzeźwiające jasne piwo, idealne na każdą okazję.',
                'category' => 'Napoje',
                'price' => 3.00,
                'stock_quantity' => 200,
                'image' => '/images/products/beer.png',
            ],
            [
                'name' => 'Chleb na Zakwasie',
                'description' => 'Rzemieślniczy chleb na zakwasie z chrupiącą skórką.',
                'category' => 'Pieczywo',
                'price' => 2.50,
                'stock_quantity' => 30,
                'image' => '/images/products/bread.png',
            ],
            [
                'name' => 'Mleko Pełne',
                'description' => 'Świeże mleko pełne od szczęśliwych krów.',
                'category' => 'Nabiał',
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
