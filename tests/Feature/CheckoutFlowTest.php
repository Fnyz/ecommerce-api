<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_to_carts_and_create_order(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['stock' => 10, 'price' => 25.00]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/carts', ['product_id' => $product->id, 'quantity' => 2])
            ->assertStatus(201);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/v1/orders', ['shipping_address' => '123 Test St']);

        $response->assertStatus(201);
        $this->assertDatabaseHas('orders', ['user_id' => $user->id, 'status' => 'pending']);
    }

    public function test_cart_requires_authentication(): void
    {
        $this->postJson('/api/v1/carts', ['product_id' => 1, 'quantity' => 1])
            ->assertStatus(401);
    }

    public function test_non_admin_cannot_access_admin_routes(): void
    {
        $user = User::factory()->create(['role' => 'customer']);

        $this->actingAs($user, 'sanctum')
            ->getJson('/api/v1/admin/dashboard')
            ->assertStatus(403);
    }
}
