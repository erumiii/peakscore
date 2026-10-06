<?php

namespace Database\Seeders;

use App\Models\Soal;
use Illuminate\Database\Seeder;

class SoalSeeder extends Seeder
{
    public function run(): void
    {
        $backup = __DIR__ . '/soal-backup.json';
        if (! is_file($backup)) {
            return;
        }

        foreach (json_decode(file_get_contents($backup), true) as $row) {
            unset($row['soalId']);
            Soal::create($row);
        }
    }
}
