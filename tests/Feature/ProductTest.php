<?php

namespace Tests\Feature;

use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_inserts_30_initial_products(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseCount('products', 30);
        $this->assertDatabaseHas('products', [
            'produk' => 'Pulpen',
            'harga' => 5000,
            'keuntungan' => 2000,
            'terjual' => 65,
        ]);
        $this->assertDatabaseHas('products', [
            'produk' => 'Lakban',
            'harga' => 10000,
            'keuntungan' => 1500,
            'terjual' => 400,
        ]);
    }

    public function test_index_displays_products_from_database(): void
    {
        $this->seed(ProductSeeder::class);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Bubble Chart Penjualan Peralatan Sekolah');
        $response->assertSee('Pulpen');
        $response->assertSee('Lakban');
        $response->assertSee('database MySQL');
    }

    public function test_can_create_update_delete_and_reset_products_via_json(): void
    {
        $this->seed(ProductSeeder::class);

        $createResponse = $this->postJson('/products', [
            'produk' => 'Kompas Sekolah',
            'harga' => 25000,
            'keuntungan' => 6000,
            'terjual' => 80,
        ]);

        $createResponse->assertCreated();
        $createResponse->assertJsonCount(31, 'products');
        $this->assertDatabaseHas('products', [
            'produk' => 'Kompas Sekolah',
            'harga' => 25000,
            'keuntungan' => 6000,
            'terjual' => 80,
        ]);

        $product = Product::where('produk', 'Kompas Sekolah')->firstOrFail();

        $updateResponse = $this->putJson("/products/{$product->id}", [
            'produk' => 'Kompas Geometri',
            'harga' => 27000,
            'keuntungan' => 7000,
            'terjual' => 95,
        ]);

        $updateResponse->assertOk();
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'produk' => 'Kompas Geometri',
            'harga' => 27000,
            'keuntungan' => 7000,
            'terjual' => 95,
        ]);

        $deleteResponse = $this->deleteJson("/products/{$product->id}");

        $deleteResponse->assertOk();
        $deleteResponse->assertJsonCount(30, 'products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);

        Product::query()->delete();
        $this->assertDatabaseCount('products', 0);

        $resetResponse = $this->postJson('/products/reset');
        $resetResponse->assertOk();
        $resetResponse->assertJsonCount(30, 'products');
        $this->assertDatabaseCount('products', 30);
    }

    public function test_can_create_update_delete_and_reset_products_via_form_redirect(): void
    {
        $storeResponse = $this->post('/products', [
            'produk' => 'Busur Derajat',
            'harga' => 3500,
            'keuntungan' => 1000,
            'terjual' => 50,
        ]);

        $storeResponse->assertRedirect(route('products.index'));
        $product = Product::where('produk', 'Busur Derajat')->firstOrFail();

        $updateResponse = $this->put("/products/{$product->id}", [
            'produk' => 'Busur Besar',
            'harga' => 4500,
            'keuntungan' => 1500,
            'terjual' => 60,
        ]);
        $updateResponse->assertRedirect(route('products.index'));

        $deleteResponse = $this->delete("/products/{$product->id}");
        $deleteResponse->assertRedirect(route('products.index'));
        $this->assertDatabaseCount('products', 0);

        $resetResponse = $this->post('/products/reset');
        $resetResponse->assertRedirect(route('products.index'));
        $this->assertDatabaseCount('products', 30);
    }

    public function test_validation_rejects_invalid_product_inputs(): void
    {
        $response = $this->postJson('/products', [
            'produk' => '',
            'harga' => -100,
            'keuntungan' => -50,
            'terjual' => 12.5,
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors(['produk', 'harga', 'keuntungan', 'terjual']);
    }
}
