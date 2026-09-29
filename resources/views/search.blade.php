@extends('layouts.public')

@section('title', 'Pencarian Barang Hilang & Ditemukan - FINDIT UBSI')

@section('content')
<div class="bg-slate-100/70 border-b border-slate-200 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">Katalog & Pencarian Barang</h1>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Cari dan filter barang hilang atau ditemukan di seluruh kampus UBSI.</p>
            </div>
            
            <div class="flex items-center gap-2">
                @auth
                    @if(Auth::user()->isMahasiswa())
                        <a href="{{ route('mahasiswa.lapor.hilang') }}" class="px-4 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                            <i class="fa-solid fa-circle-exclamation"></i> Lapor Hilang
                        </a>
                        <a href="{{ route('mahasiswa.lapor.temukan') }}" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm transition-all flex items-center gap-2">
                            <i class="fa-solid fa-bullhorn"></i> Lapor Temuan
                        </a>
                    @endif
                @endauth
            </div>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- Sidebar Filter Form -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm sticky top-28 space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <span class="text-sm font-bold text-slate-800 flex items-center gap-2">
                        <i class="fa-solid fa-sliders text-blue-600"></i> Filter Pencarian
                    </span>
                    <a href="{{ route('search') }}" class="text-xs font-semibold text-rose-600 hover:underline">Reset</a>
                </div>

                <form action="{{ route('search') }}" method="GET" class="space-y-4">
                    <!-- Search Input -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Kata Kunci</label>
                        <div class="relative">
                            <input type="text" name="q" value="{{ request('q') }}" placeholder="Nama barang / deskripsi..." 
                                   class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-all">
                            <i class="fa-solid fa-magnifying-glass absolute right-3.5 top-3 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <!-- Kampus Filter -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            <i class="fa-solid fa-school text-blue-500 mr-1"></i> Kampus UBSI
                        </label>
                        <select name="kampus_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-all">
                            <option value="">Semua Kampus UBSI</option>
                            @foreach($kampusList as $kmp)
                                <option value="{{ $kmp->id }}" {{ request('kampus_id') == $kmp->id ? 'selected' : '' }}>
                                    {{ $kmp->nama_kampus }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Kategori Filter -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            <i class="fa-solid fa-tag text-purple-500 mr-1"></i> Kategori Barang
                        </label>
                        <select name="kategori_id" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-all">
                            <option value="">Semua Kategori</option>
                            @foreach($kategoriList as $kat)
                                <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                                    {{ $kat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Jenis Laporan Filter -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Jenis Laporan</label>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 text-xs font-medium cursor-pointer hover:bg-slate-50 {{ request('jenis_laporan') === 'HILANG' ? 'bg-rose-50 border-rose-300 text-rose-700' : 'text-slate-600' }}">
                                <input type="radio" name="jenis_laporan" value="HILANG" {{ request('jenis_laporan') === 'HILANG' ? 'checked' : '' }} class="text-rose-600">
                                <span>Hilang</span>
                            </label>
                            <label class="flex items-center gap-2 p-2.5 rounded-xl border border-slate-200 text-xs font-medium cursor-pointer hover:bg-slate-50 {{ request('jenis_laporan') === 'DITEMUKAN' ? 'bg-emerald-50 border-emerald-300 text-emerald-700' : 'text-slate-600' }}">
                                <input type="radio" name="jenis_laporan" value="DITEMUKAN" {{ request('jenis_laporan') === 'DITEMUKAN' ? 'checked' : '' }} class="text-emerald-600">
                                <span>Temuan</span>
                            </label>
                        </div>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Status Barang</label>
                        <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-all">
                            <option value="">Semua Status</option>
                            <option value="SEDANG DICARI" {{ request('status') === 'SEDANG DICARI' ? 'selected' : '' }}>🔴 Sedang Dicari</option>
                            <option value="MENUNGGU VERIFIKASI" {{ request('status') === 'MENUNGGU VERIFIKASI' ? 'selected' : '' }}>🟡 Menunggu Verifikasi</option>
                            <option value="BARANG DIAMANKAN" {{ request('status') === 'BARANG DIAMANKAN' ? 'selected' : '' }}>🟢 Barang Diamankan</option>
                            <option value="SIAP DIAMBIL" {{ request('status') === 'SIAP DIAMBIL' ? 'selected' : '' }}>🔵 Siap Diambil</option>
                            <option value="DIKEMBALIKAN" {{ request('status') === 'DIKEMBALIKAN' ? 'selected' : '' }}>🎉 Dikembalikan</option>
                        </select>
                    </div>

                    <!-- Tanggal Filter -->
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">Tanggal Kejadian</label>
                        <input type="date" name="tanggal" value="{{ request('tanggal') }}" 
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-blue-500 focus:outline-none transition-all">
                    </div>

                    <button type="submit" class="w-full py-3 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/10 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-filter"></i>
                        <span>Terapkan Filter</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Items Results Grid -->
        <div class="lg:col-span-3 space-y-6">

            <!-- Quick Campus Filter Strip (Pilihan Kampus yang Memiliki Laporan) -->
            <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-xs space-y-2.5">
                <div class="flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-800 flex items-center gap-1.5">
                        <i class="fa-solid fa-location-dot text-rose-500"></i>
                        <span>Filter Cepat Kampus dengan Laporan Aktif:</span>
                    </span>
                    @if(request('kampus_id'))
                        <a href="{{ request()->fullUrlWithQuery(['kampus_id' => null]) }}" class="text-[11px] font-semibold text-rose-600 hover:underline">
                            Hapus Filter Kampus
                        </a>
                    @endif
                </div>

                <div class="flex items-center gap-2 overflow-x-auto pb-1 custom-scrollbar text-xs">
                    <!-- Semua Kampus Button -->
                    <a href="{{ request()->fullUrlWithQuery(['kampus_id' => null]) }}" 
                       class="px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all text-xs {{ !request('kampus_id') ? 'bg-brand-ubsi text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                        Semua Kampus
                    </a>

                    @forelse($kampusWithReports as $kmp)
                        <a href="{{ request()->fullUrlWithQuery(['kampus_id' => $kmp->id]) }}" 
                           class="px-3 py-1.5 rounded-xl font-bold whitespace-nowrap transition-all text-xs flex items-center gap-1.5 {{ request('kampus_id') == $kmp->id ? 'bg-blue-600 text-white shadow-sm' : 'bg-blue-50 text-brand-ubsi hover:bg-blue-100 border border-blue-100' }}">
                            <span>{{ $kmp->nama_kampus }}</span>
                            <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ request('kampus_id') == $kmp->id ? 'bg-white text-blue-900' : 'bg-blue-200/80 text-blue-900' }}">
                                {{ $kmp->laporan_barang_count }}
                            </span>
                        </a>
                    @empty
                        <span class="text-xs text-slate-400 italic">Belum ada kampus dengan laporan aktif saat ini.</span>
                    @endforelse
                </div>
            </div>

            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500">
                    Menampilkan <span class="font-bold text-slate-900">{{ $laporanList->total() }}</span> laporan barang ditemukan & hilang
                </p>
            </div>

            @if($laporanList->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                    @foreach($laporanList as $item)
                        @php
                            $badge = $item->status_badge;
                            $isHilang = ($item->jenis_laporan === 'HILANG');
                        @endphp
                        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow flex flex-col overflow-hidden group">
                            
                            @if($item->thumbnail_foto)
                                <div class="relative h-44 w-full bg-slate-100 overflow-hidden border-b border-slate-100">
                                    <img src="{{ asset('storage/' . $item->thumbnail_foto) }}" alt="{{ $item->nama_barang }}" 
                                         class="w-full h-full object-cover group-hover:scale-105 transition-all duration-300">
                                    @if(count($item->foto_list) > 1)
                                        <span class="absolute bottom-2 right-2 px-2 py-0.5 rounded-lg bg-black/60 text-white text-[10px] font-bold backdrop-blur-xs flex items-center gap-1">
                                            <i class="fa-solid fa-images"></i> {{ count($item->foto_list) }} Foto
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Card Header with Badges -->
                            <div class="p-5 flex-1 flex flex-col">
                                <div class="flex items-center justify-between gap-2 mb-3">
                                    <span class="px-3 py-1 text-[10px] font-bold rounded-full border {{ $badge['bg'] }} uppercase flex items-center gap-1.5">
                                        <i class="fa-solid {{ $badge['icon'] }}"></i>
                                        <span>{{ $item->status }}</span>
                                    </span>
                                    <span class="text-[10px] font-mono font-semibold text-slate-400 bg-slate-100 px-2 py-0.5 rounded">
                                        {{ $item->kode_laporan }}
                                    </span>
                                </div>

                                <div class="mb-2">
                                    <span class="text-[11px] font-bold uppercase tracking-wider {{ $isHilang ? 'text-rose-600' : 'text-emerald-600' }}">
                                        {{ $item->jenis_laporan }}
                                    </span>
                                    <h3 class="text-base font-bold text-slate-900 group-hover:text-brand-ubsi transition-colors line-clamp-1 mt-0.5">
                                        {{ $item->nama_barang }}
                                    </h3>
                                </div>

                                <p class="text-xs text-slate-500 line-clamp-2 leading-relaxed mb-4">
                                    {{ $item->deskripsi }}
                                </p>

                                <!-- Details list -->
                                <div class="mt-auto pt-3 border-t border-slate-100 space-y-1.5 text-xs text-slate-600">
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-school text-blue-500 w-4"></i>
                                        <span class="font-medium text-slate-800 truncate">{{ $item->kampus->nama_kampus }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="fa-solid fa-location-dot text-slate-400 w-4"></i>
                                        <span class="truncate">{{ $item->lokasi_kejadian }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-[11px] text-slate-400">
                                        <i class="fa-regular fa-calendar text-slate-400 w-4"></i>
                                        <span>{{ $item->tanggal_kejadian->translatedFormat('d F Y') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Card Footer -->
                            <div class="px-5 py-3 bg-slate-50/80 border-t border-slate-100 flex items-center justify-between">
                                <span class="text-[11px] font-semibold text-slate-500 flex items-center gap-1.5">
                                    <i class="fa-solid {{ $item->kategori->icon }} text-slate-400"></i>
                                    <span>{{ $item->kategori->nama_kategori }}</span>
                                </span>

                                @auth
                                    <a href="{{ route('mahasiswa.laporan.detail', $item->id) }}" 
                                       class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:border-blue-500 hover:text-blue-600 text-xs font-bold transition-all shadow-sm">
                                        Detail
                                    </a>
                                @else
                                    <a href="{{ route('login') }}" 
                                       class="px-3.5 py-1.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:border-blue-500 hover:text-blue-600 text-xs font-bold transition-all shadow-sm">
                                        Lihat Detail
                                    </a>
                                @endauth
                            </div>

                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="pt-6">
                    {{ $laporanList->links() }}
                </div>
            @else
                <div class="p-16 bg-white rounded-3xl border border-slate-200/80 text-center space-y-4 shadow-sm">
                    <div class="w-16 h-16 rounded-2xl bg-blue-50 text-brand-ubsi flex items-center justify-center mx-auto text-2xl">
                        <i class="fa-solid fa-magnifying-glass-arrow-right"></i>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800">Tidak ada laporan yang cocok</h3>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto mt-1">
                            Coba ubah kata kunci pencarian atau bersihkan filter kampus dan kategori Anda.
                        </p>
                    </div>
                    <a href="{{ route('search') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-slate-900 text-white text-xs font-semibold hover:bg-slate-800 transition-all">
                        Reset Semua Filter
                    </a>
                </div>
            @endif
        </div>

    </div>
</div>
@endsection
