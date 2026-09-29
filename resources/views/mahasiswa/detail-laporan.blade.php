@extends('layouts.app')

@section('title', $laporan->nama_barang . ' - Detail Laporan')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">
    
    <!-- Breadcrumb -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 text-xs text-slate-400 font-medium">
            <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-slate-600">Dashboard</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <a href="{{ route('mahasiswa.laporan.saya') }}" class="hover:text-slate-600">Laporan</a>
            <i class="fa-solid fa-chevron-right text-[10px]"></i>
            <span class="text-slate-600 font-mono">{{ $laporan->kode_laporan }}</span>
        </div>
        <a href="javascript:history.back()" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
            &larr; Kembali
        </a>
    </div>

    <!-- Status Banner Header -->
    @php
        $badge = $laporan->status_badge;
        $isOwner = ($laporan->user_id === Auth::id());
        $isDitemukan = ($laporan->jenis_laporan === 'DITEMUKAN');
    @endphp
    
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="space-y-1">
            <div class="flex items-center gap-2">
                <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $badge['bg'] }} uppercase flex items-center gap-1.5">
                    <i class="fa-solid {{ $badge['icon'] }}"></i>
                    <span>{{ $laporan->status }}</span>
                </span>
                <span class="text-xs font-mono font-bold text-slate-400 bg-slate-100 px-2.5 py-1 rounded-lg">
                    {{ $laporan->kode_laporan }}
                </span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight pt-1">
                {{ $laporan->nama_barang }}
            </h1>
        </div>

        <div class="flex items-center gap-2 flex-wrap">
            @if($isDitemukan && !$isOwner)
                @if($myClaim)
                    <div class="px-4 py-2 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-bold flex items-center gap-2">
                        <i class="fa-solid fa-clock"></i>
                        <span>Status Klaim Anda: {{ $myClaim->status }}</span>
                    </div>
                @elseif(in_array($laporan->status, ['BARANG DIAMANKAN', 'MENUNGGU VERIFIKASI']))
                    <a href="{{ route('mahasiswa.klaim.form', $laporan->id) }}" 
                       class="px-5 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-md shadow-amber-500/20 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-key"></i>
                        <span>Ini Barang Saya (Ajukan Klaim)</span>
                    </a>
                @endif
            @endif

            @if($isOwner && $laporan->canBeManagedByStudent())
                <a href="{{ route('mahasiswa.laporan.edit', $laporan->id) }}" 
                   class="px-4 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                    <i class="fa-solid fa-pen mr-1"></i> Edit Laporan
                </a>
            @endif
        </div>
    </div>

    <!-- Visual Progress Tracker (Timeline Laporan) -->
    @php
        $currentStep = 1;
        if (in_array($laporan->status, ['BARANG DIAMANKAN', 'SIAP DIAMBIL', 'DIKEMBALIKAN'])) {
            $currentStep = 2;
        }
        if ($myClaim && $myClaim->status === 'DISETUJUI') {
            $currentStep = 4;
        }
        if ($laporan->status === 'DIKEMBALIKAN') {
            $currentStep = 5;
        }

        $steps = [
            1 => ['title' => 'Laporan Dibuat', 'desc' => 'Diterima sistem', 'icon' => 'fa-file-lines'],
            2 => ['title' => 'Diamankan Kampus', 'desc' => 'Disimpan staf', 'icon' => 'fa-vault'],
            3 => ['title' => 'Klaim Diverifikasi', 'desc' => 'Validasi identitas', 'icon' => 'fa-user-check'],
            4 => ['title' => 'Tiket QR Terbit', 'desc' => 'Siap diambil', 'icon' => 'fa-qrcode'],
            5 => ['title' => 'Selesai Diserahkan', 'desc' => 'BAST resmi', 'icon' => 'fa-circle-check'],
        ];
    @endphp

    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
        <div class="flex items-center justify-between">
            <span class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                <i class="fa-solid fa-route text-brand-ubsi"></i> Timeline & Status Pelacakan Barang
            </span>
            <span class="text-xs font-bold text-brand-ubsi bg-blue-50 px-2.5 py-1 rounded-full border border-blue-200">
                Tahap {{ $currentStep }} dari 5
            </span>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 pt-2">
            @foreach($steps as $stepNum => $step)
                @php
                    $isPassed = ($currentStep >= $stepNum);
                    $isCurrent = ($currentStep === $stepNum);
                @endphp
                <div class="p-3 rounded-2xl border transition-all text-center space-y-1.5 {{ $isCurrent ? 'bg-blue-50 border-brand-ubsi shadow-sm' : ($isPassed ? 'bg-slate-50 border-slate-200' : 'bg-white border-slate-100 opacity-60') }}">
                    <div class="w-8 h-8 rounded-full mx-auto flex items-center justify-center text-xs font-bold {{ $isCurrent ? 'bg-brand-ubsi text-white shadow-md' : ($isPassed ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-500') }}">
                        @if($isPassed && !$isCurrent)
                            <i class="fa-solid fa-check text-xs"></i>
                        @else
                            <i class="fa-solid {{ $step['icon'] }} text-xs"></i>
                        @endif
                    </div>
                    <div class="text-xs font-bold {{ $isCurrent ? 'text-brand-ubsi' : 'text-slate-800' }}">
                        {{ $step['title'] }}
                    </div>
                    <div class="text-[10px] text-slate-400">
                        {{ $step['desc'] }}
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- Digital QR Code Ticket Card (If Claim Approved) -->
    @if($myClaim && $myClaim->status === 'DISETUJUI')
        <div class="p-6 sm:p-8 rounded-3xl bg-gradient-to-r from-slate-950 via-brand-ubsi to-slate-950 text-white shadow-2xl border border-blue-800 relative overflow-hidden space-y-5 animate-fadeIn">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-white/15 pb-4">
                <div class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-ubsi.png') }}" alt="Logo UBSI" class="w-10 h-10 object-contain rounded-xl bg-white p-1">
                    <div>
                        <span class="px-2.5 py-0.5 rounded-full bg-emerald-400/20 text-emerald-300 text-[10px] font-extrabold border border-emerald-400/30 uppercase tracking-wider">
                            Tiket Pengambilan Resmi
                        </span>
                        <h3 class="text-lg sm:text-xl font-black text-white tracking-tight mt-0.5">
                            Tiket Digital Serah Terima Barang
                        </h3>
                    </div>
                </div>

                <div class="text-right">
                    <span class="text-xs text-blue-200 block">Unit Pengambilan:</span>
                    <strong class="text-amber-300 text-xs sm:text-sm font-bold">{{ $laporan->kampus->nama_kampus }}</strong>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-12 gap-6 items-center">
                <!-- QR Code Display -->
                <div class="md:col-span-5 flex flex-col items-center justify-center p-5 rounded-2xl bg-white text-slate-900 shadow-xl space-y-3">
                    <div id="qrcode-ticket" class="w-44 h-44 flex items-center justify-center bg-white p-2"></div>
                    <div class="text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-bold tracking-wider block">Kode Tiket Pengambilan:</span>
                        <span class="font-mono text-lg font-black text-brand-ubsi tracking-wider">{{ $myClaim->kode_tiket }}</span>
                    </div>
                </div>

                <!-- Ticket Instructions & Owner Details -->
                <div class="md:col-span-7 space-y-3 text-xs">
                    <div class="p-4 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15 space-y-2">
                        <span class="text-amber-300 font-bold block text-xs flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check"></i> Klaim Anda Telah Disetujui!
                        </span>
                        <p class="text-slate-200 text-[11px] leading-relaxed">
                            Tunjukkan layar HP yang menampilkan QR Code ini ke <strong>Ruang Layanan & Staf Kampus {{ $laporan->kampus->nama_kampus }}</strong> untuk memverifikasi dan mengambil fisik barang:
                        </p>
                        <div class="p-2.5 rounded-xl bg-slate-900/60 border border-white/10 space-y-1 font-mono text-[11px]">
                            <div class="text-slate-300">Barang: <strong class="text-white">{{ $laporan->nama_barang }}</strong></div>
                            <div class="text-slate-300">Pemilik: <strong class="text-white">{{ Auth::user()->name }} ({{ Auth::user()->nim ?? '-' }})</strong></div>
                        </div>
                    </div>

                    @if($laporan->status === 'DIKEMBALIKAN' && $laporan->pengembalian)
                        <div class="p-3.5 rounded-2xl bg-emerald-500/20 border border-emerald-400/40 text-emerald-200 flex items-center justify-between">
                            <span class="text-xs font-semibold">
                                <i class="fa-solid fa-check-double text-emerald-400 mr-1"></i> Telah Diserahterimakan (No. BAST: {{ $laporan->pengembalian->nomor_bast }})
                            </span>
                            <a href="{{ route('petugas.bast.cetak', $laporan->pengembalian->id) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-white text-slate-900 font-bold text-xs hover:bg-slate-100 shadow transition-all">
                                <i class="fa-solid fa-print text-amber-500 mr-1"></i> BAST
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Include QRCode.js library -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const qrContainer = document.getElementById('qrcode-ticket');
                if (qrContainer) {
                    new QRCode(qrContainer, {
                        text: '{{ $myClaim->qr_token ?: $myClaim->kode_tiket }}',
                        width: 160,
                        height: 160,
                        colorDark: '#0f172a',
                        colorLight: '#ffffff',
                        correctLevel: QRCode.CorrectLevel.H
                    });
                }
            });
        </script>
    @endif

    <!-- Smart Auto-Matching Recommendations Banner (If HILANG and has matches) -->
    @if($laporan->jenis_laporan === 'HILANG' && isset($matches) && count($matches) > 0)
        <div class="p-6 rounded-3xl bg-gradient-to-r from-amber-50 via-white to-amber-50 border border-amber-200 shadow-sm space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <div class="w-9 h-9 rounded-xl bg-amber-500 text-white flex items-center justify-center text-base shadow-md shadow-amber-500/30">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div>
                        <h3 class="text-sm sm:text-base font-bold text-slate-900 flex items-center gap-2">
                            <span>Smart Auto-Matching: Ditemukan {{ count($matches) }} Barang Berpotensi Cocok!</span>
                        </h3>
                        <p class="text-[11px] text-slate-500">Sistem otomatis mencocokkan kemiripan kategori, lokasi kampus, dan waktu kehilangan.</p>
                    </div>
                </div>
                <span class="hidden sm:inline-flex px-3 py-1 rounded-full bg-amber-100 text-amber-800 text-[10px] font-bold border border-amber-200 uppercase">
                    Rekomendasi Cerdas
                </span>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 pt-1">
                @foreach($matches as $m)
                    @php
                        $itemMatch = is_array($m) ? ($m['item'] ?? ($m['laporan'] ?? null)) : $m;
                        $score = is_array($m) ? ($m['score'] ?? 75) : 75;
                        $reasons = is_array($m) && isset($m['reasons']) ? $m['reasons'] : ['Kecocokan Terdeteksi'];
                    @endphp
                    @if($itemMatch)
                    <div class="p-3.5 rounded-2xl bg-white border border-slate-200/80 shadow-sm hover:shadow-md hover:border-amber-400 transition-all flex flex-col justify-between space-y-3 group">
                        <div class="flex items-start gap-3">
                            <img src="{{ $itemMatch->foto_utama_url ?? asset('images/no-image.png') }}" alt="{{ $itemMatch->nama_barang }}" class="w-14 h-14 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                            <div class="min-w-0 space-y-0.5">
                                <span class="text-[9px] font-bold px-1.5 py-0.5 rounded bg-emerald-100 text-emerald-800">
                                    {{ $score }}% Cocok
                                </span>
                                <h4 class="text-xs font-bold text-slate-900 group-hover:text-amber-600 truncate transition-colors">
                                    {{ $itemMatch->nama_barang }}
                                </h4>
                                <p class="text-[10px] text-slate-400 truncate">
                                    {{ $itemMatch->kampus?->nama_kampus }}
                                </p>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[10px] text-slate-500 font-medium truncate">
                                {{ $reasons[0] ?? ($itemMatch->kategori?->nama_kategori ?? 'Kemiripan tinggi') }}
                            </span>
                            <a href="{{ route('mahasiswa.laporan.detail', $itemMatch->id) }}" class="px-2.5 py-1 rounded-lg bg-brand-ubsi hover:bg-blue-900 text-white text-[10px] font-bold transition-all flex items-center gap-1">
                                <span>Lihat & Klaim</span>
                                <i class="fa-solid fa-arrow-right text-[8px]"></i>
                            </a>
                        </div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif

    <!-- Main Detail Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Details Column (col-span-2) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Information Card -->
            <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
                <div>
                    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Deskripsi Kejadian</h2>
                    <p class="text-sm text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-2xl border border-slate-100">
                        {{ $laporan->deskripsi }}
                    </p>
                </div>

                @if($laporan->ciri_khusus)
                    <div>
                        <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Ciri-Ciri Khusus / Tanda Pengenal</h2>
                        <div class="p-4 rounded-2xl bg-amber-50/50 border border-amber-100 text-amber-950 text-xs leading-relaxed flex items-start gap-3">
                            <i class="fa-solid fa-fingerprint text-amber-500 text-base mt-0.5 flex-shrink-0"></i>
                            <div>
                                <span class="font-semibold block mb-0.5">Tanda Pengenal Spesifik:</span>
                                {{ $laporan->ciri_khusus }}
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Metadata List -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kampus UBSI</span>
                        <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-school text-blue-500"></i>
                            {{ $laporan->kampus->nama_kampus }}
                        </div>
                        <div class="text-[11px] text-slate-500 mt-0.5">{{ $laporan->kampus->kota }}</div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Kategori Barang</span>
                        <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid {{ $laporan->kategori->icon }} text-purple-500"></i>
                            {{ $laporan->kategori->nama_kategori }}
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Lokasi Tempat Kejadian</span>
                        <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-solid fa-location-dot text-rose-500"></i>
                            {{ $laporan->lokasi_kejadian }}
                        </div>
                    </div>

                    <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-100">
                        <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Tanggal & Waktu</span>
                        <div class="text-xs font-bold text-slate-800 flex items-center gap-2">
                            <i class="fa-regular fa-calendar text-slate-400"></i>
                            {{ $laporan->tanggal_kejadian->translatedFormat('d F Y') }}
                            @if($laporan->waktu_kejadian)
                                • {{ substr($laporan->waktu_kejadian, 0, 5) }} WIB
                            @endif
                        </div>
                    </div>
                </div>

                @if(count($laporan->foto_list) > 0)
                    <div class="pt-4 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-3">
                            <span class="text-xs font-bold text-slate-700 uppercase tracking-wider flex items-center gap-2">
                                <i class="fa-solid fa-images text-blue-600"></i>
                                <span>Foto Dokumentasi Barang</span>
                            </span>
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-brand-ubsi border border-blue-100">
                                <i class="fa-solid fa-camera mr-1"></i> {{ count($laporan->foto_list) }} Foto Tersedia
                            </span>
                        </div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            @foreach($laporan->foto_list as $index => $foto)
                                <div class="relative group rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 aspect-square shadow-sm cursor-pointer" onclick="openPhotoModal('{{ asset('storage/' . $foto) }}', 'Foto #{{ $index + 1 }} - {{ addslashes($laporan->nama_barang) }}')">
                                    <img src="{{ asset('storage/' . $foto) }}" alt="{{ $laporan->nama_barang }} - Foto {{ $index + 1 }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-xs font-semibold gap-1.5 backdrop-blur-xs">
                                        <i class="fa-solid fa-magnifying-glass-plus"></i> Perbesar
                                    </div>
                                    <span class="absolute bottom-2 left-2 px-2 py-0.5 rounded-md bg-black/60 text-white text-[10px] font-bold backdrop-blur-sm">
                                        Foto #{{ $index + 1 }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Smart Matching Candidates Box -->
            @if(count($matches) > 0)
                <div class="bg-gradient-to-br from-indigo-50/80 via-white to-blue-50/80 p-6 rounded-3xl border border-indigo-100 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-xl bg-indigo-600 text-white flex items-center justify-center text-xs">
                                <i class="fa-solid fa-bolt text-amber-300"></i>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900">⚡ Kemungkinan Barang Cocok (Smart Matching)</h3>
                                <p class="text-[11px] text-slate-500">Algoritma otomatis membandingkan kampus, kategori, nama, lokasi, dan tanggal</p>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-3">
                        @foreach($matches as $m)
                            @php
                                $matchedItem = is_array($m) ? ($m['item'] ?? ($m['laporan'] ?? null)) : $m;
                                $score = is_array($m) ? ($m['score'] ?? 75) : 75;
                                $breakdown = is_array($m) && isset($m['breakdown']) ? $m['breakdown'] : [];
                            @endphp
                            @if($matchedItem)
                            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2.5 py-0.5 text-[10px] font-extrabold rounded-full bg-emerald-100 text-emerald-800">
                                            {{ $score }}% Tingkat Kecocokan
                                        </span>
                                        <span class="text-[11px] font-mono text-slate-400">{{ $matchedItem->kode_laporan }}</span>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-900">{{ $matchedItem->nama_barang }}</h4>
                                    <p class="text-[11px] text-slate-500">
                                        {{ $matchedItem->kampus?->nama_kampus }} • {{ $matchedItem->lokasi_kejadian }} • {{ $matchedItem->tanggal_kejadian ? \Carbon\Carbon::parse($matchedItem->tanggal_kejadian)->translatedFormat('d M Y') : '-' }}
                                    </p>
                                    
                                    <!-- Breakdown tags -->
                                    @if(isset($breakdown['kampus']))
                                    <div class="flex items-center gap-2 pt-1 text-[10px] text-slate-400 flex-wrap">
                                        <span>Kampus: {{ $breakdown['kampus'] }}/30%</span>
                                        <span>Kategori: {{ $breakdown['kategori'] }}/20%</span>
                                        <span>Nama: {{ $breakdown['nama'] }}/20%</span>
                                        <span>Lokasi: {{ $breakdown['lokasi'] }}/15%</span>
                                        <span>Tanggal: {{ $breakdown['tanggal'] }}/15%</span>
                                    </div>
                                    @endif
                                </div>

                                <div class="flex items-center gap-2 flex-shrink-0">
                                    <a href="{{ route('mahasiswa.laporan.detail', $matchedItem->id) }}" 
                                       class="px-4 py-2 rounded-xl bg-blue-50 text-brand-ubsi hover:bg-brand-ubsi hover:text-white font-bold text-xs transition-colors">
                                        Cek Barang Ini
                                    </a>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif

        </div>

        <!-- Sidebar / Custody & Handover Info (col-span-1) -->
        <div class="space-y-6">
            
            <!-- Pelapor Info Card -->
            <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-3">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block">Informasi Pelapor</span>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-blue-100 text-brand-ubsi flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr($laporan->user->name, 0, 2)) }}
                    </div>
                    <div>
                        <div class="text-xs font-bold text-slate-800">{{ $laporan->user->name }}</div>
                        <div class="text-[11px] text-slate-400">NIM: {{ $laporan->user->nim ?? '-' }}</div>
                    </div>
                </div>
                <div class="text-[11px] text-slate-500 pt-2 border-t border-slate-100">
                    Dibuat pada: {{ $laporan->created_at->translatedFormat('d F Y, H:i') }} WIB
                </div>
            </div>

            <!-- Custody / Penyimpanan Fisik Card -->
            @if($laporan->penyimpanan)
                <div class="bg-emerald-50/60 p-6 rounded-3xl border border-emerald-200/80 shadow-sm space-y-3">
                    <div class="flex items-center gap-2 text-emerald-800 font-bold text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-vault text-emerald-600"></i>
                        <span>Pengamanan Fisik Barang</span>
                    </div>
                    
                    <div class="space-y-2 text-xs text-slate-700 pt-1">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Lokasi Pos Penyimpanan:</span>
                            <span class="font-bold text-emerald-950">{{ $laporan->penyimpanan->lokasi_penyimpanan }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Kondisi Fisik Barang:</span>
                            <span class="font-semibold text-slate-800">{{ $laporan->penyimpanan->kondisi_barang }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Admin Penerima:</span>
                            <span class="font-medium text-slate-800">{{ $laporan->penyimpanan->petugas->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Tanggal Diamankan:</span>
                            <span class="text-slate-600">{{ $laporan->penyimpanan->tanggal_diterima->translatedFormat('d M Y, H:i') }} WIB</span>
                        </div>
                        @if($laporan->penyimpanan->catatan)
                            <div class="pt-2 border-t border-emerald-200/60 text-[11px] text-emerald-900 italic">
                                "{{ $laporan->penyimpanan->catatan }}"
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Pengembalian Handover Card -->
            @if($laporan->pengembalian)
                <div class="bg-purple-50/60 p-6 rounded-3xl border border-purple-200/80 shadow-sm space-y-3">
                    <div class="flex items-center gap-2 text-purple-800 font-bold text-xs uppercase tracking-wider">
                        <i class="fa-solid fa-champagne-glasses text-purple-600"></i>
                        <span>Telah Selesai Dikembalikan</span>
                    </div>
                    
                    <div class="space-y-2 text-xs text-slate-700 pt-1">
                        <div>
                            <span class="text-slate-400 block text-[11px]">Diterima Oleh:</span>
                            <span class="font-bold text-purple-950">{{ $laporan->pengembalian->user->name }} (NIM: {{ $laporan->pengembalian->user->nim ?? '-' }})</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Admin Penyerah:</span>
                            <span class="font-medium text-slate-800">{{ $laporan->pengembalian->petugas->name }}</span>
                        </div>
                        <div>
                            <span class="text-slate-400 block text-[11px]">Waktu Pengembalian:</span>
                            <span class="text-slate-600">{{ $laporan->pengembalian->tanggal_pengembalian->translatedFormat('d F Y, H:i') }} WIB</span>
                        </div>
                        @if($laporan->pengembalian->catatan)
                            <div class="pt-2 border-t border-purple-200/60 text-[11px] text-purple-900 italic">
                                "{{ $laporan->pengembalian->catatan }}"
                            </div>
                        @endif
                    </div>
                </div>
            @endif

        </div>

    </div>

    <!-- Lightbox Modal for Photo Preview -->
    <div id="photoModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
        <div class="relative max-w-4xl w-full max-h-[90vh] flex flex-col items-center">
            <div class="flex items-center justify-between w-full mb-3 px-2 text-white">
                <span id="photoModalCaption" class="text-xs sm:text-sm font-semibold truncate"></span>
                <button type="button" onclick="closePhotoModal()" class="w-8 h-8 rounded-full bg-white/20 hover:bg-white/40 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-white"></i>
                </button>
            </div>
            <div class="w-full flex items-center justify-center overflow-hidden rounded-2xl bg-black">
                <img id="photoModalImg" src="" alt="Pratinjau Foto" class="max-h-[80vh] w-auto object-contain rounded-2xl">
            </div>
        </div>
    </div>

    <script>
        function openPhotoModal(src, caption) {
            document.getElementById('photoModalImg').src = src;
            document.getElementById('photoModalCaption').innerText = caption;
            document.getElementById('photoModal').classList.remove('hidden');
        }
        function closePhotoModal() {
            document.getElementById('photoModal').classList.add('hidden');
        }
        document.getElementById('photoModal')?.addEventListener('click', function(e) {
            if (e.target === this) closePhotoModal();
        });
    </script>

</div>
@endsection
