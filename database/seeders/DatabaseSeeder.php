<?php

namespace Database\Seeders;

use App\Models\Soal;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'username' => 'admin',
            'name' => 'Admin',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'username' => 'peserta',
            'name' => 'Peserta Contoh',
            'password' => Hash::make('password'),
            'role' => 'peserta',
        ]);

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
