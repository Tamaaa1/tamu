<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Agenda;
use App\Models\MasterDinas;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AgendaSeeder extends Seeder
{
    public function run()
    {
        $dinasIds = MasterDinas::pluck('dinas_id')->toArray();

        if (empty($dinasIds)) {
            $this->command->info('No MasterDinas data found. Please seed MasterDinas first.');
            return;
        }

        for ($i = 1; $i <= 30; $i++) {
            $date = Carbon::today()->addDays(rand(-10, 30));
            $dinasId = $dinasIds[array_rand($dinasIds)];

            Agenda::create([
                'dinas_id' => $dinasId,
                'nama_agenda' => 'Agenda Sample ' . $i,
                'tanggal_agenda' => $date->format('Y-m-d'),
                'nama_koordinator' => 'admin', // assuming admin user exists
                'link_acara' => null,
                'link_active' => (bool)rand(0, 1),
                'unique_token' => Str::random(16),
            ]);
        }
    }
}
