<!-- PWA Service Worker Registration & Interactive Install Prompt -->
<div id="pwa-install-container" 
     x-data="{
         deferredPrompt: null,
         showPrompt: false,
         isIos: false,
         showIosGuide: false,
         installed: false,

         init() {
             // 1. Cek apakah sudah berjalan dalam mode standalone (sudah terinstal)
             const isStandalone = window.matchMedia('(display-mode: standalone)').matches || window.navigator.standalone;
             if (isStandalone) {
                 this.installed = true;
                 return;
             }

             // 2. Deteksi iOS Safari
             const userAgent = window.navigator.userAgent.toLowerCase();
             this.isIos = /iphone|ipad|ipod/.test(userAgent) && !window.MSStream;

             // 3. Tangkap event beforeinstallprompt (Android Chrome, Edge, Chromium)
             window.addEventListener('beforeinstallprompt', (e) => {
                 e.preventDefault();
                 this.deferredPrompt = e;
                 // Tampilkan jika belum pernah di-dismiss pada sesi ini
                 if (!sessionStorage.getItem('pwa_prompt_dismissed')) {
                     setTimeout(() => {
                         this.showPrompt = true;
                     }, 1500);
                 }
             });

             // 4. Deteksi aplikasi berhasil diinstal
             window.addEventListener('appinstalled', () => {
                 this.showPrompt = false;
                 this.deferredPrompt = null;
                 this.installed = true;
                 console.log('FINDIT UBSI PWA berhasil diinstal!');
             });

             // 4b. Listen for manual trigger from buttons
             window.addEventListener('pwa:open-install', () => {
                 this.showPrompt = true;
                 if (this.deferredPrompt) {
                     this.triggerInstall();
                 } else if (this.isIos) {
                     this.showIosGuide = true;
                 }
             });

             // 5. Registrasi Service Worker
             if ('serviceWorker' in navigator) {
                 navigator.serviceWorker.register('/sw.js')
                     .then(reg => {
                         console.log('FINDIT PWA Service Worker terdaftar:', reg.scope);
                     })
                     .catch(err => {
                         console.error('FINDIT PWA Service Worker gagal mendaftar:', err);
                     });
             }
         },

         showHttpGuide: false,

         async triggerInstall() {
             if (this.deferredPrompt) {
                 try {
                     await this.deferredPrompt.prompt();
                     const { outcome } = await this.deferredPrompt.userChoice;
                     if (outcome === 'accepted') {
                         console.log('Pengguna menyetujui instalasi PWA');
                     }
                 } catch (e) {
                     console.warn('PWA prompt fallback to guide:', e);
                     this.showHttpGuide = true;
                 }
                 this.deferredPrompt = null;
                 this.showPrompt = false;
             } else if (this.isIos) {
                 this.showIosGuide = true;
             } else {
                 this.showHttpGuide = true;
             }
         },

         dismiss() {
             this.showPrompt = false;
             this.showIosGuide = false;
             this.showHttpGuide = false;
             sessionStorage.setItem('pwa_prompt_dismissed', 'true');
         }
     }"
     class="relative z-50">

    <!-- Floating PWA Install Banner -->
    <div x-show="showPrompt && !installed" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-8 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-8 scale-95"
         x-cloak
         class="fixed bottom-16 sm:bottom-6 left-4 right-4 sm:left-auto sm:right-6 sm:max-w-md bg-slate-900/95 text-white p-4 rounded-3xl shadow-2xl border border-blue-500/30 backdrop-blur-xl flex flex-col gap-3">
        
        <div class="flex items-start justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-blue-900/80 border border-blue-400/30 p-2 flex items-center justify-center flex-shrink-0 shadow-md">
                    <img src="{{ asset('icons/icon-96x96.png') }}" alt="Ikon FINDIT UBSI" class="w-full h-full object-contain">
                </div>
                <div>
                    <div class="flex items-center gap-1.5">
                        <h4 class="text-xs font-bold text-white tracking-wide">Instal FINDIT UBSI</h4>
                        <span class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-amber-400 text-slate-950 uppercase">PWA</span>
                    </div>
                    <p class="text-[11px] text-slate-300 leading-snug mt-0.5">
                        Pasang aplikasi di layar utama HP Anda untuk akses cepat tanpa perlu membuka browser.
                    </p>
                </div>
            </div>

            <button type="button" @click="dismiss()" class="text-slate-400 hover:text-white text-xs p-1">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <div class="flex items-center gap-2 pt-1">
            <button type="button" 
                    @click="triggerInstall()"
                    class="flex-1 py-2 px-3.5 rounded-xl bg-gradient-to-r from-blue-600 via-brand-ubsi to-indigo-700 hover:from-blue-500 hover:to-indigo-600 text-white text-xs font-bold shadow-md shadow-blue-900/40 active:scale-95 transition-all flex items-center justify-center gap-2">
                <i class="fa-solid fa-cloud-arrow-down text-amber-300 text-xs"></i>
                <span>Instal Sekarang</span>
            </button>
            <button type="button" 
                    @click="dismiss()"
                    class="py-2 px-3 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-300 text-xs font-semibold active:scale-95 transition-colors">
                Nanti
            </button>
        </div>
    </div>

    <!-- Petunjuk Khusus iOS Safari (Jika diklik di iPhone) -->
    <div x-show="showIosGuide" 
         x-cloak
         @click.away="showIosGuide = false"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm flex items-end sm:items-center justify-center p-4 z-50">
        <div class="bg-slate-900 text-white p-6 rounded-3xl max-w-sm w-full border border-slate-700 shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-brands fa-apple text-xl text-amber-400"></i>
                    <h4 class="text-sm font-bold">Pasang di iPhone / iPad</h4>
                </div>
                <button type="button" @click="showIosGuide = false" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <p class="text-xs text-slate-300 leading-relaxed">
                Di Safari iOS, Anda dapat memasang aplikasi ini ke Layar Utama dengan 2 langkah mudah:
            </p>
            <ol class="text-xs text-slate-300 space-y-2.5 list-decimal list-inside bg-slate-800/60 p-3.5 rounded-2xl border border-slate-700">
                <li>Ketuk tombol <strong class="text-amber-400">Bagikan (Share)</strong> <i class="fa-solid fa-arrow-up-from-bracket ml-1 text-blue-400"></i> di bar bawah Safari.</li>
                <li>Gulir ke bawah lalu pilih <strong class="text-white">"Tambahkan ke Layar Utama"</strong> (Add to Home Screen).</li>
            </ol>
            <button type="button" @click="showIosGuide = false" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 font-bold text-xs rounded-xl text-white">
                Mengerti
            </button>
        </div>
    </div>

    <!-- Petunjuk Khusus Android (Jika diakses via HTTP IP Lokal) -->
    <div x-show="showHttpGuide" 
         x-cloak
         @click.away="showHttpGuide = false"
         class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm flex items-end sm:items-center justify-center p-4 z-50">
        <div class="bg-slate-900 text-white p-6 rounded-3xl max-w-sm w-full border border-slate-700 shadow-2xl space-y-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i class="fa-brands fa-android text-xl text-emerald-400"></i>
                    <h4 class="text-sm font-bold">Pasang di Android</h4>
                </div>
                <button type="button" @click="showHttpGuide = false" class="text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <p class="text-xs text-slate-300 leading-relaxed">
                Di jaringan lokal, pasang aplikasi langsung ke layar utama melalui menu Google Chrome:
            </p>
            <ol class="text-xs text-slate-300 space-y-2.5 list-decimal list-inside bg-slate-800/60 p-3.5 rounded-2xl border border-slate-700">
                <li>Ketuk ikon <strong class="text-amber-400">titik tiga (⋮)</strong> di pojok kanan atas browser Chrome.</li>
                <li>Pilih menu <strong class="text-white">"Tambahkan ke Layar Utama"</strong> *(Add to Home screen)*.</li>
                <li>Ketuk <strong class="text-emerald-400">Tambahkan</strong>. Ikon FINDIT UBSI akan langsung muncul di Homescreen HP Anda!</li>
            </ol>
            <button type="button" @click="showHttpGuide = false" class="w-full py-2.5 bg-emerald-600 hover:bg-emerald-700 font-bold text-xs rounded-xl text-white">
                Siap, Mengerti!
            </button>
        </div>
    </div>

</div>
