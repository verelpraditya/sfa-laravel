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
        $type = fake()->randomElement(['prospek', 'noo', 'pelanggan_lama']);
        $verified = fake()->boolean(35);

        return [
            'branch_id' => Branch::factory(),
            'name' => 'Outlet '.fake()->company(),
            'address' => fake()->streetAddress(),
            'district' => fake()->citySuffix(),
            'city' => fake()->city(),
            'category' => fake()->randomElement(['salon', 'toko', 'barbershop', 'lainnya']),
            'outlet_type' => $type,
            'outlet_status' => fake()->boolean(15) ? 'inactive' : 'active',
            'official_kode' => $type === 'pelanggan_lama' ? strtoupper(fake()->unique()->bothify('OFF-####')) : null,
            'verification_status' => $type === 'prospek' ? null : ($verified ? 'verified' : 'pending'),
            'verified_by' => $type !== 'prospek' && $verified ? User::factory() : null,
            'verified_at' => $type !== 'prospek' && $verified ? now() : null,
            'created_by' => User::factory(),
            'updated_by' => null,
        ];
    }
}
