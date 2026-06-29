<?php

namespace Tests\Feature\Admin\ProductCatalog;

use App\Enums\UomType;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UnitControllerTest extends TestCase
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
        $response = $this->get(route('admin.units.index'));
        $response->assertRedirectToRoute('admin.auth.login');
    }

    public function test_index_page_returns_successful_response(): void
    {
        $response = $this->actingAs($this->user)->get(route('admin.units.index'));
        $response->assertStatus(200);
        $response->assertViewIs('admin.product-catalog.units.index');
    }

    public function test_can_list_units_via_ajax_datatables(): void
    {
        Unit::factory()->create([
            'name' => 'Kilogram',
            'short_name' => 'kg',
            'uom_type' => UomType::WEIGHT,
        ]);

        $response = $this->actingAs($this->user)->post(route('admin.units.list'), [
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
                    'name',
                    'short_name',
                    'uom_type',
                    'base_unit_id',
                    'conversion_factor',
                    'action',
                ],
            ],
        ]);
        $this->assertCount(1, $response->json('data'));
    }

    public function test_can_store_base_unit(): void
    {
        $data = [
            'name' => 'Meter',
            'short_name' => 'm',
            'uom_type' => UomType::LENGTH,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)->post(route('admin.units.store'), $data);

        $response->assertStatus(201);
        $response->assertJsonFragment([
            'message' => 'Unit created successfully.',
            'status' => 201,
        ]);

        $this->assertDatabaseHas('units', [
            'name' => 'Meter',
            'short_name' => 'm',
            'uom_type' => UomType::LENGTH,
            'conversion_factor' => 1.0,
        ]);
    }

    public function test_can_store_subunit_with_conversion_factor(): void
    {
        $baseUnit = Unit::factory()->create([
            'name' => 'Kilogram',
            'uom_type' => UomType::WEIGHT,
        ]);

        $data = [
            'name' => 'Gram',
            'short_name' => 'g',
            'uom_type' => UomType::WEIGHT,
            'base_unit_id' => $baseUnit->id,
            'conversion_factor' => 1000.0,
            'is_active' => true,
        ];

        $response = $this->actingAs($this->user)->post(route('admin.units.store'), $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('units', [
            'name' => 'Gram',
            'base_unit_id' => $baseUnit->id,
            'conversion_factor' => 1000.0,
        ]);
    }

    public function test_store_validation_errors(): void
    {
        $response = $this->actingAs($this->user)->post(route('admin.units.store'), [
            'name' => '', // Required field missing
        ]);

        $response->assertSessionHasErrors(['name', 'uom_type']);
    }

    public function test_can_fetch_unit_for_editing(): void
    {
        $unit = Unit::factory()->create();

        $response = $this->actingAs($this->user)->get(route('admin.units.edit', $unit->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Unit fetched successfully.',
            'status' => 200,
        ]);
        $response->assertJsonPath('data.id', $unit->id);
    }

    public function test_can_update_unit(): void
    {
        $unit = Unit::factory()->create([
            'name' => 'Old Name',
            'short_name' => 'old',
            'uom_type' => UomType::WEIGHT,
        ]);

        $response = $this->actingAs($this->user)->put(route('admin.units.update', $unit->id), [
            'name' => 'New Name',
            'short_name' => 'new',
            'uom_type' => UomType::WEIGHT,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Unit updated successfully.',
            'status' => 200,
        ]);

        $this->assertDatabaseHas('units', [
            'id' => $unit->id,
            'name' => 'New Name',
            'short_name' => 'new',
        ]);
    }

    public function test_can_delete_unit(): void
    {
        $unit = Unit::factory()->create();

        $response = $this->actingAs($this->user)->delete(route('admin.units.destroy', $unit->id));

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Unit deleted successfully.',
            'status' => 200,
        ]);

        $this->assertSoftDeleted('units', [
            'id' => $unit->id,
        ]);
    }
}
