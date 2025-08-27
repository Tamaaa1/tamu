<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthSecurityTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function admin_can_login_successfully()
    {
        // Create admin user
        $user = User::factory()->create([
            'username' => 'adminuser',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $response = $this->post('/login', [
            'username' => 'adminuser',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function non_admin_user_cannot_login()
    {
        // Create non-admin user
        $user = User::factory()->create([
            'username' => 'regularuser',
            'password' => bcrypt('password123'),
            'role' => 'user'
        ]);

        $response = $this->post('/login', [
            'username' => 'regularuser',
            'password' => 'password123'
        ]);

        $response->assertRedirect('/');
        $response->assertSessionHasErrors(['username' => 'Akses hanya untuk administrator.']);
        $this->assertGuest();
    }

    /** @test */
    public function login_rate_limiting_works()
    {
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post('/login', [
                'username' => 'testuser',
                'password' => 'wrongpassword'
            ]);
        }

        $response->assertStatus(429); // Too Many Requests
    }

    /** @test */
    public function authenticated_user_can_logout()
    {
        $user = User::factory()->create([
            'username' => 'testuser',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $this->actingAs($user);
        $this->assertAuthenticatedAs($user);

        $response = $this->post('/logout');
        
        $response->assertRedirect('/');
        $this->assertGuest();
    }

    /** @test */
    public function admin_routes_require_authentication()
    {
        $response = $this->get('/admin/dashboard');
        $response->assertRedirect('/login');
    }

    /** @test */
    public function public_routes_are_accessible_without_auth()
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
}
