@extends('layouts.app')

@section('title', 'Dashboard Mahasiswa')

@section('content')
<div class="space-y-8 max-w-7xl mx-auto">
    
    <!-- Welcome Banner -->
    <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-brand-ubsi via-blue-900 to-indigo-900 text-white shadow-xl shadow-blue-950/10 flex flex-col md:flex-row md:items-center justify-between gap-6 relative overflow-hidden">
        <div class="space-y-2 relative z-10">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-white/10 text-amber-300 text-xs font-semibold backdrop-blur-sm">
                <i class="fa-solid fa-graduation-cap"></i> Mahasiswa Aktif UBSI
            </span>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">Selamat Datang, {{ $user->name }} 👋</h1>
            <p class="text-blue-200 text-xs sm:text-sm max-w-xl">
                Pantau laporan kehilangan Anda, serahkan barang temuan ke Pos Keamanan kampus, atau ajukan klaim kepemilikan barang yang telah diamankan.
            </p>
        </div>

        <div class="flex items-center gap-3 relative z-10 flex-shrink-0">
            <a href="{{ route('mahasiswa.lapor.hilang') }}" class="px-5 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-900/30 transition-all flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span>Lapor Barang Hilang</span>
            </a>
            <a href="{{ route('mahasiswa.lapor.temukan') }}" class="px-5 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-900/30 transition-all flex items-center gap-2">
                <i class="fa-solid fa-bullhorn"></i>
                <span>Lapor Temuan</span>
            </a>
        </div>

        <!-- Decorative background shape -->
        <div class="absolute right-0 bottom-0 translate-x-10 translate-y-10 opacity-10 text-white pointer-events-none">
            <i class="fa-solid fa-magnifying-glass-location text-[240px]"></i>
        </div>
    </div>

    <!-- 4 Dashboard Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
        <!-- Card 1: Laporan Hilang -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                <i class="fa-solid fa-circle-exclamation"></i>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $stats['hilang'] }}</div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-0.5">Laporan Hilang</div>
            </div>
        </div>

        <!-- Card 2: Laporan Ditemukan -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                <i class="fa-solid fa-hand-holding-hand"></i>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $stats['ditemukan'] }}</div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-0.5">Laporan Ditemukan</div>
            </div>
        </div>

        <!-- Card 3: Dalam Proses -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                <i class="fa-solid fa-clock-rotate-left"></i>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $stats['proses'] }}</div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-0.5">Dalam Proses</div>
            </div>
        </div>

        <!-- Card 4: Berhasil Dikembalikan -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex items-center gap-4 hover:shadow-md transition-shadow">
            <div class="w-14 h-14 rounded-2xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl font-bold flex-shrink-0">
                <i class="fa-solid fa-champagne-glasses"></i>
            </div>
            <div>
                <div class="text-2xl sm:text-3xl font-black text-slate-900">{{ $stats['dikembalikan'] }}</div>
                <div class="text-xs font-bold text-slate-400 uppercase tracking-wider mt-0.5">Dikembalikan</div>
            </div>
        </div>
    </div>

    <!-- Main Section: Laporan Terbaru Saya & Notifikasi -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Table: Laporan Terbaru Saya (col-span-2) -->
        <div class="lg:col-span-2 bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h2 class="text-base font-bold text-slate-900">Laporan Terbaru Saya</h2>
                    <p class="text-xs text-slate-500 mt-0.5">Daftar laporan barang hilang atau temuan yang baru Anda buat</p>
                </div>
                <a href="{{ route('mahasiswa.laporan.saya') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto flex-1">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Kode</th>
                            <th class="py-3.5 px-6">Barang</th>
                            <th class="py-3.5 px-6">Kampus</th>
                            <th class="py-3.5 px-6">Jenis</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                        @forelse($myReports as $report)
                            @php
                                $badge = $report->status_badge;
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-mono font-semibold text-slate-900">{{ $report->kode_laporan }}</td>
                                <td class="py-4 px-6">
                                    <div class="font-bold text-slate-900">{{ $report->nama_barang }}</div>
                                    <div class="text-[11px] text-slate-400">{{ $report->kategori->nama_kategori }}</div>
                                </td>
                                <td class="py-4 px-6">
                                    <span class="text-slate-600">{{ $report->kampus->nama_kampus }}</span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($report->jenis_laporan === 'HILANG')
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-700 uppercase">HILANG</span>
                                    @else
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-700 uppercase">TEMUAN</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badge['bg'] }} uppercase inline-flex items-center gap-1">
                                        <i class="fa-solid {{ $badge['icon'] }}"></i>
                                        {{ $report->status }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('mahasiswa.laporan.detail', $report->id) }}" 
                                       class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-brand-ubsi hover:text-white font-bold text-slate-700 transition-colors">
                                        Detail
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-slate-400">
                                    <i class="fa-regular fa-folder-open text-3xl mb-2 block text-slate-300"></i>
                                    Anda belum membuat laporan kehilangan atau penemuan.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Notifications Preview Column (col-span-1) -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 flex flex-col">
            <div class="flex items-center justify-between pb-4 border-b border-slate-100 mb-4">
                <div class="flex items-center gap-2">
                    <i class="fa-solid fa-bell text-amber-500"></i>
                    <h2 class="text-sm font-bold text-slate-900">Notifikasi Terbaru</h2>
                </div>
                <a href="{{ route('mahasiswa.notifikasi') }}" class="text-xs font-semibold text-blue-600 hover:text-blue-800">
                    Buka Semua
                </a>
            </div>

            <div class="space-y-3 flex-1 overflow-y-auto">
                @forelse($unreadNotifications as $notif)
                    <div class="p-3.5 rounded-2xl bg-blue-50/50 border border-blue-100 space-y-1">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-slate-900">{{ $notif->judul }}</h4>
                            <span class="text-[10px] text-slate-400">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-[11px] text-slate-600 line-clamp-2">{{ $notif->pesan }}</p>
                        @if($notif->link)
                            <a href="{{ $notif->link }}" class="text-[10px] font-bold text-blue-600 hover:underline inline-block mt-1">
                                Cek Informasi &rarr;
                            </a>
                        @endif
                    </div>
                @empty
                    <div class="text-center py-10 text-xs text-slate-400">
                        <i class="fa-regular fa-bell-slash text-2xl mb-2 block text-slate-300"></i>
                        Semua notifikasi sudah dibaca.
                    </div>
                @endforelse
            </div>

            <!-- Smart Matching Info Card -->
            <div class="mt-6 p-4 rounded-2xl bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200/60 text-xs text-amber-900">
                <div class="flex items-center gap-2 font-bold mb-1">
                    <i class="fa-solid fa-bolt text-amber-500"></i>
                    <span>Fitur Smart Matching</span>
                </div>
                <p class="text-[11px] text-amber-800/80 leading-relaxed">
                    Sistem FINDIT otomatis mendeteksi kecocokan laporan kehilangan Anda dengan barang yang diamankan security.
                </p>
            </div>
        </div>

    </div>

</div>
@endsection
