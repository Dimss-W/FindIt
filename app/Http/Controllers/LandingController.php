<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use App\Models\Kategori;
use App\Models\LaporanBarang;
use Illuminate\Http\Request;

class LandingController extends Controller
{
    public function index()
    {
        $stats = [
            'total_laporan' => LaporanBarang::count(),
            'barang_hilang' => LaporanBarang::where('jenis_laporan', 'HILANG')->count(),
            'barang_ditemukan' => LaporanBarang::where('jenis_laporan', 'DITEMUKAN')->count(),
            'barang_diamankan' => LaporanBarang::where('status', 'BARANG DIAMANKAN')->count(),
            'barang_dikembalikan' => LaporanBarang::where('status', 'DIKEMBALIKAN')->count(),
            'kampus_terhubung' => Kampus::where('status', 'aktif')->count(),
        ];

        $kategoris = Kategori::where('status', 'aktif')
            ->withCount(['laporanBarang' => function ($query) {
                $query->whereIn('status', ['SEDANG DICARI', 'MENUNGGU VERIFIKASI', 'BARANG DIAMANKAN', 'SIAP DIAMBIL']);
            }])
            ->get();

        $kampusList = Kampus::where('status', 'aktif')->orderBy('nama_kampus')->get();

        $recentLost = LaporanBarang::with(['kampus', 'kategori'])
            ->where('jenis_laporan', 'HILANG')
            ->whereIn('status', ['SEDANG DICARI', 'ADA KEMUNGKINAN COCOK'])
            ->latest()
            ->take(4)
            ->get();

        $recentFound = LaporanBarang::with(['kampus', 'kategori'])
            ->where('jenis_laporan', 'DITEMUKAN')
            ->whereIn('status', ['MENUNGGU VERIFIKASI', 'BARANG DIAMANKAN', 'SIAP DIAMBIL'])
            ->latest()
            ->take(4)
            ->get();

        return view('landing', compact('stats', 'kategoris', 'kampusList', 'recentLost', 'recentFound'));
    }
}
