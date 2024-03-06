<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ProductTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_index_api_return_ok(): void
    {
        $response = $this->getJson('/api/products');

        $response->assertStatus(200);
    }

    /**
     * Comprobamos que la estructura de cada item contiene los campos name,
     * description y price
     */
    public function test_each_item_has_correct_structure(): void {
        $this->getJson('/api/products')
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                '*' => [
                    'name',
                    'description',
                    'price'
                ]
                ]);
    }

    /**
     * Comprobamos que la base de datos contiene 10 productos.
     */
    public function test_index_return_10_products(): void {
        $response = $this->getJson('/api/products')
            ->assertStatus(Response::HTTP_OK);
        $this->assertEquals(count($response->original), 10);
    }

    /**
     * Comprobamos que se guarda un producto y la respuesta es correcta.
     */
    public function test_store_save_product_then_index_has_11_products(): void {
        $product = new Product;
        $product->name = 'IPHONE 15';
        $product->description = 'Iphone 15 description.';
        $product->price = 599.99;
        $postResponse = $this->postJson('/api/products', $product->toArray())
            ->assertStatus(Response::HTTP_CREATED);
        $this->assertEquals($postResponse->original->name, $product->name);
        $response = $this->getJson('/api/products')
            ->assertStatus(Response::HTTP_OK);
        $this->assertEquals(count($response->original), 11);
    }

    /**
     * Comprobamos que se muestra un producto por su id.
     */
    public function test_show_display_1_product_same_store_test(): void {
        $product = new Product;
        $product->name = 'IPHONE 15';
        $product->description = 'Iphone 15 description.';
        $product->price = 599.99;
        $this->postJson('/api/products', $product->toArray())
            ->assertStatus(Response::HTTP_CREATED);
        $response = $this->getJson('/api/products/11')
            ->assertStatus(Response::HTTP_OK);
        $this->assertEquals($response->original->name, 'IPHONE 15');
    }

    /**
     * Comprobamos que se actualiza un producto por su id y que ese nombre es el mismo.
     */
    public function test_update_product_created_previously_has_same_name(): void {
        $product = new Product;
        $product->name = 'IPHONE 15';
        $product->description = 'Iphone 15 description.';
        $product->price = 599.99;
        $this->postJson('/api/products', $product->toArray())
            ->assertStatus(Response::HTTP_CREATED);
        $product->name = 'IPHONE 14';
        $response = $this->putJson('/api/products/11', $product->toArray())
            ->assertStatus(Response::HTTP_OK);
        $this->assertEquals($response->original->name, 'IPHONE 14');
    }

    /**
     * Comprobamos que se borra un producto por su id y que la base de datos contiene
     * 9 productos.
     */
    public function test_delete_product_by_id_and_index_return_9_products(): void {
        $this->deleteJson('/api/products/10')
            ->assertStatus(204);
        $response = $this->getJson('/api/products')
            ->assertStatus(Response::HTTP_OK);
        $this->assertEquals(count($response->original), 9);
    }
}
