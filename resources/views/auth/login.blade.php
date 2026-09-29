<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Masuk ke Sistem FINDIT UBSI - Lost and Found 27 Kampus Universitas Bina Sarana Informatika">
    <title>Masuk ke Sistem - FINDIT UBSI</title>
    
    <!-- Favicon Resmi UBSI -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-ubsi.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-ubsi.png') }}">
    
    <!-- PWA Head Metadata & Manifest -->
    @include('partials.pwa-head')
    
    <!-- Google Fonts Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    <!-- FontAwesome 6 Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            200: '#c7d2fe',
                            300: '#a5b4fc',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#1e1b4b',
                            ubsi: '#1e3a8a', // Deep UBSI Navy
                            gold: '#f59e0b',
                        }
                    }
                }
            }
        }
    </script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow: hidden;
        }
        @media (max-height: 640px) {
            body {
                overflow-y: auto !important;
            }
        }
        .bg-sharp-ubsi {
            background-image: url('{{ asset('images/gedung-ubsi.jpg') }}');
            background-size: cover;
            background-position: center;
            image-rendering: -webkit-optimize-contrast;
            image-rendering: crisp-edges;
            transform: translateZ(0);
            backface-visibility: hidden;
        }
        @keyframes subtleFloat {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-3px); }
        }
        .animate-float {
            animation: subtleFloat 5s ease-in-out infinite;
        }
        @keyframes pulseGlow {
            0%, 100% { box-shadow: 0 0 15px rgba(30, 58, 138, 0.2); }
            50% { box-shadow: 0 0 30px rgba(245, 158, 11, 0.35); }
        }
        .glow-card {
            animation: pulseGlow 6s infinite;
        }
    </style>
