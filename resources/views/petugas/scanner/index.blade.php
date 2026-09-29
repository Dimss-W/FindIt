@extends('layouts.app')

@section('title', 'Scanner QR Serah Terima - FINDIT UBSI')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto"
     x-data="{
         scanMode: 'camera', // 'camera' or 'manual'
         manualQuery: '',
         loadingVerify: false,
         verifyData: null,
         errorMessage: '',
         isSubmitting: false,
         completedResult: null,
         html5QrCode: null,
         cameraActive: false,
         cameraError: '',

         initScanner() {
             if (this.scanMode === 'camera' && !this.cameraActive) {
                 this.$nextTick(() => {
                     this.startCamera();
                 });
             }
         },

         startCamera() {
             this.cameraError = '';
             const qrElement = document.getElementById('qr-reader');
             if (!qrElement) return;

             if (typeof Html5Qrcode === 'undefined') {
                 this.cameraError = 'Library Scanner sedang dimuat, silakan coba sesaat lagi.';
                 return;
             }

             if (this.html5QrCode) {
                 this.stopCamera();
             }

             this.html5QrCode = new Html5Qrcode('qr-reader');
             const config = { fps: 10, qrbox: { width: 250, height: 250 } };

             this.html5QrCode.start(
                 { facingMode: 'environment' },
                 config,
                 (decodedText) => {
                     // Success scan callback
                     this.onScanSuccess(decodedText);
                 },
                 (error) => {
                     // parse error, ignore as it continuously scans
                 }
             ).then(() => {
                 this.cameraActive = true;
             }).catch((err) => {
                 this.cameraActive = false;
                 this.cameraError = 'Izin kamera ditolak atau kamera tidak ditemukan pada perangkat Anda. Silakan gunakan tab Input Manual di samping.';
                 console.warn('Camera start error:', err);
             });
         },

         stopCamera() {
             if (this.html5QrCode && this.cameraActive) {
                 this.html5QrCode.stop().then(() => {
                     this.cameraActive = false;
                 }).catch((err) => console.log('Camera stop error', err));
             }
         },

         onScanSuccess(code) {
             // Mainkan feedback suara beep halus jika didukung
             try {
                 const ctx = new (window.AudioContext || window.webkitAudioContext)();
                 const osc = ctx.createOscillator();
                 osc.frequency.setValueAtTime(880, ctx.currentTime);
                 osc.connect(ctx.destination);
                 osc.start();
                 osc.stop(ctx.currentTime + 0.15);
             } catch(e) {}

             // Pause scanner
             this.stopCamera();
             this.verifyTicket(code);
         },

         verifyTicket(queryCode) {
             const code = queryCode || this.manualQuery;
             if (!code || !code.trim()) {
                 this.errorMessage = 'Silakan masukkan kode tiket atau scan QR terlebih dahulu.';
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
                 body: JSON.stringify({ query: code.trim() })
             })
             .then(res => res.json().then(data => ({ status: res.status, body: data })))
             .then(({ status, body }) => {
                 this.loadingVerify = false;
                 if (status === 200 && body.success) {
                     this.verifyData = body;
                     this.errorMessage = '';
                 } else {
                     this.errorMessage = body.message || 'Terjadi kesalahan saat memverifikasi tiket.';
                     if (body.already_completed && body.bast_url) {
                         this.completedResult = {
                             already: true,
                             message: body.message,
                             bast_url: body.bast_url
                         };
                     }
                 }
             })
             .catch(err => {
                 this.loadingVerify = false;
                 this.errorMessage = 'Gagal menghubungi server. Periksa koneksi internet Anda.';
             });
         },

         submitSerahTerima() {
             if (!this.verifyData) return;
             this.isSubmitting = true;

             const form = document.getElementById('form-serah-terima');
             const formData = new FormData(form);
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
                     this.manualQuery = '';
                 } else {
                     alert(body.message || 'Gagal memproses serah terima barang.');
                 }
             })
             .catch(err => {
                 this.isSubmitting = false;
                 alert('Terjadi kesalahan jaringan.');
             });
         },

         resetScanner() {
             this.verifyData = null;
             this.errorMessage = '';
             this.completedResult = null;
             this.manualQuery = '';
             if (this.scanMode === 'camera') {
                 this.startCamera();
             }
         }
     }"
     x-init="initScanner()">

    <!-- Page Header Banner -->
    <div class="p-6 rounded-3xl bg-gradient-to-r from-slate-900 via-brand-ubsi to-slate-900 text-white shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 border border-blue-900/40">
        <div>
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold border border-emerald-500/30 mb-2">
                <i class="fa-solid fa-qrcode"></i> Fitur Verifikasi Pengambilan Resmi
            </div>
            <h1 class="text-2xl sm:text-3xl font-black tracking-tight">
                Scanner QR Serah Terima Barang
            </h1>
            <p class="text-slate-300 text-xs sm:text-sm mt-1">
                Pindai QR Code dari layar HP mahasiswa atau masukkan Kode Tiket untuk validasi kepemilikan dan serah terima fisik barang.
            </p>
        </div>

        <div class="flex items-center gap-3">
            <a href="{{ route('petugas.riwayat.pengembalian') }}" class="px-4 py-2.5 rounded-2xl bg-white/10 hover:bg-white/20 text-white text-xs font-semibold backdrop-blur-md border border-white/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left text-amber-300"></i>
                <span>Riwayat BAST</span>
            </a>
        </div>
    </div>

    <!-- Success Result Banner (After Hand-over) -->
    <div x-show="completedResult" x-cloak class="p-6 rounded-3xl bg-emerald-50 border-2 border-emerald-300 shadow-xl space-y-4 animate-fadeIn">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-2xl bg-emerald-500 text-white flex items-center justify-center text-2xl flex-shrink-0 shadow-lg shadow-emerald-500/30">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div class="flex-1">
                <span class="px-2.5 py-0.5 rounded-full bg-emerald-200 text-emerald-800 text-[11px] font-bold uppercase">Berhasil Diserahterimakan</span>
                <h2 class="text-xl font-black text-slate-900 mt-1" x-text="completedResult?.message"></h2>
                <p class="text-xs text-slate-600 mt-1">
                    Nomor Registrasi BAST: <strong class="text-emerald-700 font-mono text-sm" x-text="completedResult?.nomor_bast"></strong>
                </p>
                <p class="text-xs text-slate-500 mt-0.5">
                    Status barang resmi diperbarui menjadi <strong>DIKEMBALIKAN</strong> dan tercatat ke riwayat kampus.
                </p>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-3 pt-2 border-t border-emerald-200">
            <a :href="completedResult?.bast_url" 
               target="_blank"
               class="px-5 py-2.5 rounded-xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/20 transition-all flex items-center gap-2">
                <i class="fa-solid fa-print text-amber-300"></i>
                <span>Cetak Berita Acara Serah Terima (BAST)</span>
            </a>

            <button type="button" 
                    @click="resetScanner()" 
                    class="px-5 py-2.5 rounded-xl bg-white border border-slate-300 hover:bg-slate-50 text-slate-700 text-xs font-bold transition-all">
                <span>Scan Tiket Lainnya</span>
            </button>
        </div>
    </div>

    <!-- Scanner & Verification Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6" x-show="!completedResult">
        
        <!-- Left Column: Scanner Mode (Camera / Manual) -->
        <div class="lg:col-span-6 space-y-4">
            <div class="bg-white rounded-3xl p-6 border border-slate-200/80 shadow-sm space-y-4">
                
                <!-- Segmented Tabs -->
                <div class="p-1 bg-slate-100 rounded-2xl flex items-center gap-1 border border-slate-200/80">
                    <button type="button" 
                            @click="scanMode = 'camera'; stopCamera(); $nextTick(() => startCamera());"
                            :class="scanMode === 'camera' ? 'bg-brand-ubsi text-white font-bold shadow-md' : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="flex-1 py-2 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-camera"></i>
                        <span>Kamera HP / Webcam</span>
                    </button>

                    <button type="button" 
                            @click="scanMode = 'manual'; stopCamera();"
                            :class="scanMode === 'manual' ? 'bg-brand-ubsi text-white font-bold shadow-md' : 'text-slate-600 hover:text-slate-900 font-medium'"
                            class="flex-1 py-2 px-3 rounded-xl text-xs transition-all flex items-center justify-center gap-2">
                        <i class="fa-solid fa-keyboard"></i>
                        <span>Ketik Kode Manual</span>
                    </button>
                </div>

                <!-- Camera Scanner View -->
                <div x-show="scanMode === 'camera'" class="space-y-3">
                    <div class="relative w-full aspect-square max-w-[340px] mx-auto rounded-2xl overflow-hidden bg-slate-950 flex items-center justify-center border-4 border-slate-800 shadow-inner">
                        <div id="qr-reader" class="w-full h-full"></div>

                        <!-- Target Frame Overlay -->
                        <div class="absolute inset-0 pointer-events-none flex items-center justify-center" x-show="cameraActive">
                            <div class="w-48 h-48 border-2 border-dashed border-emerald-400 rounded-2xl animate-pulse flex items-center justify-center">
                                <span class="text-[10px] text-emerald-300 bg-slate-950/70 px-2 py-0.5 rounded-full font-semibold">Posisikan QR di Sini</span>
                            </div>
                        </div>

                        <!-- Camera Error State -->
                        <div x-show="cameraError" class="p-6 text-center text-white space-y-3">
                            <i class="fa-solid fa-video-slash text-3xl text-rose-400"></i>
                            <p class="text-xs text-slate-300 leading-relaxed" x-text="cameraError"></p>
                            <button type="button" @click="startCamera()" class="px-4 py-2 rounded-xl bg-blue-600 text-white text-xs font-bold shadow">
                                Coba Kamera Lagi
                            </button>
                        </div>
                    </div>

                    <p class="text-center text-xs text-slate-500">
                        <i class="fa-solid fa-shield-halved text-brand-ubsi mr-1"></i> Arahkan kamera ke QR Code Tiket di layar HP mahasiswa.
                    </p>
                </div>

                <!-- Manual Input View -->
                <div x-show="scanMode === 'manual'" class="space-y-4 py-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                            Nomor Tiket Pengambilan (Contoh: TK-UBSI-KRM-XXXX)
                        </label>
                        <div class="flex items-center gap-2">
                            <div class="relative flex-1">
                                <input type="text" 
                                       x-model="manualQuery" 
                                       @keydown.enter.prevent="verifyTicket()"
                                       placeholder="Ketik kode tiket..."
                                       class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-300 rounded-2xl text-xs font-mono font-bold text-slate-800 uppercase focus:bg-white focus:border-brand-ubsi focus:ring-4 focus:ring-blue-500/10 focus:outline-none transition-all">
                                <i class="fa-solid fa-ticket absolute left-3.5 top-3.5 text-slate-400 text-xs"></i>
                            </div>

                            <button type="button" 
                                    @click="verifyTicket()" 
                                    :disabled="loadingVerify"
                                    class="px-5 py-3 rounded-2xl bg-brand-ubsi hover:bg-blue-900 text-white text-xs font-bold shadow-md shadow-blue-900/20 transition-all flex items-center gap-2 disabled:opacity-50">
                                <template x-if="!loadingVerify">
                                    <span class="flex items-center gap-1.5">
                                        <i class="fa-solid fa-magnifying-glass"></i>
                                        <span>Periksa</span>
                                    </span>
                                </template>
                                <template x-if="loadingVerify">
                                    <i class="fa-solid fa-circle-notch fa-spin text-amber-300"></i>
                                </template>
                            </button>
                        </div>
                    </div>

                    <div class="p-4 rounded-2xl bg-blue-50/60 border border-blue-100 text-xs text-blue-900 space-y-1.5">
                        <span class="font-bold flex items-center gap-1.5 text-brand-ubsi">
                            <i class="fa-solid fa-circle-info"></i> Petunjuk Pemeriksaan:
                        </span>
                        <p class="text-[11px] text-slate-600 leading-relaxed">
                            Kode tiket tertera jelas pada bagian bawah QR Code tiket pengambilan di aplikasi mahasiswa. Anda juga dapat menempelkan token QR lengkap jika menggunakan barcode scanner USB eksternal.
                        </p>
                    </div>
                </div>

                <!-- Error Message Banner -->
                <div x-show="errorMessage" x-cloak class="p-3.5 rounded-2xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2.5">
                    <i class="fa-solid fa-circle-exclamation text-rose-500 text-base flex-shrink-0"></i>
                    <span x-text="errorMessage"></span>
                </div>

            </div>
        </div>

        <!-- Right Column: Verification & Hand-over Panel -->
        <div class="lg:col-span-6 space-y-4">
            
            <!-- Default Placeholder State -->
            <div x-show="!verifyData && !loadingVerify" class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm text-center space-y-3">
                <div class="w-16 h-16 rounded-3xl bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mx-auto">
                    <i class="fa-solid fa-id-card-clip"></i>
                </div>
                <h3 class="text-base font-bold text-slate-800">Menunggu Pemindaian Tiket</h3>
                <p class="text-xs text-slate-500 max-w-sm mx-auto leading-relaxed">
                    Arahkan kamera ke QR Code atau masukkan kode tiket mahasiswa. Detail data barang dan identitas pemilik akan otomatis tampil di panel ini.
                </p>
            </div>

            <!-- Loading Skeleton -->
            <div x-show="loadingVerify" x-cloak class="bg-white rounded-3xl p-8 border border-slate-200/80 shadow-sm text-center space-y-3 animate-pulse">
                <div class="w-12 h-12 rounded-full bg-blue-100 text-brand-ubsi flex items-center justify-center text-xl mx-auto">
                    <i class="fa-solid fa-circle-notch fa-spin"></i>
                </div>
                <h3 class="text-sm font-bold text-slate-700">Memverifikasi Tiket Pengambilan...</h3>
                <p class="text-xs text-slate-400">Mencocokkan kode dengan basis data klaim UBSI</p>
            </div>

            <!-- Verified Item & Owner Card -->
            <div x-show="verifyData" x-cloak class="bg-white rounded-3xl p-6 border border-emerald-200 shadow-lg space-y-5">
                
                <!-- Ticket Badge & Verification Stamp -->
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-xl bg-emerald-100 text-emerald-800 text-xs font-black tracking-wider flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-check text-emerald-600"></i>
                            <span x-text="verifyData?.kode_tiket"></span>
                        </span>
                        <span class="text-[10px] text-slate-400">Terverifikasi</span>
                    </div>

                    <span class="text-[11px] font-bold text-brand-ubsi bg-blue-50 px-2.5 py-1 rounded-full border border-blue-200" x-text="verifyData?.kampus"></span>
                </div>

                <!-- Item Details Summary -->
                <div class="flex items-start gap-4 p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80">
                    <img :src="verifyData?.foto_barang" alt="Foto Barang" class="w-20 h-20 rounded-xl object-cover border border-slate-200 flex-shrink-0">
                    <div class="space-y-1 min-w-0">
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-amber-100 text-amber-800" x-text="verifyData?.kategori"></span>
                        <h4 class="text-base font-bold text-slate-900 truncate" x-text="verifyData?.nama_barang"></h4>
                        <p class="text-xs text-slate-500">
                            Lokasi Simpan: <strong class="text-slate-700" x-text="verifyData?.lokasi_ditemukan"></strong>
                        </p>
                    </div>
                </div>

                <!-- Owner Identification Details -->
                <div class="space-y-2 border border-blue-100 rounded-2xl p-4 bg-blue-50/40">
                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500 block">Identitas Pengambil (Mahasiswa):</span>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-brand-ubsi text-white flex items-center justify-center font-bold text-sm">
                            <span x-text="verifyData?.mahasiswa?.name?.charAt(0) || 'M'"></span>
                        </div>
                        <div class="min-w-0">
                            <h5 class="text-sm font-bold text-slate-900" x-text="verifyData?.mahasiswa?.name"></h5>
                            <p class="text-xs text-slate-600">
                                NIM: <strong class="font-mono text-brand-ubsi" x-text="verifyData?.mahasiswa?.nim"></strong> | Telp: <span x-text="verifyData?.mahasiswa?.no_hp"></span>
                            </p>
                        </div>
                    </div>
                    <div class="pt-2 text-[11px] text-slate-600 border-t border-blue-100/80">
                        <strong>Catatan Ciri-ciri Klaim:</strong>
                        <p class="text-slate-700 italic mt-0.5" x-text="verifyData?.bukti_klaim"></p>
                    </div>
                </div>

                <!-- Handover Submission Form -->
                <form id="form-serah-terima" @submit.prevent="submitSerahTerima()" class="space-y-3 pt-2">
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Catatan Berita Acara Serah Terima (Opsional):
                        </label>
                        <input type="text" 
                               name="catatan" 
                               placeholder="Contoh: Diserahkan dalam kondisi lengkap dan utuh kepada pemilik sah."
                               class="w-full px-3.5 py-2 bg-slate-50 border border-slate-300 rounded-xl text-xs text-slate-800 focus:bg-white focus:border-brand-ubsi focus:outline-none">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">
                            Foto Dokumentasi Penyerahan Fisik (Opsional):
                        </label>
                        <input type="file" 
                               name="foto_penyerahan" 
                               accept="image/*"
                               class="w-full text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-brand-ubsi hover:file:bg-blue-100">
                    </div>

                    <!-- Submit Handover Button -->
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="w-full py-3.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-700 hover:from-emerald-700 hover:to-teal-800 text-white text-xs font-bold shadow-lg shadow-emerald-600/30 active:scale-[0.98] transition-all flex items-center justify-center gap-2 disabled:opacity-50">
                        <template x-if="!isSubmitting">
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-handshake text-base"></i>
                                <span>KONFIRMASI SERAH TERIMA & TERBITKAN BAST</span>
                            </span>
                        </template>
                        <template x-if="isSubmitting">
                            <span class="flex items-center gap-2">
                                <i class="fa-solid fa-circle-notch fa-spin text-white"></i>
                                <span>Menyimpan Serah Terima...</span>
                            </span>
                        </template>
                    </button>
                </form>

            </div>

        </div>

    </div>

</div>

<!-- HTML5-QRCode Library for Web Camera Scanning -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
@endsection
