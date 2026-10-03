<?php

namespace Database\Seeders;

use App\Models\Soal;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminSeeder::class);

        // Restore data soal dari backup pra-migrate:fresh
        $backup = __DIR__ . '/soal-backup.json';
        if (is_file($backup)) {
            foreach (json_decode(file_get_contents($backup), true) as $row) {
                unset($row['soalId']);
                Soal::create($row);
            }
        }
    }
}