</head>
<body class="h-full w-full bg-slate-950 text-slate-800 antialiased relative flex flex-col justify-between p-3 sm:p-5 select-none"
      x-data="{
          activeTab: 'mahasiswa', // 'mahasiswa' or 'admin'
          showPassword: false,
          loginValue: '',
          passwordValue: '',
          nimStatus: '',
          dateParsedText: '',
          justCopied: '',
          isSubmitting: false,
          bgDimLevel: 'normal', // 'normal', 'clear', 'dim'
          cardTiltX: 0,
          cardTiltY: 0,
          
          handleMouseMove(e) {
              const rect = e.currentTarget.getBoundingClientRect();
              const x = e.clientX - rect.left - rect.width / 2;
              const y = e.clientY - rect.top - rect.height / 2;
              this.cardTiltX = (y / rect.height) * -6;
              this.cardTiltY = (x / rect.width) * 6;
          },
          
          resetTilt() {
              this.cardTiltX = 0;
              this.cardTiltY = 0;
          },
          
          updateNimFeedback() {
              const clean = this.loginValue.replace(/\D/g, '');
              if (this.activeTab === 'mahasiswa') {
                  if (clean.length === 8) {
                      this.nimStatus = 'valid';
                  } else if (clean.length > 8) {
                      this.nimStatus = 'overflow';
                  } else if (clean.length > 0) {
                      this.nimStatus = 'typing';
                  } else {
                      this.nimStatus = '';
                  }
              }
          },
          
          updateDateFeedback() {
              if (this.activeTab === 'mahasiswa') {
                  const val = (this.passwordValue || '').trim();
                  const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

                  // Format 1: YYYY-MM-DD (contoh: 2004-05-14)
                  const matchYmd = val.match(/^(\d{4})[-/.](\d{1,2})[-/.](\d{1,2})$/);
                  if (matchYmd) {
                      const y = parseInt(matchYmd[1], 10);
                      const m = parseInt(matchYmd[2], 10);
                      const d = parseInt(matchYmd[3], 10);
                      if (d >= 1 && d <= 31 && m >= 1 && m <= 12 && y >= 1950 && y <= 2026) {
                          this.dateParsedText = `${d} ${months[m - 1]} ${y} (Format YYYY-MM-DD Valid)`;
                          return;
                      }
                  }

                  // Format 2: 8 digit angka YYYYMMDD atau DDMMYYYY
                  const clean = val.replace(/\D/g, '');
                  if (clean.length === 8) {
                      const first4 = parseInt(clean.substring(0, 4), 10);
                      if (first4 >= 1950 && first4 <= 2026) {
                          const y = first4;
                          const m = parseInt(clean.substring(4, 6), 10);
                          const d = parseInt(clean.substring(6, 8), 10);
                          if (d >= 1 && d <= 31 && m >= 1 && m <= 12) {
                              this.dateParsedText = `${d} ${months[m - 1]} ${y} (Format: ${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')})`;
                              return;
                          }
                      }
                      const d = parseInt(clean.substring(0, 2), 10);
                      const m = parseInt(clean.substring(2, 4), 10);
                      const y = parseInt(clean.substring(4, 8), 10);
                      if (d >= 1 && d <= 31 && m >= 1 && m <= 12 && y >= 1950 && y <= 2026) {
                          this.dateParsedText = `${d} ${months[m - 1]} ${y} (Format: ${y}-${String(m).padStart(2, '0')}-${String(d).padStart(2, '0')})`;
                          return;
                      }
                  }
                  this.dateParsedText = '';
              } else {
                  this.dateParsedText = '';
              }
          },
          
          switchTab(tab) {
              this.activeTab = tab;
              this.dateParsedText = '';
              this.nimStatus = '';
              if (tab === 'mahasiswa' && this.loginValue.includes('@')) {
                  this.loginValue = '';
                  this.passwordValue = '';
              } else if (tab === 'admin' && !this.loginValue.includes('@') && this.loginValue !== '') {
                  this.loginValue = '';
                  this.passwordValue = '';
              }
          },
          
          setDemo(login, pass, tab, label) {
              this.switchTab(tab);
              this.loginValue = login;
              this.passwordValue = pass;
              this.updateNimFeedback();
              this.updateDateFeedback();
              this.justCopied = label;
              setTimeout(() => { this.justCopied = ''; }, 3000);
              $nextTick(() => {
                  document.getElementById('login_input')?.focus();
              });
          },
          
          toggleBgDim() {
              if (this.bgDimLevel === 'normal') this.bgDimLevel = 'clear';
              else if (this.bgDimLevel === 'clear') this.bgDimLevel = 'dim';
              else this.bgDimLevel = 'normal';
          }
      }">

    <!-- Authentic Crystal-Sharp UBSI Building Background Image -->
    <div class="absolute inset-0 z-0 bg-sharp-ubsi transition-all duration-700"></div>

    <!-- Interactive Atmospheric Overlay (Brightness Adjustable) -->
    <div class="absolute inset-0 z-0 transition-opacity duration-500 pointer-events-none"
         :class="{
             'bg-gradient-to-t from-slate-950/80 via-slate-950/25 to-slate-950/50': bgDimLevel === 'normal',
             'bg-gradient-to-t from-slate-950/40 via-transparent to-slate-950/20': bgDimLevel === 'clear',
             'bg-gradient-to-t from-slate-950/90 via-slate-950/55 to-slate-950/75': bgDimLevel === 'dim'
         }">
    </div>

    <!-- Soft Ambient Highlights -->
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-blue-600/20 rounded-full blur-[130px] pointer-events-none z-0"></div>
    <div class="absolute -bottom-32 -right-32 w-96 h-96 bg-amber-500/15 rounded-full blur-[130px] pointer-events-none z-0"></div>

    <!-- Minimalist Top Navigation Header (No Bulky Navbar) -->
    <header class="relative z-10 flex items-center justify-between w-full max-w-6xl mx-auto py-1">
        <!-- Back to Home Link -->
        <a href="{{ route('landing') }}" 
           class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 active:scale-95 backdrop-blur-md border border-white/25 text-white text-xs font-semibold shadow-lg transition-all duration-200 hover:-translate-x-0.5">
            <i class="fa-solid fa-arrow-left text-amber-300 text-xs"></i>
            <span>Kembali ke Beranda</span>
        </a>

        <!-- Interactive Background View Mode & Status -->
        <div class="flex items-center gap-2">
            <!-- Background Clarity Switcher Button -->
            <button type="button" 
                    @click="toggleBgDim()" 
                    title="Ubah kecerahan background gedung"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-white/15 hover:bg-white/25 active:scale-95 backdrop-blur-md border border-white/25 text-white text-[11px] font-semibold shadow-lg transition-all">
                <i class="fa-solid fa-wand-magic-sparkles text-amber-300 text-xs"></i>
                <span class="hidden sm:inline">Background:</span>
                <span class="font-bold text-amber-300" x-text="bgDimLevel === 'normal' ? 'Normal' : (bgDimLevel === 'clear' ? 'Jernih (HD)' : 'Fokus')"></span>
            </button>

            <!-- Live Campus Status Badge -->
            <div class="hidden sm:flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-white/15 backdrop-blur-md border border-white/25 text-white text-xs font-semibold shadow-lg">
                <span class="relative flex h-2 w-2">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                </span>
                <span>27 Kampus Terpadu UBSI</span>
            </div>
        </div>
    </header>

    <!-- Center Login Card Section (Zero Scroll Viewport Fit with 3D Interaction) -->
    <main class="relative z-10 flex-1 flex items-center justify-center my-auto py-1"
          @mousemove="handleMouseMove($event)"
          @mouseleave="resetTilt()">
        
        <div class="max-w-[420px] w-full bg-white/95 backdrop-blur-2xl px-5 py-4 sm:px-6 sm:py-5 rounded-3xl border border-white/80 shadow-[0_25px_60px_-15px_rgba(0,0,0,0.5)] glow-card space-y-3 transition-transform duration-150 ease-out"
             :style="`transform: perspective(1000px) rotateX(${cardTiltX}deg) rotateY(${cardTiltY}deg)`">
            
            <!-- Toast Feedback Notification -->
            <div x-show="justCopied" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-300 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-sm">
                <span class="flex items-center gap-1.5">
                    <i class="fa-solid fa-circle-check text-emerald-600"></i>
                    <span>Akun <strong x-text="justCopied"></strong> otomatis terisi!</span>
                </span>
                <span class="text-[10px] text-emerald-600">Siap login</span>
            </div>

            <!-- Card Brand Header -->
            <div class="text-center space-y-1">
                <div class="inline-flex p-1.5 rounded-2xl bg-slate-50 border border-slate-200/80 shadow-sm hover:scale-105 transition-transform duration-200">
                    <img src="{{ asset('images/logo-ubsi.png') }}" alt="Logo Resmi UBSI" class="w-10 h-10 object-contain">
                </div>
                <h1 class="text-xl font-black text-slate-900 tracking-tight flex items-center justify-center gap-1.5">
                    <span>FIND<span class="text-amber-500">IT</span></span>
                    <span class="text-[11px] font-bold px-2 py-0.5 rounded-full bg-blue-50 text-brand-ubsi border border-blue-200 uppercase">UBSI</span>
                </h1>
                <p class="text-[11px] text-slate-500 font-medium">
                    Sistem Terpadu Lost & Found 27 Kampus UBSI
                </p>
            </div>

            <!-- Interactive Role Switcher Tabs -->
            <div class="p-1 bg-slate-100 rounded-2xl flex items-center gap-1 border border-slate-200/80 relative">
                <button type="button" 
                        @click="switchTab('mahasiswa')" 
                        :class="activeTab === 'mahasiswa' ? 'bg-gradient-to-r from-brand-ubsi to-blue-700 text-white font-bold shadow-md' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="flex-1 py-1.5 px-3 rounded-xl text-xs transition-all duration-200 flex items-center justify-center gap-1.5 relative z-10">
                    <i class="fa-solid fa-user-graduate text-xs"></i>
                    <span>Mahasiswa</span>
                </button>

                <button type="button" 
                        @click="switchTab('admin')" 
                        :class="activeTab === 'admin' ? 'bg-gradient-to-r from-brand-ubsi to-blue-700 text-white font-bold shadow-md' : 'text-slate-600 hover:text-slate-900 font-medium'"
                        class="flex-1 py-1.5 px-3 rounded-xl text-xs transition-all duration-200 flex items-center justify-center gap-1.5 relative z-10">
                    <i class="fa-solid fa-shield-halved text-xs"></i>
                    <span>Admin / Staf</span>
                </button>
            </div>

            <!-- Dynamic Role Context Banner -->
            <div class="px-2.5 py-1 rounded-xl bg-blue-50/70 border border-blue-100 text-[10px] text-blue-900 flex items-center justify-between">
                <span class="flex items-center gap-1.5 font-medium">
                    <template x-if="activeTab === 'mahasiswa'">
                        <span>🎓 Akses Mahasiswa via NIM & Tanggal Lahir</span>
                    </template>
                    <template x-if="activeTab === 'admin'">
                        <span>🛡️ Akses Petugas & Administrator Kampus</span>
                    </template>
                </span>
                <span class="text-slate-400">FINDIT</span>
            </div>

            <!-- Compact 1-Click Demo Bar -->
            <div class="p-2 rounded-2xl bg-slate-50 border border-slate-200/80 space-y-1">
                <div class="flex items-center justify-between text-[10px]">
                    <span class="font-bold text-slate-600 uppercase tracking-wider flex items-center gap-1">
                        <i class="fa-solid fa-bolt text-amber-500"></i> Akun Uji Coba:
                    </span>
                    <span class="text-slate-400">Klik untuk isi instan</span>
                </div>

                <div class="grid grid-cols-3 gap-1.5">
                    <button type="button" 
                            @click="setDemo('12220199', '2004-05-14', 'mahasiswa', 'Mahasiswa Dimas')"
                            class="py-1 px-1.5 rounded-xl bg-white border border-slate-200 text-center hover:border-emerald-400 hover:bg-emerald-50/25 active:scale-95 transition-all group">
                        <div class="text-[10px] font-bold text-slate-800 group-hover:text-emerald-700 truncate">🎓 Mahasiswa</div>
                        <div class="text-[9px] text-slate-400 font-mono">12220199</div>
                    </button>

                    <button type="button" 
                            @click="setDemo('admin@bsi.ac.id', 'password123', 'admin', 'Admin Pusat')"
                            class="py-1 px-1.5 rounded-xl bg-white border border-slate-200 text-center hover:border-amber-400 hover:bg-amber-50/25 active:scale-95 transition-all group">
                        <div class="text-[10px] font-bold text-slate-800 group-hover:text-amber-700 truncate">👑 Admin Pusat</div>
                        <div class="text-[9px] text-slate-400">27 Kampus</div>
                    </button>

                    <button type="button" 
                            @click="setDemo('admin.krm@bsi.ac.id', 'password123', 'admin', 'Staf Kramat 98')"
                            class="py-1 px-1.5 rounded-xl bg-white border border-slate-200 text-center hover:border-blue-400 hover:bg-blue-50/25 active:scale-95 transition-all group">
                        <div class="text-[10px] font-bold text-slate-800 group-hover:text-brand-ubsi truncate">🏢 Staf Kampus</div>
                        <div class="text-[9px] text-slate-400 truncate">Kramat 98</div>
                    </button>
                </div>
            </div>

            <!-- Flash Error Message -->
            @if(session('error'))
                <div class="px-3 py-1.5 rounded-xl bg-rose-50 border border-rose-200 text-[11px] text-rose-700 font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-exclamation text-rose-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Flash Success Message -->
            @if(session('success'))
                <div class="px-3 py-1.5 rounded-xl bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-700 font-semibold flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-emerald-500"></i>
                    <span>{{ session('success') }}</span>
                </div>
            @endif

            <!-- Login Form -->
            <form action="{{ route('login') }}" 
                  method="POST" 
                  class="space-y-2.5"
                  @submit="isSubmitting = true">
                @csrf

                <!-- Input Identitas (NIM / Email) -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[11px] font-bold text-slate-800 uppercase tracking-wider">
                            <template x-if="activeTab === 'mahasiswa'">
                                <span>Nomor Induk Mahasiswa (NIM) <span class="text-rose-500">*</span></span>
                            </template>
                            <template x-if="activeTab === 'admin'">
                                <span>Email Admin UBSI <span class="text-rose-500">*</span></span>
                            </template>
                        </label>

                        <!-- Live NIM Validation Feedback -->
                        <template x-if="activeTab === 'mahasiswa'">
                            <div>
                                <template x-if="nimStatus === 'valid'">
                                    <span class="text-[9px] font-bold text-emerald-700 bg-emerald-50 px-1.5 py-0.5 rounded border border-emerald-200 flex items-center gap-1 animate-pulse">
                                        <i class="fa-solid fa-circle-check text-emerald-600"></i> Valid (8 Digit)
                                    </span>
                                </template>
                                <template x-if="nimStatus === 'typing'">
                                    <span class="text-[9px] font-semibold text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-200" x-text="loginValue.length + ' / 8 Digit'"></span>
                                </template>
                                <template x-if="nimStatus === 'overflow'">
                                    <span class="text-[9px] font-semibold text-amber-700 bg-amber-50 px-1.5 py-0.5 rounded border border-amber-200">
                                        Max 8 digit
                                    </span>
                                </template>
                            </div>
                        </template>
                    </div>

                    <div class="relative">
                        <input type="text" 
                               id="login_input" 
                               name="login" 
                               x-model="loginValue" 
                               @input="updateNimFeedback()"
                               required 
                               :placeholder="activeTab === 'mahasiswa' ? 'Contoh: 12220199' : 'Contoh: admin@bsi.ac.id'"
                               class="w-full pl-3.5 pr-9 py-2 bg-slate-50 border @error('login') border-rose-400 @else border-slate-300 @enderror rounded-xl text-xs text-slate-900 font-medium focus:bg-white focus:border-brand-ubsi focus:ring-4 focus:ring-blue-500/15 focus:outline-none transition-all">
                        
                        <div class="absolute right-3 top-2.5 text-slate-400 text-xs pointer-events-none">
                            <i :class="activeTab === 'mahasiswa' ? 'fa-solid fa-id-card' : 'fa-solid fa-envelope'"></i>
                        </div>
                    </div>
                    @error('login')
                        <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Input Password / Tanggal Lahir -->
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-[11px] font-bold text-slate-800 uppercase tracking-wider">
                            <template x-if="activeTab === 'mahasiswa'">
                                <span>Tanggal Lahir (Format: YYYY-MM-DD) <span class="text-rose-500">*</span></span>
                            </template>
                            <template x-if="activeTab === 'admin'">
                                <span>Kata Sandi Akun <span class="text-rose-500">*</span></span>
                            </template>
                        </label>
                    </div>

                    <div class="relative">
                        <input :type="showPassword ? 'text' : 'password'" 
                               id="password_input" 
                               name="password" 
                               x-model="passwordValue" 
                               @input="updateDateFeedback()"
                               required 
                               :placeholder="activeTab === 'mahasiswa' ? 'Format YYYY-MM-DD (contoh: 2004-05-14)' : 'Masukkan kata sandi akun'"
                               class="w-full pl-3.5 pr-10 py-2 bg-slate-50 border @error('password') border-rose-400 @else border-slate-300 @enderror rounded-xl text-xs text-slate-900 font-mono focus:bg-white focus:border-brand-ubsi focus:ring-4 focus:ring-blue-500/15 focus:outline-none transition-all">
                        
                        <!-- Toggle Show/Hide Password Button with Tooltip -->
                        <button type="button" 
                                @click="showPassword = !showPassword"
                                class="absolute right-3 top-2 text-slate-400 hover:text-blue-600 active:scale-90 text-xs p-0.5 focus:outline-none transition-transform"
                                :title="showPassword ? 'Sembunyikan karakter' : 'Tampilkan karakter'">
                            <i :class="showPassword ? 'fa-solid fa-eye-slash text-brand-ubsi' : 'fa-solid fa-eye'"></i>
                        </button>
                    </div>

                    <!-- Real-time Live Date Preview Helper -->
                    <template x-if="dateParsedText">
                        <div class="mt-1 px-2.5 py-0.5 rounded-lg bg-blue-50 border border-blue-200 text-[10px] text-blue-900 font-semibold flex items-center justify-between animate-fadeIn">
                            <span class="flex items-center gap-1.5">
                                <i class="fa-regular fa-calendar-check text-brand-ubsi"></i>
                                <span>Tanggal: <strong class="text-brand-ubsi font-bold" x-text="dateParsedText"></strong></span>
                            </span>
                            <span class="text-[9px] text-emerald-600 font-bold">✓ Terformat</span>
                        </div>
                    </template>

                    @error('password')
                        <p class="text-[10px] text-rose-600 font-semibold mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center justify-between text-[11px] pt-0.5">
                    <label class="flex items-center gap-1.5 cursor-pointer select-none">
                        <input type="checkbox" name="remember" class="rounded text-brand-ubsi focus:ring-blue-500 border-slate-300 text-xs">
                        <span class="text-slate-600 font-medium">Ingat saya</span>
                    </label>
                    <span class="text-slate-400 hover:text-slate-600 cursor-pointer">Kendala? Hubungi Admin</span>
                </div>

                <!-- Submit Button with Dynamic Loading State -->
                <button type="submit" 
                        :disabled="isSubmitting"
                        class="w-full py-2.5 rounded-xl bg-gradient-to-r from-brand-ubsi via-blue-700 to-indigo-800 hover:from-blue-900 hover:to-brand-ubsi text-white text-xs font-bold shadow-md shadow-blue-900/25 active:scale-[0.98] transition-all flex items-center justify-center gap-2 mt-1 disabled:opacity-75 cursor-pointer">
                    <template x-if="!isSubmitting">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-right-to-bracket text-amber-300"></i>
                            <span>MASUK KE SISTEM FINDIT</span>
                        </div>
                    </template>
                    <template x-if="isSubmitting">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-circle-notch fa-spin text-amber-300"></i>
                            <span>Memverifikasi Kredensial...</span>
                        </div>
                    </template>
                </button>
            </form>

            <!-- Bottom Discreet Notice -->
            <div class="pt-2 border-t border-slate-100 text-center">
                <p class="text-[10px] text-slate-400 leading-tight">
                    Mahasiswa masuk menggunakan <strong>NIM</strong> & <strong>Tanggal Lahir</strong> terdaftar. Tidak perlu registrasi mandiri.
                </p>
            </div>

        </div>

    </main>

    <!-- Minimalist Bottom Bar / Tagline -->
    <footer class="relative z-10 text-center py-1">
        <p class="text-[11px] text-white/80 font-medium tracking-wide">
            © {{ date('Y') }} Universitas Bina Sarana Informatika • Kuliah...? <span class="text-amber-300 font-bold">BSI Aja!</span>
        </p>
    </footer>

    <!-- PWA Install Prompt & Service Worker Init -->
    @include('partials.pwa-prompt')

</body>
</html>
