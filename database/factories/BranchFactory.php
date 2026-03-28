<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Branch>
 */
class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('BR##')),
            'name' => 'Cabang '.fake()->city(),
            'city' => fake()->city(),
            'timezone' => fake()->randomElement(array_keys(Branch::timezoneOptions())),
            'address' => fake()->address(),
            'is_active' => true,
        ];
    }
}
