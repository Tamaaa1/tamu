<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'name' => 'Administrator'
        ]);

        // Create regular user (for testing)
        User::create([
            'username' => 'user',
            'password' => Hash::make('user123'),
            'role' => 'user',
            'name' => 'Regular User'
        ]);

        $this->command->info('Admin user created:');
        $this->command->info('Username: admin');
        $this->command->info('Password: admin123');
        $this->command->info('');
        $this->command->info('Regular user created:');
        $this->command->info('Username: user');
        $this->command->info('Password: user123');
    }
}
