@extends('layouts.app')

@section('title', 'Daftar Pengajuan Klaim Mahasiswa')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-amber-500"></i>
                Pengajuan Klaim Kepemilikan Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Verifikasi bukti kepemilikan yang diajukan oleh mahasiswa untuk barang temuan di kampus Anda</p>
        </div>

        <!-- Filter Status -->
        <div class="flex items-center gap-2">
            <a href="{{ route('petugas.klaim.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ !request('status') ? 'bg-brand-ubsi text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Semua
            </a>
            <a href="{{ route('petugas.klaim.index', ['status' => 'MENUNGGU VERIFIKASI']) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'MENUNGGU VERIFIKASI' ? 'bg-amber-500 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Menunggu
            </a>
            <a href="{{ route('petugas.klaim.index', ['status' => 'DISETUJUI']) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'DISETUJUI' ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Disetujui
            </a>
            <a href="{{ route('petugas.klaim.index', ['status' => 'DITOLAK']) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'DITOLAK' ? 'bg-rose-600 text-white' : 'bg-white border border-slate-200 text-slate-600 hover:bg-slate-50' }}">
                Ditolak
            </a>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Barang Temuan</th>
                        <th class="py-4 px-6">Mahasiswa Pemohon Klaim</th>
                        <th class="py-4 px-6">Deskripsi Klaim & Bukti</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6">Tanggal Diajukan</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($klaims as $k)
                        @php
                            $badge = $k->status_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $k->laporan->nama_barang }}</div>
                                <div class="text-[11px] font-mono text-slate-400">{{ $k->laporan->kode_laporan }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-900">{{ $k->user->name }}</div>
                                <div class="text-[11px] text-slate-400">NIM: {{ $k->user->nim ?? '-' }}</div>
                                <div class="text-[11px] text-slate-400">HP: {{ $k->user->no_telp ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 max-w-xs">
                                <p class="line-clamp-2 text-slate-600 leading-relaxed">{{ $k->deskripsi_klaim }}</p>
                                @if($k->foto_bukti)
                                    <span class="inline-flex items-center gap-1 text-[10px] text-blue-600 font-semibold mt-1">
                                        <i class="fa-solid fa-image"></i> Ada lampiran foto
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold border {{ $badge['bg'] }} uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid {{ $badge['icon'] }}"></i>
                                    {{ $k->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $k->created_at->translatedFormat('d M Y, H:i') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('petugas.klaim.detail', $k->id) }}" 
                                   class="px-3.5 py-2 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white font-bold text-xs shadow-sm transition-all inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    <span>Review Klaim</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-4xl mb-3 block text-slate-300"></i>
                                Belum ada pengajuan klaim untuk barang di kampus ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $klaims->links() }}
        </div>
    </div>

</div>
@endsection
