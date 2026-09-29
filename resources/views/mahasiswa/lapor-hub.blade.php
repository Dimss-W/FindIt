@extends('layouts.app')

@section('title', 'Pusat Pelaporan Barang - FINDIT UBSI')

@section('content')
<div class="space-y-6 max-w-3xl mx-auto" x-data="{ reportType: '{{ $activeTab ?? 'hilang' }}' }">
    
    <!-- Page Header -->
    <div class="text-center space-y-1">
        <h1 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">
            Pusat Pelaporan Barang UBSI
        </h1>
        <p class="text-xs text-slate-500">
            Pilih jenis laporan yang ingin Anda ajukan. Laporan akan diteruskan otomatis ke petugas kampus terkait.
        </p>
    </div>

    <!-- Toggle Selector (Lapor Hilang vs Lapor Temuan) -->
    <div class="grid grid-cols-2 p-1.5 bg-slate-200/80 rounded-2xl gap-2 shadow-inner">
        <button type="button" @click="reportType = 'hilang'" 
                :class="reportType === 'hilang' ? 'bg-rose-600 text-white shadow-md shadow-rose-600/30 font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="py-3 px-4 rounded-xl text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-base"></i>
            <span>Saya Kehilangan Barang</span>
        </button>

        <button type="button" @click="reportType = 'temukan'" 
                :class="reportType === 'temukan' ? 'bg-emerald-600 text-white shadow-md shadow-emerald-600/30 font-bold' : 'text-slate-600 hover:text-slate-900 font-medium'"
                class="py-3 px-4 rounded-xl text-xs sm:text-sm transition-all flex items-center justify-center gap-2">
            <i class="fa-solid fa-hand-holding-hand text-base"></i>
            <span>Saya Menemukan Barang</span>
        </button>
    </div>

    <!-- FORM 1: LAPOR KEHILANGAN -->
    <div x-show="reportType === 'hilang'" x-cloak class="space-y-5">
        <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 text-xs flex items-start gap-3">
            <i class="fa-solid fa-circle-info text-rose-500 text-base mt-0.5 flex-shrink-0"></i>
            <div class="space-y-1">
                <span class="font-bold block">Status Laporan: 🔴 SEDANG DICARI</span>
                <p class="leading-relaxed text-[11px]">
                    Laporan ini akan langsung masuk ke dashboard <strong>staf kampus yang Anda pilih</strong> dan dicocokkan otomatis (*Smart Auto-Matching*) dengan barang yang telah diamankan petugas.
                </p>
            </div>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
            <form action="{{ route('mahasiswa.lapor.hilang') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Kampus Pilihan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-school text-blue-500 mr-1"></i> Kampus Tempat Kehilangan <span class="text-rose-500">*</span>
                    </label>
                    <select name="kampus_id" required 
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
                        <option value="">-- Pilih Kampus UBSI Tempat Kejadian --</option>
                        @foreach($campuses as $kmp)
                            <option value="{{ $kmp->id }}" {{ (old('kampus_id') == $kmp->id || (Auth::user()->kampus_id == $kmp->id && !old('kampus_id'))) ? 'selected' : '' }}>
                                {{ $kmp->nama_kampus }} ({{ $kmp->kota }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-tag text-purple-500 mr-1"></i> Kategori Barang <span class="text-rose-500">*</span>
                        </label>
                        <select name="kategori_id" required 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('kategori_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Barang <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" required 
                               placeholder="Contoh: Dompet Kulit Hitam Eiger" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-location-dot text-rose-500 mr-1"></i> Perkiraan Lokasi Terakhir <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" required 
                               placeholder="Contoh: Meja Kantin Utama / Lab Komputer 3" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            <i class="fa-regular fa-calendar text-slate-400 mr-1"></i> Tanggal Kejadian <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Deskripsi Rinci <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deskripsi" rows="3" required placeholder="Jelaskan kronologi singkat atau rincian barang Anda..."
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Ciri-ciri Khusus / Pengenal
                    </label>
                    <input type="text" name="ciri_khusus" value="{{ old('ciri_khusus') }}" 
                           placeholder="Contoh: Ada stiker logo UBSI, goresan di sisi kanan..." 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-rose-500 focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Foto Barang (Opsional / Foto Pendukung)
                    </label>
                    <input type="file" name="foto_barang[]" multiple accept="image/*"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-rose-50 file:text-rose-700 hover:file:bg-rose-100">
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs rounded-2xl shadow-lg shadow-rose-600/30 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Laporan Kehilangan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- FORM 2: LAPOR PENEMUAN -->
    <div x-show="reportType === 'temukan'" x-cloak class="space-y-5">
        <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200/80 text-emerald-800 text-xs flex items-start gap-3">
            <i class="fa-solid fa-circle-check text-emerald-500 text-base mt-0.5 flex-shrink-0"></i>
            <div class="space-y-1">
                <span class="font-bold block">Status Awal: 🟡 MENUNGGU VERIFIKASI FISIK</span>
                <p class="leading-relaxed text-[11px]">
                    Setelah mengisi form, mohon segera serahkan fisik barang kepada <strong>Petugas Layanan Kampus terkait</strong> agar dapat diamankan di loker resmi kampus.
                </p>
            </div>
        </div>

        <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
            <form action="{{ route('mahasiswa.lapor.temukan') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf

                <!-- Kampus Pilihan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-school text-emerald-600 mr-1"></i> Kampus Tempat Menemukan <span class="text-rose-500">*</span>
                    </label>
                    <select name="kampus_id" required 
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 focus:outline-none transition-all">
                        <option value="">-- Pilih Kampus UBSI Lokasi Temuan --</option>
                        @foreach($campuses as $kmp)
                            <option value="{{ $kmp->id }}" {{ (old('kampus_id') == $kmp->id || (Auth::user()->kampus_id == $kmp->id && !old('kampus_id'))) ? 'selected' : '' }}>
                                {{ $kmp->nama_kampus }} ({{ $kmp->kota }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-tag text-purple-500 mr-1"></i> Kategori Barang <span class="text-rose-500">*</span>
                        </label>
                        <select name="kategori_id" required 
                                class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 focus:outline-none transition-all">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('kategori_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nama_kategori }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Nama Barang Temuan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" required 
                               placeholder="Contoh: Kunci Motor Vario / Flashdisk Sandisk" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 focus:outline-none transition-all">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            <i class="fa-solid fa-location-dot text-emerald-500 mr-1"></i> Lokasi Spesifik Ditemukan <span class="text-rose-500">*</span>
                        </label>
                        <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" required 
                               placeholder="Contoh: Tergeletak di Kursi Parkir Motor Barat" 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 focus:outline-none transition-all">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            <i class="fa-regular fa-calendar text-slate-400 mr-1"></i> Tanggal Penemuan <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required 
                               class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 focus:outline-none transition-all">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Deskripsi Keadaan Barang <span class="text-rose-500">*</span>
                    </label>
                    <textarea name="deskripsi" rows="3" required placeholder="Jelaskan kondisi saat ditemukan..."
                              class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 focus:outline-none transition-all">{{ old('deskripsi') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Ciri-ciri Khusus / Gantungan
                    </label>
                    <input type="text" name="ciri_khusus" value="{{ old('ciri_khusus') }}" 
                           placeholder="Contoh: Gantungan kunci akrilik anime, warna merah..." 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-emerald-500 focus:outline-none transition-all">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Foto Bukti Fisik Barang Temuan (Minimal 2 Foto) <span class="text-rose-500">*</span>
                    </label>
                    <input type="file" name="foto_barang[]" multiple required accept="image/*"
                           class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100">
                    <p class="text-[11px] text-slate-400 mt-1">Lampirkan foto tampak depan dan detail barang agar mudah dikenali pemiliknya.</p>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-2xl shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span>Kirim Laporan Barang Temuan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
