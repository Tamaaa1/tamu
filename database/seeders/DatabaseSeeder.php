<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\MasterDinas;
use App\Models\Agenda;
use App\Models\AgendaDetail;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create single admin user
        $admin = User::factory()->create([
            'username' => 'admin',
            'name' => 'Administrator',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create sample dinas
        $dinas1 = MasterDinas::create([
            'dinas_id' => 'D001',
            'nama_dinas' => 'Dinas Komunikasi dan Informatika',
            'no_telp' => '021-1234567',
            'alamat' => 'Jl. Sudirman No. 123, Jakarta Pusat',
        ]);

        $dinas2 = MasterDinas::create([
            'dinas_id' => 'D002',
            'nama_dinas' => 'Dinas Pendidikan',
            'no_telp' => '021-2345678',
            'alamat' => 'Jl. Thamrin No. 456, Jakarta Pusat',
        ]);

        // Create sample agendas
        $agenda1 = Agenda::create([
            'dinas_id' => 'D001',
            'nama_agenda' => 'Workshop Digital Marketing',
            'tanggal_agenda' => '2025-01-15',
            'nama_koordinator' => $admin->username,
            'link_acara' => 'https://meet.google.com/abc-defg-hij',
        ]);

        $agenda2 = Agenda::create([
            'dinas_id' => 'D002',
            'nama_agenda' => 'Pelatihan Guru IT',
            'tanggal_agenda' => '2025-01-20',
            'nama_koordinator' => $admin->username,
            'link_acara' => 'https://meet.google.com/xyz-uvw-rst',
        ]);

        // Create sample agenda details
        AgendaDetail::create([
            'agenda_id' => $agenda1->id,
            'nama' => 'Ahmad Fauzi',
            'dinas_id' => 'D001',
            'jabatan' => 'Staff IT',
            'no_hp' => '081234567890',
        ]);

        AgendaDetail::create([
            'agenda_id' => $agenda1->id,
            'nama' => 'Dewi Sartika',
            'dinas_id' => 'D001',
            'jabatan' => 'Staff Marketing',
            'no_hp' => '081234567891',
        ]);

        AgendaDetail::create([
            'agenda_id' => $agenda2->id,
            'nama' => 'Rina Marlina',
            'dinas_id' => 'D002',
            'jabatan' => 'Guru SD',
            'no_hp' => '081234567892',
        ]);

        // Create additional users for testing (optional)
        User::factory(3)->create();
    }
}
