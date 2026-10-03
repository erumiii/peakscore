<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['username' => config('admin.username')],
            [
                'name' => 'Admin',
                'password' => Hash::make(config('admin.password')),
                'role' => 'admin',
            ],
        );
    }
}
