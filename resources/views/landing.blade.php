@extends('layouts.public')

@section('title', 'FINDIT - UBSI Lost & Found Terpadu')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient pt-12 pb-24 border-b border-slate-200/60 relative overflow-hidden">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="text-center max-w-3xl mx-auto space-y-5">
            <div class="flex items-center justify-center">
                <img src="{{ asset('images/logo-ubsi.png') }}" alt="Logo Resmi UBSI" class="w-16 h-16 sm:w-20 sm:h-20 object-contain drop-shadow-md ">
            </div>

            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-blue-100/80 border border-blue-200/80 text-brand-ubsi text-xs font-semibold shadow-sm">
                <i class="fa-solid fa-shield-halved text-brand-ubsi"></i>
                <span>Platform Resmi Pengelolaan Barang Hilang & Ditemukan UBSI</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-slate-900 tracking-tight leading-tight sm:leading-tight">
                Kehilangan Barang di Kampus? <br>
                <span class="text-brand-ubsi">Temukan Kembali</span> Bersama FindIt.
            </h1>

            <p class="text-base sm:text-lg text-slate-600 font-normal leading-relaxed max-w-2xl mx-auto">
                Platform Lost & Found UBSI untuk melaporkan, mencari, mengamankan, dan mengembalikan barang di berbagai kampus UBSI secara transparan dan terverifikasi.
            </p>

            <!-- Main Quick Search Box -->
            <div class="pt-4 max-w-2xl mx-auto">
                <form action="{{ route('search') }}" method="GET" class="p-2 sm:p-2.5 bg-white rounded-xl shadow-xl shadow-blue-900/5 border border-slate-200/80 flex flex-col sm:flex-row items-center gap-2">
                    <div class="flex-1 flex items-center gap-3 px-4 w-full">
                        <i class="fa-solid fa-magnifying-glass text-slate-400 text-lg"></i>
                        <input type="text" name="q" placeholder="Cari nama barang (contoh: Dompet, KTM, Laptop)..." 
                               class="w-full py-2.5 text-sm bg-transparent outline-none text-slate-800 placeholder-slate-400 font-medium">
                    </div>
                    
                    <div class="sm:w-56 w-full px-4 border-t sm:border-t-0 sm:border-l border-slate-100 py-1">
                        <select name="kampus_id" class="w-full text-xs font-medium text-slate-600 bg-transparent outline-none cursor-pointer py-2">
                            <option value="">Semua Kampus UBSI</option>
                            @foreach($kampusList as $kmp)
                                <option value="{{ $kmp->id }}">{{ $kmp->nama_kampus }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="w-full sm:w-auto px-7 py-3.5 rounded-2xl bg-brand-ubsi hover:bg-blue-900 text-white text-sm font-bold shadow-lg shadow-blue-900/20 transition-all flex items-center justify-center gap-2 flex-shrink-0">
                        <span>Cari Barang</span>
                        <i class="fa-solid fa-arrow-right text-xs"></i>
                    </button>
                </form>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center justify-center gap-4 pt-2">
                <a href="{{ route('search') }}" class="px-6 py-3 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-sm font-semibold shadow-md transition-all flex items-center gap-2">
                    <i class="fa-solid fa-compass text-amber-400"></i>
                    <span>Jelajahi Semua Barang</span>
                </a>
                @auth
                    @if(Auth::user()->isMahasiswa())
                        <a href="{{ route('mahasiswa.lapor.hilang') }}" class="px-6 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-sm font-semibold shadow-md shadow-rose-600/20 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span>Laporkan Kehilangan</span>
                        </a>
                        <a href="{{ route('mahasiswa.lapor.temukan') }}" class="px-6 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold shadow-md shadow-emerald-600/20 transition-all flex items-center gap-2">
                            <i class="fa-solid fa-bullhorn"></i>
                            <span>Laporkan Temuan</span>
                        </a>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="px-6 py-3 rounded-2xl bg-white hover:bg-slate-100 text-slate-800 border border-slate-200 text-sm font-semibold shadow-sm transition-all flex items-center gap-2">
                        <i class="fa-solid fa-circle-plus text-blue-600"></i>
                        <span>Laporkan Barang (Masuk Dahulu)</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>
</section>

<!-- Statistik FindIt Section -->
<section class="py-12 bg-white border-b border-slate-200/80 -mt-8 relative z-20 max-w-6xl mx-auto rounded-xl shadow-xl shadow-slate-200/50 px-6 sm:px-10">
    <div class="text-center mb-8">
        <span class="text-xs font-bold text-brand-ubsi tracking-wider uppercase">Statistik Transparansi</span>
        <h2 class="text-2xl font-black text-slate-900 mt-1">Aktivitas FindIt Terkini</h2>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
        <!-- Card 1: Total Laporan -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-700 flex items-center justify-center mx-auto mb-3 text-lg font-bold">
                <i class="fa-solid fa-folder-open"></i>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($stats['total_laporan']) }}</div>
            <div class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-wider">Total Laporan</div>
        </div>

        <!-- Card 2: Barang Ditemukan -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center mx-auto mb-3 text-lg font-bold">
                <i class="fa-solid fa-hand-holding-hand"></i>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($stats['barang_ditemukan']) }}</div>
            <div class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-wider">Barang Ditemukan</div>
        </div>

        <!-- Card 3: Barang Diamankan -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 text-indigo-700 flex items-center justify-center mx-auto mb-3 text-lg font-bold">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($stats['barang_diamankan']) }}</div>
            <div class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-wider">Barang Diamankan</div>
        </div>

        <!-- Card 4: Barang Dikembalikan -->
        <div class="p-5 rounded-2xl bg-slate-50 border border-slate-100 text-center hover:shadow-md transition-shadow">
            <div class="w-12 h-12 rounded-xl bg-purple-100 text-purple-700 flex items-center justify-center mx-auto mb-3 text-lg font-bold">
                <i class="fa-solid fa-handshake"></i>
            </div>
            <div class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($stats['barang_dikembalikan']) }}</div>
            <div class="text-xs font-semibold text-slate-500 mt-1 uppercase tracking-wider">Telah Dikembalikan</div>
        </div>
    </div>
