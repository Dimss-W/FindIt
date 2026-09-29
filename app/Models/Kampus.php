<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kampus extends Model
{
    use HasFactory;

    protected $table = 'kampus';

    protected $fillable = [
        'kode_kampus',
        'nama_kampus',
        'alamat',
        'kota',
        'status',
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'kampus_id');
    }

    public function laporanBarang()
    {
        return $this->hasMany(LaporanBarang::class, 'kampus_id');
    }
}
