<?php

namespace Tests\Feature;

use App\Models\Soal;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SoalSeederTest extends TestCase
{
    use DatabaseTransactions;

    public function test_soal_seeder_restores_rows_from_backup()
    {
        $backup = database_path('seeders/soal-backup.json');

        if (! is_file($backup)) {
            $this->markTestSkipped('soal-backup.json tidak ada (file tidak di-track git)');
        }

        Soal::query()->delete();

        $this->seed(\Database\Seeders\SoalSeeder::class);

        $expected = count(json_decode(file_get_contents($backup), true));
        $this->assertSame($expected, Soal::count());
    }
}
