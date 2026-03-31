<?php

namespace Database\Factories;

use App\Models\Branch;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Outlet>
 */
class OutletFactory extends Factory
{
    protected $model = Outlet::class;

    public function definition(): array
    {
        $status = fake()->randomElement(['prospek', 'pending', 'active', 'inactive']);

        return [
            'branch_id' => Branch::factory(),
            'name' => 'Outlet '.fake()->company(),
            'address' => fake()->streetAddress(),
            'district' => fake()->citySuffix(),
            'city' => fake()->city(),
            'category' => fake()->randomElement(['salon', 'toko', 'barbershop', 'lainnya']),
            'outlet_status' => $status,
            'official_kode' => $status === 'active' ? strtoupper(fake()->unique()->bothify('OFF-####')) : null,
            'verified_by' => $status === 'active' ? User::factory() : null,
            'verified_at' => $status === 'active' ? now() : null,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
