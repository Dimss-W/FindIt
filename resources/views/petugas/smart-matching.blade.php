@extends('layouts.app')

@section('title', 'Smart Matching Barang Hilang & Ditemukan')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-amber-500 to-indigo-600 text-white flex items-center justify-center text-sm shadow-md">
                    <i class="fa-solid fa-bolt text-amber-200"></i>
                </span>
                Smart Matching: Kecocokan Otomatis
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Sistem menghitung kemiripan bobot: Kampus (30%), Kategori (20%), Nama (20%), Lokasi (15%), Tanggal (15%)
            </p>
        </div>
    </div>

    <!-- Weight Breakdown Banner -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <div class="p-3 bg-white rounded-2xl border border-slate-200/80 shadow-xs text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kampus</span>
            <span class="text-base font-black text-blue-600">30%</span>
            <span class="text-[10px] text-slate-500 block">Kecocokan unit kampus</span>
        </div>
        <div class="p-3 bg-white rounded-2xl border border-slate-200/80 shadow-xs text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Kategori</span>
            <span class="text-base font-black text-purple-600">20%</span>
            <span class="text-[10px] text-slate-500 block">Kategori barang</span>
        </div>
        <div class="p-3 bg-white rounded-2xl border border-slate-200/80 shadow-xs text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Nama Barang</span>
            <span class="text-base font-black text-indigo-600">20%</span>
            <span class="text-[10px] text-slate-500 block">Kemiripan teks judul</span>
        </div>
        <div class="p-3 bg-white rounded-2xl border border-slate-200/80 shadow-xs text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lokasi</span>
            <span class="text-base font-black text-rose-600">15%</span>
            <span class="text-[10px] text-slate-500 block">Kemiripan area / gedung</span>
        </div>
        <div class="p-3 bg-white rounded-2xl border border-slate-200/80 shadow-xs text-center">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Tanggal</span>
            <span class="text-base font-black text-emerald-600">15%</span>
            <span class="text-[10px] text-slate-500 block">Kedekatan hari kejadian</span>
        </div>
    </div>

    <!-- Match Results List -->
    <div class="space-y-6">
        @forelse($matchedPairs as $pair)
            @php
                $lost = $pair['lost'];
                $matches = $pair['matches'];
            @endphp

            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 sm:p-8 space-y-6">
                <!-- Lost Item Banner -->
                <div class="p-4 rounded-2xl bg-rose-50/70 border border-rose-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-start gap-3">
                        <div class="w-10 h-10 rounded-xl bg-rose-600 text-white flex items-center justify-center font-black flex-shrink-0 text-sm">
                            <i class="fa-solid fa-circle-exclamation"></i>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-bold bg-rose-200 text-rose-800 px-2 py-0.5 rounded uppercase">Laporan Kehilangan</span>
                                <span class="text-[11px] font-mono text-slate-400">{{ $lost->kode_laporan }}</span>
                            </div>
                            <h3 class="text-base font-bold text-slate-900 mt-0.5">{{ $lost->nama_barang }}</h3>
                            <p class="text-xs text-slate-500">
                                Pelapor: <strong>{{ $lost->user->name }}</strong> (Telp: {{ $lost->user->no_telp ?? '-' }}) • Lokasi: {{ $lost->lokasi_kejadian }} • {{ $lost->tanggal_kejadian->translatedFormat('d M Y') }}
                            </p>
                        </div>
                    </div>

                    <a href="{{ route('mahasiswa.laporan.detail', $lost->id) }}" class="text-xs font-bold text-rose-600 hover:text-rose-800">
                        Buka Laporan Kehilangan &rarr;
                    </a>
                </div>

                <!-- Matches Grid -->
                <div class="space-y-4">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider flex items-center gap-2">
                        <i class="fa-solid fa-link text-indigo-500"></i>
                        <span>Kandidat Barang Ditemukan yang Cocok ({{ count($matches) }} Item Terdeteksi):</span>
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($matches as $m)
                            @php
                                $candidate = $m['item'];
                                $score = $m['score'];
                                $breakdown = $m['breakdown'];
                            @endphp

                            <div class="p-5 rounded-2xl bg-slate-50/80 border border-slate-200/80 hover:bg-white hover:border-blue-400 hover:shadow-md transition-all flex flex-col justify-between">
                                <div class="space-y-3">
                                    <div class="flex items-center justify-between">
                                        <!-- Big Match Badge -->
                                        <div class="px-3 py-1 rounded-full bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs font-black flex items-center gap-1.5 shadow-xs">
                                            <i class="fa-solid fa-bolt text-amber-500"></i>
                                            <span>⚡ Tingkat Kecocokan: {{ $score }}%</span>
                                        </div>
                                        <span class="text-[10px] font-mono font-semibold text-slate-400">{{ $candidate->kode_laporan }}</span>
                                    </div>

                                    <div>
                                        <h5 class="text-sm font-bold text-slate-900">{{ $candidate->nama_barang }}</h5>
                                        <p class="text-xs text-slate-500 mt-0.5 line-clamp-2">{{ $candidate->deskripsi }}</p>
                                    </div>

                                    <!-- Comparison Details -->
                                    <div class="p-3 bg-white rounded-xl border border-slate-100 text-[11px] space-y-1">
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-400">Kampus (30%):</span>
                                            <span class="font-bold {{ $breakdown['kampus'] == 30 ? 'text-emerald-600' : 'text-slate-400' }}">
                                                {{ $breakdown['kampus'] }}/30% ({{ $candidate->kampus->nama_kampus }})
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-400">Kategori (20%):</span>
                                            <span class="font-bold {{ $breakdown['kategori'] == 20 ? 'text-emerald-600' : 'text-slate-400' }}">
                                                {{ $breakdown['kategori'] }}/20% ({{ $candidate->kategori->nama_kategori }})
                                            </span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-400">Kemiripan Nama (20%):</span>
                                            <span class="font-bold text-slate-700">{{ $breakdown['nama'] }}/20%</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-400">Lokasi (15%):</span>
                                            <span class="font-bold text-slate-700">{{ $breakdown['lokasi'] }}/15% ({{ $candidate->lokasi_kejadian }})</span>
                                        </div>
                                        <div class="flex items-center justify-between">
                                            <span class="text-slate-400">Tanggal Kejadian (15%):</span>
                                            <span class="font-bold text-slate-700">{{ $breakdown['tanggal'] }}/15% ({{ $candidate->tanggal_kejadian->translatedFormat('d M Y') }})</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4 mt-4 border-t border-slate-100 flex items-center justify-between">
                                    <span class="text-[11px] font-bold text-slate-500">
                                        Status: <span class="text-emerald-600">{{ $candidate->status }}</span>
                                    </span>
                                    <a href="{{ route('mahasiswa.laporan.detail', $candidate->id) }}" 
                                       class="px-4 py-1.5 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-xs transition-colors">
                                        Lihat Barang Temuan
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white p-16 rounded-3xl border border-slate-200/80 text-center space-y-3 shadow-sm">
                <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-3xl mx-auto">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Tidak Ada Pasangan Barang yang Cocok Saat Ini</h3>
                <p class="text-xs text-slate-400 max-w-md mx-auto">
                    Sistem akan secara otomatis mencocokkan setiap laporan kehilangan dan barang temuan baru yang masuk.
                </p>
            </div>
        @endforelse
    </div>

</div>
@endsection
