<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LaporanBarang extends Model
{
    use HasFactory;

    protected $table = 'laporan_barang';

    protected $fillable = [
        'kode_laporan',
        'user_id',
        'kampus_id',
        'kategori_id',
        'jenis_laporan',
        'nama_barang',
        'lokasi_kejadian',
        'tanggal_kejadian',
        'waktu_kejadian',
        'deskripsi',
        'ciri_khusus',
        'foto_barang',
        'status',
    ];

    protected $casts = [
        'tanggal_kejadian' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function kampus()
    {
        return $this->belongsTo(Kampus::class, 'kampus_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function penyimpanan()
    {
        return $this->hasOne(PenyimpananBarang::class, 'laporan_id');
    }

    public function klaim()
    {
        return $this->hasMany(Klaim::class, 'laporan_id')->latest();
    }

    public function pengembalian()
    {
        return $this->hasOne(Pengembalian::class, 'laporan_id');
    }

    // Cek apakah mahasiswa masih berhak mengedit / menghapus laporan
    public function canBeManagedByStudent(): bool
    {
        if ($this->jenis_laporan === 'HILANG') {
            return in_array($this->status, ['SEDANG DICARI', 'ADA KEMUNGKINAN COCOK']);
        }
        return $this->status === 'MENUNGGU VERIFIKASI' && !$this->penyimpanan()->exists();
    }

    // Helper untuk badge class warna sesuai spesifikasi
    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'SEDANG DICARI' => ['bg' => 'bg-rose-100 text-rose-700 border-rose-200', 'color' => '#dc2626', 'icon' => 'fa-magnifying-glass'],
            'ADA KEMUNGKINAN COCOK' => ['bg' => 'bg-amber-100 text-amber-700 border-amber-200', 'color' => '#d97706', 'icon' => 'fa-bolt'],
            'MENUNGGU VERIFIKASI' => ['bg' => 'bg-amber-100 text-amber-700 border-amber-200', 'color' => '#d97706', 'icon' => 'fa-clock'],
            'BARANG DIAMANKAN' => ['bg' => 'bg-emerald-100 text-emerald-700 border-emerald-200', 'color' => '#16a34a', 'icon' => 'fa-shield-halved'],
            'SIAP DIAMBIL' => ['bg' => 'bg-blue-100 text-blue-700 border-blue-200', 'color' => '#2563eb', 'icon' => 'fa-box-archive'],
            'DIKEMBALIKAN' => ['bg' => 'bg-purple-100 text-purple-700 border-purple-200', 'color' => '#9333ea', 'icon' => 'fa-champagne-glasses'],
            'DIDONASIKAN' => ['bg' => 'bg-amber-100 text-amber-800 border-amber-300', 'color' => '#b45309', 'icon' => 'fa-hand-holding-heart'],
            'DITOLAK' => ['bg' => 'bg-slate-100 text-slate-700 border-slate-200', 'color' => '#64748b', 'icon' => 'fa-ban'],
            default => ['bg' => 'bg-gray-100 text-gray-700 border-gray-200', 'color' => '#4b5563', 'icon' => 'fa-circle-info'],
        };
    }

    public function isExpiredForDonation(): bool
    {
        return $this->status === 'BARANG DIAMANKAN' 
            && $this->created_at 
            && $this->created_at->diffInDays(now()) >= 90;
    }

    public function getDaysStoredAttribute(): int
    {
        return $this->created_at ? (int) $this->created_at->diffInDays(now()) : 0;
    }

    public function getFotoListAttribute(): array
    {
        if (empty($this->foto_barang)) {
            return [];
        }
        $decoded = json_decode($this->foto_barang, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return $decoded;
        }
        return [$this->foto_barang];
    }

    public function getThumbnailFotoAttribute(): ?string
    {
        $list = $this->foto_list;
        return !empty($list) ? $list[0] : null;
    }

    public function getFotoUtamaUrlAttribute(): string
    {
        $thumb = $this->thumbnail_foto;
        if ($thumb) {
            return asset('storage/' . $thumb);
        }
        return asset('images/no-image.png');
    }
}