</section>

<!-- Bagaimana FindIt Bekerja (5 Langkah) -->
<section class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-2xl mx-auto mb-16 space-y-3">
            <span class="text-xs font-bold text-blue-600 tracking-wider uppercase px-3 py-1 bg-blue-100 rounded-full">Alur Terintegrasi</span>
            <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">Bagaimana FindIt Bekerja?</h2>
            <p class="text-sm text-slate-600">Alur transparan menghubungkan mahasiswa pelapor, penemu, dan petugas keamanan kampus UBSI.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 relative">
            <!-- Step 1 -->
            <div class="p-6 rounded-xl bg-white border border-slate-200/80 shadow-sm relative group hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 text-brand-ubsi flex items-center justify-center font-black text-lg mb-4">
                    1
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <span>Laporkan</span>
                    <i class="fa-solid fa-pen-to-square text-blue-500 text-xs"></i>
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Mahasiswa mengisi formulir kehilangan atau penemuan barang dengan memilih kampus UBSI terkait.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="p-6 rounded-xl bg-white border border-slate-200/80 shadow-sm relative group hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 text-amber-700 flex items-center justify-center font-black text-lg mb-4">
                    2
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <span>Verifikasi</span>
                    <i class="fa-solid fa-shield-check text-amber-500 text-xs"></i>
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Barang temuan diserahkan ke Petugas Keamanan, diperiksa kondisi fisiknya, dan disimpan di pos resmi.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="p-6 rounded-xl bg-white border border-slate-200/80 shadow-sm relative group hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 rounded-2xl bg-indigo-100 text-indigo-700 flex items-center justify-center font-black text-lg mb-4">
                    3
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <span>Pencocokan Sistem</span>
                    <i class="fa-solid fa-code-compare text-indigo-500 text-xs"></i>
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sistem otomatis menghitung kecocokan berbasis bobot kampus, kategori, nama, lokasi, dan tanggal.
                </p>
            </div>

            <!-- Step 4 -->
            <div class="p-6 rounded-xl bg-white border border-slate-200/80 shadow-sm relative group hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-lg mb-4">
                    4
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <span>Klaim</span>
                    <i class="fa-solid fa-key text-emerald-500 text-xs"></i>
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Pemilik mengajukan bukti kepemilikan. Petugas keamanan menelaah dan menyetujui klaim yang sah.
                </p>
            </div>

            <!-- Step 5 -->
            <div class="p-6 rounded-xl bg-white border border-slate-200/80 shadow-sm relative group hover:-translate-y-1 transition-transform">
                <div class="w-12 h-12 rounded-2xl bg-purple-100 text-purple-700 flex items-center justify-center font-black text-lg mb-4">
                    5
                </div>
                <h3 class="text-base font-bold text-slate-900 mb-2 flex items-center gap-2">
                    <span>Kembalikan</span>
                    <i class="fa-solid fa-handshake text-purple-500 text-xs"></i>
                </h3>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Mahasiswa mengambil fisik barang di Pos Security dengan validasi KTM/KTP dan penerbitan Berita Acara Serah Terima (BAST) resmi.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Kategori Barang Section -->
