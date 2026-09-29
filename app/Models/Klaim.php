<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Klaim extends Model
{
    use HasFactory;

    protected $table = 'klaim';

    protected $fillable = [
        'laporan_id',
        'user_id',
        'petugas_id',
        'deskripsi_klaim',
        'bukti_kepemilikan',
        'foto_bukti',
        'status',
        'kode_tiket',
        'qr_token',
        'catatan_petugas',
        'tanggal_diverifikasi',
    ];

    protected $casts = [
        'tanggal_diverifikasi' => 'datetime',
    ];

    public function generateTiketPengambilan(): string
    {
        $kodeKampus = $this->laporan?->kampus?->kode_kampus ?? 'PST';
        $random = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 4));
        $this->kode_tiket = 'TK-UBSI-' . strtoupper($kodeKampus) . '-' . $random;
        $this->qr_token = sha1('FINDIT-UBSI-' . $this->id . '-' . time() . '-' . $random);
        $this->save();

        return $this->kode_tiket;
    }

    public function laporan()
    {
        return $this->belongsTo(LaporanBarang::class, 'laporan_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'petugas_id');
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'klaim_id');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'MENUNGGU VERIFIKASI' => ['bg' => 'bg-amber-100 text-amber-700 border-amber-200', 'color' => '#d97706', 'icon' => 'fa-clock'],
            'DISETUJUI' => ['bg' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'color' => '#16a34a', 'icon' => 'fa-circle-check'],
            'DITOLAK' => ['bg' => 'bg-rose-100 text-rose-700 border-rose-200', 'color' => '#dc2626', 'icon' => 'fa-circle-xmark'],
            default => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'color' => '#4b5563', 'icon' => 'fa-circle-info'],
        };
    }
}
