<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use DatabaseTransactions;

    private function user(string $role): User
    {
        return User::create([
            'username' => 'test-' . $role . '-' . uniqid(),
            'name' => ucfirst($role) . ' Test',
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    public function test_guest_redirects_to_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_login_page_loads(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_login_page_uses_design_system(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertSee('--color-canvas: #F7F5F2')
            ->assertSee('border-canvas')
            ->assertSee('bg-ink')
            ->assertDontSee('bg-blue-600')
            ->assertDontSee('--color-brand-light');
    }

    public function test_login_requires_fields(): void
    {
        $this->post('/login', [])->assertSessionHasErrors(['username', 'password']);
    }

    public function test_login_fails_wrong_password(): void
    {
        $admin = $this->user('admin');
        $this->post('/login', ['username' => $admin->username, 'password' => 'wrong'])
            ->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_throttles_after_five_attempts(): void
    {
        $admin = $this->user('admin');
        foreach (range(1, 5) as $i) {
            $this->post('/login', ['username' => $admin->username, 'password' => 'wrong']);
        }
        // Percobaan ke-6 dengan password benar pun harus ditolak (sedang throttle)
        $this->post('/login', ['username' => $admin->username, 'password' => 'password'])
            ->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_login_success_admin(): void
    {
        $admin = $this->user('admin');
        $this->post('/login', ['username' => $admin->username, 'password' => 'password'])
            ->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    public function test_admin_access_questions(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->get('/questions')->assertOk();
    }

    public function test_peserta_forbidden_questions(): void
    {
        $peserta = $this->user('peserta');
        $this->actingAs($peserta)->get('/questions')->assertStatus(403);
    }

    public function test_logout(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->post('/logout')->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_get_logout_removed(): void
    {
        // Route /logout kini POST-only - GET diblok Method Not Allowed
        $this->get('/logout')->assertStatus(405);
    }
}