<section class="py-20 bg-white border-y border-slate-200/80">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-12">
            <div>
                <span class="text-xs font-bold text-brand-ubsi tracking-wider uppercase">Pengelompokan</span>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight mt-1">Kategori Barang Kampus</h2>
                <p class="text-sm text-slate-500 mt-1">Telusuri barang berdasarkan kategori yang terdaftar di database.</p>
            </div>
            <a href="{{ route('search') }}" class="mt-4 md:mt-0 inline-flex items-center gap-2 text-sm font-bold text-blue-600 hover:text-blue-800">
                <span>Lihat Semua Kategori</span>
                <i class="fa-solid fa-arrow-right text-xs"></i>
            </a>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-7 gap-4">
            @foreach($kategoris as $kat)
                <a href="{{ route('search', ['kategori_id' => $kat->id]) }}" 
                   class="p-5 rounded-2xl bg-slate-50 border border-slate-100 hover:border-blue-300 hover:bg-blue-50/50 hover:shadow-lg hover:-translate-y-1 transition-all text-center group flex flex-col items-center justify-center">
                    <div class="w-14 h-14 rounded-2xl bg-white shadow-sm flex items-center justify-center text-brand-ubsi text-2xl mb-3 group-hover:scale-110 transition-transform border border-slate-100">
                        <i class="fa-solid {{ $kat->icon }}"></i>
                    </div>
                    <span class="text-xs font-bold text-slate-800 group-hover:text-brand-ubsi transition-colors leading-snug">{{ $kat->nama_kategori }}</span>
                    <span class="text-[10px] text-slate-400 font-medium mt-1">{{ $kat->laporan_barang_count }} Laporan Aktif</span>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Barang Terbaru (Hilang & Ditemukan) -->
