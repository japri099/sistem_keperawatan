<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MataKuliah extends Model
{
    use HasFactory;

    protected $table = 'mata_kuliah';
    protected $primaryKey = 'kode_mk';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_mk',
        'kode_kelas',
        'username',
        'nama_mk',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kode_kelas', 'kode_kelas');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'username', 'username');
    }

    public function dosen()
{
    return $this->belongsTo(User::class, 'username', 'username')->withDefault([
        'name' => 'Dosen tidak ditemukan'
    ]);
}
}
