<?php

namespace Database\Factories;

use App\Models\MasterDinas;
use Illuminate\Database\Eloquent\Factories\Factory;

class MasterDinasFactory extends Factory
{
    protected $model = MasterDinas::class;

    public function definition()
    {
        return [
            'dinas_id' => 'DINAS-' . $this->faker->unique()->numberBetween(1000, 9999),
            'nama_dinas' => $this->faker->company(),
            'alamat' => $this->faker->address(),
            'no_telp' => $this->faker->phoneNumber(),
        ];
    }
}
