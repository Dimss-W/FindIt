@extends('layouts.app')

@section('title', 'Barang Diamankan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-vault text-emerald-600"></i>
                Daftar Barang Diamankan di Pos Security
            </h1>
            <p class="text-xs text-slate-500 mt-1">Barang temuan yang telah diverifikasi fisik dan disimpan aman menunggu klaim pemilik</p>
        </div>
        <a href="{{ route('petugas.smart.matching') }}" class="px-4 py-2.5 rounded-xl bg-indigo-50 text-indigo-700 hover:bg-indigo-600 hover:text-white text-xs font-bold transition-all flex items-center gap-1.5">
            <i class="fa-solid fa-bolt text-amber-500"></i> Cek Smart Matching
        </a>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Kode Laporan</th>
                        <th class="py-4 px-6">Nama Barang</th>
                        <th class="py-4 px-6">Lokasi Penyimpanan Pos</th>
                        <th class="py-4 px-6">Kondisi Barang</th>
                        <th class="py-4 px-6">Petugas Penyimpan</th>
                        <th class="py-4 px-6">Tanggal Diamankan</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">{{ $item->kode_laporan }}</td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $item->nama_barang }}</div>
                                <div class="text-[11px] text-slate-400">{{ $item->kategori->nama_kategori }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 font-bold border border-emerald-200 inline-block text-[11px]">
                                    <i class="fa-solid fa-location-pin mr-1"></i>
                                    {{ $item->penyimpanan->lokasi_penyimpanan ?? 'Pos Security' }}
                                </span>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-700">
                                {{ $item->penyimpanan->kondisi_barang ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $item->penyimpanan->petugas->name ?? '-' }}
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                <div>{{ $item->penyimpanan->tanggal_diterima ? $item->penyimpanan->tanggal_diterima->translatedFormat('d M Y, H:i') : '-' }}</div>
                                @if($item->isExpiredForDonation())
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 mt-1 rounded-md bg-amber-50 text-amber-800 font-bold border border-amber-300 text-[10px]">
                                        <i class="fa-solid fa-triangle-exclamation text-amber-500"></i> {{ $item->days_stored }} Hari (Siap Didonasikan)
                                    </span>
                                @else
                                    <span class="text-[10px] text-slate-400 block mt-0.5">{{ $item->days_stored }} hari tersimpan</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('mahasiswa.laporan.detail', $item->id) }}" 
                                       class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-ubsi hover:text-white font-bold text-slate-700 transition-colors text-xs">
                                        Detail
                                    </a>
                                    @if($item->isExpiredForDonation())
                                        <form action="{{ route('petugas.barang.donasikan', $item->id) }}" method="POST" onsubmit="return confirm('Konfirmasi alokasi donasi sosial untuk barang {{ $item->nama_barang }} (Masa simpan > 90 hari)?')">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs transition-all shadow-sm flex items-center gap-1">
                                                <i class="fa-solid fa-hand-holding-heart"></i> Donasikan
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">
                                <i class="fa-solid fa-box-open text-4xl mb-3 block text-slate-300"></i>
                                Belum ada barang dengan status BARANG DIAMANKAN di kampus ini.
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
