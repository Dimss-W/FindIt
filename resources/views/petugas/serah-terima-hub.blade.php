@extends('layouts.app')

@section('title', 'Pusat Klaim & Serah Terima QR')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto"
     x-data="{
         activeTab: '{{ $activeTab }}',
         scanMode: 'camera',
         manualQuery: '',
         loadingVerify: false,
         verifyData: null,
         errorMessage: '',
         isSubmitting: false,
         completedResult: null,
         html5QrCode: null,
         cameraActive: false,
         cameraError: '',

         init() {
             if (this.activeTab === 'scanner') {
                 this.$nextTick(() => { this.startCamera(); });
             }
         },

         setTab(tab) {
             this.activeTab = tab;
             if (tab === 'scanner') {
                 this.$nextTick(() => { this.startCamera(); });
             } else {
                 this.stopCamera();
             }
         },

         startCamera() {
             this.cameraError = '';
             const qrElement = document.getElementById('qr-reader-hub');
             if (!qrElement) return;

             if (typeof Html5Qrcode === 'undefined') {
                 this.cameraError = 'Library Scanner sedang dimuat, silakan coba lagi.';
                 return;
             }

             if (this.html5QrCode) {
                 this.stopCamera();
             }

             this.html5QrCode = new Html5Qrcode('qr-reader-hub');
             const config = { fps: 10, qrbox: { width: 250, height: 250 } };

             this.html5QrCode.start(
                 { facingMode: 'environment' },
                 config,
                 (decodedText) => {
                     this.onScanSuccess(decodedText);
                 },
                 (error) => {}
             ).then(() => {
                 this.cameraActive = true;
             }).catch((err) => {
                 this.cameraActive = false;
                 this.cameraError = 'Izin kamera ditolak atau kamera tidak ditemukan. Gunakan tab Input Manual.';
             });
         },

         stopCamera() {
             if (this.html5QrCode && this.cameraActive) {
                 this.html5QrCode.stop().then(() => {
                     this.cameraActive = false;
                 }).catch((err) => console.warn(err));
             }
         },

         onScanSuccess(code) {
             this.stopCamera();
             this.manualQuery = code;
             this.verifyTicket(code);
         },

         verifyTicket(code) {
             const queryStr = code || this.manualQuery;
             if (!queryStr.trim()) {
                 this.errorMessage = 'Masukkan kode tiket atau scan QR terlebih dahulu.';
                 return;
             }

             this.loadingVerify = true;
             this.errorMessage = '';
             this.verifyData = null;
             this.completedResult = null;

             fetch('{{ route('petugas.scan.qr.verify') }}', {
                 method: 'POST',
                 headers: {
                     'Content-Type': 'application/json',
                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
                     'Accept': 'application/json'
                 },
                 body: JSON.stringify({ query: queryStr.trim() })
             })
             .then(res => res.json().then(data => ({ status: res.status, body: data })))
             .then(({ status, body }) => {
                 this.loadingVerify = false;
                 if (status === 200 && body.success) {
                     this.verifyData = body;
                 } else {
                     this.errorMessage = body.message || 'Tiket tidak valid atau tidak ditemukan.';
                 }
             })
             .catch(err => {
                 this.loadingVerify = false;
                 this.errorMessage = 'Terjadi kesalahan jaringan saat memvalidasi tiket.';
             });
         },

         submitSerahTerima() {
             if (!this.verifyData) return;
             if (!confirm('Apakah Anda yakin fisik barang telah diserahkan langsung kepada mahasiswa pemilik sah?')) return;

             this.isSubmitting = true;
             this.errorMessage = '';

             const formElem = document.getElementById('hub-form-serah-terima');
             const formData = new FormData(formElem);
             formData.append('klaim_id', this.verifyData.klaim_id);

             fetch('{{ route('petugas.scan.qr.proses') }}', {
                 method: 'POST',
                 headers: {
                     'X-CSRF-TOKEN': '{{ csrf_token() }}',
                     'Accept': 'application/json'
                 },
                 body: formData
             })
             .then(res => res.json().then(data => ({ status: res.status, body: data })))
             .then(({ status, body }) => {
                 this.isSubmitting = false;
                 if (status === 200 && body.success) {
                     this.completedResult = body;
                     this.verifyData = null;
                 } else {
                     this.errorMessage = body.message || 'Gagal memproses serah terima.';
                 }
             })
             .catch(err => {
                 this.isSubmitting = false;
                 this.errorMessage = 'Terjadi kendala saat menyimpan serah terima.';
             });
         },

         resetScanner() {
             this.verifyData = null;
             this.completedResult = null;
             this.errorMessage = '';
             this.manualQuery = '';
             if (this.scanMode === 'camera') {
                 this.startCamera();
             }
         }
     }">

    <!-- Header Workspace -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[11px] font-bold uppercase tracking-wider bg-emerald-50 text-emerald-800 border border-emerald-200 mb-2">
                <i class="fa-solid fa-handshake"></i>
                Layanan Serah Terima &bull; {{ $kampus->nama_kampus ?? 'UBSI' }}
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight flex items-center gap-2">
                <i class="fa-solid fa-qrcode text-emerald-600"></i>
                Pusat Klaim & Serah Terima QR
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Kelola validasi bukti kepemilikan, pemindaian tiket QR, hingga penerbitan Berita Acara Serah Terima (BAST).
            </p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('petugas.inventaris.hub') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition-all flex items-center gap-2">
                <i class="fa-solid fa-boxes-stacked"></i>
                <span>Ke Inventaris Barang</span>
            </a>
        </div>
    </div>

    <!-- 3 Utama Tab Switcher -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
        <button type="button" @click="setTab('klaim')" 
                class="p-4 rounded-2xl border transition-all text-left flex items-center justify-between"
                :class="activeTab === 'klaim' ? 'bg-amber-500 text-white border-amber-600 shadow-md shadow-amber-500/20' : 'bg-white text-slate-700 border-slate-200/80 hover:border-amber-400'">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider block" :class="activeTab === 'klaim' ? 'text-amber-100' : 'text-slate-400'">1. Klaim Mahasiswa</span>
                <span class="text-xl font-black">{{ $counts['klaim_pending'] }} <span class="text-xs font-normal opacity-80">perlu review</span></span>
            </div>
            <i class="fa-solid fa-user-check text-xl" :class="activeTab === 'klaim' ? 'text-amber-200' : 'text-amber-500'"></i>
        </button>

        <button type="button" @click="setTab('scanner')" 
                class="p-4 rounded-2xl border transition-all text-left flex items-center justify-between"
                :class="activeTab === 'scanner' ? 'bg-emerald-600 text-white border-emerald-700 shadow-md shadow-emerald-600/20' : 'bg-white text-slate-700 border-slate-200/80 hover:border-emerald-400'">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider block" :class="activeTab === 'scanner' ? 'text-emerald-100' : 'text-slate-400'">2. Scan QR / Input Tiket</span>
                <span class="text-xl font-black">{{ $counts['klaim_siap_ambil'] }} <span class="text-xs font-normal opacity-80">siap diambil</span></span>
            </div>
            <i class="fa-solid fa-qrcode text-xl" :class="activeTab === 'scanner' ? 'text-emerald-200' : 'text-emerald-500'"></i>
        </button>

        <button type="button" @click="setTab('riwayat')" 
                class="p-4 rounded-2xl border transition-all text-left flex items-center justify-between"
                :class="activeTab === 'riwayat' ? 'bg-purple-600 text-white border-purple-700 shadow-md shadow-purple-600/20' : 'bg-white text-slate-700 border-slate-200/80 hover:border-purple-400'">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider block" :class="activeTab === 'riwayat' ? 'text-purple-100' : 'text-slate-400'">3. Riwayat & Cetak BAST</span>
                <span class="text-xl font-black">{{ $counts['riwayat_selesai'] }} <span class="text-xs font-normal opacity-80">tuntas</span></span>
            </div>
            <i class="fa-solid fa-file-invoice text-xl" :class="activeTab === 'riwayat' ? 'text-purple-200' : 'text-purple-500'"></i>
        </button>
    </div>

    <!-- TAB 1: KLAIM MAHASISWA & VERIFIKASI -->
    <div x-show="activeTab === 'klaim'" x-cloak class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-slate-900">Pengajuan Klaim Kepemilikan Barang</h3>
                <p class="text-xs text-slate-500 mt-0.5">Tinjau bukti kepemilikan yang diajukan oleh mahasiswa sebelum menerbitkan tiket QR.</p>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Barang Temuan</th>
                        <th class="py-4 px-6">Mahasiswa Pemohon</th>
                        <th class="py-4 px-6">Uraian Bukti</th>
                        <th class="py-4 px-6">Status Klaim</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($klaimList as $k)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $k->laporan?->nama_barang }}</span>
                                <span class="font-mono text-[11px] text-slate-400">{{ $k->laporan?->kode_laporan }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-900">{{ $k->user?->name }}</span>
                                <span class="block text-[11px] text-slate-400">NIM. {{ $k->user?->nim ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6 max-w-xs">
                                <p class="line-clamp-2 text-slate-600">{{ $k->bukti_kepemilikan }}</p>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $k->status_badge['bg'] }} uppercase">
                                    {{ $k->status }}
                                </span>
                                @if($k->kode_tiket)
                                    <div class="mt-1 font-mono text-[10px] font-bold text-emerald-700">
                                        Tiket: {{ $k->kode_tiket }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('petugas.klaim.detail', $k->id) }}" class="px-3 py-1.5 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white font-bold text-xs transition-all">
                                    Tinjau Klaim
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-user-check text-4xl text-slate-300 mb-2 block"></i>
                                Belum ada pengajuan klaim untuk barang di kampus ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($klaimList->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $klaimList->links() }}</div>
        @endif
    </div>

    <!-- TAB 2: SCANNER QR & INPUT TIKET MANUAL -->
    <div x-show="activeTab === 'scanner'" x-cloak class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Left: Scanner & Manual Lookup Panel -->
        <div class="lg:col-span-6 space-y-4">
            <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                
                <!-- Scan Mode Toggle -->
                <div class="grid grid-cols-2 p-1 bg-slate-100 rounded-2xl">
                    <button type="button" @click="scanMode = 'camera'; $nextTick(() => { startCamera(); })"
                            :class="scanMode === 'camera' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-500 font-medium'"
                            class="py-2.5 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-camera"></i>
                        <span>Scan Kamera / Webcam</span>
                    </button>
                    <button type="button" @click="scanMode = 'manual'; stopCamera()"
                            :class="scanMode === 'manual' ? 'bg-white text-slate-900 shadow-sm font-bold' : 'text-slate-500 font-medium'"
                            class="py-2.5 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-keyboard"></i>
                        <span>Input Kode Manual</span>
                    </button>
                </div>

                <!-- Camera Viewport -->
                <div x-show="scanMode === 'camera'" class="space-y-3">
                    <div class="relative bg-slate-950 rounded-2xl overflow-hidden min-h-[300px] flex items-center justify-center border border-slate-800">
                        <div id="qr-reader-hub" class="w-full h-full"></div>
                        <template x-if="cameraError">
                            <div class="p-4 text-center text-rose-300 text-xs space-y-2">
                                <i class="fa-solid fa-video-slash text-3xl"></i>
                                <p x-text="cameraError"></p>
                                <button type="button" @click="startCamera()" class="px-3 py-1.5 bg-rose-600 text-white rounded-lg text-xs font-bold">
                                    Coba Lagi
                                </button>
                            </div>
                        </template>
                    </div>
                    <p class="text-[11px] text-slate-500 text-center">
                        <i class="fa-solid fa-circle-info text-blue-500 mr-1"></i>
                        Arahkan kamera ke QR Code pada layar HP mahasiswa pemohon.
                    </p>
                </div>

                <!-- Manual Lookup Form -->
                <div x-show="scanMode === 'manual'" class="space-y-3">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1">
                            Kode Tiket Pengambilan:
                        </label>
                        <div class="flex gap-2">
                            <input type="text" x-model="manualQuery" @keydown.enter.prevent="verifyTicket(manualQuery)"
                                   placeholder="Contoh: TK-UBSI-KRM-XXXX"
                                   class="flex-1 px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-xs font-mono font-bold uppercase focus:bg-white focus:border-blue-500 focus:outline-none">
                            <button type="button" @click="verifyTicket(manualQuery)" :disabled="loadingVerify"
                                    class="px-5 py-3 rounded-2xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md transition-all flex items-center gap-1.5">
                                <i class="fa-solid fa-magnifying-glass" x-show="!loadingVerify"></i>
                                <i class="fa-solid fa-circle-notch fa-spin" x-show="loadingVerify"></i>
                                <span>Cek Tiket</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Error Notification -->
                <template x-if="errorMessage">
                    <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 text-xs flex items-start gap-3">
                        <i class="fa-solid fa-circle-xmark text-lg mt-0.5 text-rose-500"></i>
                        <div class="flex-1">
                            <p class="font-bold">Verifikasi Gagal</p>
                            <p class="mt-0.5" x-text="errorMessage"></p>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        <!-- Right: Verification Result & Handover Confirmation -->
        <div class="lg:col-span-6 space-y-4">
            
            <!-- Default Placeholder -->
            <div x-show="!verifyData && !completedResult" class="bg-white rounded-3xl border border-dashed border-slate-200 p-12 text-center text-slate-400 space-y-3">
                <div class="w-16 h-16 rounded-2xl bg-slate-50 border border-slate-100 mx-auto flex items-center justify-center text-slate-300 text-2xl">
                    <i class="fa-solid fa-ticket"></i>
                </div>
                <h4 class="text-sm font-bold text-slate-700">Menunggu Pemindaian Tiket</h4>
                <p class="text-xs text-slate-400 max-w-sm mx-auto">
                    Scan QR Code atau masukkan kode tiket mahasiswa. Detail barang dan pemilik akan langsung diverifikasi oleh sistem.
                </p>
            </div>

            <!-- Validated Ticket Card -->
            <div x-show="verifyData" x-cloak class="bg-white rounded-3xl border-2 border-emerald-500 shadow-xl overflow-hidden p-6 space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-300 uppercase">
                            Tiket Valid &bull; Siap Serah Terima
                        </span>
                    </div>
                    <span class="font-mono text-xs font-black text-brand-ubsi" x-text="verifyData?.kode_tiket"></span>
                </div>

                <!-- Item & Owner Info -->
                <div class="flex items-start gap-4">
                    <img :src="verifyData?.foto_barang" class="w-20 h-20 rounded-2xl object-cover border border-slate-200 shadow-sm">
                    <div class="space-y-1">
                        <h4 class="text-base font-black text-slate-900" x-text="verifyData?.nama_barang"></h4>
                        <p class="text-xs text-slate-500">
                            Kategori: <span class="font-semibold text-slate-700" x-text="verifyData?.kategori"></span> &bull; Kampus: <span class="font-bold text-blue-900" x-text="verifyData?.kampus"></span>
                        </p>
                        <div class="pt-1 text-xs">
                            <span class="text-slate-400">Pemilik:</span>
                            <strong class="text-slate-900" x-text="verifyData?.mahasiswa?.name"></strong>
                            <span class="font-mono text-[11px] text-slate-500" x-text="'(NIM: ' + verifyData?.mahasiswa?.nim + ')'"></span>
                        </div>
                    </div>
                </div>

                <!-- Form Handover -->
                <form id="hub-form-serah-terima" @submit.prevent="submitSerahTerima()" class="space-y-4 pt-2 border-t border-slate-100">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-700 uppercase tracking-wider mb-1">Catatan Serah Terima:</label>
                        <input type="text" name="catatan" value="Fisik barang telah dicocokkan dengan KTM & identitas mahasiswa dan diserahkan dalam kondisi lengkap."
                               class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-xs text-slate-800 focus:bg-white focus:outline-none">
                    </div>

                    <div class="flex items-center gap-3">
                        <button type="submit" :disabled="isSubmitting"
                                class="flex-1 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs rounded-xl shadow-lg shadow-emerald-600/30 transition-all flex items-center justify-center gap-2">
                            <i class="fa-solid fa-circle-check" x-show="!isSubmitting"></i>
                            <i class="fa-solid fa-circle-notch fa-spin" x-show="isSubmitting"></i>
                            <span>Konfirmasi Serah Terima Barang</span>
                        </button>
                        <button type="button" @click="resetScanner()" class="px-4 py-3 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold text-xs rounded-xl transition-all">
                            Batal
                        </button>
                    </div>
                </form>
            </div>

            <!-- Completed Handover Success Card -->
            <div x-show="completedResult" x-cloak class="bg-emerald-50 border border-emerald-200 rounded-3xl p-8 text-center space-y-4">
                <div class="w-16 h-16 rounded-full bg-emerald-500 text-white mx-auto flex items-center justify-center text-3xl shadow-lg shadow-emerald-500/30">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <h3 class="text-lg font-black text-slate-900">Serah Terima Berhasil Diselesaikan!</h3>
                    <p class="text-xs text-slate-600 mt-1" x-text="completedResult?.message"></p>
                    <p class="text-xs font-mono font-bold text-blue-950 mt-2" x-text="'Nomor Register: ' + completedResult?.nomor_bast"></p>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-center gap-3 pt-2">
                    <a :href="completedResult?.bast_url" target="_blank"
                       class="px-6 py-3 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-xl shadow-md transition-all flex items-center gap-2">
                        <i class="fa-solid fa-print text-amber-300"></i>
                        <span>Cetak Berita Acara (BAST)</span>
                    </a>
                    <button type="button" @click="resetScanner()"
                            class="px-5 py-3 bg-white border border-slate-300 text-slate-700 text-xs font-bold rounded-xl hover:bg-slate-50 transition-all">
                        Scan Tiket Lainnya
                    </button>
                </div>
            </div>

        </div>

    </div>

    <!-- TAB 3: RIWAYAT PENGEMBALIAN & CETAK BAST -->
    <div x-show="activeTab === 'riwayat'" x-cloak class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-slate-900">Riwayat Serah Terima & Arsip Dokumen BAST</h3>
                <p class="text-xs text-slate-500 mt-0.5">Arsip seluruh barang yang telah sah diserahkan kembali kepada pemilik di {{ $kampus->nama_kampus }}.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-purple-50 text-purple-800 border border-purple-200">
                {{ $riwayatList->total() }} Selesai
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                        <th class="py-4 px-6">Nomor Dokumen BAST</th>
                        <th class="py-4 px-6">Barang Diserahkan</th>
                        <th class="py-4 px-6">Penerima (Mahasiswa)</th>
                        <th class="py-4 px-6">Staf Penyerah</th>
                        <th class="py-4 px-6">Waktu Serah Terima</th>
                        <th class="py-4 px-6 text-right">Aksi Dokumen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs text-slate-700">
                    @forelse($riwayatList as $rw)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-mono font-bold text-blue-900">
                                {{ $rw->nomor_bast }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-bold text-slate-900 block">{{ $rw->laporan?->nama_barang }}</span>
                                <span class="text-[11px] text-slate-400">{{ $rw->laporan?->kategori?->nama_kategori }}</span>
                            </td>
                            <td class="py-4 px-6">
                                <span class="font-semibold text-slate-900">{{ $rw->user?->name }}</span>
                                <span class="block text-[11px] text-slate-400">NIM. {{ $rw->user?->nim ?? '-' }}</span>
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                {{ $rw->petugas?->name ?? 'Staf Kampus' }}
                            </td>
                            <td class="py-4 px-6 text-slate-500">
                                {{ $rw->tanggal_pengembalian ? $rw->tanggal_pengembalian->translatedFormat('d M Y, H:i') : '-' }} WIB
                            </td>
                            <td class="py-4 px-6 text-right">
                                <a href="{{ route('petugas.bast.cetak', $rw->id) }}" target="_blank"
                                   class="px-3.5 py-1.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-bold text-xs shadow-sm transition-all inline-flex items-center gap-1.5">
                                    <i class="fa-solid fa-print text-amber-300"></i>
                                    <span>Cetak BAST</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400">
                                <i class="fa-solid fa-file-invoice text-4xl text-slate-300 mb-2 block"></i>
                                Belum ada riwayat serah terima di kampus ini.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayatList->hasPages())
            <div class="p-4 border-t border-slate-100">{{ $riwayatList->links() }}</div>
        @endif
    </div>

</div>

<!-- Include html5-qrcode CDN -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endsection
