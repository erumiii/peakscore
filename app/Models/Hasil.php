<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hasil extends Model
{
    protected $table = 'hasil';

    protected $fillable = [
        'jadwalId',
        'userId',
        'skorVerbal',
        'skorNumerik',
        'skorLogika',
        'skorSpasial',
        'diterbitkanPada',
    ];

    protected $casts = [
        'diterbitkanPada' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function jadwal()
    {
        return $this->belongsTo(Jadwal::class, 'jadwalId');
    }
}
