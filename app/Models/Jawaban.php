<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    protected $table = 'jawaban';

    protected $fillable = [
        'jadwalId',
        'userId',
        'soalId',
        'opsiDipilih',
    ];

    public function soal()
    {
        return $this->belongsTo(Soal::class, 'soalId', 'soalId');
    }
}
