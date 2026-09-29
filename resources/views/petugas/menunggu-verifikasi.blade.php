@extends('layouts.app')

@section('title', 'Barang Menunggu Verifikasi')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-amber-500 animate-ping"></span>
                Barang Menunggu Verifikasi & Pengamanan
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar laporan barang temuan oleh mahasiswa yang perlu diverifikasi fisik di Pos Security</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Kode Laporan</th>
                        <th class="py-4 px-6">Nama Barang</th>
                        <th class="py-4 px-6">Kategori</th>
                        <th class="py-4 px-6">Lokasi Ditemukan</th>
                        <th class="py-4 px-6">Pelapor / Penemu</th>
                        <th class="py-4 px-6">Tanggal Lapor</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($items as $item)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">{{ $item->kode_laporan }}</td>
                            <td class="py-4 px-6 font-bold text-slate-900">{{ $item->nama_barang }}</td>
                            <td class="py-4 px-6">{{ $item->kategori->nama_kategori }}</td>
                            <td class="py-4 px-6 font-medium text-slate-800">{{ $item->lokasi_kejadian }}</td>
                            <td class="py-4 px-6">
                                <div class="font-semibold text-slate-800">{{ $item->user->name }}</div>
                                <div class="text-[11px] text-slate-400">NIM: {{ $item->user->nim ?? '-' }}</div>
                            </td>
                            <td class="py-4 px-6 text-slate-500">{{ $item->created_at->translatedFormat('d M Y, H:i') }}</td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('petugas.verifikasi.form', $item->id) }}" 
                                   class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs shadow-sm transition-all inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-clipboard-check"></i>
                                    <span>Verifikasi Fisik</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">
                                <i class="fa-solid fa-circle-check text-4xl mb-3 block text-emerald-400"></i>
                                Tidak ada barang temuan yang menunggu verifikasi saat ini.
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
