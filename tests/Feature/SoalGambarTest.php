<?php

namespace Tests\Feature;

use App\Models\Hasil;
use App\Models\Jadwal;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SoalGambarTest extends TestCase
{
    use DatabaseTransactions;

    private function signInAdmin(): User
    {
        $admin = User::create([
            'username' => 'gambar-admin-' . uniqid(),
            'name' => 'Gambar Admin',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        return $admin;
    }

    private function user(string $role): User
    {
        return User::create([
            'username' => 'test-' . $role . '-' . uniqid(),
            'name' => ucfirst($role) . ' Test',
            'password' => Hash::make('password'),
            'role' => $role,
        ]);
    }

    private function uploadPath(string $name): string
    {
        return public_path('uploads/soal/' . $name);
    }

    public function test_store_soal_with_gambar_soal(): void
    {
        $this->signInAdmin();
        $marker = 'GAMBAR-TEST-' . uniqid();
        $name = 'test-' . uniqid() . '.png';

        $this->post('/questions/add', [
            'kategori' => 'Spatial',
            'isiSoal' => $marker,
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'A',
            'gambarSoal' => UploadedFile::fake()->create($name, 10, 'image/png'),
        ])->assertRedirect(route('questions.index'));

        $row = Soal::where('isiSoal', $marker)->first();
        $this->assertNotNull($row);
        $this->assertStringStartsWith('uploads/soal/', $row->gambarSoal);
        $this->assertFileExists($this->uploadPath(basename($row->gambarSoal)));

        @unlink($this->uploadPath(basename($row->gambarSoal)));
    }

    public function test_store_soal_gambar_only_without_text(): void
    {
        $this->signInAdmin();
        $marker = 'GAMBAR-ONLY-' . uniqid();

        // Soal full-gambar: tanpa teks soal, opsi C berupa gambar
        $this->post('/questions/add', [
            'kategori' => 'Spatial',
            'isiSoal' => null,
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => null, 'opsiD' => 'd',
            'jawabanBenar' => 'C',
            'gambarSoal' => UploadedFile::fake()->create('soal.png', 10, 'image/png'),
            'gambarOpsiC' => UploadedFile::fake()->create('opsi-c.png', 10, 'image/png'),
        ])->assertRedirect(route('questions.index'));

        $row = Soal::where('kategori', 'Spatial')
            ->whereNull('isiSoal')
            ->whereNull('opsiC')
            ->orderByDesc('soalId')
            ->first();
        $this->assertNotNull($row, 'Soal tanpa teks (gambar saja) harus tersimpan');
        $this->assertStringStartsWith('uploads/soal/', $row->gambarSoal);
        $this->assertStringStartsWith('uploads/soal/', $row->gambarOpsiC);

        @unlink($this->uploadPath(basename($row->gambarSoal)));
        @unlink($this->uploadPath(basename($row->gambarOpsiC)));
    }

    public function test_store_rejects_soal_without_text_and_image(): void
    {
        $this->signInAdmin();
        $this->post('/questions/add', [
            'kategori' => 'Spatial',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'A',
        ])->assertSessionHasErrors('isiSoal');
    }

    public function test_store_rejects_option_without_text_and_image(): void
    {
        $this->signInAdmin();
        $this->post('/questions/add', [
            'kategori' => 'Spatial',
            'isiSoal' => 'Soal apa saja',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiD' => 'd',
            'jawabanBenar' => 'A',
        ])->assertSessionHasErrors('opsiC');
    }

    public function test_store_rejects_non_image_file(): void
    {
        $this->signInAdmin();
        $this->post('/questions/add', [
            'kategori' => 'Spatial',
            'isiSoal' => 'Soal dengan file bukan gambar',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'A',
            'gambarSoal' => UploadedFile::fake()->create('dokumen.pdf', 100),
        ])->assertSessionHasErrors('gambarSoal');
    }

    public function test_update_without_new_file_keeps_existing_image(): void
    {
        $this->signInAdmin();
        $marker = 'GAMBAR-KEEP-' . uniqid();
        $row = Soal::create([
            'isiSoal' => $marker, 'kategori' => 'Spatial',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'A',
            'gambarSoal' => 'uploads/soal/lama-' . uniqid() . '.png',
        ]);

        $this->put('/questions/' . $row->soalId, [
            'kategori' => 'Spatial',
            'isiSoal' => $marker . '-UPD',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'B',
        ])->assertRedirect(route('questions.index'));

        $this->assertDatabaseHas('soal', [
            'soalId' => $row->soalId,
            'isiSoal' => $marker . '-UPD',
            'gambarSoal' => $row->gambarSoal,
        ]);
    }

    public function test_peserta_sees_image_in_test_page_and_can_submit(): void
    {
        // Isolasi: hapus soal seed supaya halaman hanya memuat soal tes ini (rollback via DatabaseTransactions)
        Soal::query()->delete();
        $peserta = $this->user('peserta');
        $jadwal = Jadwal::create([
            'judul' => 'TPA Gambar',
            'deskripsi' => null,
            'mulai' => now()->subDay(),
            'selesai' => now()->addDay(),
        ]);
        $soal = Soal::create([
            'isiSoal' => null, 'kategori' => 'Spatial',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'A',
            'gambarSoal' => 'uploads/soal/soal-view-' . uniqid() . '.png',
        ]);

        $this->actingAs($peserta)->get("/test/{$jadwal->id}")
            ->assertOk()
            ->assertSee($soal->gambarSoal);

        $this->actingAs($peserta)->post("/test/{$jadwal->id}", [
            'jawaban' => [$soal->soalId => 'A'],
        ])->assertRedirect('/');

        $hasil = Hasil::where('jadwalId', $jadwal->id)->where('userId', $peserta->id)->first();
        $this->assertNotNull($hasil, 'Soal gambar harus tetap bisa dinilai');
    }

    public function test_audit_view_shows_question_image(): void
    {
        $peserta = $this->user('peserta');
        $jadwal = Jadwal::create([
            'judul' => 'TPA Gambar Audit',
            'deskripsi' => null,
            'mulai' => now()->subDay(),
            'selesai' => now()->addDay(),
        ]);
        $soal = Soal::create([
            'isiSoal' => null, 'kategori' => 'Spatial',
            'opsiA' => 'a', 'opsiB' => 'b', 'opsiC' => 'c', 'opsiD' => 'd',
            'jawabanBenar' => 'A',
            'gambarSoal' => 'uploads/soal/audit-' . uniqid() . '.png',
        ]);
        $hasil = Hasil::create([
            'jadwalId' => $jadwal->id, 'userId' => $peserta->id,
            'diterbitkanPada' => now(),
        ]);
        \App\Models\Jawaban::create([
            'jadwalId' => $jadwal->id, 'userId' => $peserta->id,
            'soalId' => $soal->soalId, 'opsiDipilih' => 'B',
        ]);

        $this->actingAs($this->user('admin'))->get("/test-results/{$hasil->id}")
            ->assertOk()
            ->assertSee($soal->gambarSoal);
    }
}
