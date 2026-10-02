<?php

namespace Tests\Feature;

use App\Models\Hasil;
use App\Models\Jadwal;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UiEnhancementsTest extends TestCase
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
            'judul' => 'TPA Ui Test',
            'deskripsi' => null,
            'mulai' => now()->subDay(),
            'selesai' => now()->addDay(),
        ], $overrides));
    }

    private function soal(string $kategori): Soal
    {
        return Soal::create([
            'isiSoal' => "Soal {$kategori} " . uniqid(),
            'kategori' => $kategori,
            'opsiA' => 'A', 'opsiB' => 'B', 'opsiC' => 'C', 'opsiD' => 'D',
            'jawabanBenar' => 'A',
        ]);
    }

    private function hasil(Jadwal $jadwal, User $user, int $perKategori, bool $terbit = true): Hasil
    {
        return Hasil::create([
            'jadwalId' => $jadwal->id,
            'userId' => $user->id,
            'skorVerbal' => $perKategori,
            'skorNumerik' => $perKategori,
            'skorLogika' => $perKategori,
            'skorSpasial' => $perKategori,
            'diterbitkanPada' => $terbit ? now() : null,
        ]);
    }

    public function test_dashboard_shows_results_stat_and_category_breakdown(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $this->soal('Verbal');
        $this->soal('Verbal');
        $this->hasil($jadwal, $peserta, 200);

        $this->actingAs($admin)->get('/')
            ->assertOk()
            ->assertSee('% passed')
            ->assertSee('Questions by Category');
    }

    public function test_schedules_show_participants_count(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $this->hasil($jadwal, $peserta, 200);

        $this->actingAs($admin)->get('/schedules')
            ->assertOk()
            ->assertSee('taken');
    }

    public function test_questions_dropdown_shows_category_counts(): void
    {
        $admin = $this->user('admin');
        $this->soal('Verbal');
        $expected = Soal::where('kategori', 'Verbal')->count();

        $this->actingAs($admin)->get('/questions')
            ->assertOk()
            ->assertSee("Verbal ({$expected})");
    }

    public function test_results_show_summary_bar(): void
    {
        $admin = $this->user('admin');
        $peserta1 = $this->user('peserta');
        $peserta2 = $this->user('peserta');
        $jadwal = $this->jadwal();
        $this->hasil($jadwal, $peserta1, 200); // total 800 - lulus
        $this->hasil($jadwal, $peserta2, 100); // total 400 - tidak lulus

        $this->actingAs($admin)->get('/test-results')
            ->assertOk()
            ->assertSee('Pass Rate');
    }

    public function test_peserta_home_shows_ongoing_hero(): void
    {
        $peserta = $this->user('peserta');
        $this->jadwal(['judul' => 'TPA Hero ' . uniqid()]);

        $this->actingAs($peserta)->get('/')
            ->assertOk()
            ->assertSee('Open Now')
            ->assertSee('Time left');
    }

    public function test_peserta_home_hides_hero_without_ongoing(): void
    {
        // Data live bisa memuat jadwal yang sedang berlangsung - kosongkan agar precondition "tanpa ongoing" terjamin (rollback otomatis).
        Hasil::query()->delete();
        Jadwal::query()->delete();

        $peserta = $this->user('peserta');
        $this->jadwal(['mulai' => now()->addDay(), 'selesai' => now()->addDays(2)]);

        $this->actingAs($peserta)->get('/')
            ->assertOk()
            ->assertDontSee('Open Now');
    }

    public function test_my_results_show_stat_cards(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $this->hasil($jadwal, $peserta, 200);

        $this->actingAs($peserta)->get('/my-results')
            ->assertOk()
            ->assertSee('Tests Taken')
            ->assertSee('Best Score');
    }

    public function test_test_page_shows_progress_counter(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = $this->jadwal();
        $this->soal('Verbal');

        $this->actingAs($peserta)->get("/test/{$jadwal->id}")
            ->assertOk()
            ->assertSee('answered');
    }

    public function test_pagination_uses_boxed_links(): void
    {
        $admin = $this->user('admin');
        for ($i = 0; $i < 11; $i++) {
            $this->user('peserta');
        }

        $this->actingAs($admin)->get('/participants')
            ->assertOk()
            ->assertSee('?page=2');
    }

    public function test_sidebar_is_sticky_on_both_layouts(): void
    {
        $admin = $this->user('admin');
        $peserta = $this->user('peserta');

        $this->actingAs($admin)->get('/questions')
            ->assertOk()
            ->assertSee('sticky top-0 h-screen');

        $this->actingAs($peserta)->get('/')
            ->assertOk()
            ->assertSee('sticky top-0 h-screen');
    }
}
