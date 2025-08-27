<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\MasterDinas;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample dinas
        $dinasData = [
            [
                'dinas_id' => 'DIN-' . Str::random(8),
                'nama_dinas' => 'Dinas Kesehatan Kota Pontianak',
                'no_telp' => '0561-123456',
                'alamat' => 'Jl. Ahmad Yani No. 1, Pontianak'
            ],
            [
                'dinas_id' => 'DIN-' . Str::random(8),
                'nama_dinas' => 'Dinas Pendidikan Kota Pontianak',
                'no_telp' => '0561-234567',
                'alamat' => 'Jl. Gajah Mada No. 2, Pontianak'
            ],
            [
                'dinas_id' => 'DIN-' . Str::random(8),
                'nama_dinas' => 'Dinas Pekerjaan Umum Kota Pontianak',
                'no_telp' => '0561-345678',
                'alamat' => 'Jl. Sultan Syahrir No. 3, Pontianak'
            ]
        ];

        foreach ($dinasData as $dinas) {
            MasterDinas::create($dinas);
        }

        // Create sample agenda
        $agendaData = [
            [
                'dinas_id' => $dinasData[0]['dinas_id'],
                'nama_agenda' => 'Rapat Mengatasi Stunting di Kota Pontianak',
                'tanggal_agenda' => now()->addDays(2)->format('Y-m-d'),
                'link_acara' => 'https://meet.google.com/test-agenda',
                'nama_koordinator' => 'Administrator'
            ],
            [
                'dinas_id' => $dinasData[1]['dinas_id'],
                'nama_agenda' => 'Workshop Pendidikan Karakter',
                'tanggal_agenda' => now()->addDays(5)->format('Y-m-d'),
                'link_acara' => 'https://meet.google.com/workshop-pendidikan',
                'nama_koordinator' => 'Administrator'
            ]
        ];

        foreach ($agendaData as $agenda) {
            Agenda::create($agenda);
        }

        $this->command->info('Test data created successfully!');
        $this->command->info('Dinas: ' . count($dinasData) . ' records');
        $this->command->info('Agenda: ' . count($agendaData) . ' records');
    }
}
