@extends('layouts.app')

@section('title', 'Semua Laporan Barang Ditemukan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Semua Laporan Barang Ditemukan</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar seluruh barang temuan yang dilaporkan di kampus {{ Auth::user()->kampus->nama_kampus }}</p>
        </div>

        <form action="{{ route('petugas.barang.ditemukan') }}" method="GET" class="flex items-center gap-2">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari nama barang..." 
                   class="px-3.5 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-blue-500">
            <button type="submit" class="px-4 py-2 bg-brand-ubsi text-white text-xs font-bold rounded-xl hover:bg-blue-900 transition-colors">
                Cari
            </button>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Kode Laporan</th>
                        <th class="py-4 px-6">Nama Barang</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Lokasi Temuan</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6">Tanggal Lapor</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($items as $item)
                        @php
                            $badge = $item->status_badge;
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">{{ $item->kode_laporan }}</td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $item->nama_barang }}</div>
                                <div class="text-[11px] text-slate-400">Penemu: {{ $item->user->name }}</div>
                            </td>
                            <td class="py-4 px-6 text-slate-600">{{ $item->kategori->nama_kategori }}</td>
                            <td class="py-4 px-6 font-medium text-slate-800">{{ $item->lokasi_kejadian }}</td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge['bg'] }} uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid {{ $badge['icon'] }}"></i>
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-500">{{ $item->created_at->translatedFormat('d M Y') }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('mahasiswa.laporan.detail', $item->id) }}" 
                                   class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-ubsi hover:text-white font-bold text-slate-700 transition-colors text-xs">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">
                                Tidak ada laporan barang temuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $items->links() }}
        </div>
    </div>

</div>
@endsection
