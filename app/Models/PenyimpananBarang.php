<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PenyimpananBarang extends Model
{
    use HasFactory;

    protected $table = 'penyimpanan_barang';

    protected $fillable = [
        'laporan_id',
        'petugas_id',
        'lokasi_penyimpanan',
        'tanggal_diterima',
        'kondisi_barang',
        'catatan',
    ];

    protected $casts = [
        'tanggal_diterima' => 'datetime',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanBarang::class, 'laporan_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }
}
