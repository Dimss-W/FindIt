@extends('layouts.app')

@section('title', 'Riwayat Pengembalian Barang')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-purple-600"></i>
                Riwayat Pengembalian Barang
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar arsip serah terima barang yang telah berhasil dikembalikan kepada pemilik sah di kampus ini</p>
        </div>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Waktu & Nomor BAST</th>
                        <th class="py-4 px-6">Barang</th>
                        <th class="py-4 px-6">Penerima (Pemilik)</th>
                        <th class="py-4 px-6">Petugas Penyerah</th>
                        <th class="py-4 px-6">Catatan Serah Terima</th>
                        <th class="py-4 px-6 text-right">Aksi BAST</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($riwayat as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-medium text-slate-600">
                                {{ $item->tanggal_pengembalian->translatedFormat('d M Y') }}
                                <div class="text-[11px] text-slate-400">{{ $item->tanggal_pengembalian->format('H:i') }} WIB</div>
                                @if($item->nomor_bast)
                                    <span class="inline-block mt-1 font-mono text-[10px] font-bold text-blue-900 bg-blue-50 px-2 py-0.5 rounded border border-blue-200">
                                        {{ $item->nomor_bast }}
                                    </span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $item->laporan->nama_barang }}</div>
                                <div class="text-[11px] font-mono text-slate-400">{{ $item->laporan->kode_laporan }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-purple-900">{{ $item->user->name }}</div>
                                <div class="text-[11px] text-slate-500">NIM: {{ $item->user->nim ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-800">
                                {{ $item->petugas->name }}
                            </td>
                            <td class="py-4 px-6 max-w-xs">
                                <p class="line-clamp-2 text-slate-600 leading-relaxed">{{ $item->catatan ?? '-' }}</p>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('petugas.bast.cetak', $item->id) }}" 
                                   target="_blank"
                                   class="px-3.5 py-1.5 rounded-xl bg-blue-900 hover:bg-blue-950 text-white font-bold text-xs shadow-sm transition-all inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-print text-amber-300"></i>
                                    <span>Cetak BAST</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-4xl mb-3 block text-slate-300"></i>
                                Belum ada riwayat pengembalian barang di kampus ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100">
            {{ $riwayat->links() }}
        </div>
    </div>

</div>
@endsection
