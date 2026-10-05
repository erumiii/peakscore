<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $table = 'soal';
    public $timestamps = false;
    protected $primaryKey = 'soalId';
    protected $fillable = ['isiSoal', 'kategori', 'opsiA', 'opsiB', 'opsiC', 'opsiD', 'gambarSoal', 'gambarOpsiA', 'gambarOpsiB', 'gambarOpsiC', 'gambarOpsiD', 'jawabanBenar'];

    public function jawaban()
    {
        return $this->hasMany(Jawaban::class, 'soalId', 'soalId');
    }
}
