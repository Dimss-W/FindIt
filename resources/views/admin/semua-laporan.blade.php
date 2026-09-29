@extends('layouts.app')

@section('title', 'Moderasi Semua Laporan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-folder-tree text-blue-600"></i>
                Moderasi Seluruh Laporan Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Pengawasan global dan penghapusan laporan tidak valid / spam di seluruh unit kampus</p>
        </div>
    </div>

    <!-- Filters Header -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('admin.laporan.index') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama / kode..." 
                   class="px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none">

            <select name="kampus_id" class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none">
                <option value="">Semua Kampus</option>
                @foreach($campuses as $kmp)
                    <option value="{{ $kmp->id }}" {{ request('kampus_id') == $kmp->id ? 'selected' : '' }}>
                        {{ $kmp->nama_kampus }}
                    </option>
                @endforeach
            </select>

            <select name="kategori_id" class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none">
                <option value="">Semua Kategori</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('kategori_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->nama_kategori }}
                    </option>
                @endforeach
            </select>

            <select name="status" class="px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-700 focus:outline-none">
                <option value="">Semua Status</option>
                <option value="SEDANG DICARI" {{ request('status') === 'SEDANG DICARI' ? 'selected' : '' }}>🔴 Sedang Dicari</option>
                <option value="MENUNGGU VERIFIKASI" {{ request('status') === 'MENUNGGU VERIFIKASI' ? 'selected' : '' }}>🟡 Menunggu Verifikasi</option>
                <option value="BARANG DIAMANKAN" {{ request('status') === 'BARANG DIAMANKAN' ? 'selected' : '' }}>🟢 Barang Diamankan</option>
                <option value="SIAP DIAMBIL" {{ request('status') === 'SIAP DIAMBIL' ? 'selected' : '' }}>🔵 Siap Diambil</option>
                <option value="DIKEMBALIKAN" {{ request('status') === 'DIKEMBALIKAN' ? 'selected' : '' }}>🎉 Dikembalikan</option>
            </select>

            <button type="submit" class="px-4 py-2.5 bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold rounded-xl transition-all">
                Terapkan Filter
            </button>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Kode</th>
                        <th class="py-4 px-6">Nama Barang</th>
                        <th class="py-4 px-6">Kampus UBSI</th>
                        <th class="py-4 px-6">Pelapor</th>
                        <th class="py-4 px-6">Jenis</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6">Waktu Kejadian</th>
                        <th class="py-4 px-6 text-right">Moderasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($laporans as $item)
                        @php
                            $badge = $item->status_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">{{ $item->kode_laporan }}</td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $item->nama_barang }}</div>
                                <div class="text-[11px] text-slate-400">{{ $item->kategori->nama_kategori }}</div>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-800">{{ $item->kampus->nama_kampus }}</td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-900">{{ $item->user->name }}</div>
                                <div class="text-[11px] text-slate-400">{{ $item->user->email }}</div>
                            </td>
                            <td class="py-4 px-6">
                                @if($item->jenis_laporan === 'HILANG')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-700 uppercase">HILANG</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase">TEMUAN</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge['bg'] }} uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid {{ $badge['icon'] }}"></i>
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $item->tanggal_kejadian->translatedFormat('d M Y') }}
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('mahasiswa.laporan.detail', $item->id) }}" 
                                       class="px-2.5 py-1.5 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs">
                                        Detail
                                    </a>
                                    <form method="POST" action="{{ route('admin.laporan.hapus', $item->id) }}" 
                                          onsubmit="return confirm('Hapus laporan ini secara permanen dari database?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2.5 py-1.5 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-600 hover:text-white font-bold text-xs transition-colors">
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-16 text-center text-slate-400">Tidak ada laporan yang cocok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $laporans->links() }}
        </div>
    </div>

</div>
@endsection
