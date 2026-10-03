<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set([
            'admin.username' => 'admin',
            'admin.password' => 'password',
        ]);
    }

    public function test_admin_seeder_creates_admin_account()
    {
        // Pastikan keadaan kosong agar firstOrCreate benar-benar membuat baru
        User::where('username', config('admin.username'))->delete();

        $this->seed(\Database\Seeders\AdminSeeder::class);

        $admin = User::where('username', config('admin.username'))->first();
        $this->assertNotNull($admin);
        $this->assertSame('admin', $admin->role);
        $this->assertTrue(Hash::check('password', $admin->password));
    }

    public function test_admin_seeder_is_idempotent()
    {
        User::where('username', config('admin.username'))->delete();

        $this->seed(\Database\Seeders\AdminSeeder::class);
        $this->seed(\Database\Seeders\AdminSeeder::class);

        $this->assertSame(1, User::where('username', config('admin.username'))->count());
    }
}
