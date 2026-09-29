<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $fillable = [
        'nama_kategori',
        'icon',
        'deskripsi',
        'status',
    ];

    public function laporanBarang()
    {
        return $this->hasMany(LaporanBarang::class, 'kategori_id');
    }
}
