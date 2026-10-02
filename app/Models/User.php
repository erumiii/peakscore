<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = ['username', 'name', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPeserta(): bool
    {
        return $this->role === 'peserta';
    }

    public function hasil()
    {
        return $this->hasMany(\App\Models\Hasil::class, 'userId');
    }
}
