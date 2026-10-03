<?php

namespace Tests\Feature;

use App\Models\Jadwal;
use App\Models\Soal;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class QuestionValidationTest extends TestCase
{
    use DatabaseTransactions;

    private function admin(): User
    {
        $admin = User::create([
            'username' => 'test-admin-'.uniqid(),
            'name' => 'Admin Test',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);
        $this->actingAs($admin);

        return $admin;
    }

    private function soal(string $kategori): Soal
    {
        return Soal::create([
            'isiSoal' => 'Soal '.$kategori.' '.uniqid(),
            'kategori' => $kategori,
            'opsiA' => 'A', 'opsiB' => 'B', 'opsiC' => 'C', 'opsiD' => 'D',
            'jawabanBenar' => 'A',
        ]);
    }

    private function payload(string $judul): array
    {
        return [
            'judul' => $judul,
            'deskripsi' => null,
            'mulai' => now()->addDay()->format('Y-m-d H:i:s'),
            'selesai' => now()->addDays(2)->format('Y-m-d H:i:s'),
        ];
    }

    public function test_store_jadwal_blocked_when_bank_has_fewer_than_4_soal(): void
    {
        Soal::query()->delete();
        $this->admin();
        $this->soal('Verbal');
        $this->soal('Verbal');
        $this->soal('Logic');

        $this->post('/schedules', $this->payload('TPA Bank Kurang'))
            ->assertRedirect()
            ->assertSessionHasErrors('soal');

        $this->assertDatabaseMissing('jadwal', ['judul' => 'TPA Bank Kurang']);
    }

    public function test_store_jadwal_saved_when_bank_has_at_least_4_soal(): void
    {
        Soal::query()->delete();
        $this->admin();
        $this->soal('Verbal');
        $this->soal('Verbal');
        $this->soal('Numeric');
        $this->soal('Spatial');

        $this->post('/schedules', $this->payload('TPA Bank Lengkap'))
            ->assertRedirect(route('schedules.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('jadwal', ['judul' => 'TPA Bank Lengkap']);
    }

    public function test_update_jadwal_blocked_when_bank_has_fewer_than_4_soal(): void
    {
        Soal::query()->delete();
        $this->admin();
        $jadwal = Jadwal::create($this->payload('TPA Lama'));
        $this->soal('Verbal');

        $this->put('/schedules/'.$jadwal->id, $this->payload('TPA Baru'))
            ->assertRedirect()
            ->assertSessionHasErrors('soal');

        $this->assertSame('TPA Lama', $jadwal->fresh()->judul);
    }
}
