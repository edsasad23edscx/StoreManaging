<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticate()
    {
        $user = User::factory()->create();
        $this->actingAs($user);
        return $user;
    }

    public function test_can_list_products()
    {
        $this->authenticate();
        Product::factory()->count(3)->create();

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJsonCount(3);
    }

    public function test_can_create_product()
    {
        $this->authenticate();

        $data = [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'category' => 'Test Category',
            'price' => 10.50,
            'stock_quantity' => 100,
        ];

        /* Attempt to authenticate */
        $response = $this->postJson('/api/products', $data);

        $response->assertStatus(201)
                 ->assertJsonFragment(['name' => 'Test Product']);
                 
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_validates_required_fields()
    {
        $this->authenticate();

        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'price', 'stock_quantity']);
    }

    public function test_validates_price_format()
    {
        $this->authenticate();

        $response = $this->postJson('/api/products', [
            'name' => 'Test',
            'category' => 'Test',
            'price' => 'not-a-number',
            'stock_quantity' => 10,
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['price']);
    }

    public function test_can_upload_product_image()
    {
        $this->authenticate();
        Storage::fake('public');

        // Use create instead of image() to avoid GD extension requirement
        $file = UploadedFile::fake()->create('product.jpg', 100);

        $response = $this->postJson('/api/products', [
            'name' => 'Image Product',
            'category' => 'Test',
            'price' => 10,
            'stock_quantity' => 10,
            'image' => $file,
        ]);

        $response->assertStatus(201);
        
        // Assert the file was stored...
        // Note: The controller stores in 'products' folder inside public disk
        $this->assertNotEmpty($response->json('image'));
    }
}
