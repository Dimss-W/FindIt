@extends('layouts.app')

@section('title', 'Dashboard Layanan Kampus')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Officer Campus Header Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-slate-900 via-blue-950 to-slate-900 text-white shadow-xl shadow-slate-950/10 flex flex-col md:flex-row md:items-center justify-between gap-6 border border-slate-800 relative overflow-hidden">
        <div class="space-y-2 relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 text-xs font-bold">
                <i class="fa-solid fa-user-shield"></i> Layanan & Pengamanan Fisik Barang
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                Admin Layanan Kampus — {{ $kampus->nama_kampus }}
            </h1>
            <p class="text-slate-400 text-xs sm:text-sm max-w-xl">
                Alamat: {{ $kampus->alamat ?? '-' }} ({{ $kampus->kota }}). Seluruh data barang yang Anda kelola otomatis terikat dengan unit kampus ini.
            </p>
        </div>

        <div class="flex items-center gap-3 relative z-10 flex-shrink-0">
            <a href="{{ route('petugas.menunggu.verifikasi') }}" class="px-5 py-3 rounded-2xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-amber-500/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left"></i>
                <span>Verifikasi Barang Masuk</span>
            </a>
            <a href="{{ route('petugas.smart.matching') }}" class="px-5 py-3 rounded-2xl bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold shadow-lg shadow-indigo-600/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-bolt text-amber-300"></i>
                <span>Smart Matching</span>
            </a>
        </div>
    </div>

    <!-- 5 Dashboard Stats Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <!-- 1. Total Ditemukan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $stats['total_ditemukan'] }}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Barang Ditemukan</div>
        </div>

        <!-- 2. Menunggu Verifikasi -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $stats['menunggu_verifikasi'] }}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Menunggu Verifikasi</div>
        </div>

        <!-- 3. Barang Diamankan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-vault"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $stats['barang_diamankan'] }}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Barang Diamankan</div>
        </div>

        <!-- 4. Menunggu Pengambilan (Siap Diambil) -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $stats['siap_diambil'] }}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Siap Diambil</div>
        </div>

        <!-- 5. Barang Dikembalikan -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-lg mb-2">
                <i class="fa-solid fa-champagne-glasses"></i>
            </div>
            <div class="text-2xl font-black text-slate-900">{{ $stats['dikembalikan'] }}</div>
            <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Telah Dikembalikan</div>
        </div>
    </div>

    <!-- Table: Barang Terbaru di Kampus Ini -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h2 class="text-base font-bold text-slate-900">Barang Temuan & Hilang Terbaru di Kampus Ini</h2>
                <p class="text-xs text-slate-500 mt-0.5">Daftar item terbaru yang berada dalam yurisdiksi pengamanan Anda</p>
            </div>
            <a href="{{ route('petugas.barang.ditemukan') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                <span>Kelola Semua Barang</span>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Kode Laporan</th>
                        <th class="py-4 px-6">Nama Barang</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Lokasi Kampus</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($recentItems as $item)
                        @php
                            $badge = $item->status_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">{{ $item->kode_laporan }}</td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $item->nama_barang }}</div>
                                <div class="text-[11px] text-slate-400">Pelapor: {{ $item->user->name }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="text-slate-600">{{ $item->kategori->nama_kategori }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-medium text-slate-800">{{ $item->lokasi_kejadian }}</span>
                                <div class="text-[11px] text-slate-400">{{ $item->tanggal_kejadian->translatedFormat('d M Y') }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge['bg'] }} uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid {{ $badge['icon'] }}"></i>
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    @if($item->status === 'MENUNGGU VERIFIKASI')
                                        <a href="{{ route('petugas.verifikasi.form', $item->id) }}" 
                                           class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-sm">
                                            Verifikasi & Simpan
                                        </a>
                                    @elseif($item->status === 'SIAP DIAMBIL')
                                        <a href="{{ route('petugas.pengembalian.form', $item->id) }}" 
                                           class="px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-sm">
                                            Serah Terima
                                        </a>
                                    @else
                                        <a href="{{ route('mahasiswa.laporan.detail', $item->id) }}" 
                                           class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                                            Detail
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-3xl mb-2 block text-slate-300"></i>
                                Belum ada laporan barang di kampus ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
