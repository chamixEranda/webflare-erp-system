<?php

namespace Database\Factories;

use App\Enums\UomType;
use App\Models\Unit;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Unit>
 */
class UnitFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'company_id' => 1,
            'name' => $this->faker->word(),
            'short_name' => $this->faker->lexify('???'),
            'uom_type' => UomType::WEIGHT,
            'base_unit_id' => null,
            'conversion_factor' => 1.0,
            'is_active' => true,
        ];
    }
}
