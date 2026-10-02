<?php

namespace App\Models;

use App\Models\Hasil;
use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $fillable = [
        'judul',
        'deskripsi',
        'mulai',
        'selesai',
    ];

    protected $casts = [
        'mulai' => 'datetime',
        'selesai' => 'datetime',
    ];

    public function status(): string
    {
        $now = now();
        if ($now->lt($this->mulai)) {
            return 'Scheduled';
        }
        if ($now->lte($this->selesai)) {
            return 'Ongoing';
        }
        return 'Finished';
    }

    public function hasil()
    {
        return $this->hasMany(Hasil::class, 'jadwalId');
    }
}