<section class="py-24 bg-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-16">
        
        <!-- Tab Hilang Terkini -->
        <div>
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-rose-500 animate-ping"></span>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Barang Hilang Terbaru (Sedang Dicari)</h2>
                </div>
                <a href="{{ route('search', ['jenis_laporan' => 'HILANG']) }}" class="text-xs font-bold text-rose-600 hover:text-rose-800 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($recentLost as $lost)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-rose-100 text-rose-700 border border-rose-200 uppercase">
                                    <i class="fa-solid fa-magnifying-glass text-[9px] mr-1"></i> Sedang Dicari
                                </span>
                                <span class="text-[11px] font-mono text-slate-400">{{ $lost->kode_laporan }}</span>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 line-clamp-1 mb-1">{{ $lost->nama_barang }}</h3>
                            <p class="text-xs text-slate-500 line-clamp-2 mb-4 leading-relaxed">{{ $lost->deskripsi }}</p>

                            <div class="mt-auto space-y-2 pt-3 border-t border-slate-100 text-xs text-slate-500">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-school text-blue-500 w-4"></i>
                                    <span class="truncate font-medium text-slate-700">{{ $lost->kampus->nama_kampus }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-rose-500 w-4"></i>
                                    <span class="truncate">{{ $lost->lokasi_kejadian }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                    <i class="fa-regular fa-calendar text-slate-400 w-4"></i>
                                    <span>{{ $lost->tanggal_kejadian->translatedFormat('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-slate-500">
                                <i class="fa-solid fa-tag text-slate-400 mr-1"></i> {{ $lost->kategori->nama_kategori }}
                            </span>
                            @auth
                                <a href="{{ route('mahasiswa.laporan.detail', $lost->id) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Detail &rarr;</a>
                            @else
                                <a href="{{ route('login') }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Detail &rarr;</a>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 p-8 bg-white rounded-2xl text-center text-xs text-slate-400">
                        Belum ada laporan kehilangan baru.
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Tab Ditemukan Terkini -->
        <div>
            <div class="flex items-center justify-between mb-8">
                <div class="flex items-center gap-3">
                    <span class="w-3 h-3 rounded-full bg-emerald-500 animate-ping"></span>
                    <h2 class="text-2xl font-black text-slate-900 tracking-tight">Barang Ditemukan (Menunggu Pengambilan)</h2>
                </div>
                <a href="{{ route('search', ['jenis_laporan' => 'DITEMUKAN']) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800 flex items-center gap-1">
                    <span>Lihat Semua</span>
                    <i class="fa-solid fa-chevron-right text-[10px]"></i>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($recentFound as $found)
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden hover:shadow-md transition-shadow flex flex-col">
                        <div class="p-5 flex-1 flex flex-col">
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200 uppercase">
                                    <i class="fa-solid fa-shield-halved text-[9px] mr-1"></i> {{ $found->status }}
                                </span>
                                <span class="text-[11px] font-mono text-slate-400">{{ $found->kode_laporan }}</span>
                            </div>

                            <h3 class="text-base font-bold text-slate-900 line-clamp-1 mb-1">{{ $found->nama_barang }}</h3>
                            <p class="text-xs text-slate-500 line-clamp-2 mb-4 leading-relaxed">{{ $found->deskripsi }}</p>

                            <div class="mt-auto space-y-2 pt-3 border-t border-slate-100 text-xs text-slate-500">
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-school text-blue-500 w-4"></i>
                                    <span class="truncate font-medium text-slate-700">{{ $found->kampus->nama_kampus }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <i class="fa-solid fa-location-dot text-emerald-500 w-4"></i>
                                    <span class="truncate">{{ $found->lokasi_kejadian }}</span>
                                </div>
                                <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                    <i class="fa-regular fa-calendar text-slate-400 w-4"></i>
                                    <span>{{ $found->tanggal_kejadian->translatedFormat('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-slate-50 border-t border-slate-100 flex items-center justify-between">
                            <span class="text-[11px] font-semibold text-slate-500">
                                <i class="fa-solid fa-tag text-slate-400 mr-1"></i> {{ $found->kategori->nama_kategori }}
                            </span>
                            @auth
                                <a href="{{ route('mahasiswa.laporan.detail', $found->id) }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800">Detail & Klaim &rarr;</a>
                            @else
                                <a href="{{ route('login') }}" class="text-xs font-bold text-emerald-600 hover:text-emerald-800">Detail & Klaim &rarr;</a>
                            @endauth
                        </div>
                    </div>
                @empty
                    <div class="col-span-4 p-8 bg-white rounded-2xl text-center text-xs text-slate-400">
                        Belum ada barang temuan baru.
                    </div>
                @endforelse
            </div>
        </div>

    </div>
</section>

<!-- Tanya Jawab Seputar SOP & Prosedur Kampus (FAQ) -->
<section id="faq-section" class="py-20 bg-slate-50 border-t border-slate-200/80" x-data="{ activeFaq: null }">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-10">
        
        <div class="text-center space-y-3">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-100 text-brand-ubsi text-xs font-bold uppercase tracking-wider">
                <i class="fa-solid fa-circle-question"></i> Prosedur & Tata Tertib
            </div>
            <h2 class="text-3xl font-black text-slate-900 tracking-tight">
                Tanya Jawab Layanan Lost & Found Kampus
            </h2>
            <p class="text-sm text-slate-600 max-w-xl mx-auto">
                Panduan resmi operasional, ketentuan penyimpanan, dan prosedur verifikasi kepemilikan barang di Universitas Bina Sarana Informatika.
            </p>
        </div>

        <div class="space-y-3">
            <!-- FAQ 1 -->
            <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs transition-all">
                <button type="button" 
                        @click="activeFaq = (activeFaq === 1 ? null : 1)" 
                        class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-sm text-slate-900 hover:text-brand-ubsi transition-colors">
                    <span class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-brand-ubsi flex items-center justify-center text-xs flex-shrink-0">1</span>
                        <span>Berapa lama batas waktu penyimpanan barang temuan di Pos Pengamanan?</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" 
                       :class="activeFaq === 1 ? 'rotate-180 text-brand-ubsi' : ''"></i>
                </button>
                <div x-show="activeFaq === 1" x-collapse x-cloak class="px-5 pb-5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                    Berdasarkan ketentuan operasional inventaris kampus UBSI, barang temuan disimpan di loker Pos Pengamanan maksimal selama <strong>30 hari kalender</strong>. Jika dalam kurun waktu tersebut tidak ada pihak yang mengajukan klaim terverifikasi, barang akan diserahkan secara resmi ke Biro Kemahasiswaan untuk dialokasikan pada program donasi atau kegiatan sosial kampus sesuai SK Rektor.
                </div>
            </div>

            <!-- FAQ 2 -->
            <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs transition-all">
                <button type="button" 
                        @click="activeFaq = (activeFaq === 2 ? null : 2)" 
                        class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-sm text-slate-900 hover:text-brand-ubsi transition-colors">
                    <span class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-brand-ubsi flex items-center justify-center text-xs flex-shrink-0">2</span>
                        <span>Bagaimana jika barang saya tertinggal di kampus UBSI cabang lain?</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" 
                       :class="activeFaq === 2 ? 'rotate-180 text-brand-ubsi' : ''"></i>
                </button>
                <div x-show="activeFaq === 2" x-collapse x-cloak class="px-5 pb-5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                    Sistem FINDIT mengintegrasikan seluruh <strong>27 unit kampus UBSI di Indonesia</strong> (wilayah Jabodetabek dan PSDKU seperti Sukabumi, Solo, Yogyakarta, Pontianak). Anda cukup memilih unit kampus tempat kejadian saat membuat laporan atau mencari barang. Pengambilan fisik tetap dilayani oleh petugas di Pos Pengamanan unit kampus tempat barang diamankan.
                </div>
            </div>

            <!-- FAQ 3 -->
            <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs transition-all">
                <button type="button" 
                        @click="activeFaq = (activeFaq === 3 ? null : 3)" 
                        class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-sm text-slate-900 hover:text-brand-ubsi transition-colors">
                    <span class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-brand-ubsi flex items-center justify-center text-xs flex-shrink-0">3</span>
                        <span>Dokumen dan syarat apa saja yang wajib dibawa saat mengambil barang?</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" 
                       :class="activeFaq === 3 ? 'rotate-180 text-brand-ubsi' : ''"></i>
                </button>
                <div x-show="activeFaq === 3" x-collapse x-cloak class="px-5 pb-5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50 space-y-1.5">
                    <p>Saat proses serah terima fisik di Pos Pengamanan Kampus, Anda wajib:</p>
                    <ul class="list-disc list-inside space-y-1 text-slate-700">
                        <li>Membawa <strong>Kartu Tanda Mahasiswa (KTM)</strong> atau KTP asli untuk verifikasi data pemohon.</li>
                        <li>Memperlihatkan <strong>Tiket Digital Serah Terima (QR Code)</strong> dari sistem FINDIT yang telah disetujui.</li>
                        <li>Khusus barang elektronik (Laptop/HP): Wajib membuka kunci layar (passcode/pola) di depan petugas.</li>
                        <li>Khusus kendaraan / helm: Wajib memperlihatkan STNK asli yang bersesuaian.</li>
                    </ul>
                </div>
            </div>

            <!-- FAQ 4 -->
            <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs transition-all">
                <button type="button" 
                        @click="activeFaq = (activeFaq === 4 ? null : 4)" 
                        class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-sm text-slate-900 hover:text-brand-ubsi transition-colors">
                    <span class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-brand-ubsi flex items-center justify-center text-xs flex-shrink-0">4</span>
                        <span>Bagaimana membuktikan kepemilikan jika nota atau dus barang sudah hilang?</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" 
                       :class="activeFaq === 4 ? 'rotate-180 text-brand-ubsi' : ''"></i>
                </button>
                <div x-show="activeFaq === 4" x-collapse x-cloak class="px-5 pb-5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                    Pembuktian kepemilikan tidak mutlak memerlukan nota pembelian. Anda dapat menjelaskan ciri fisik khusus yang tersembunyi (misalnya stiker di balik casing, warna gantungan kunci, isi khusus dalam dompet, nomor IMEI, atau foto dokumentasi lama Anda bersama barang tersebut).
                </div>
            </div>

            <!-- FAQ 5 -->
            <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs transition-all">
                <button type="button" 
                        @click="activeFaq = (activeFaq === 5 ? null : 5)" 
                        class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-sm text-slate-900 hover:text-brand-ubsi transition-colors">
                    <span class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-brand-ubsi flex items-center justify-center text-xs flex-shrink-0">5</span>
                        <span>Kapan jam operasional loket pengambilan barang di pos pengamanan?</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" 
                       :class="activeFaq === 5 ? 'rotate-180 text-brand-ubsi' : ''"></i>
                </button>
                <div x-show="activeFaq === 5" x-collapse x-cloak class="px-5 pb-5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                    Pos Pengamanan dan Layanan Kampus UBSI melayani pengambilan barang setiap hari kerja:<br>
                    <strong>&bull; Senin – Jumat:</strong> Pukul 08.00 – 17.00 WIB<br>
                    <strong>&bull; Sabtu:</strong> Pukul 08.00 – 12.00 WIB<br>
                    <span class="text-slate-400 italic">*(Hari Minggu dan Hari Libur Nasional layanan loket tutup).*</span>
                </div>
            </div>

            <!-- FAQ 6 -->
            <div class="bg-white rounded-xl border border-slate-200/80 overflow-hidden shadow-xs transition-all">
                <button type="button" 
                        @click="activeFaq = (activeFaq === 6 ? null : 6)" 
                        class="w-full p-5 text-left flex items-center justify-between gap-4 font-bold text-sm text-slate-900 hover:text-brand-ubsi transition-colors">
                    <span class="flex items-center gap-3">
                        <span class="w-7 h-7 rounded-lg bg-blue-50 text-brand-ubsi flex items-center justify-center text-xs flex-shrink-0">6</span>
                        <span>Saya menemukan barang milik orang lain di kampus, apa yang harus saya lakukan?</span>
                    </span>
                    <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform duration-200" 
                       :class="activeFaq === 6 ? 'rotate-180 text-brand-ubsi' : ''"></i>
                </button>
                <div x-show="activeFaq === 6" x-collapse x-cloak class="px-5 pb-5 pt-1 text-xs text-slate-600 leading-relaxed border-t border-slate-100 bg-slate-50/50">
                    Silakan isi formulir <strong>Laporkan Temuan</strong> di FINDIT, lalu serahkan fisik barang tersebut ke Pos Pengamanan atau Staf Layanan Kampus terdekat dalam waktu 1x24 jam. Petugas akan mencatat kondisi fisik dan menyimpannya di loker brankas kampus hingga pemilik sah mengajukan klaim.
                </div>
            </div>
        </div>

    </div>
</section>

<!-- Multi-Campus Banner -->
<section class="py-16 bg-brand-ubsi text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row items-center justify-between gap-8">
            <div class="space-y-3 max-w-2xl text-center lg:text-left">
                <span class="px-3 py-1 bg-white/10 rounded-full text-xs font-bold text-amber-300 uppercase tracking-wider">Multi-Kampus Terpadu</span>
                <h2 class="text-3xl font-black tracking-tight">Terhubung di 27 Kampus UBSI Seluruh Indonesia</h2>
                <p class="text-blue-200 text-sm leading-relaxed">
                    Setiap laporan barang hilang atau temuan otomatis dikelompokkan sesuai kampus setempat dan ditangani langsung oleh Admin Layanan Kampus di masing-masing unit.
                </p>
            </div>
            <div class="flex items-center gap-4 flex-wrap justify-center">
                <a href="{{ route('search') }}" class="px-6 py-3.5 rounded-2xl bg-amber-400 hover:bg-amber-300 text-slate-900 font-bold text-sm shadow-lg transition-all">
                    Cari Berdasarkan Kampus
                </a>
                <a href="{{ route('login') }}" class="px-6 py-3.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white font-semibold text-sm border border-white/20 transition-all">
                    Masuk Akun (NIM / DOB)
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
