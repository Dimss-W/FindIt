@extends('layouts.app')

@section('title', 'Verifikasi Klaim - FINDIT UBSI')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    
    <div class="flex items-center justify-between">
        <div>
            <div class="flex items-center gap-2 text-xs text-slate-400 font-medium mb-1">
                <a href="{{ route('petugas.dashboard') }}" class="hover:text-slate-600">Dashboard</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <a href="{{ route('petugas.klaim.index') }}" class="hover:text-slate-600">Pengajuan Klaim</a>
                <i class="fa-solid fa-chevron-right text-[10px]"></i>
                <span class="text-slate-600">Detail Klaim #{{ $klaim->id }}</span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-user-check text-blue-600"></i>
                Verifikasi & Telaah Klaim Kepemilikan
            </h1>
        </div>
        <a href="{{ route('petugas.klaim.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-700">
            &larr; Kembali ke Daftar Klaim
        </a>
    </div>

    <!-- Comparison Grid: Item Info vs Claim Info -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Left: Item Diamankan -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Barang yang Diamankan</span>
                <span class="text-[10px] font-mono font-bold text-slate-500 bg-slate-100 px-2 py-0.5 rounded">{{ $klaim->laporan->kode_laporan }}</span>
            </div>

            <div>
                <h3 class="text-lg font-black text-slate-900">{{ $klaim->laporan->nama_barang }}</h3>
                <p class="text-xs text-slate-500 mt-1">{{ $klaim->laporan->deskripsi }}</p>
            </div>

            @if($klaim->laporan->ciri_khusus)
                <div class="p-3.5 rounded-2xl bg-amber-50 text-amber-900 text-xs">
                    <span class="font-bold block text-[11px] uppercase tracking-wider mb-1">Ciri Khusus Tercatat:</span>
                    {{ $klaim->laporan->ciri_khusus }}
                </div>
            @endif

            <div class="space-y-1.5 text-xs text-slate-600 pt-2 border-t border-slate-100">
                <div>Lokasi Temuan: <strong>{{ $klaim->laporan->lokasi_kejadian }}</strong></div>
                <div>Tanggal: <strong>{{ $klaim->laporan->tanggal_kejadian->translatedFormat('d F Y') }}</strong></div>
                @if($klaim->laporan->penyimpanan)
                    <div>Pos Penyimpanan: <strong class="text-emerald-700">{{ $klaim->laporan->penyimpanan->lokasi_penyimpanan }}</strong></div>
                    <div>Kondisi Fisik: <strong>{{ $klaim->laporan->penyimpanan->kondisi_barang }}</strong></div>
                @endif
            </div>

            @if($klaim->laporan->foto_barang)
                <div class="pt-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase block mb-1">Foto Barang:</span>
                    <img src="{{ asset('storage/' . $klaim->laporan->foto_barang) }}" alt="Foto Barang" class="h-36 rounded-xl border border-slate-200 object-cover">
                </div>
            @endif
        </div>

        <!-- Right: Claim Statement from Student -->
        <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-100">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Klaim Mahasiswa</span>
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $klaim->status_badge['bg'] }} uppercase">
                    {{ $klaim->status }}
                </span>
            </div>

            <!-- Mahasiswa Info with 1-Click WhatsApp Button -->
            <div class="flex items-center justify-between p-3 bg-slate-50 rounded-2xl border border-slate-100">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-brand-ubsi text-white flex items-center justify-center font-bold text-xs">
                        {{ strtoupper(substr($klaim->user->name, 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">{{ $klaim->user->name }}</h4>
                        <p class="text-[11px] text-slate-500">NIM: {{ $klaim->user->nim ?? '-' }} • WA: {{ $klaim->user->no_telp ?? '-' }}</p>
                    </div>
                </div>
                @if($klaim->user->no_telp)
                    <a href="{{ \App\Services\WhatsAppService::generateChatLink($klaim->user->no_telp, 'Halo ' . $klaim->user->name . ', saya Admin Layanan Kampus terkait klaim barang Anda di FINDIT UBSI (' . $klaim->laporan->nama_barang . ').') }}" 
                       target="_blank"
                       class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-bold flex items-center gap-1.5 shadow-sm transition-all">
                        <i class="fa-brands fa-whatsapp text-sm"></i>
                        <span>Chat WA</span>
                    </a>
                @endif
            </div>

            <!-- Deskripsi Klaim Mahasiswa -->
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Ciri Khusus yang Disebutkan Mahasiswa:</span>
                <p class="text-xs text-slate-800 leading-relaxed bg-blue-50/50 p-3.5 rounded-2xl border border-blue-100 font-medium">
                    "{{ $klaim->deskripsi_klaim }}"
                </p>
            </div>

            <!-- Bukti Kepemilikan -->
            <div>
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">Bukti Kepemilikan yang Diajukan:</span>
                <p class="text-xs text-slate-700 leading-relaxed bg-slate-50 p-3.5 rounded-2xl border border-slate-100">
                    {{ $klaim->bukti_kepemilikan }}
                </p>
            </div>

            @if($klaim->foto_bukti)
                <div class="pt-2">
                    <span class="text-[11px] font-bold text-slate-400 uppercase block mb-1">Foto Bukti yang Diunggah Mahasiswa:</span>
                    <a href="{{ asset('storage/' . $klaim->foto_bukti) }}" target="_blank">
                        <img src="{{ asset('storage/' . $klaim->foto_bukti) }}" alt="Foto Bukti" class="h-36 rounded-xl border border-slate-200 object-cover hover:opacity-90 transition-opacity">
                    </a>
                    <span class="text-[10px] text-slate-400 block mt-1">Klik gambar untuk memperbesar</span>
                </div>
            @endif
        </div>

    </div>

    <!-- Verification Decision Form -->
    <div class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-200/80 shadow-sm space-y-6">
        <div class="pb-4 border-b border-slate-100">
            <h3 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="fa-solid fa-gavel text-amber-500"></i>
                Keputusan Verifikasi Admin / Staf Layanan Kampus
            </h3>
            <p class="text-xs text-slate-500 mt-0.5">
                Bandingkan bukti mahasiswa dengan kondisi barang fisik yang diamankan di kantor layanan kampus Anda.
            </p>
        </div>

        <form action="{{ route('petugas.klaim.verifikasi', $klaim->id) }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Pilih Keputusan <span class="text-rose-500">*</span></label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <label class="flex items-center gap-3 p-4 rounded-2xl border-2 border-emerald-200 bg-emerald-50/50 cursor-pointer hover:bg-emerald-50 transition-colors">
                        <input type="radio" name="keputusan" value="DISETUJUI" {{ $klaim->status === 'DISETUJUI' ? 'checked' : '' }} required class="w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                        <div>
                            <div class="text-xs font-bold text-emerald-800">✅ Setujui Klaim</div>
                            <div class="text-[11px] text-emerald-600 mt-0.5">Barang sah milik mahasiswa. Status berubah menjadi <strong>SIAP DIAMBIL</strong>.</div>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 rounded-2xl border-2 border-rose-200 bg-rose-50/50 cursor-pointer hover:bg-rose-50 transition-colors">
                        <input type="radio" name="keputusan" value="DITOLAK" {{ $klaim->status === 'DITOLAK' ? 'checked' : '' }} required class="w-4 h-4 text-rose-600 focus:ring-rose-500">
                        <div>
                            <div class="text-xs font-bold text-rose-800">❌ Tolak Klaim</div>
                            <div class="text-[11px] text-rose-600 mt-0.5">Bukti tidak valid atau tidak cocok. Mahasiswa akan menerima alasan penolakan.</div>
                        </div>
                    </label>
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                    Catatan Verifikasi Petugas <span class="text-rose-500">*</span>
                </label>
                <textarea name="catatan_petugas" rows="3" required 
                          placeholder="Tuliskan catatan verifikasi (contoh: Ciri kartu KTM cocok, nomor seri sesuai / Ciri goresan tidak cocok)..." 
                          class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs text-slate-800 focus:bg-white focus:border-blue-600 focus:outline-none transition-all">{{ old('catatan_petugas', $klaim->catatan_petugas) }}</textarea>
            </div>

            <div class="pt-4 border-t border-slate-100 flex items-center justify-end gap-3">
                <a href="{{ route('petugas.klaim.index') }}" class="px-5 py-3 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all">
                    Batal
                </a>
                <button type="submit" class="px-7 py-3 rounded-2xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/10 transition-all flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i>
                    <span>SIMPAN KEPUTUSAN VERIFIKASI</span>
                </button>
            </div>
        </form>
    </div>

</div>
@endsection
