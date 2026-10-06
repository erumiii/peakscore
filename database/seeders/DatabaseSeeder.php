<?php

namespace Database\Seeders;

use App\Models\Soal;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(AdminSeeder::class);
        $this->call(SoalSeeder::class);
    }
}
