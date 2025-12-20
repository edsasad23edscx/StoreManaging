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
            // Owoce (Fruits)
            ['image' => '/images/products/apple.png', 'category' => 'Owoce', 'names' => ['Jabłko Gala', 'Jabłko Szampion', 'Jabłko Ligol']],
            ['image' => '/images/products/banana.png', 'category' => 'Owoce', 'names' => ['Banany Premium', 'Banany Bio']],
            ['image' => '/images/products/orange.png', 'category' => 'Owoce', 'names' => ['Pomarańcze Hiszpańskie', 'Pomarańcze Deserowe']],
            ['image' => '/images/products/lemon.png', 'category' => 'Owoce', 'names' => ['Cytryna Sycylijska', 'Cytryna Bio']],
            
            // Warzywa (Vegetables)
            ['image' => '/images/products/tomato.png', 'category' => 'Warzywa', 'names' => ['Pomidor Malinowy', 'Pomidor Gałązka', 'Pomidor Bawole Serce']],
            ['image' => '/images/products/cucumber.png', 'category' => 'Warzywa', 'names' => ['Ogórek Gruntowy', 'Ogórek Szklarniowy']],
            ['image' => '/images/products/potato.png', 'category' => 'Warzywa', 'names' => ['Ziemniaki Młode', 'Ziemniaki Irga']],
            ['image' => '/images/products/carrot.png', 'category' => 'Warzywa', 'names' => ['Marchew Polska', 'Marchew Myta']],
            ['image' => '/images/products/onion.png', 'category' => 'Warzywa', 'names' => ['Cebula Żółta', 'Cebula Czerwona']],

            // Mięso (Meat)
            ['image' => '/images/products/sausage.png', 'category' => 'Mięso', 'names' => ['Kiełbasa Śląska', 'Kiełbasa Podwawelska', 'Kiełbasa Krakowia']],
            ['image' => '/images/products/chicken.png', 'category' => 'Mięso', 'names' => ['Kurczak Zagrodowy', 'Filet z Kurczaka', 'Udka z Kurczaka']],
            ['image' => '/images/products/ham.png', 'category' => 'Mięso', 'names' => ['Szynka Wiejska', 'Szynka Konserwowa', 'Szynka Wędzona']],

            // Nabiał (Dairy)
            ['image' => '/images/products/cheese.png', 'category' => 'Nabiał', 'names' => ['Ser Gouda', 'Ser Edamski', 'Ser Królewski']],
            ['image' => '/images/products/milk.png', 'category' => 'Nabiał', 'names' => ['Mleko Świeże 3.2%', 'Mleko 2%', 'Mleko Wiejskie']],
            
            // Pieczywo (Bakery)
            ['image' => '/images/products/bread.png', 'category' => 'Pieczywo', 'names' => ['Chleb Razowy', 'Chleb Wiejski', 'Chleb Żytni']],
            
            // Napoje (Beverages)
            ['image' => '/images/products/beer.png', 'category' => 'Napoje', 'names' => ['Piwo Jasne', 'Piwo Lager', 'Piwo Pszeniczne']],
        ];

        $selected = $this->faker->randomElement($products);

        return [
            'name' => $this->faker->randomElement($selected['names']),
            'description' => $this->faker->randomElement([
                'Świeży produkt najwyższej jakości.',
                'Idealny dodatek do codziennych potraw.',
                'Produkt polski, naturalny i zdrowy.',
                'Gwarancja smaku i świeżości.',
                'Tradycyjna receptura, wyśmienity smak.'
            ]),
            'image' => $selected['image'],
            'category' => $selected['category'],
            'price' => $this->faker->randomFloat(2, 1, 30),
            'stock_quantity' => $this->faker->numberBetween(1, 100),
            'minimum_stock' => $this->faker->numberBetween(3, 10),
        ];
    }
}
