<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $products = [
            [
                'image' => '/images/products/apple.png',
                'names' => ['Gala Apple', 'Fuji Apple', 'Honeycrisp Apple', 'Red Delicious', 'Granny Smith'],
                'category' => 'Fruit',
            ],
            [
                'image' => '/images/products/cheese.png',
                'names' => ['Cheddar Cheese', 'Swiss Cheese', 'Gouda', 'Brie', 'Camembert'],
                'category' => 'Dairy',
            ],
            [
                'image' => '/images/products/beer.png',
                'names' => ['Craft Lager', 'IPA', 'Stout', 'Pilsner', 'Wheat Ale'],
                'category' => 'Beverages',
            ],
            [
                'image' => '/images/products/bread.png',
                'names' => ['Sourdough Loaf', 'Whole Wheat', 'Rye Bread', 'Baguette', 'Ciabatta'],
                'category' => 'Bakery',
            ],
            [
                'image' => '/images/products/milk.png',
                'names' => ['Whole Milk', 'Skim Milk', 'Oat Milk', 'Almond Milk', 'Soy Milk'],
                'category' => 'Dairy',
            ],
        ];

        $selected = $this->faker->randomElement($products);

        return [
            'name' => $this->faker->randomElement($selected['names']),
            'description' => $this->faker->sentence(),
            'image' => $selected['image'],
            'category' => $selected['category'],
            'price' => $this->faker->randomFloat(2, 1, 20),
            'stock_quantity' => $this->faker->numberBetween(0, 100),
        ];
    }
}
