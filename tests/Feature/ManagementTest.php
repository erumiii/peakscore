<?php

namespace Tests\Feature;

use App\Models\Hasil;
use App\Models\Jadwal;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ManagementTest extends TestCase
{
    use DatabaseTransactions;

    private function user(string $role, string $name = null): User
    {
        return User::create([
            'username' => 'test-' . $role . '-' . uniqid(),
            'name' => $name ?? ucfirst($role) . ' Test',
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    private function jadwal(): Jadwal
    {
        return Jadwal::create([
            'judul' => 'TPA Gelombang Audit',
            'deskripsi' => null,
            'mulai' => now()->subDay(),
            'selesai' => now()->addDay(),
        ]);
    }

    private function soal(string $kategori, string $jawaban): Soal
    {
        return Soal::create([
            'isiSoal' => "Soal {$kategori} " . uniqid(),
            'kategori' => $kategori,
            'opsiA' => 'A', 'opsiB' => 'B', 'opsiC' => 'C', 'opsiD' => 'D',
            'jawabanBenar' => $jawaban,
        ]);
    }

    private function hasil(User $peserta): Hasil
    {
        $jadwal = Jadwal::create([
            'judul' => 'TPA Gelombang Uji',
            'deskripsi' => null,
            'mulai' => '2026-10-10 08:00:00',
            'selesai' => '2026-10-10 10:00:00',
        ]);

        return Hasil::create([
            'jadwalId' => $jadwal->id,
            'userId' => $peserta->id,
            'skorVerbal' => 80,
            'skorNumerik' => 70,
            'skorLogika' => null,
            'skorSpasial' => 90,
        ]);
    }

    public function test_guest_redirects_to_login(): void
    {
        $this->get('/participants')->assertRedirect(route('login'));
        $this->get('/test-results')->assertRedirect(route('login'));
    }

    public function test_peserta_forbidden(): void
    {
        $peserta = $this->user('peserta');
        $this->actingAs($peserta)->get('/participants')->assertStatus(403);
        $this->actingAs($peserta)->get('/test-results')->assertStatus(403);
    }

    public function test_admin_participants_shows_peserta(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta', 'Budi Peserta');
        $this->actingAs($admin)->get('/participants')
            ->assertOk()
            ->assertSee('Budi Peserta')
            ->assertSee($peserta->username);
    }

    public function test_admin_participants_search_filters(): void
    {
        $admin = $this->user('admin');
        $this->user('peserta', 'Budi Santoso');
        $this->user('peserta', 'Ani Lestari');
        $this->actingAs($admin)->get('/participants?search=Budi')
            ->assertOk()
            ->assertSee('Budi Santoso')
            ->assertDontSee('Ani Lestari');
    }

    public function test_admin_results_shows_hasil(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta', 'Budi Peserta');
        $this->hasil($peserta);
        $this->actingAs($admin)->get('/test-results')
            ->assertOk()
            ->assertSee('Budi Peserta')
            ->assertSee('TPA Gelombang Uji');
    }

    public function test_publish_sets_diterbitkan_pada(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta');
        $hasil = $this->hasil($peserta);
        $this->assertNull($hasil->fresh()->diterbitkanPada);

        $this->actingAs($admin)->post("/test-results/{$hasil->id}/publish")
            ->assertRedirect(route('results.index'));

        $this->assertNotNull($hasil->fresh()->diterbitkanPada);
    }

    public function test_publish_missing_id_returns_404(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->post('/test-results/99999999/publish')->assertStatus(404);
    }

    public function test_submit_records_jawaban(): void
    {
        Soal::query()->delete();
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $v1 = $this->soal('Verbal', 'A');
        $v2 = $this->soal('Verbal', 'B');

        $this->actingAs($peserta)->post("/test/{$jadwal->id}", [
            'jawaban' => [$v1->soalId => 'A', $v2->soalId => 'C'],
        ])->assertRedirect('/');

        $this->assertDatabaseHas('jawaban', ['userId' => $peserta->id, 'soalId' => $v1->soalId, 'opsiDipilih' => 'A']);
        $this->assertDatabaseHas('jawaban', ['userId' => $peserta->id, 'soalId' => $v2->soalId, 'opsiDipilih' => 'C']);
        $this->assertDatabaseCount('jawaban', 2);
    }

    public function test_admin_can_view_jawaban_detail(): void
    {
        Soal::query()->delete();
        $admin = $this->user('admin');
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $v1 = $this->soal('Verbal', 'A');
        $hasil = Hasil::create(['jadwalId' => $jadwal->id, 'userId' => $peserta->id, 'skorVerbal' => 250]);
        DB::table('jawaban')->insert([
            'jadwalId' => $jadwal->id, 'userId' => $peserta->id, 'soalId' => $v1->soalId,
            'opsiDipilih' => 'A', 'created_at' => now(), 'updated_at' => now(),
        ]);

        $this->actingAs($admin)->get("/test-results/{$hasil->id}")
            ->assertOk()
            ->assertSee($v1->isiSoal)
            ->assertSee('Participant Answer')
            ->assertSee('Correct');
    }

    public function test_admin_can_register_peserta(): void
    {
        $admin = $this->user('admin');
        $username = 'peserta-baru-' . uniqid();

        $this->actingAs($admin)->post('/participants', [
            'name' => 'Peserta Baru',
            'username' => $username,
            'password' => 'rahasia123',
        ])->assertRedirect(route('participants.index'));

        $row = User::where('username', $username)->first();
        $this->assertNotNull($row, 'Peserta hasil registrasi harus tersimpan');
        $this->assertSame('peserta', $row->role);
        $this->assertSame('Peserta Baru', $row->name);
        $this->assertNotSame('rahasia123', $row->password, 'Password tidak boleh plaintext');
        $this->assertTrue(Hash::check('rahasia123', $row->password));
    }

    public function test_register_rejects_duplicate_username(): void
    {
        $admin = $this->user('admin');
        $existing = $this->user('peserta');

        $this->actingAs($admin)->post('/participants', [
            'name' => 'Duplikat',
            'username' => $existing->username,
            'password' => 'rahasia123',
        ])->assertSessionHasErrors('username');
    }

    public function test_register_rejects_missing_fields(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->post('/participants', [])
            ->assertSessionHasErrors(['name', 'username', 'password']);
    }

    public function test_register_requires_min_password_length(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->post('/participants', [
            'name' => 'Pendek',
            'username' => 'pendek-' . uniqid(),
            'password' => '123',
        ])->assertSessionHasErrors('password');
    }

    public function test_admin_can_update_peserta(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta', 'Nama Lama');
        $usernameBaru = 'username-baru-' . uniqid();

        // Password kosong = tetap; nama + username berubah
        $this->actingAs($admin)->put("/participants/{$peserta->id}", [
            'name' => 'Nama Baru',
            'username' => $usernameBaru,
            'password' => '',
        ])->assertRedirect(route('participants.index'));

        $row = $peserta->fresh();
        $this->assertSame('Nama Baru', $row->name);
        $this->assertSame($usernameBaru, $row->username);
        $this->assertTrue(Hash::check('password', $row->password), 'Password lama harus tetap');

        // Password diisi = berubah
        $this->actingAs($admin)->put("/participants/{$peserta->id}", [
            'name' => 'Nama Baru',
            'username' => $usernameBaru,
            'password' => 'sandi-baru-456',
        ])->assertRedirect(route('participants.index'));
        $this->assertTrue(Hash::check('sandi-baru-456', $peserta->fresh()->password));
    }

    public function test_update_rejects_username_taken_by_other(): void
    {
        $admin = $this->user('admin');
        $lain = $this->user('peserta');
        $target = $this->user('peserta');

        $this->actingAs($admin)->put("/participants/{$target->id}", [
            'name' => $target->name,
            'username' => $lain->username,
            'password' => '',
        ])->assertSessionHasErrors('username');
    }

    public function test_admin_can_delete_peserta_without_hasil(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta');

        $this->actingAs($admin)->delete("/participants/{$peserta->id}")
            ->assertRedirect(route('participants.index'));

        $this->assertDatabaseMissing('users', ['id' => $peserta->id]);
    }

    public function test_delete_blocked_when_peserta_has_riwayat_tes(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta');
        $this->hasil($peserta);

        $this->actingAs($admin)->delete("/participants/{$peserta->id}")
            ->assertRedirect(route('participants.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('users', ['id' => $peserta->id]);
        $this->assertDatabaseHas('hasil', ['userId' => $peserta->id]);
    }
}
