@extends('layouts.app')

@section('title', 'Buat Laporan Barang Ditemukan')

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    
    <!-- Breadcrumb & Header -->
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('mahasiswa.dashboard') }}" class="hover:text-slate-600">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Lapor Barang Ditemukan</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-emerald-500"></span>
                Formulir Lapor Barang Ditemukan
            </h1>
        </div>
        <a href="{{ route('mahasiswa.laporan.saya') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
            &larr; Kembali ke Laporan Saya
        </a>
    </div>

    <!-- Alert Info -->
    <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200/80 text-amber-900 text-xs flex items-start gap-3">
        <i class="fa-solid fa-user-shield text-amber-500 text-base mt-0.5 flex-shrink-0"></i>
        <div class="space-y-1">
            <span class="font-bold block">Status Awal: 🟡 MENUNGGU VERIFIKASI ADMIN</span>
            <p class="leading-relaxed">
                Setelah mengirim laporan ini, <strong>segera serahkan fisik barang kepada Admin / Staf Layanan Kampus</strong> di kantor pelayanan kampus terkait. Admin akan memverifikasi kesesuaian fisik dan mengubah status menjadi <strong>🟢 BARANG DIAMANKAN</strong>.
            </p>
        </div>
    </div>

    <!-- Form Card -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm">
        <form action="{{ route('mahasiswa.lapor.temukan') }}" method="POST" enctype="multipart/form-data" class="space-y-5" id="formLaporTemukan">
            @csrf

            <!-- Jenis Laporan Visual -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">Jenis Laporan</label>
                <div class="p-3 bg-emerald-50/60 border border-emerald-200 rounded-2xl flex items-center justify-between">
                    <span class="text-xs font-bold text-emerald-800 flex items-center gap-2">
                        <i class="fa-solid fa-bullhorn text-emerald-600"></i> Laporan Barang Temuan
                    </span>
                    <span class="text-[10px] font-bold text-emerald-700 uppercase bg-emerald-100 px-2 py-0.5 rounded">DITEMUKAN</span>
                </div>
            </div>

            <!-- Kampus Dropdown -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    <i class="fa-solid fa-school text-blue-500 mr-1"></i> Kampus Tempat Menemukan Barang <span class="text-rose-500">*</span>
                </label>
                <select name="kampus_id" required 
                        class="w-full px-4 py-3 bg-slate-50 border @error('kampus_id') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all cursor-pointer">
                    <option value="">-- Pilih Kampus UBSI Tempat Ditemukan --</option>
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
                           placeholder="Contoh: Kunci Motor Honda / Jaket Hoodie Hitam" 
                           class="w-full px-4 py-3 bg-slate-50 border @error('nama_barang') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                    @error('nama_barang')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Lokasi Ditemukan -->
                <div class="sm:col-span-1">
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Lokasi Ditemukan <span class="text-rose-500">*</span>
                    </label>
                    <input type="text" name="lokasi_kejadian" value="{{ old('lokasi_kejadian') }}" required 
                           placeholder="Contoh: Meja Kantin B, Parkir Motor, Lab 2" 
                           class="w-full px-4 py-3 bg-slate-50 border @error('lokasi_kejadian') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">
                    @error('lokasi_kejadian')
                        <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tanggal Ditemukan -->
                <div>
                    <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                        Tanggal Ditemukan <span class="text-rose-500">*</span>
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
                    Deskripsi Barang <span class="text-rose-500">*</span>
                </label>
                <textarea name="deskripsi" rows="3" required 
                          placeholder="Jelaskan kondisi saat ditemukan dan rincian fisik barang..." 
                          class="w-full px-4 py-3 bg-slate-50 border @error('deskripsi') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">{{ old('deskripsi') }}</textarea>
                @error('deskripsi')
                    <p class="text-[11px] text-rose-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            <!-- Ciri-ciri Khusus -->
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Ciri-Ciri Khusus / Tanda Pengenal
                </label>
                <textarea name="ciri_khusus" rows="2" 
                          placeholder="Contoh: Ada stiker khusus di belakang casing, warna tali biru, dsb." 
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">{{ old('ciri_khusus') }}</textarea>
            </div>

            <!-- Foto Barang Wajib Minimal 2 Foto -->
            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-900 uppercase tracking-wider">
                            <i class="fa-solid fa-camera text-emerald-600 mr-1"></i> Foto Fisik Barang Temuan <span class="text-rose-500">*</span>
                        </label>
                        <p class="text-[11px] text-slate-500 mt-0.5">
                            Wajib melampirkan <strong>minimal 2 foto</strong> (misal: tampak depan & tampak belakang / detail sudut barang).
                        </p>
                    </div>
                    <div id="photoCounterBadge" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800 self-start sm:self-auto">
                        <i class="fa-solid fa-circle-info"></i>
                        <span id="photoCountText">0 foto dipilih (Minimal 2)</span>
                    </div>
                </div>

                <div class="relative">
                    <input type="file" name="foto_barang[]" id="fotoBarangInput" multiple required accept="image/jpeg,image/png,image/webp,image/jpg" 
                           class="w-full px-4 py-3 bg-white border @error('foto_barang') border-rose-400 @else border-slate-200 @enderror rounded-2xl text-xs text-slate-600 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-emerald-600 file:text-white hover:file:bg-emerald-700 cursor-pointer shadow-sm">
                </div>

                @error('foto_barang')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror
                @error('foto_barang.*')
                    <p class="text-[11px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                @enderror

                <!-- Live Preview Grid -->
                <div id="previewContainer" class="hidden pt-2">
                    <p class="text-[11px] font-bold text-slate-600 uppercase tracking-wider mb-2">Pratinjau Foto Yang Akan Diunggah:</p>
                    <div id="imagePreviewGrid" class="grid grid-cols-2 sm:grid-cols-4 gap-3"></div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-slate-100 flex flex-col-reverse sm:flex-row items-center justify-end gap-3">
                <a href="{{ route('mahasiswa.laporan.saya') }}" class="w-full sm:w-auto text-center px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" id="btnSubmitLaporan" class="w-full sm:w-auto px-7 py-3 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-lg shadow-emerald-600/20 transition-all flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i>
                    <span>KIRIM LAPORAN TEMUAN (MIN. 2 FOTO)</span>
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const input = document.getElementById('fotoBarangInput');
            const previewContainer = document.getElementById('previewContainer');
            const previewGrid = document.getElementById('imagePreviewGrid');
            const counterBadge = document.getElementById('photoCounterBadge');
            const counterText = document.getElementById('photoCountText');
            const form = document.getElementById('formLaporTemukan');

            input.addEventListener('change', function() {
                previewGrid.innerHTML = '';
                const files = Array.from(this.files);
                const count = files.length;

                if (count > 0) {
                    previewContainer.classList.remove('hidden');
                } else {
                    previewContainer.classList.add('hidden');
                }

                if (count >= 2) {
                    counterBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800 self-start sm:self-auto';
                    counterText.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-600"></i> ${count} foto dipilih (Memenuhi syarat)`;
                } else {
                    counterBadge.className = 'inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-100 text-rose-800 self-start sm:self-auto';
                    counterText.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-rose-600"></i> ${count} foto dipilih (Kurang, minimal 2 foto)`;
                }

                files.forEach((file, index) => {
                    if (file.type.startsWith('image/')) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            const card = document.createElement('div');
                            card.className = 'relative rounded-2xl overflow-hidden border border-slate-200 bg-slate-100 aspect-square group shadow-sm';
                            card.innerHTML = `
                                <img src="${e.target.result}" class="w-full h-full object-cover">
                                <div class="absolute bottom-1 left-1 right-1 bg-black/60 backdrop-blur-sm text-white text-[10px] font-semibold py-0.5 px-2 rounded-lg truncate text-center">
                                    Foto #${index + 1}
                                </div>
                            `;
                            previewGrid.appendChild(card);
                        };
                        reader.readAsDataURL(file);
                    }
                });
            });

            form.addEventListener('submit', function(e) {
                if (input.files.length < 2) {
                    e.preventDefault();
                    alert('Mohon lampirkan minimal 2 foto fisik barang temuan (misal: tampak depan dan tampak belakang/detail).');
                    input.focus();
                }
            });
        });
    </script>
</div>
@endsection
