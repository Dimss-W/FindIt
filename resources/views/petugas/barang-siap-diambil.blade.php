@extends('layouts.app')

@section('title', 'Barang Siap Diambil')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-handshake text-blue-600"></i>
                Barang Siap Diambil di Pos Security
            </h1>
            <p class="text-xs text-slate-500 mt-1">Daftar barang temuan yang klaimnya telah disetujui dan menunggu kedatangan mahasiswa ke pos</p>
        </div>
    </div>

    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Kode Laporan</th>
                        <th class="py-4 px-6">Nama Barang</th>
                        <th class="py-4 px-6">Lokasi Pos Simpan</th>
                        <th class="py-4 px-6">Pemilik yang Mengklaim (Sah)</th>
                        <th class="py-4 px-6">Status</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($items as $item)
                        @php
                            $approvedClaim = $item->klaim->firstWhere('status', 'DISETUJUI');
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">{{ $item->kode_laporan }}</td>
                            <td class="py-4 px-6 font-bold text-slate-900">
                                {{ $item->nama_barang }}
                                <div class="text-[11px] text-slate-400">{{ $item->kategori->nama_kategori }}</div>
                            </td>
                            <td class="py-4 px-6 font-medium text-emerald-800">
                                <i class="fa-solid fa-vault text-emerald-600 mr-1"></i>
                                {{ $item->penyimpanan->lokasi_penyimpanan ?? 'Pos Security' }}
                            </td>
                            <td class="py-4 px-6">
                                @if($approvedClaim)
                                    <div class="font-bold text-slate-900">{{ $approvedClaim->user->name }}</div>
                                    <div class="text-[11px] text-slate-500">NIM: {{ $approvedClaim->user->nim ?? '-' }}</div>
                                    <div class="text-[11px] text-blue-600">HP: {{ $approvedClaim->user->no_telp ?? '-' }}</div>
                                @else
                                    <span class="text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-blue-100 text-blue-700 border border-blue-200 uppercase inline-flex items-center gap-1">
                                    <i class="fa-solid fa-box-archive"></i>
                                    Siap Diambil
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('petugas.pengembalian.form', $item->id) }}" 
                                   class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs shadow-md shadow-blue-600/20 transition-all inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-handshake"></i>
                                    <span>Konfirmasi Penyerahan</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center text-slate-400">
                                <i class="fa-solid fa-circle-check text-4xl mb-3 block text-slate-300"></i>
                                Tidak ada barang yang sedang menunggu pengambilan di pos Anda.
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
