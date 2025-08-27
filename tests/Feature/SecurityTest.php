<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_cannot_access_admin_pages()
    {
        $user = User::factory()->create([
            'username' => 'regularuser',
            'password' => bcrypt('password123'),
            'role' => 'user'
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_admin_pages()
    {
        $user = User::factory()->create([
            'username' => 'adminuser',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $response = $this->actingAs($user)->get('/admin/dashboard');
        $response->assertOk();
    }

    public function test_public_agenda_route_accessible()
    {
        // Create test agenda data
        $agenda = \App\Models\Agenda::factory()->create([
            'nama_agenda' => 'Test Agenda',
            'tanggal_agenda' => now()->addDays(1),
            'link_acara' => 'https://example.com/test'
        ]);

        $response = $this->get('/agenda');
        $response->assertOk();
    }

    public function test_admin_routes_require_authentication()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }
}
