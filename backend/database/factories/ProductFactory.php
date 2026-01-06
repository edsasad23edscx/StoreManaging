<?php

namespace Database\Factories;

use App\Models\Category;
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
        $categoryMapping = [
            'Owoce' => ['apple.png', 'banana.png', 'orange.png', 'lemon.png'],
            'Warzywa' => ['tomato.png', 'cucumber.png', 'potato.png', 'carrot.png', 'onion.png'],
            'Mięso' => ['sausage.png', 'chicken.png', 'ham.png'],
            'Nabiał' => ['cheese.png', 'milk.png'],
            'Pieczywo' => ['bread.png'],
            'Napoje' => ['beer.png'],
        ];

        $productNames = [
            'Owoce' => ['Jabłko Gala', 'Jabłko Szampion', 'Banany Premium', 'Pomarańcze Hiszpańskie', 'Cytryna Sycylijska'],
            'Warzywa' => ['Pomidor Malinowy', 'Pomidor Gałązka', 'Ogórek Gruntowy', 'Ziemniaki Młode', 'Marchew Polska', 'Cebula Żółta'],
            'Mięso' => ['Kiełbasa Śląska', 'Kurczak Zagrodowy', 'Filet z Kurczaka', 'Szynka Wiejska'],
            'Nabiał' => ['Ser Gouda', 'Ser Edamski', 'Mleko Świeże 3.2%', 'Mleko 2%'],
            'Pieczywo' => ['Chleb Razowy', 'Chleb Wiejski', 'Chleb Żytni'],
            'Napoje' => ['Piwo Jasne', 'Piwo Lager', 'Piwo Pszeniczne'],
        ];

        $categories = Category::all();
        $category = $categories->random();
        $categoryName = $category->name;

        $images = $categoryMapping[$categoryName] ?? ['placeholder.png'];
        $names = $productNames[$categoryName] ?? ['Produkt'];

        return [
            'name' => $this->faker->randomElement($names),
            'description' => $this->faker->randomElement([
                'Świeży produkt najwyższej jakości.',
                'Idealny dodatek do codziennych potraw.',
                'Produkt polski, naturalny i zdrowy.',
                'Gwarancja smaku i świeżości.',
                'Tradycyjna receptura, wyśmienity smak.'
            ]),
            'image' => '/images/products/' . $this->faker->randomElement($images),
            'category_id' => $category->id,
            'price' => $this->faker->randomFloat(2, 1, 30),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
            'minimum_stock' => $this->faker->numberBetween(3, 10),
        ];
    }
}

