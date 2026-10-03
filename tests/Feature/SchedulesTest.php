<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SchedulesTest extends TestCase
{
    use DatabaseTransactions;

    private function user(string $role): User
    {
        return User::create([
            'username' => 'test-'.$role.'-'.uniqid(),
            'name' => ucfirst($role).' Test',
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    private function data(array $overrides = []): array
    {
        return array_merge([
            'judul' => 'TPA Gelombang 1',
            'deskripsi' => 'Tes perdana',
            'mulai' => '2026-10-10 08:00:00',
            'selesai' => '2026-10-10 10:00:00',
        ], $overrides);
    }

    private function bankSoalCukup(): void
    {
        foreach (['Verbal', 'Numeric', 'Logic', 'Spatial'] as $kategori) {
            Soal::create([
                'isiSoal' => 'Soal '.$kategori.' '.uniqid(),
                'kategori' => $kategori,
                'opsiA' => 'A', 'opsiB' => 'B', 'opsiC' => 'C', 'opsiD' => 'D',
                'jawabanBenar' => 'A',
            ]);
        }
    }

    public function test_guest_redirects_to_login(): void
    {
        $this->get('/schedules')->assertRedirect(route('login'));
    }

    public function test_peserta_forbidden(): void
    {
        $peserta = $this->user('peserta');
        $this->actingAs($peserta)->get('/schedules')->assertStatus(403);
    }

    public function test_admin_index_shows_jadwal(): void
    {
        $admin = $this->user('admin');
        Jadwal::create($this->data());
        $this->actingAs($admin)->get('/schedules')
            ->assertOk()
            ->assertSee('TPA Gelombang 1');
    }

    public function test_store_creates_jadwal(): void
    {
        $admin = $this->user('admin');
        $this->bankSoalCukup();
        $this->actingAs($admin)->post('/schedules', $this->data())
            ->assertRedirect(route('schedules.index'));
        $this->assertDatabaseHas('jadwal', ['judul' => 'TPA Gelombang 1']);
    }

    public function test_store_requires_fields(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->post('/schedules', [])
            ->assertSessionHasErrors(['judul', 'mulai', 'selesai']);
    }

    public function test_store_rejects_selesai_before_mulai(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->post('/schedules', $this->data([
            'selesai' => '2026-10-10 07:00:00',
        ]))->assertSessionHasErrors('selesai');
        $this->assertDatabaseMissing('jadwal', ['judul' => 'TPA Gelombang 1']);
    }

    public function test_update_modifies_jadwal(): void
    {
        $admin = $this->user('admin');
        $this->bankSoalCukup();
        $jadwal = Jadwal::create($this->data());
        $this->actingAs($admin)->put("/schedules/{$jadwal->id}", $this->data([
            'judul' => 'TPA Gelombang 2',
        ]))->assertRedirect(route('schedules.index'));
        $this->assertDatabaseHas('jadwal', ['judul' => 'TPA Gelombang 2']);
    }

    public function test_destroy_deletes_jadwal(): void
    {
        $admin = $this->user('admin');
        $jadwal = Jadwal::create($this->data());
        $this->actingAs($admin)->delete("/schedules/{$jadwal->id}")
            ->assertRedirect(route('schedules.index'));
        $this->assertDatabaseMissing('jadwal', ['id' => $jadwal->id]);
    }
}
