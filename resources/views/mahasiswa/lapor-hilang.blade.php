@extends('layouts.app')

@section('title', 'Buat Laporan Barang Hilang')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-slate-600">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Lapor Barang Hilang</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-rose-500"></span>
                Formulir Lapor Barang Hilang
            </h1>
        </div>
        <a href="{{ route('mahasiswa.laporan.saya') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
            &larr; Kembali ke Laporan Saya
        </a>
    </div>

    <!-- Alert Info -->
    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200/80 text-rose-800 text-xs flex items-start gap-3">
        <i class="fa-solid fa-circle-info text-rose-500 text-base mt-0.5 flex-shrink-0"></i>
        <div class="space-y-1">
            <span class="font-bold block">Status Awal: 🔴 SEDANG DICARI</span>
            <p class="leading-relaxed">
                Setelah formulir dikirim, sistem otomatis meng-generate kode laporan resmi (contoh: <strong>FD-{{ date('Y') }}-00001</strong>) dan mencocokkan dengan barang temuan yang sudah diamankan petugas.
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('mahasiswa.lapor.hilang') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <!-- Jenis Laporan (Hidden / Disabled Visual) -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Laporan</label>
                <div class="p-3 bg-rose-50/60 border border-rose-200 rounded-2xl flex items-center justify-between">
                    <span class="text-xs font-bold text-rose-700 flex items-center gap-2">
                        <i class="fa-solid fa-circle-exclamation"></i> Laporan Kehilangan Barang
                    </span>
                    <span class="text-[10px] font-bold text-rose-600 uppercase bg-rose-100 px-2 py-0.5 rounded">HILANG</span>
                </div>
            </div>

            <!-- Kampus Dropdown -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-school text-blue-500 mr-1"></i> Kampus Tempat Kehilangan <span class="text-rose-500">*</span>
                </label>
                <select name="kampus_id" required 
                        class="w-full px-4 py-3 bg-slate-50 border @error('kampus_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all cursor-pointer">
                    <option value="">-- Pilih Kampus UBSI Tempat Kejadian --</option>
                    @foreach($campuses as $kmp)
                        <option value="{{ $kmp->id }}" {{ (old('kampus_id') == $kmp->id || (Auth::user()->kampus_id == $kmp->id && !old('kampus_id'))) ? 'selected' : '' }}>
                            {{ $kmp->nama_kampus }} ({{ $kmp->kota }})
                        </option>
                    @endforeach
                </select>
                @error('kampus_id')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kategori Dropdown -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        <i class="fa-solid fa-tag text-purple-500 mr-1"></i> Kategori Barang <span class="text-rose-500">*</span>
                    </label>
                    <select name="kategori_id" required 
                            class="w-full px-4 py-3 bg-slate-50 border @error('kategori_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all cursor-pointer">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('kategori_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                    @error('kategori_id')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Nama Barang -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Barang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" required 
                           placeholder="Contoh: Dompet Kulit Hitam / KTM UBSI" 
                           class="w-full px-4 py-3 bg-slate-50 border @error('nama_barang') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                    @error('nama_barang')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Lokasi Terakhir -->
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Lokasi Terakhir <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" required 
                           placeholder="Contoh: Kantin, Lab 3, Parkiran" 
                           class="w-full px-4 py-3 bg-slate-50 border @error('lokasi_kejadian') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                    @error('lokasi_kejadian')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Kehilangan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tanggal Kehilangan <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required 
                           class="w-full px-4 py-3 bg-slate-50 border @error('tanggal_kejadian') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                    @error('tanggal_kejadian')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Perkiraan Waktu -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Perkiraan Waktu
                    </label>
                    <input type="time" name="waktu_kejadian" value="{{ old('waktu_kejadian') }}" 
                           class="w-full px-4 py-3 bg-slate-50 border @error('waktu_kejadian') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                </div>
            </div>

            <!-- Deskripsi Barang -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Deskripsi Lengkap Kejadian <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskripsi" rows="3" required 
                          placeholder="Jelaskan kronologi singkat dan rincian barang yang hilang..." 
                          class="w-full px-4 py-3 bg-slate-50 border @error('deskripsi') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ciri-ciri Khusus -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Ciri-Ciri Khusus / Tanda Unik
                </label>
                <textarea name="ciri_khusus" rows="2" 
                          placeholder="Contoh: Ada stiker hologram BSI, goresan di sudut kanan, gantungan kunci boneka..." 
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">{{ old('ciri_khusus') }}</textarea>
                <p class="text-[11px] text-slate-400 mt-1">Ciri khusus akan digunakan untuk pembuktian klaim verifikasi petugas.</p>
            </div>

            <!-- Foto Barang -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Foto Barang (Jika Tersedia)
                </label>
                <input type="file" name="foto_barang" accept="image/*" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-brand-ubsi hover:file:bg-blue-200 cursor-pointer">
                <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP (Maks. 3MB)</p>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('mahasiswa.laporan.saya') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-7 py-3 rounded-2xl bg-rose-600 hover:bg-rose-700 text-white text-xs font-bold shadow-lg shadow-rose-600/20 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>KIRIM LAPORAN</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
