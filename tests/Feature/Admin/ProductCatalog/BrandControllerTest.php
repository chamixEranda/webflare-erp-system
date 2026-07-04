<?php

namespace Tests\Feature\Admin\ProductCatalog;

use App\Models\ProductBrand;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BrandControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_index_page_requires_authentication(): void
    {
        $response = $this->get(route('admin.brands.index'));
        $response->assertRedirectToRoute('admin.auth.login');
    }

    public function test_index_page_returns_successful_response(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.brands.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.product-catalog.brands.index');
    }

    public function test_can_list_brands_via_ajax_datatables(): void
    {
        ProductBrand::factory()->create([
            'name' => 'Nike',
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.brands.list'), [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'order' => [
                ['column' => 2, 'dir' => 'asc'],
            ],
            'search' => ['value' => ''],
        ]);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'draw',
            'recordsTotal',
            'recordsFiltered',
            'data' => [
                '*' => [
                    'key',
                    'id',
                    'image',
                    'name',
                    'action',
                ],
            ],
        ]);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_store_brand(): void
    {
        $data = [
            'name' => 'Adidas',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.brands.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'message' => 'Brand created successfully.',
            'status' => 201,
        ]);

        $this->assertDatabaseHas('product_brands', [
            'name' => 'Adidas',
        ]);
    }

    public function test_store_validation_errors(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.brands.store'), [
            'name' => '', // Required field missing
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_can_fetch_brand_for_editing(): void
    {
        $brand = ProductBrand::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.brands.edit', $brand->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Brand fetched successfully.',
            'status' => 200,
        ]);
        $response->assertJsonPath('data.id', $brand->id);
    }

    public function test_can_update_brand(): void
    {
        $brand = ProductBrand::factory()->create([
            'name' => 'Puma',
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.brands.update', $brand->id), [
            'name' => 'Puma New',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Brand updated successfully.',
            'status' => 200,
        ]);

        $this->assertDatabaseHas('product_brands', [
            'id' => $brand->id,
            'name' => 'Puma New',
        ]);
    }

    public function test_can_delete_brand(): void
    {
        $brand = ProductBrand::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('admin.brands.destroy', $brand->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Brand deleted successfully.',
            'status' => 200,
        ]);

        $this->assertSoftDeleted('product_brands', [
            'id' => $brand->id,
        ]);
    }
}
