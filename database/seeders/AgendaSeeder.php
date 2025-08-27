<?php

namespace Database\Seeders;

use App\Models\Agenda;
use App\Models\MasterDinas;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AgendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada data dinas dan user terlebih dahulu
        $dinas = MasterDinas::first();
        $user = User::first();

        if (!$dinas) {
            $dinas = MasterDinas::create([
                'nama_dinas' => 'Dinas Komunikasi dan Informatika',
                'kode_dinas' => 'KOMINFO'
            ]);
        }

        if (!$user) {
            $user = User::create([
                'name' => 'Admin',
                'username' => 'admin',
                'password' => bcrypt('password'),
                'role' => 'admin'
            ]);
        }

        // Buat data agenda contoh
        Agenda::create([
            'dinas_id' => $dinas->dinas_id,
            'nama_agenda' => 'Rapat Koordinasi Bulanan',
            'tanggal_agenda' => now()->addDays(2)->format('Y-m-d'),
            'link_acara' => 'rapat-koordinasi-bulanan',
            'nama_koordinator' => $user->name
        ]);

        Agenda::create([
            'dinas_id' => $dinas->dinas_id,
            'nama_agenda' => 'Workshop Digital Marketing',
            'tanggal_agenda' => now()->addDays(5)->format('Y-m-d'),
            'link_acara' => 'workshop-digital-marketing',
            'nama_koordinator' => $user->name
        ]);

        $this->command->info('Data agenda berhasil ditambahkan!');
    }
}
