@extends('layouts.app')

@section('title', 'Edit Laporan - FINDIT UBSI')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-slate-600">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <a href="{{ route('mahasiswa.laporan.saya') }}" class="hover:text-slate-600">Laporan</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Edit {{ $laporan->kode_laporan }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Edit Laporan Barang</h1>
        </div>
        <a href="{{ route('mahasiswa.laporan.detail', $laporan->id) }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
            &larr; Batal & Kembali
        </a>
    </div>

    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('mahasiswa.laporan.update', $laporan->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <!-- Kampus Dropdown -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Kampus UBSI <span class="text-rose-500">*</span>
                </label>
                <select name="kampus_id" required 
                        class="w-full px-4 py-3 bg-slate-50 border @error('kampus_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all cursor-pointer">
                    @foreach($campuses as $kmp)
                        <option value="{{ $kmp->id }}" {{ old('kampus_id', $laporan->kampus_id) == $kmp->id ? 'selected' : '' }}>
                            {{ $kmp->nama_kampus }} ({{ $kmp->kota }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Kategori Dropdown -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Kategori Barang <span class="text-rose-500">*</span>
                    </label>
                    <select name="kategori_id" required 
                            class="w-full px-4 py-3 bg-slate-50 border @error('kategori_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all cursor-pointer">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('kategori_id', $laporan->kategori_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->nama_kategori }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Nama Barang -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Nama Barang <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="nama_barang" value="{{ old('nama_barang', $laporan->nama_barang) }}" required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Lokasi Terakhir -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Lokasi Kejadian <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian', $laporan->lokasi_kejadian) }}" required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                </div>

                <!-- Tanggal -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tanggal <span class="text-rose-500">*</span>
                    </label>
                    <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', $laporan->tanggal_kejadian->format('Y-m-d')) }}" max="{{ date('Y-m-d') }}" required 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                </div>

                <!-- Waktu -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Perkiraan Waktu
                    </label>
                    <input type="time" name="waktu_kejadian" value="{{ old('waktu_kejadian', substr($laporan->waktu_kejadian, 0, 5)) }}" 
                           class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                </div>
            </div>

            <!-- Deskripsi -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Deskripsi Lengkap <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskripsi" rows="3" required 
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
            </div>

            <!-- Ciri Khusus -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Ciri-Ciri Khusus
                </label>
                <textarea name="ciri_khusus" rows="2" 
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">{{ old('ciri_khusus', $laporan->ciri_khusus) }}</textarea>
            </div>

            <!-- Foto -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Ubah / Ganti Foto Barang
                </label>
                @if($laporan->foto_barang)
                    <div class="mb-2">
                        <img src="{{ asset('storage/' . $laporan->foto_barang) }}" alt="Foto Lama" class="w-24 h-24 object-cover rounded-xl border border-slate-200">
                    </div>
                @endif
                <input type="file" name="foto_barang" accept="image/*" 
                       class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-600 file:mr-4 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-100 file:text-brand-ubsi cursor-pointer">
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('mahasiswa.laporan.detail', $laporan->id) }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-7 py-3 rounded-2xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/10 transition-all">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
