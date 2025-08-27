<?php

namespace Database\Factories;

use App\Models\Agenda;
use App\Models\MasterDinas;
use Illuminate\Database\Eloquent\Factories\Factory;

class AgendaFactory extends Factory
{
    protected $model = Agenda::class;

    public function definition()
    {
        return [
            'dinas_id' => function () {
                return MasterDinas::factory()->create()->dinas_id;
            },
            'nama_agenda' => $this->faker->sentence(3),
            'tanggal_agenda' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
            'link_acara' => $this->faker->url(),
            'nama_koordinator' => $this->faker->name(),
        ];
    }
}
