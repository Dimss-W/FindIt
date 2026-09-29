<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pengembalian extends Model
{
    use HasFactory;

    protected $table = 'pengembalian';

    protected $fillable = [
        'nomor_bast',
        'laporan_id',
        'klaim_id',
        'petugas_id',
        'user_id',
        'tanggal_pengembalian',
        'catatan',
        'foto_penyerahan',
        'status',
    ];

    public static function generateNomorBast(?string $kampusKode = 'PST'): string
    {
        $year = date('Y');
        $code = strtoupper($kampusKode ?: 'PST');
        $countThisYear = static::whereYear('created_at', $year)->count() + 1;
        $seq = str_pad($countThisYear, 4, '0', STR_PAD_LEFT);

        return "BAST/UBSI-{$code}/{$year}/{$seq}";
    }

    protected $casts = [
        'tanggal_pengembalian' => 'datetime',
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanBarang::class, 'laporan_id');
    }

    public function klaim()
    {
        return $this->belongsTo(Klaim::class, 'klaim_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
