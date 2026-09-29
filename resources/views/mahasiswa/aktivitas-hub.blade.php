@extends('layouts.app')

@section('title', 'Aktivitas Saya - FINDIT UBSI')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ currentTab: '{{ $activeTab ?? 'laporan' }}' }">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-brand-ubsi"></i>
                Pusat Aktivitas Saya
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Semua riwayat laporan, proses klaim, tiket QR pengambilan, dan notifikasi Anda terintegrasi dalam 1 layar.
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('mahasiswa.lapor.hub') }}" class="px-4 py-2.5 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus-circle"></i>
                <span>Buat Laporan Baru</span>
            </a>
        </div>
    </div>

    <!-- 3 Utama Tab Switcher -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <button type="button" @click="currentTab = 'laporan'" 
                :class="currentTab === 'laporan' ? 'bg-blue-600 text-white border-blue-700 shadow-md shadow-blue-600/20' : 'bg-white text-slate-700 border-slate-200/80 hover:border-blue-400'"
                class="p-4 rounded-2xl border transition-all text-left flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider block" :class="currentTab === 'laporan' ? 'text-blue-100' : 'text-slate-400'">1. Laporan Saya</span>
                <span class="text-xl font-black">{{ $counts['laporan'] }} <span class="text-xs font-normal opacity-80">laporan aktif</span></span>
            </div>
            <i class="fa-solid fa-clipboard-list text-xl" :class="currentTab === 'laporan' ? 'text-blue-200' : 'text-blue-500'"></i>
        </button>

        <button type="button" @click="currentTab = 'klaim'" 
                :class="currentTab === 'klaim' ? 'bg-amber-500 text-white border-amber-600 shadow-md shadow-amber-500/20' : 'bg-white text-slate-700 border-slate-200/80 hover:border-amber-400'"
                class="p-4 rounded-2xl border transition-all text-left flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider block" :class="currentTab === 'klaim' ? 'text-amber-100' : 'text-slate-400'">2. Klaim & Tiket QR</span>
                <span class="text-xl font-black">{{ $counts['klaim'] }} <span class="text-xs font-normal opacity-80">klaim diajukan</span></span>
            </div>
            <i class="fa-solid fa-qrcode text-xl" :class="currentTab === 'klaim' ? 'text-amber-200' : 'text-amber-500'"></i>
        </button>

        <button type="button" @click="currentTab = 'notifikasi'" 
                :class="currentTab === 'notifikasi' ? 'bg-purple-600 text-white border-purple-700 shadow-md shadow-purple-600/20' : 'bg-white text-slate-700 border-slate-200/80 hover:border-purple-400'"
                class="p-4 rounded-2xl border transition-all text-left flex items-center justify-between">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider block" :class="currentTab === 'notifikasi' ? 'text-purple-100' : 'text-slate-400'">3. Notifikasi</span>
                <span class="text-xl font-black">{{ $counts['notif_unread'] }} <span class="text-xs font-normal opacity-80">belum dibaca</span></span>
            </div>
            <i class="fa-solid fa-bell text-xl" :class="currentTab === 'notifikasi' ? 'text-purple-200' : 'text-purple-500'"></i>
        </button>
    </div>

    <!-- TAB 1: LAPORAN SAYA -->
    <div x-show="currentTab === 'laporan'" x-cloak class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Daftar Laporan Kehilangan & Temuan Saya</h3>
                <p class="text-xs text-slate-500 mt-0.5">Pantau status penelusuran barang yang pernah Anda laporkan ke kampus.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-brand-ubsi border border-blue-200">
                {{ $myReports->total() }} Laporan
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Barang & Kode</th>
                        <th class="py-4 px-6">Tipe Laporan</th>
                        <th class="py-4 px-6">Kampus Terkait</th>
                        <th class="py-4 px-6">Waktu Kejadian</th>
                        <th class="py-4 px-6">Status Terkini</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($myReports as $rep)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $rep->nama_barang }}</span>
                                <span class="font-mono text-[11px] text-slate-400">{{ $rep->kode_laporan }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-lg text-[10px] font-bold uppercase {{ $rep->jenis_laporan === 'HILANG' ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                    {{ $rep->jenis_laporan }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-slate-600 font-medium">
                                {{ $rep->kampus?->nama_kampus }}
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $rep->tanggal_kejadian ? $rep->tanggal_kejadian->translatedFormat('d M Y') : '-' }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $rep->status_badge['bg'] }} uppercase">
                                    {{ $rep->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('mahasiswa.laporan.detail', $rep->id) }}" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-ubsi hover:text-white text-slate-700 font-bold text-xs transition-all inline-flex items-center gap-1">
                                    <span>Lihat Detail</span>
                                    <i class="fa-solid fa-arrow-right text-[9px]"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fa-regular fa-folder-open text-4xl text-slate-300 mb-2 block"></i>
                                Anda belum memiliki riwayat laporan barang.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($myReports->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $myReports->links() }}</div>
        @endif
    </div>

    <!-- TAB 2: KLAIM SAYA & TIKET QR -->
    <div x-show="currentTab === 'klaim'" x-cloak class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Pengajuan Klaim & Tiket Pengambilan (QR Code)</h3>
                <p class="text-xs text-slate-500 mt-0.5">Buka tiket digital ber-QR Code untuk mengambil fisik barang yang telah disetujui staf kampus.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 text-amber-800 border border-amber-200">
                {{ $myClaims->total() }} Klaim
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Barang & Kampus</th>
                        <th class="py-4 px-6">Bukti Diajukan</th>
                        <th class="py-4 px-6">Status Klaim</th>
                        <th class="py-4 px-6">Tiket Pengambilan</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($myClaims as $klm)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $klm->laporan?->nama_barang }}</span>
                                <span class="text-[11px] text-blue-600 font-medium">{{ $klm->laporan?->kampus?->nama_kampus }}</span>
                            </td>
                            <td class="py-4 px-6 max-w-xs">
                                <p class="line-clamp-2 text-slate-600">{{ $klm->bukti_kepemilikan }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $klm->status_badge['bg'] }} uppercase">
                                    {{ $klm->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                @if($klm->status === 'DISETUJUI' && $klm->kode_tiket)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-800 border border-emerald-200 font-mono font-bold text-xs">
                                        <i class="fa-solid fa-qrcode text-emerald-600"></i>
                                        {{ $klm->kode_tiket }}
                                    </span>
                                @elseif($klm->status === 'MENUNGGU VERIFIKASI')
                                    <span class="text-slate-400 italic text-[11px]">Menunggu Verifikasi</span>
                                @else
                                    <span class="text-slate-400 text-[11px]">-</span>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if($klm->status === 'DISETUJUI')
                                    <a href="{{ route('mahasiswa.laporan.detail', $klm->laporan_id) }}" 
                                       class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all inline-flex items-center gap-1.5">
                                        <i class="fa-solid fa-qrcode text-amber-300"></i>
                                        <span>Buka Tiket QR</span>
                                    </a>
                                @else
                                    <a href="{{ route('mahasiswa.laporan.detail', $klm->laporan_id) }}" 
                                       class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs transition-all">
                                        Detail
                                    </a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-ticket text-4xl text-slate-300 mb-2 block"></i>
                                Anda belum mengajukan klaim barang temuan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($myClaims->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $myClaims->links() }}</div>
        @endif
    </div>

    <!-- TAB 3: NOTIFIKASI -->
    <div x-show="currentTab === 'notifikasi'" x-cloak class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden p-6 space-y-4">
        <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Pemberitahuan Sistem FINDIT</h3>
                <p class="text-xs text-slate-500 mt-0.5">Informasi terkait pencocokan barang, persetujuan klaim, dan update laporan Anda.</p>
            </div>
            @if($counts['notif_unread'] > 0)
                <form action="{{ route('mahasiswa.notifikasi.read_all') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs font-bold text-blue-600 hover:underline">
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        <div class="space-y-3">
            @forelse($notifikasis as $notif)
                <div class="p-4 rounded-2xl border transition-all flex items-start justify-between gap-4 {{ $notif->is_read ? 'bg-slate-50 border-slate-100' : 'bg-blue-50/50 border-blue-200/80' }}">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-xl flex items-center justify-center text-sm {{ $notif->is_read ? 'bg-slate-200 text-slate-500' : 'bg-blue-600 text-white' }}">
                            <i class="fa-solid fa-bell"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-slate-900">{{ $notif->judul }}</h4>
                            <p class="text-xs text-slate-600 mt-0.5">{{ $notif->pesan }}</p>
                            <span class="text-[10px] text-slate-400 mt-1 block">{{ $notif->created_at ? $notif->created_at->diffForHumans() : '-' }}</span>
                        </div>
                    </div>
                    @if($notif->link)
                        <form action="{{ route('mahasiswa.notifikasi.read', $notif->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-3 py-1.5 rounded-lg bg-white border border-slate-200 hover:bg-blue-50 text-xs font-bold text-blue-700 transition-all whitespace-nowrap">
                                Buka
                            </button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="py-12 text-center text-slate-400">
                    <i class="fa-regular fa-bell text-4xl text-slate-300 mb-2 block"></i>
                    Tidak ada notifikasi saat ini.
                </div>
            @endforelse
        </div>
        @if($notifikasis->hasPages())
            <div class="pt-4 border-t border-slate-100">{{ $notifikasis->links() }}</div>
        @endif
    </div>

</div>
@endsection
