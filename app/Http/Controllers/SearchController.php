<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use App\Models\Kategori;
use App\Models\LaporanBarang;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function index(Request $request)
    {
        $query = LaporanBarang::with(['kampus', 'kategori', 'user']);

        // Search text
        if ($request->filled('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('nama_barang', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('deskripsi', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('lokasi_kejadian', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('kode_laporan', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Filter kampus
        if ($request->filled('kampus_id')) {
            $query->where('kampus_id', $request->kampus_id);
        }

        // Filter kategori
        if ($request->filled('kategori_id')) {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Filter jenis laporan
        if ($request->filled('jenis_laporan')) {
            $query->where('jenis_laporan', $request->jenis_laporan);
        }

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter tanggal
        if ($request->filled('tanggal')) {
            $query->whereDate('tanggal_kejadian', $request->tanggal);
        }

        $laporanList = $query->latest()->paginate(12)->withQueryString();

        $kampusList = Kampus::where('status', 'aktif')->orderBy('nama_kampus')->get();
        $kategoriList = Kategori::where('status', 'aktif')->orderBy('nama_kategori')->get();

        // Kampus yang memiliki laporan aktif untuk tombol filter cepat mahasiswa
        $kampusWithReports = Kampus::where('status', 'aktif')
            ->withCount(['laporanBarang' => function ($q) use ($request) {
                if ($request->filled('jenis_laporan')) {
                    $q->where('jenis_laporan', $request->jenis_laporan);
                }
            }])
            ->having('laporan_barang_count', '>', 0)
            ->orderByDesc('laporan_barang_count')
            ->get();

        return view('search', compact('laporanList', 'kampusList', 'kategoriList', 'kampusWithReports'));
    }
}
