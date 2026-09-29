@extends('layouts.app')

@section('title', 'Semua Pengajuan Klaim - Admin')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-file-signature text-amber-500"></i>
                Pengawasan Seluruh Klaim Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Monitoring pengajuan klaim dan tindak lanjut verifikasi petugas di seluruh kampus</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.klaim.index') }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ !request('status') ? 'bg-brand-ubsi text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
                Semua Status
            </a>
            <a href="{{ route('admin.klaim.index', ['status' => 'MENUNGGU VERIFIKASI']) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'MENUNGGU VERIFIKASI' ? 'bg-amber-500 text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
                Menunggu
            </a>
            <a href="{{ route('admin.klaim.index', ['status' => 'DISETUJUI']) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'DISETUJUI' ? 'bg-emerald-600 text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
                Disetujui
            </a>
            <a href="{{ route('admin.klaim.index', ['status' => 'DITOLAK']) }}" 
               class="px-3 py-1.5 rounded-xl text-xs font-bold transition-all {{ request('status') === 'DITOLAK' ? 'bg-rose-600 text-white' : 'bg-white border border-slate-200 text-slate-600' }}">
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
                        <th class="py-4 px-6">Barang</th>
                        <th class="py-4 px-6">Kampus UBSI</th>
                        <th class="py-4 px-6">Mahasiswa Pemohon</th>
                        <th class="py-4 px-6">Deskripsi Klaim</th>
                        <th class="py-4 px-6">Status Klaim</th>
                        <th class="py-4 px-6">Petugas Pemeriksa</th>
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
                            <td class="py-4 px-6 font-medium text-slate-800">
                                {{ $k->laporan->kampus->nama_kampus }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-900">{{ $k->user->name }}</div>
                                <div class="text-[11px] text-slate-400">NIM: {{ $k->user->nim ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 max-w-xs truncate text-slate-600">
                                {{ $k->deskripsi_klaim }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge['bg'] }} uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid {{ $badge['icon'] }}"></i>
                                    {{ $k->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                {{ $k->petugas->name ?? 'Belum diproses' }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('mahasiswa.laporan.detail', $k->laporan_id) }}" 
                                   class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                                    Lihat
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">Belum ada data klaim.</td>
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
