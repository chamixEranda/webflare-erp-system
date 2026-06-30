<?php

namespace Tests\Feature\Admin\ProductCatalog;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
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
        $response = $this->get(route('admin.categories.index'));
        $response->assertRedirectToRoute('admin.auth.login');
    }

    public function test_index_page_returns_successful_response(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.categories.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.product-catalog.categories.index');
    }

    public function test_can_list_categories_via_ajax_datatables(): void
    {
        ProductCategory::factory()->create([
            'name' => 'Electronics',
            'slug' => 'electronics',
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.categories.list'), [
            'draw' => 1,
            'start' => 0,
            'length' => 10,
            'order' => [
                ['column' => 1, 'dir' => 'asc'],
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
                    'parent_id',
                    'slug',
                    'action',
                ],
            ],
        ]);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_store_category(): void
    {
        $data = [
            'name' => 'Computers',
            'slug' => 'computers',
            'description' => 'Computer description',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.categories.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'message' => 'Category created successfully.',
            'status' => 201,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'name' => 'Computers',
            'slug' => 'computers',
        ]);
    }

    public function test_can_store_subcategory(): void
    {
        $parent = ProductCategory::factory()->create();

        $data = [
            'name' => 'Laptops',
            'slug' => 'laptops',
            'parent_id' => $parent->id,
            'description' => 'Laptops subcategory',
        ];

        $response = $this->actingAs($this->user)->post(route('admin.categories.store'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('product_categories', [
            'name' => 'Laptops',
            'parent_id' => $parent->id,
        ]);
    }

    public function test_store_validation_errors(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.categories.store'), [
            'name' => '', // Required field missing
        ]);

        $response->assertSessionHasErrors(['name']);
    }

    public function test_can_fetch_category_for_editing(): void
    {
        $category = ProductCategory::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.categories.edit', $category->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Category fetched successfully.',
            'status' => 200,
        ]);
        $response->assertJsonPath('data.id', $category->id);
    }

    public function test_can_update_category(): void
    {
        $category = ProductCategory::factory()->create([
            'name' => 'Old Name',
            'slug' => 'old-name',
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.categories.update', $category->id), [
            'name' => 'New Name',
            'slug' => 'new-name',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Category updated successfully.',
            'status' => 200,
        ]);

        $this->assertDatabaseHas('product_categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'slug' => 'new-name',
        ]);
    }

    public function test_can_delete_category(): void
    {
        $category = ProductCategory::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('admin.categories.destroy', $category->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Category deleted successfully.',
            'status' => 200,
        ]);

        $this->assertSoftDeleted('product_categories', [
            'id' => $category->id,
        ]);
    }
}
