@extends('layouts.app')

@section('title', 'Klaim Saya')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Status Pengajuan Klaim Saya</h1>
            <p class="text-xs text-slate-500 mt-1">Pantau proses verifikasi klaim kepemilikan barang oleh Petugas Keamanan</p>
        </div>
        <a href="{{ route('search', ['jenis_laporan' => 'DITEMUKAN']) }}" class="px-4 py-2.5 rounded-xl bg-blue-50 text-brand-ubsi hover:bg-brand-ubsi hover:text-white text-xs font-bold transition-all">
            <i class="fa-solid fa-magnifying-glass mr-1"></i> Cari Barang Ditemukan
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Barang & Kampus</th>
                        <th class="py-4 px-6">Deskripsi Klaim</th>
                        <th class="py-4 px-6">Bukti Diajukan</th>
                        <th class="py-4 px-6">Status Klaim</th>
                        <th class="py-4 px-6">Petugas & Catatan</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($klaimList as $klaim)
                        @php
                            $badge = $klaim->status_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $klaim->laporan->nama_barang }}</div>
                                <div class="text-[11px] text-slate-500 font-mono">{{ $klaim->laporan->kode_laporan }}</div>
                                <div class="text-[11px] text-blue-600 font-medium">{{ $klaim->laporan->kampus->nama_kampus }}</div>
                            </td>
                            <td class="py-4 px-6 max-w-xs">
                                <p class="line-clamp-2 text-slate-600 leading-relaxed">{{ $klaim->deskripsi_klaim }}</p>
                            </td>
                            <td class="py-4 px-6 max-w-xs">
                                <p class="line-clamp-2 text-slate-600 leading-relaxed">{{ $klaim->bukti_kepemilikan }}</p>
                                @if($klaim->foto_bukti)
                                    <span class="inline-flex items-center gap-1 text-[10px] text-blue-600 font-semibold mt-1">
                                        <i class="fa-solid fa-image"></i> Ada lampiran foto
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold border {{ $badge['bg'] }} uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid {{ $badge['icon'] }}"></i>
                                    {{ $klaim->status }}
                                </span>
                                @if($klaim->status === 'DISETUJUI')
                                    <div class="text-[10px] text-emerald-700 font-bold mt-1.5 flex items-center gap-1">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Siap Diambil di Staf Kampus
                                    </div>
                                    @if($klaim->kode_tiket)
                                        <div class="mt-1">
                                            <span class="inline-flex items-center gap-1 font-mono text-[10px] font-bold text-brand-ubsi bg-blue-50 border border-blue-200 px-2 py-0.5 rounded-md">
                                                <i class="fa-solid fa-qrcode text-emerald-600"></i> {{ $klaim->kode_tiket }}
                                            </span>
                                        </div>
                                    @endif
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                @if($klaim->petugas)
                                    <div class="font-semibold text-slate-800">{{ $klaim->petugas->name }}</div>
                                    @if($klaim->catatan_petugas)
                                        <div class="text-[11px] text-slate-500 italic mt-0.5">"{{ $klaim->catatan_petugas }}"</div>
                                    @endif
                                @else
                                    <span class="text-slate-400 italic">Menunggu review staf</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($klaim->status === 'DISETUJUI')
                                    <a href="{{ route('mahasiswa.laporan.detail', $klaim->laporan_id) }}" 
                                       class="px-3 py-1.5 rounded-xl bg-brand-ubsi text-white font-bold text-xs shadow-md shadow-blue-900/20 hover:bg-blue-900 transition-all inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-qrcode text-amber-300"></i>
                                        <span>Buka Tiket QR</span>
                                    </a>
                                @else
                                    <a href="{{ route('mahasiswa.laporan.detail', $klaim->laporan_id) }}" 
                                       class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-ubsi hover:text-white font-bold text-slate-700 transition-colors">
                                        Lihat Barang
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-4xl mb-3 block text-slate-300"></i>
                                Anda belum mengajukan klaim untuk barang temuan manapun.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $klaimList->links() }}
        </div>
    </div>

</div>
@endsection
