<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AgendaDetail;
use App\Models\Agenda;
use App\Models\MasterDinas;
use Faker\Factory as Faker;

class AgendaDetailSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        $agendaIds = Agenda::pluck('id')->toArray();
        $dinasIds = MasterDinas::pluck('dinas_id')->toArray();

        if (empty($agendaIds) || empty($dinasIds)) {
            $this->command->info('No Agenda or MasterDinas data found. Please seed them first.');
            return;
        }

        for ($i = 1; $i <= 30; $i++) {
            $agendaId = $agendaIds[array_rand($agendaIds)];
            $dinasId = $dinasIds[array_rand($dinasIds)];

            AgendaDetail::create([
                'agenda_id' => $agendaId,
                'nama' => $faker->name,
                'dinas_id' => $dinasId,
                'jabatan' => $faker->jobTitle,
                'no_hp' => $faker->numerify('08##########'),
                'gambar_ttd' => null,
            ]);
        }
    }
}
