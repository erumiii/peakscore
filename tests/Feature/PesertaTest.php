<?php

namespace Tests\Feature;

use App\Models\Hasil;
use App\Models\Jadwal;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PesertaTest extends TestCase
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

    private function jadwal(array $overrides = []): Jadwal
    {
        return Jadwal::create(array_merge([
            'judul' => 'TPA Gelombang Peserta',
            'deskripsi' => null,
            'mulai' => now()->subDay(),
            'selesai' => now()->addDay(),
        ], $overrides));
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

    public function test_peserta_dashboard_shows_jadwal(): void
    {
        $peserta = $this->user('peserta');
        $this->jadwal();
        $this->actingAs($peserta)->get('/')
            ->assertOk()
            ->assertSee('TPA Gelombang Peserta');
    }

    public function test_admin_dashboard_unchanged(): void
    {
        $admin = $this->user('admin');
        $this->actingAs($admin)->get('/')->assertOk()->assertSee('Dashboard');
    }

    public function test_test_page_locked_before_jadwal(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal(['mulai' => now()->addDay(), 'selesai' => now()->addDays(2)]);
        $this->actingAs($peserta)->get("/test/{$jadwal->id}")->assertRedirect('/');
    }

    public function test_test_page_locked_after_jadwal(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal(['mulai' => now()->subDays(2), 'selesai' => now()->subDay()]);
        $this->actingAs($peserta)->get("/test/{$jadwal->id}")->assertRedirect('/');
    }

    public function test_test_page_open_during_jadwal(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $soal = $this->soal('Verbal', 'A');
        $this->actingAs($peserta)->get("/test/{$jadwal->id}")
            ->assertOk()
            ->assertSee($soal->isiSoal);
    }

    public function test_submit_creates_hasil_with_scores(): void
    {
        // Isolasi: hapus soal seed supaya perhitungan skor deterministik (rollback via DatabaseTransactions)
        Soal::query()->delete();

        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();

        $v1 = $this->soal('Verbal', 'A');
        $v2 = $this->soal('Verbal', 'B');
        $n1 = $this->soal('Numeric', 'B');
        $n2 = $this->soal('Numeric', 'B');

        // Verbal: 1 dari 2 benar -> 50% x 250 = 125. Numeric: 2 dari 2 -> 250.
        $this->actingAs($peserta)->post("/test/{$jadwal->id}", [
            'jawaban' => [
                $v1->soalId => 'A',
                $v2->soalId => 'C',
                $n1->soalId => 'B',
                $n2->soalId => 'B',
            ],
        ])->assertRedirect('/');

        $hasil = Hasil::where('userId', $peserta->id)->where('jadwalId', $jadwal->id)->first();
        $this->assertNotNull($hasil, 'Hasil tidak tersimpan');
        $this->assertSame(125, $hasil->skorVerbal);
        $this->assertSame(250, $hasil->skorNumerik);
        $this->assertNull($hasil->skorLogika);
        $this->assertNull($hasil->skorSpasial);
        $this->assertNull($hasil->diterbitkanPada);
    }

    public function test_submit_rejects_incomplete_answers(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $v1 = $this->soal('Verbal', 'A');
        $v2 = $this->soal('Verbal', 'A');

        $this->actingAs($peserta)->post("/test/{$jadwal->id}", [
            'jawaban' => [$v1->soalId => 'A'],
        ])->assertRedirect();

        $this->assertDatabaseMissing('hasil', ['userId' => $peserta->id, 'jadwalId' => $jadwal->id]);
    }

    public function test_submit_rejected_when_already_done(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $v1 = $this->soal('Verbal', 'A');
        Hasil::create(['jadwalId' => $jadwal->id, 'userId' => $peserta->id]);

        $this->actingAs($peserta)->post("/test/{$jadwal->id}", [
            'jawaban' => [$v1->soalId => 'A'],
        ])->assertRedirect('/');

        $this->assertSame(1, Hasil::where('userId', $peserta->id)->where('jadwalId', $jadwal->id)->count());
    }

    public function test_submit_rejected_after_jadwal(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal(['mulai' => now()->subDays(2), 'selesai' => now()->subDay()]);
        $v1 = $this->soal('Verbal', 'A');

        $this->actingAs($peserta)->post("/test/{$jadwal->id}", [
            'jawaban' => [$v1->soalId => 'A'],
        ])->assertRedirect('/');

        $this->assertDatabaseMissing('hasil', ['userId' => $peserta->id, 'jadwalId' => $jadwal->id]);
    }

    public function test_my_results_hidden_before_publish(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        Hasil::create([
            'jadwalId' => $jadwal->id, 'userId' => $peserta->id,
            'skorVerbal' => 125, 'skorNumerik' => 250,
        ]);

        // Asersi struktural - angka polos bentrok dengan path data SVG di sidebar
        $this->actingAs($peserta)->get('/my-results')
            ->assertOk()
            ->assertSee('TPA Gelombang Peserta')
            ->assertSee('Awaiting transcript')
            ->assertDontSee('/ 1000')
            ->assertDontSee('Passed');
    }

    public function test_my_results_visible_after_publish(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        Hasil::create([
            'jadwalId' => $jadwal->id, 'userId' => $peserta->id,
            'skorVerbal' => 125, 'skorNumerik' => 250,
            'diterbitkanPada' => now(),
        ]);

        $this->actingAs($peserta)->get('/my-results')
            ->assertOk()
            ->assertSee('/ 1000')
            ->assertSee('Not Passed')
            ->assertSee('Published')
            ->assertDontSee('Awaiting');
    }

    public function test_admin_dashboard_shows_recent_activity(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        Hasil::create(['jadwalId' => $jadwal->id, 'userId' => $peserta->id]);

        $this->actingAs($admin)->get('/')
            ->assertOk()
            ->assertSee($peserta->name)
            ->assertSee($jadwal->judul)
            ->assertDontSee('No activity yet');
    }

    public function test_dashboard_empty_activity_state(): void
    {
        // Isolasi: hapus hasil di DB live (rollback via DatabaseTransactions)
        Hasil::query()->delete();
        $admin = $this->user('admin');

        $this->actingAs($admin)->get('/')
            ->assertOk()
            ->assertSee('No activity yet');
    }
}
