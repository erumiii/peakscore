<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use DatabaseTransactions;

    // Semua halaman saat ini untuk admin (peserta belum punya halaman).

    private function signInAdmin(): User
    {
        $admin = User::create([
            'username' => 'smoke-admin-' . uniqid(),
            'name' => 'Smoke Admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        return $admin;
    }

    public function test_dashboard_loads(): void
    {
        $this->signInAdmin();
        $this->get('/')->assertOk();
    }

    public function test_questions_list_loads(): void
    {
        $this->signInAdmin();
        $this->get('/questions')->assertOk();
    }

    public function test_questions_create_form_loads(): void
    {
        $this->signInAdmin();
        $this->get('/questions/add')->assertOk();
    }

    public function test_questions_edit_form_loads(): void
    {
        $this->signInAdmin();
        $soal = \App\Models\Soal::query()->first();
        $this->assertNotNull($soal, 'Tabel soal kosong - butuh minimal 1 row (seed)');
        $this->get('/questions/' . $soal->soalId . '/edit')->assertOk();
    }

    public function test_questions_edit_missing_id_returns_404(): void
    {
        $this->signInAdmin();
        $this->get('/questions/99999999/edit')->assertStatus(404);
    }

    public function test_store_stores_question_and_cleanup(): void
    {
        $this->signInAdmin();
        $marker = 'SMOKE-TEST-' . uniqid();
        $response = $this->post('/questions/add', [
            'kategori' => 'Logic',
            'isiSoal' => $marker,
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'A',
        ]);
        $response->assertRedirect(route('questions.index'));
        $row = \App\Models\Soal::where('isiSoal', $marker)->first();
        $this->assertNotNull($row, 'Soal tidak tersimpan ke DB');
        $this->assertSame('Logic', $row->kategori);
        $row->delete();
    }

    public function test_store_rejects_missing_fields(): void
    {
        $this->signInAdmin();
        $this->post('/questions/add', [])->assertSessionHasErrors([
            'kategori', 'isiSoal', 'opsiA', 'opsiB', 'opsiC', 'opsiD', 'jawabanBenar',
        ]);
    }

    public function test_update_and_delete_roundtrip(): void
    {
        $this->signInAdmin();
        $marker = 'SMOKE-TEST-' . uniqid();
        $row = \App\Models\Soal::create([
            'isiSoal' => $marker, 'kategori' => 'Verbal',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'B',
        ]);
        $this->put('/questions/' . $row->soalId, [
            'kategori' => 'Logic',
            'isiSoal' => $marker . '-UPD',
            'opsiA' => 'a2', 'opsiB' => 'b2', 'opsiC' => 'c2', 'opsiD' => 'd2',
            'jawabanBenar' => 'C',
        ])->assertRedirect(route('questions.index'));
        $this->assertDatabaseHas('soal', ['soalId' => $row->soalId, 'isiSoal' => $marker . '-UPD']);

        $this->delete('/questions/' . $row->soalId)->assertRedirect(route('questions.index'));
        $this->assertDatabaseMissing('soal', ['soalId' => $row->soalId]);
    }

    public function test_delete_prevents_deleting_question_with_recorded_answers(): void
    {
        $this->signInAdmin();
        $marker = 'SMOKE-TEST-WITH-ANSWER-' . uniqid();
        $row = \App\Models\Soal::create([
            'isiSoal' => $marker, 'kategori' => 'Verbal',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'B',
        ]);
        $peserta = \App\Models\User::create([
            'username' => 'peserta-' . uniqid(),
            'name' => 'Peserta Test',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'peserta',
        ]);
        $jadwal = \App\Models\Jadwal::create([
            'judul' => 'Jadwal Test ' . uniqid(),
            'mulai' => now()->subDay(),
            'selesai' => now()->addDay(),
        ]);
        \App\Models\Jawaban::create([
            'jadwalId' => $jadwal->id,
            'userId' => $peserta->id,
            'soalId' => $row->soalId,
            'opsiDipilih' => 'B',
        ]);

        $this->delete('/questions/' . $row->soalId)
            ->assertRedirect(route('questions.index'))
            ->assertSessionHas('error', 'Question has recorded participant answers and cannot be deleted.');

        $this->assertDatabaseHas('soal', ['soalId' => $row->soalId]);
    }

    public function test_search_filter_works(): void
    {
        $this->signInAdmin();
        $marker = 'SMOKE-SEARCH-' . uniqid();
        $row = \App\Models\Soal::create([
            'isiSoal' => $marker, 'kategori' => 'Verbal',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'A',
        ]);
        $this->get('/questions?search=' . $marker)->assertOk()->assertSee($marker);
        $this->get('/questions?category=Logic')->assertOk();
        $row->delete();
    }

    public function test_store_rejects_invalid_jawaban_benar(): void
    {
        $this->signInAdmin();
        $this->post('/questions/add', [
            'kategori' => 'Logic',
            'isiSoal' => 'SMOKE-INVALID-' . uniqid(),
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'E',
        ])->assertSessionHasErrors('jawabanBenar');
    }

    public function test_store_rejects_invalid_kategori(): void
    {
        $this->signInAdmin();
        $this->post('/questions/add', [
            'kategori' => 'Matematika',
            'isiSoal' => 'SMOKE-INVALID-' . uniqid(),
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'A',
        ])->assertSessionHasErrors('kategori');
    }

    public function test_update_rejects_invalid_jawaban_benar(): void
    {
        $this->signInAdmin();
        $row = \App\Models\Soal::create([
            'isiSoal' => 'SMOKE-UPD-INVALID-' . uniqid(), 'kategori' => 'Verbal',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'B',
        ]);
        $this->put('/questions/' . $row->soalId, [
            'kategori' => 'Logic',
            'isiSoal' => 'SMOKE-UPD-INVALID-' . uniqid(),
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'X',
        ])->assertSessionHasErrors('jawabanBenar');
    }
}
