<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Warzywa', 'slug' => 'warzywa'],
            ['name' => 'Owoce', 'slug' => 'owoce'],
            ['name' => 'Mięso', 'slug' => 'mieso'],
            ['name' => 'Nabiał', 'slug' => 'nabial'],
            ['name' => 'Pieczywo', 'slug' => 'pieczywo'],
            ['name' => 'Napoje', 'slug' => 'napoje'],
        ];

        foreach ($categories as $category) {
            Category::firstOrCreate(
                ['slug' => $category['slug']],
                ['name' => $category['name']]
            );
        }
    }
}
