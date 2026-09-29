<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="FINDIT - Sistem Informasi Lost and Found Multi-Kampus Universitas Bina Sarana Informatika (UBSI)">
    <title>@yield('title', 'FINDIT - UBSI Lost & Found')</title>
    
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
                            navy: '#0f172a',
                            darknavy: '#0b1c3d',
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
        .glass-nav {
            background: rgba(255, 255, 255, 0.94);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
        }
        .hero-gradient {
            background: radial-gradient(circle at 10% 20%, rgba(30, 58, 138, 0.08) 0%, rgba(248, 250, 252, 1) 90%);
        }
        @view-transition {
            navigation: auto;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased flex flex-col min-h-screen">

    <!-- Top Announcement Bar -->
    <div class="bg-gradient-to-r from-brand-ubsi via-blue-800 to-indigo-900 text-white text-xs py-2 px-4 text-center font-medium shadow-sm">
        <div class="max-w-7xl mx-auto flex items-center justify-between">
            <span class="hidden sm:inline-flex items-center gap-1.5 text-blue-200">
                <i class="fa-solid fa-graduation-cap text-amber-400"></i> Universitas Bina Sarana Informatika
            </span>
            <span class="mx-auto sm:mx-0 flex items-center gap-2">
                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                Sistem Terpadu Lost & Found 27 Kampus UBSI Seluruh Indonesia
            </span>
            <span class="hidden md:inline-flex items-center gap-2 text-blue-200">
                <i class="fa-solid fa-user-shield text-emerald-400"></i> Terintegrasi Layanan Kampus UBSI
            </span>
        </div>
    </div>

    <!-- Main Navbar -->
    <header class="sticky top-0 z-50 glass-nav border-b border-slate-200/80 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <!-- Logo -->
                <a href="{{ route('landing') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/logo-ubsi.png') }}" alt="Logo Resmi UBSI" class="w-12 h-12 object-contain rounded-2xl shadow-md group-hover:scale-105 transition-transform duration-300">
                    <div>
                        <div class="flex items-center gap-1.5">
                            <span class="text-2xl font-black tracking-tight text-brand-ubsi">FIND<span class="text-amber-500">IT</span></span>
                            <span class="px-2 py-0.5 text-[10px] font-bold bg-blue-100 text-brand-ubsi rounded-full uppercase tracking-wider">UBSI</span>
                        </div>
                        <p class="text-[11px] text-slate-500 font-medium tracking-tight -mt-1">Lost & Found Information System</p>
                    </div>
                </a>

                <!-- Desktop Navigation Links -->
                <nav class="hidden md:flex items-center gap-8 text-sm font-medium text-slate-600">
                    <a href="{{ route('landing') }}" class="hover:text-brand-ubsi transition-colors {{ request()->routeIs('landing') ? 'text-brand-ubsi font-semibold' : '' }}">
                        Beranda
                    </a>
                    <a href="{{ route('search') }}" class="hover:text-brand-ubsi transition-colors {{ request()->routeIs('search') ? 'text-brand-ubsi font-semibold' : '' }}">
                        <i class="fa-solid fa-compass text-blue-500 mr-1"></i> Cari Barang
                    </a>
                    <a href="{{ route('search', ['jenis_laporan' => 'HILANG']) }}" class="hover:text-brand-ubsi transition-colors">
                        <span class="w-2 h-2 rounded-full bg-rose-500 inline-block mr-1"></span> Barang Hilang
                    </a>
                    <a href="{{ route('search', ['jenis_laporan' => 'DITEMUKAN']) }}" class="hover:text-brand-ubsi transition-colors">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 inline-block mr-1"></span> Barang Ditemukan
                    </a>
                </nav>

                <!-- Auth / Action Buttons -->
                <div class="flex items-center gap-2.5">
                    <!-- PWA Quick Install Button -->
                    <button type="button" 
                            onclick="window.dispatchEvent(new CustomEvent('pwa:open-install'))"
                            class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-750 text-xs font-semibold border border-slate-200 transition-all shadow-sm active:scale-95"
                            title="Instal aplikasi FINDIT di Layar Utama HP / Desktop">
                        <i class="fa-solid fa-mobile-screen-button text-brand-ubsi text-xs"></i>
                        <span class="hidden sm:inline">Instal App</span>
                    </button>

                    @auth
                        @php
                            $dashboardRoute = match(Auth::user()->role) {
                                'admin' => route('admin.dashboard'),
                                'petugas' => route('petugas.dashboard'),
                                default => route('mahasiswa.dashboard'),
                            };
                        @endphp
                        <a href="{{ $dashboardRoute }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-ubsi text-white text-sm font-semibold shadow-md shadow-blue-900/10 hover:bg-blue-900 transition-all">
                            <i class="fa-solid fa-gauge"></i>
                            <span>Dashboard</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-ubsi text-white text-xs sm:text-sm font-bold shadow-md shadow-blue-900/20 hover:bg-blue-900 transition-all">
                            <i class="fa-solid fa-right-to-bracket text-amber-300"></i>
                            <span>Masuk Sistem</span>
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    <!-- Flash Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full mt-4">
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

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 text-sm mt-20 border-t border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-10">
                <!-- Brand col -->
                <div class="md:col-span-1 space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white font-bold text-lg">
                            <i class="fa-solid fa-magnifying-glass-location text-amber-300"></i>
                        </div>
                        <span class="text-2xl font-black tracking-tight text-white">FIND<span class="text-amber-400">IT</span></span>
                    </div>
                    <p class="text-xs text-slate-400 leading-relaxed">
                        Sistem Informasi Pengelolaan Barang Hilang dan Ditemukan Terpadu untuk Civitas Akademika Universitas Bina Sarana Informatika di seluruh kampus Indonesia.
                    </p>
                    <div class="flex items-center gap-3 text-slate-400 pt-2">
                        <span class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-xs hover:text-white transition-colors"><i class="fa-brands fa-instagram"></i></span>
                        <span class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-xs hover:text-white transition-colors"><i class="fa-brands fa-youtube"></i></span>
                        <span class="w-8 h-8 rounded-lg bg-slate-800 flex items-center justify-center text-xs hover:text-white transition-colors"><i class="fa-solid fa-globe"></i></span>
                    </div>
                </div>

                <!-- Nav Col -->
                <div>
                    <h4 class="text-white font-semibold text-xs tracking-wider uppercase mb-4">Navigasi Cepat</h4>
                    <ul class="space-y-2 text-xs">
                        <li><a href="{{ route('landing') }}" class="hover:text-white transition-colors">Beranda</a></li>
                        <li><a href="{{ route('search') }}" class="hover:text-white transition-colors">Pencarian Terpadu</a></li>
                        <li><a href="{{ route('search', ['jenis_laporan' => 'HILANG']) }}" class="hover:text-white transition-colors">Daftar Barang Hilang</a></li>
                        <li><a href="{{ route('search', ['jenis_laporan' => 'DITEMUKAN']) }}" class="hover:text-white transition-colors">Daftar Barang Ditemukan</a></li>
                        <li><a href="{{ route('login') }}" class="hover:text-white transition-colors">Portal Masuk</a></li>
                    </ul>
                </div>

                <!-- Multi Campus Highlights -->
                <div>
                    <h4 class="text-white font-semibold text-xs tracking-wider uppercase mb-4">Cakupan Multi-Kampus</h4>
                    <ul class="space-y-2 text-xs">
                        <li><span class="text-amber-400 font-medium">18 Kampus Utama</span> (Jabodetabek)</li>
                        <li><span class="text-blue-400 font-medium">9 Kampus PSDKU</span> (Sukabumi, Solo, Yogya, dll.)</li>
                        <li>Terhubung langsung dengan Admin & Bagian Layanan Kampus UBSI</li>
                    </ul>
                </div>

                <!-- Contact & Security -->
                <div>
                    <h4 class="text-white font-semibold text-xs tracking-wider uppercase mb-4">Pengamanan & Pengambilan</h4>
                    <p class="text-xs text-slate-400 leading-relaxed mb-3">
                        Setiap barang yang ditemukan wajib diverifikasi dan diamankan oleh Admin Layanan Kampus setempat sebelum diserahkan melalui proses klaim resmi.
                    </p>
                    <div class="p-3 rounded-xl bg-slate-800/80 border border-slate-700/60 text-xs">
                        <span class="text-slate-300 font-medium block mb-1"><i class="fa-solid fa-user-shield text-emerald-400 mr-1.5"></i> Protokol Validasi</span>
                        <span class="text-slate-400 text-[11px]">Bawa KTM / KTP asli saat proses pengambilan fisik di Kantor Layanan Kampus.</span>
                    </div>
                </div>
            </div>

            <div class="mt-12 pt-8 border-t border-slate-800/80 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 gap-4 mb-16 sm:mb-0">
                <p>&copy; {{ date('Y') }} FINDIT UBSI — Universitas Bina Sarana Informatika. Hak Cipta Dilindungi.</p>
                <div class="flex items-center gap-6">
                    <span>Privasi</span>
                    <span>Ketentuan Layanan</span>
                    <span>Bantuan</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- Public Mobile Bottom Navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-40 bg-white/95 backdrop-blur-md border-t border-slate-200/80 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] px-3 py-2">
        <div class="flex items-center justify-around max-w-lg mx-auto">
            <a href="{{ route('landing') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('landing') ? 'text-brand-ubsi' : 'text-slate-500 hover:text-slate-800' }}">
                <i class="fa-solid fa-house text-base"></i>
                <span>Beranda</span>
            </a>
            <a href="{{ route('search') }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request()->routeIs('search') && !request('jenis_laporan') ? 'text-brand-ubsi' : 'text-slate-500 hover:text-slate-800' }}">
                <i class="fa-solid fa-magnifying-glass text-base"></i>
                <span>Cari</span>
            </a>
            <a href="{{ route('search', ['jenis_laporan' => 'HILANG']) }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request('jenis_laporan') === 'HILANG' ? 'text-rose-600' : 'text-slate-500 hover:text-slate-800' }}">
                <i class="fa-solid fa-circle-exclamation text-base text-rose-500"></i>
                <span>Hilang</span>
            </a>
            <a href="{{ route('search', ['jenis_laporan' => 'DITEMUKAN']) }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold {{ request('jenis_laporan') === 'DITEMUKAN' ? 'text-emerald-600' : 'text-slate-500 hover:text-slate-800' }}">
                <i class="fa-solid fa-hand-holding-hand text-base text-emerald-500"></i>
                <span>Temuan</span>
            </a>
            @auth
                <a href="{{ Auth::user()->isAdmin() ? route('admin.dashboard') : (Auth::user()->isPetugas() ? route('petugas.dashboard') : route('mahasiswa.dashboard')) }}" class="flex flex-col items-center gap-1 text-[10px] font-semibold text-brand-ubsi">
                    <i class="fa-solid fa-gauge text-base"></i>
                    <span>Dashboard</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 text-[10px] font-bold text-blue-700">
                    <i class="fa-solid fa-right-to-bracket text-base"></i>
                    <span>Masuk</span>
                </a>
            @endauth
        </div>
    </nav>

    <!-- PWA Install Prompt & Service Worker Init -->
    @include('partials.pwa-prompt')

    @yield('scripts')
</body>
</html>
