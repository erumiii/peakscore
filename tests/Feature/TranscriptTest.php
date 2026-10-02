<?php

namespace Tests\Feature;

use App\Models\Hasil;
use App\Models\Jadwal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class TranscriptTest extends TestCase
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

    private function jadwal(): Jadwal
    {
        return Jadwal::create([
            'judul' => 'TPA Gelombang Transkrip',
            'deskripsi' => null,
            'mulai' => now()->subDay(),
            'selesai' => now()->addDay(),
        ]);
    }

    public function test_guest_redirected_to_login(): void
    {
        $this->get('/test-results/1/transcript')->assertRedirect(route('login'));
    }

    public function test_peserta_views_own_published_transcript(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $hasil = Hasil::create([
            'jadwalId' => $jadwal->id, 'userId' => $peserta->id,
            'skorVerbal' => 200, 'skorNumerik' => 180,
            'skorLogika' => 190, 'skorSpasial' => 160,
            'diterbitkanPada' => now(),
        ]);

        // Asersi struktural - angka polos bentrok dengan path data SVG di sidebar
        $this->actingAs($peserta)->get("/test-results/{$hasil->id}/transcript")
            ->assertOk()
            ->assertSee('/ 1000')
            ->assertSee('Lulus')
            ->assertSee('TPA Gelombang Transkrip');
    }

    public function test_transcript_shows_belum_lulus_below_passing_grade(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $hasil = Hasil::create([
            'jadwalId' => $jadwal->id, 'userId' => $peserta->id,
            'skorVerbal' => 125, 'skorNumerik' => 250,
            'skorLogika' => 0, 'skorSpasial' => 0,
            'diterbitkanPada' => now(),
        ]);

        $this->actingAs($peserta)->get("/test-results/{$hasil->id}/transcript")
            ->assertOk()
            ->assertSee('Belum Lulus');
    }

    public function test_transcript_hidden_before_publish(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $hasil = Hasil::create([
            'jadwalId' => $jadwal->id, 'userId' => $peserta->id,
            'skorVerbal' => 200, 'skorNumerik' => 200,
            'skorLogika' => 200, 'skorSpasial' => 200,
        ]);

        $this->actingAs($peserta)->get("/test-results/{$hasil->id}/transcript")
            ->assertStatus(403);
    }

    public function test_peserta_cannot_view_others_transcript(): void
    {
        $peserta = $this->user('peserta');
        $other = $this->user('peserta');
        $jadwal = $this->jadwal();
        $hasil = Hasil::create([
            'jadwalId' => $jadwal->id, 'userId' => $other->id,
            'skorVerbal' => 200, 'skorNumerik' => 200,
            'skorLogika' => 200, 'skorSpasial' => 200,
            'diterbitkanPada' => now(),
        ]);

        $this->actingAs($peserta)->get("/test-results/{$hasil->id}/transcript")
            ->assertStatus(403);
    }

    public function test_admin_views_any_published_transcript(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $hasil = Hasil::create([
            'jadwalId' => $jadwal->id, 'userId' => $peserta->id,
            'skorVerbal' => 250, 'skorNumerik' => 250,
            'skorLogika' => 250, 'skorSpasial' => 250,
            'diterbitkanPada' => now(),
        ]);

        $this->actingAs($admin)->get("/test-results/{$hasil->id}/transcript")
            ->assertOk()
            ->assertSee('/ 1000')
            ->assertSee('Lulus')
            ->assertDontSee('Belum Lulus');
    }
}
