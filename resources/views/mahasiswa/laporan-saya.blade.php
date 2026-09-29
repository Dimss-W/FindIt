@extends('layouts.app')

@section('title', 'Laporan Saya')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Riwayat Laporan Saya</h1>
            <p class="text-xs text-slate-500 mt-1">Daftar seluruh laporan kehilangan dan temuan yang pernah Anda ajukan</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('mahasiswa.lapor.hilang') }}" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Lapor Hilang
            </a>
            <a href="{{ route('mahasiswa.lapor.temukan') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-1.5">
                <i class="fa-solid fa-plus"></i> Lapor Temuan
            </a>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="flex items-center gap-2 border-b border-slate-200 pb-3 overflow-x-auto text-xs font-semibold">
        <a href="{{ route('mahasiswa.laporan.saya') }}" 
           class="px-4 py-2 rounded-xl transition-all {{ !request('jenis') ? 'bg-brand-ubsi text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            Semua Laporan
        </a>
        <a href="{{ route('mahasiswa.laporan.saya', ['jenis' => 'HILANG']) }}" 
           class="px-4 py-2 rounded-xl transition-all {{ request('jenis') === 'HILANG' ? 'bg-rose-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            🔴 Barang Hilang
        </a>
        <a href="{{ route('mahasiswa.laporan.saya', ['jenis' => 'DITEMUKAN']) }}" 
           class="px-4 py-2 rounded-xl transition-all {{ request('jenis') === 'DITEMUKAN' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-100' }}">
            🟢 Barang Ditemukan
        </a>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Kode Laporan</th>
                        <th class="py-4 px-6">Barang</th>
                        <th class="py-4 px-6">Kampus UBSI</th>
                        <th class="py-4 px-6">Jenis</th>
                        <th class="py-4 px-6">Status Laporan</th>
                        <th class="py-4 px-6">Waktu Kejadian</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($laporans as $item)
                        @php
                            $badge = $item->status_badge;
                            $canManage = $item->canBeManagedByStudent();
                        @endphp
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-slate-900">
                                {{ $item->kode_laporan }}
                            </td>
                            <td class="py-4 px-6">
                                <div class="font-bold text-slate-900">{{ $item->nama_barang }}</div>
                                <div class="text-[11px] text-slate-400">{{ $item->kategori->nama_kategori }}</div>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-medium text-slate-700">{{ $item->kampus->nama_kampus }}</span>
                                <div class="text-[11px] text-slate-400">{{ $item->lokasi_kejadian }}</div>
                            </td>
                            <td class="py-4 px-6">
                                @if($item->jenis_laporan === 'HILANG')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 uppercase">HILANG</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase">TEMUAN</span>
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
                                @if($item->waktu_kejadian)
                                    <div class="text-[11px] text-slate-400">{{ substr($item->waktu_kejadian, 0, 5) }} WIB</div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('mahasiswa.laporan.detail', $item->id) }}" 
                                       class="p-2 rounded-xl bg-slate-100 hover:bg-brand-ubsi hover:text-white text-slate-600 transition-colors" title="Lihat Detail">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>

                                    @if($canManage)
                                        <a href="{{ route('mahasiswa.laporan.edit', $item->id) }}" 
                                           class="p-2 rounded-xl bg-slate-100 hover:bg-amber-500 hover:text-white text-slate-600 transition-colors" title="Edit Laporan">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>

                                        <form method="POST" action="{{ route('mahasiswa.laporan.delete', $item->id) }}" 
                                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus laporan ini?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 rounded-xl bg-slate-100 hover:bg-rose-600 hover:text-white text-slate-600 transition-colors" title="Hapus Laporan">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-4xl mb-3 block text-slate-300"></i>
                                Belum ada laporan yang sesuai kriteria.
                            </td>
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
