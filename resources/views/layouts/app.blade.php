<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FINDIT Dashboard - UBSI Lost & Found Information System">
    <title>@yield('title', 'Dashboard') - FINDIT UBSI</title>
    
    <!-- Favicon with UBSI Logo -->
    <link rel="icon" type="image/png" href="{{ asset('images/logo-ubsi.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('images/logo-ubsi.png') }}">

    <!-- PWA Head Metadata & Manifest -->
    @include('partials.pwa-head')

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.13.5/dist/cdn.min.js"></script>

    <!-- Chart.js (for Admin Analytics) -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
                            ubsi: '#1e3a8a',
                            navy: '#0f172a',
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
        }
        html {
            scroll-behavior: smooth;
        }
        @keyframes pageFadeIn {
            from {
                opacity: 0;
                transform: translateY(6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .page-transition {
            animation: pageFadeIn 0.35s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        @view-transition {
            navigation: auto;
        }
        @media print {
            aside, header, nav, .no-print, button:not(.allow-print) {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #000000 !important;
            }
            .min-h-screen {
                min-height: auto !important;
                display: block !important;
            }
            main {
                padding: 0 !important;
                margin: 0 !important;
                width: 100% !important;
                max-width: 100% !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false, profileDropdown: false, notifDropdown: false }">

    <div class="min-h-screen flex">
        <!-- Sidebar Backdrop for mobile -->
        <div x-show="sidebarOpen" 
             x-cloak 
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden transition-opacity">
        </div>

        <!-- Sidebar -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
               class="fixed inset-y-0 left-0 z-50 w-72 bg-brand-navy text-slate-300 flex flex-col transition-transform duration-300 ease-in-out lg:static lg:translate-x-0 border-r border-slate-800 shadow-2xl lg:shadow-none">
            
            <!-- Sidebar Header -->
            <div class="h-20 flex items-center justify-between px-6 border-b border-slate-800/80 bg-slate-950/40">
                <a href="{{ route('landing') }}" class="flex items-center gap-3">
                    <img src="{{ asset('images/logo-ubsi.png') }}" alt="Logo Resmi UBSI" class="w-10 h-10 object-contain rounded-xl shadow-md">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-xl font-black tracking-tight text-white">FIND<span class="text-amber-400">IT</span></span>
                            <span class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-900/80 text-blue-300 rounded border border-blue-700/50 uppercase">UBSI</span>
                        </div>
                        <p class="text-[10px] text-slate-400 font-medium">Lost & Found System</p>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <!-- Role Badge Banner -->
            <div class="px-5 py-3.5 bg-slate-900/60 border-b border-slate-800/60">
                @php
                    $roleLabel = match(Auth::user()->role) {
                        'admin' => ['title' => 'ADMINISTRATOR FINDIT', 'icon' => 'fa-crown', 'color' => 'text-amber-400', 'bg' => 'bg-amber-400/10 border-amber-400/20'],
                        'petugas' => ['title' => 'ADMIN LAYANAN KAMPUS', 'icon' => 'fa-user-shield', 'color' => 'text-emerald-400', 'bg' => 'bg-emerald-400/10 border-emerald-400/20'],
                        default => ['title' => 'MAHASISWA UBSI', 'icon' => 'fa-user-graduate', 'color' => 'text-blue-400', 'bg' => 'bg-blue-400/10 border-blue-400/20'],
                    };
                @endphp
                <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl {{ $roleLabel['bg'] }} border">
                    <i class="fa-solid {{ $roleLabel['icon'] }} {{ $roleLabel['color'] }}"></i>
                    <div class="min-w-0 flex-1">
                        <div class="text-[11px] font-bold tracking-wider uppercase {{ $roleLabel['color'] }}">
                            {{ $roleLabel['title'] }}
                        </div>
                        <div class="text-[11px] text-slate-400 truncate">
                            @if(Auth::user()->isPetugas() && Auth::user()->kampus)
                                {{ Auth::user()->kampus->nama_kampus }}
                            @elseif(Auth::user()->isMahasiswa() && Auth::user()->kampus)
                                {{ Auth::user()->kampus->nama_kampus }}
                            @else
                                Seluruh Kampus UBSI
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Role Switcher (Untuk Kemudahan Testing) -->
            <div class="px-5 py-2.5 bg-slate-950/30 border-b border-slate-800/40">
                <div class="text-[9px] font-extrabold uppercase tracking-wider text-slate-400 mb-1.5 flex items-center justify-between">
                    <span>Ganti Akun Cepat</span>
                    <span class="text-[8px] px-1.5 py-0.5 bg-blue-900/60 text-blue-300 rounded font-bold">1-Klik</span>
                </div>
                <div class="grid grid-cols-3 gap-1">
                    <a href="{{ route('demo.switch', 'mahasiswa') }}" 
                       class="flex items-center justify-center py-1.5 rounded-lg text-[11px] font-semibold transition {{ Auth::user()->isMahasiswa() ? 'bg-blue-600 text-white font-bold shadow-sm' : 'bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700' }}"
                       title="Beralih ke Akun Mahasiswa (Dimas)">
                        🎓 Mhs
                    </a>
                    <a href="{{ route('demo.switch', 'petugas') }}" 
                       class="flex items-center justify-center py-1.5 rounded-lg text-[11px] font-semibold transition {{ Auth::user()->isPetugas() ? 'bg-amber-600 text-white font-bold shadow-sm' : 'bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700' }}"
                       title="Beralih ke Akun Petugas Layanan Kampus">
                        🛡️ Staf
                    </a>
                    <a href="{{ route('demo.switch', 'admin') }}" 
                       class="flex items-center justify-center py-1.5 rounded-lg text-[11px] font-semibold transition {{ Auth::user()->isAdmin() ? 'bg-purple-600 text-white font-bold shadow-sm' : 'bg-slate-800/80 text-slate-400 hover:text-white hover:bg-slate-700' }}"
                       title="Beralih ke Akun Administrator Pusat">
                        ⚡ Admin
                    </a>
                </div>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 px-4 py-4 space-y-1.5 overflow-y-auto custom-scrollbar text-sm font-medium">

                {{-- =================================== --}}
                {{-- MAHASISWA MENU                     --}}
                {{-- =================================== --}}
                @if(Auth::user()->isMahasiswa())
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 pt-2 pb-1">Menu Utama</div>

                    <a href="{{ route('mahasiswa.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('mahasiswa.dashboard') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-gauge w-5 text-center"></i>
                        <span>Dashboard</span>
                    </a>

                    <a href="{{ route('search') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('search') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-magnifying-glass w-5 text-center text-blue-400"></i>
                        <span>Cari Barang (27 Kampus)</span>
                    </a>

                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 pt-4 pb-1">Layanan Terpadu</div>

                    <a href="{{ route('mahasiswa.lapor.hub') }}" 
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('mahasiswa.lapor.*') ? 'bg-gradient-to-r from-rose-600 to-amber-600 text-white font-semibold shadow-md shadow-rose-600/30' : 'hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-circle-plus w-5 text-center text-amber-400"></i>
                            <span>Pusat Pelaporan</span>
                        </div>
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-white/20 text-white rounded">1-Pintu</span>
                    </a>

                    <a href="{{ route('mahasiswa.aktivitas.hub') }}" 
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('mahasiswa.aktivitas.*') || request()->routeIs('mahasiswa.laporan.saya') || request()->routeIs('mahasiswa.klaim.saya') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-box-archive w-5 text-center text-blue-400"></i>
                            <span>Aktivitas & Tiket QR</span>
                        </div>
                        <span class="px-1.5 py-0.5 text-[9px] font-bold bg-blue-400/20 text-blue-300 rounded border border-blue-400/30">Terpadu</span>
                    </a>

                    <a href="{{ route('mahasiswa.notifikasi') }}" 
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('mahasiswa.notifikasi') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-bell w-5 text-center text-amber-400"></i>
                            <span>Notifikasi</span>
                        </div>
                        @if(Auth::user()->unreadNotificationsCount() > 0)
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-rose-500 text-white rounded-full">
                                {{ Auth::user()->unreadNotificationsCount() }}
                            </span>
                        @endif
                    </a>

                {{-- =================================== --}}
                {{-- ADMIN LAYANAN KAMPUS (PETUGAS)     --}}
                {{-- =================================== --}}
                @elseif(Auth::user()->isPetugas())
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 pt-2 pb-1">Operasional Kampus</div>

                    <a href="{{ route('petugas.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('petugas.dashboard') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-gauge w-5 text-center"></i>
                        <span>Dashboard Layanan</span>
                    </a>

                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 pt-4 pb-1">Ruang Kerja Terpadu</div>

                    <a href="{{ route('petugas.inventaris.hub') }}" 
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('petugas.inventaris.*') || request()->routeIs('petugas.menunggu.verifikasi') || request()->routeIs('petugas.barang.diamankan') || request()->routeIs('petugas.laporan.hilang') || request()->routeIs('petugas.smart.matching') ? 'bg-gradient-to-r from-blue-600 to-indigo-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-boxes-stacked w-5 text-center text-cyan-400"></i>
                            <div class="leading-tight">
                                <span class="block">Pusat Inventaris Barang</span>
                                <span class="text-[10px] text-slate-400 font-normal">Loker, Temuan & Smart Match</span>
                            </div>
                        </div>
                    </a>

                    <a href="{{ route('petugas.serah_terima.hub') }}" 
                       class="flex items-center justify-between px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('petugas.serah_terima.*') || request()->routeIs('petugas.klaim.*') || request()->routeIs('petugas.scan.qr') || request()->routeIs('petugas.barang.siap_diambil') || request()->routeIs('petugas.riwayat.pengembalian') ? 'bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold shadow-md shadow-emerald-600/30' : 'hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center gap-3">
                            <i class="fa-solid fa-qrcode w-5 text-center text-emerald-400"></i>
                            <div class="leading-tight">
                                <span class="block">Pusat Klaim & Serah Terima</span>
                                <span class="text-[10px] text-slate-400 font-normal">Klaim, Scan QR & BAST</span>
                            </div>
                        </div>
                        <span class="px-1.5 py-0.5 text-[9px] font-extrabold bg-emerald-400/20 text-emerald-300 rounded border border-emerald-400/30">SCAN</span>
                    </a>

                {{-- =================================== --}}
                {{-- ADMIN GLOBAL / PUSAT MENU          --}}
                {{-- =================================== --}}
                @elseif(Auth::user()->isAdmin())
                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 pt-2 pb-1">Master Administrator</div>

                    <a href="{{ route('admin.dashboard') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.dashboard') ? 'bg-blue-600 text-white font-semibold shadow-md shadow-blue-600/30' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-chart-pie w-5 text-center text-blue-400"></i>
                        <span>Dashboard Admin</span>
                    </a>

                    <a href="{{ route('admin.users') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.users') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-users w-5 text-center text-blue-400"></i>
                        <span>Data Mahasiswa (NIM)</span>
                    </a>

                    <a href="{{ route('admin.petugas') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.petugas') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-user-shield w-5 text-center text-emerald-400"></i>
                        <span>Data Admin Kampus</span>
                    </a>

                    <a href="{{ route('admin.kampus') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.kampus') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-school-flag w-5 text-center text-amber-400"></i>
                        <span>Data Kampus UBSI</span>
                    </a>

                    <a href="{{ route('admin.kategori') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.kategori') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-tags w-5 text-center text-purple-400"></i>
                        <span>Kategori Barang</span>
                    </a>

                    <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 pt-4 pb-1">Moderasi & Analitik Global</div>

                    <a href="{{ route('admin.laporan.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.laporan.index') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-folder-tree w-5 text-center text-indigo-400"></i>
                        <span>Monitoring Seluruh Laporan</span>
                    </a>

                    <a href="{{ route('admin.klaim.index') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.klaim.index') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-file-signature w-5 text-center text-amber-400"></i>
                        <span>Monitoring Semua Klaim</span>
                    </a>

                    <a href="{{ route('admin.statistik') }}" 
                       class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('admin.statistik') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                        <i class="fa-solid fa-chart-line w-5 text-center text-emerald-400"></i>
                        <span>Statistik & Laporan Resmi</span>
                    </a>
                @endif

                <div class="text-[10px] font-bold text-slate-500 uppercase tracking-wider px-3 pt-4 pb-1">Akun</div>

                <a href="{{ route('profile') }}" 
                   class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all {{ request()->routeIs('profile') ? 'bg-blue-600 text-white font-semibold' : 'hover:bg-slate-800 hover:text-white' }}">
                    <i class="fa-solid fa-user-gear w-5 text-center text-slate-400"></i>
                    <span>Profil Saya</span>
                </a>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" 
                            class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-rose-400 hover:bg-rose-950/40 hover:text-rose-300 transition-all text-left font-medium">
                        <i class="fa-solid fa-arrow-right-from-bracket w-5 text-center"></i>
                        <span>Keluar (Logout)</span>
                    </button>
                </form>
            </nav>

            <!-- User Footer in Sidebar -->
            <div class="p-4 border-t border-slate-800/80 bg-slate-950/40 flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center font-bold text-white border border-slate-700 text-sm overflow-hidden">
                    @if(Auth::user()->avatar)
                        <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                    @else
                        {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                    @endif
                </div>
                <div class="min-w-0 flex-1">
                    <div class="text-xs font-semibold text-white truncate">{{ Auth::user()->name }}</div>
                    <div class="text-[11px] text-slate-400 truncate">{{ Auth::user()->email }}</div>
                </div>
            </div>
        </aside>

        <!-- Main Wrapper -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Navbar -->
            <header class="h-20 bg-white border-b border-slate-200 sticky top-0 z-30 flex items-center justify-between px-4 sm:px-8">
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 text-slate-600 hover:text-slate-900 rounded-lg hover:bg-slate-100">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>

                    <div class="hidden sm:flex items-center gap-2">
                        <span class="text-xs text-slate-400 font-medium">UBSI Multi-Kampus</span>
                        <i class="fa-solid fa-chevron-right text-[10px] text-slate-300"></i>
                        <span class="text-sm font-semibold text-brand-ubsi">
                            @if(Auth::user()->isPetugas() && Auth::user()->kampus)
                                {{ Auth::user()->kampus->nama_kampus }}
                            @elseif(Auth::user()->isMahasiswa() && Auth::user()->kampus)
                                {{ Auth::user()->kampus->nama_kampus }}
                            @else
                                Seluruh Kampus UBSI (Global)
                            @endif
                        </span>
                    </div>
                </div>

                <div class="flex items-center gap-3 sm:gap-4">
                    <!-- Link Cari Barang Cepat -->
                    <a href="{{ route('search') }}" class="hidden md:inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 text-xs font-medium hover:bg-slate-200 transition-colors">
                        <i class="fa-solid fa-magnifying-glass text-blue-500"></i>
                        <span>Cari di Seluruh Kampus</span>
                    </a>

                    <!-- Notifikasi Dropdown -->
                    <div class="relative" x-data="{ notifOpen: false }">
                        <button @click="notifOpen = !notifOpen" 
                                class="relative p-2.5 text-slate-600 hover:text-slate-900 rounded-xl hover:bg-slate-100 transition-colors">
                            <i class="fa-regular fa-bell text-lg"></i>
                            @if(Auth::user()->unreadNotificationsCount() > 0)
                                <span class="absolute top-1.5 right-1.5 w-4 h-4 bg-rose-500 text-white rounded-full text-[10px] font-bold flex items-center justify-center animate-pulse">
                                    {{ Auth::user()->unreadNotificationsCount() }}
                                </span>
                            @endif
                        </button>

                        <!-- Notification Dropdown Panel -->
                        <div x-show="notifOpen" 
                             x-cloak 
                             @click.away="notifOpen = false"
                             class="absolute right-0 mt-2 w-80 sm:w-96 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden z-50">
                            
                            <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                                <span class="text-xs font-bold text-slate-700 uppercase tracking-wider">Notifikasi Saya</span>
                                @if(Auth::user()->isMahasiswa())
                                    <form method="POST" action="{{ route('mahasiswa.notifikasi.read_all') }}">
                                        @csrf
                                        <button type="submit" class="text-[11px] font-semibold text-blue-600 hover:text-blue-800">Tandai Semua Dibaca</button>
                                    </form>
                                @endif
                            </div>

                            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 custom-scrollbar">
                                @forelse(Auth::user()->notifikasi()->take(5)->get() as $notif)
                                    <div class="p-3.5 hover:bg-slate-50 transition-colors {{ !$notif->is_read ? 'bg-blue-50/50' : '' }}">
                                        <div class="flex items-start gap-3">
                                            <div class="w-8 h-8 rounded-full bg-blue-100 text-brand-ubsi flex items-center justify-center flex-shrink-0 text-xs">
                                                <i class="fa-solid fa-bell"></i>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $notif->judul }}</p>
                                                <p class="text-[11px] text-slate-500 line-clamp-2 mt-0.5">{{ $notif->pesan }}</p>
                                                <span class="text-[10px] text-slate-400 mt-1 block">{{ $notif->created_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="p-8 text-center text-xs text-slate-400">
                                        <i class="fa-regular fa-bell-slash text-2xl mb-2 text-slate-300 block"></i>
                                        Tidak ada notifikasi baru.
                                    </div>
                                @endforelse
                            </div>

                            @if(Auth::user()->isMahasiswa())
                                <a href="{{ route('mahasiswa.notifikasi') }}" class="block p-3 text-center text-xs font-semibold text-blue-600 hover:bg-slate-50 border-t border-slate-100">
                                    Lihat Seluruh Notifikasi
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- User Profile Dropdown -->
                    <div class="relative" x-data="{ userOpen: false }">
                        <button @click="userOpen = !userOpen" class="flex items-center gap-3 p-1.5 pr-3 rounded-xl hover:bg-slate-100 transition-colors">
                            <div class="w-9 h-9 rounded-full bg-brand-ubsi text-white flex items-center justify-center font-bold text-xs shadow-sm overflow-hidden">
                                @if(Auth::user()->avatar)
                                    <img src="{{ asset('storage/' . Auth::user()->avatar) }}" alt="{{ Auth::user()->name }}" class="w-full h-full object-cover">
                                @else
                                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                                @endif
                            </div>
                            <div class="hidden sm:block text-left">
                                <div class="text-xs font-bold text-slate-800 leading-none truncate max-w-[130px]">{{ Auth::user()->name }}</div>
                                <div class="text-[10px] text-slate-500 capitalize mt-0.5">{{ Auth::user()->role }}</div>
                            </div>
                            <i class="fa-solid fa-chevron-down text-[10px] text-slate-400"></i>
                        </button>

                        <div x-show="userOpen" 
                             x-cloak 
                             @click.away="userOpen = false"
                             class="absolute right-0 mt-2 w-56 bg-white rounded-2xl shadow-xl border border-slate-100 py-2 z-50">
                            
                            <div class="px-4 py-2 border-b border-slate-100">
                                <p class="text-xs font-bold text-slate-800">{{ Auth::user()->name }}</p>
                                <p class="text-[11px] text-slate-500 truncate">{{ Auth::user()->email }}</p>
                            </div>

                            <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-4 py-2.5 text-xs text-slate-700 hover:bg-slate-50 font-medium">
                                <i class="fa-solid fa-user-pen text-slate-400 w-4"></i> Ubah Profil & Password
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2.5 px-4 py-2.5 text-xs text-rose-600 hover:bg-rose-50 font-medium text-left">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-4"></i> Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Alert Messages inside App -->
            <div class="px-4 sm:px-8 pt-4">
                @if(session('success'))
                    <div class="flex items-center gap-3 p-4 mb-4 text-emerald-800 bg-emerald-50 border border-emerald-200 rounded-2xl shadow-sm">
                        <i class="fa-solid fa-circle-check text-emerald-500 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('success') }}</span>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-center gap-3 p-4 mb-4 text-rose-800 bg-rose-50 border border-rose-200 rounded-2xl shadow-sm">
                        <i class="fa-solid fa-triangle-exclamation text-rose-500 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('error') }}</span>
                    </div>
                @endif

                @if(session('warning'))
                    <div class="flex items-center gap-3 p-4 mb-4 text-amber-800 bg-amber-50 border border-amber-200 rounded-2xl shadow-sm">
                        <i class="fa-solid fa-circle-info text-amber-500 text-lg"></i>
                        <span class="text-sm font-medium">{{ session('warning') }}</span>
                    </div>
                @endif
            </div>

            <!-- Page Body Content -->
            <main class="flex-1 p-4 sm:p-8 pb-28 lg:pb-8 overflow-y-auto page-transition">
                @yield('content')
            </main>
        </div>
    </div>

    <!-- Mobile Bottom Navigation Bar (High-End Responsive UX) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] px-3 py-2">
        <div class="flex items-center justify-around max-w-lg mx-auto">
            @if(Auth::user()->isMahasiswa())
                <!-- Dashboard -->
                <a href="{{ route('mahasiswa.dashboard') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('mahasiswa.dashboard') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-gauge text-base"></i>
                    <span>Home</span>
                </a>

                <!-- Cari -->
                <a href="{{ route('search') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('search') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-magnifying-glass text-base"></i>
                    <span>Cari</span>
                </a>

                <!-- Elevated Lapor Button -->
                <div class="relative -top-5" x-data="{ openLaporMenu: false }">
                    <button @click="openLaporMenu = !openLaporMenu" class="w-12 h-12 rounded-full bg-gradient-to-tr from-brand-ubsi to-blue-600 text-white flex items-center justify-center shadow-lg shadow-blue-600/40 border-2 border-white focus:outline-none active:scale-95 transition-transform">
                        <i class="fa-solid fa-plus text-xl"></i>
                    </button>

                    <!-- Mobile Lapor Quick Popup -->
                    <div x-show="openLaporMenu" 
                         x-cloak 
                         @click.away="openLaporMenu = false"
                         class="absolute bottom-16 left-1/2 -translate-x-1/2 w-48 bg-white rounded-2xl shadow-2xl border border-slate-100 p-2 space-y-1.5 z-50">
                        <a href="{{ route('mahasiswa.lapor.temukan') }}" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-emerald-50 text-emerald-700 text-xs font-bold transition-colors">
                            <i class="fa-solid fa-bullhorn text-emerald-500 w-4"></i>
                            <span>Lapor Temukan (Min 2 Foto)</span>
                        </a>
                        <a href="{{ route('mahasiswa.lapor.hilang') }}" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-rose-50 text-rose-700 text-xs font-bold transition-colors">
                            <i class="fa-solid fa-circle-exclamation text-rose-500 w-4"></i>
                            <span>Lapor Hilang</span>
                        </a>
                    </div>
                </div>

                <!-- Laporan Saya -->
                <a href="{{ route('mahasiswa.laporan.saya') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('mahasiswa.laporan.*') && !request()->routeIs('mahasiswa.lapor.*') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-clipboard-list text-base"></i>
                    <span>Laporan</span>
                </a>

                <!-- Profil -->
                <a href="{{ route('profile') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('profile') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-user text-base"></i>
                    <span>Profil</span>
                </a>

            @else
                <!-- Admin & Layanan Kampus -->
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : route('petugas.dashboard') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('*.dashboard') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-chart-pie text-base"></i>
                    <span>Dashboard</span>
                </a>

                <!-- Verifikasi Temuan -->
                <a href="{{ route('petugas.menunggu.verifikasi') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('petugas.menunggu.verifikasi') ? 'text-amber-600' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-boxes-stacked text-base"></i>
                    <span>Verifikasi</span>
                </a>

                <!-- Barang Diamankan -->
                <a href="{{ route('petugas.barang.diamankan') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('petugas.barang.diamankan') ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-vault text-base"></i>
                    <span>Diamankan</span>
                </a>

                <!-- Klaim -->
                <a href="{{ route('petugas.klaim.index') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('petugas.klaim.*') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-user-check text-base"></i>
                    <span>Klaim</span>
                </a>

                <!-- Profil -->
                <a href="{{ route('profile') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('profile') ? 'text-blue-600' : 'text-slate-500 hover:text-slate-800' }}">
                    <i class="fa-solid fa-user-gear text-base"></i>
                    <span>Akun</span>
                </a>
            @endif
        </div>
    </nav>

    <!-- PWA Install Prompt & Service Worker Init -->
    @include('partials.pwa-prompt')

    @yield('scripts')
</body>
</html>
