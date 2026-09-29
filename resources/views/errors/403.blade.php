<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak | FINDIT UBSI</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen flex items-center justify-center p-4 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute inset-0 opacity-10 bg-[radial-gradient(#3b82f6_1px,transparent_1px)] [background-size:16px_16px] pointer-events-none"></div>
    <div class="absolute -top-40 -left-40 w-96 h-96 bg-blue-600/20 rounded-full blur-3xl pointer-events-none"></div>
    <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-amber-500/15 rounded-full blur-3xl pointer-events-none"></div>

    <div class="max-w-xl w-full bg-slate-800/90 border border-slate-700/80 rounded-2xl shadow-2xl p-6 sm:p-8 relative backdrop-blur-xl z-10 text-center">
        <!-- Logo UBSI -->
        <div class="flex items-center justify-center gap-3 mb-6">
            <img src="{{ asset('images/logo.png') }}" alt="Logo UBSI" class="w-10 h-10 object-contain drop-shadow">
            <span class="text-xl font-extrabold tracking-tight text-white">FINDIT <span class="text-blue-400 font-semibold text-sm">UBSI</span></span>
        </div>

        <!-- Warning Icon & Status -->
        <div class="w-20 h-20 mx-auto mb-5 rounded-2xl bg-amber-500/10 border border-amber-500/30 flex items-center justify-center text-amber-400">
            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
        </div>

        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-amber-500/20 text-amber-300 border border-amber-500/40 mb-3">
            Error 403 &bull; Akses Dibatasi
        </div>

        <h1 class="text-2xl sm:text-3xl font-bold text-white mb-3">Peran Akun Tidak Sesuai</h1>

        <p class="text-slate-300 text-sm sm:text-base leading-relaxed mb-6">
            {{ $exception->getMessage() ?: 'Halaman ini dilindungi sistem keamanan berbasis peran (Role-Based Access Control) dan hanya dapat diakses oleh pengguna dengan hak akses yang sesuai.' }}
        </p>

        @auth
        <!-- Akun Yang Sedang Aktif -->
        <div class="bg-slate-900/80 border border-slate-700 rounded-xl p-4 mb-6 text-left flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-blue-600/30 border border-blue-500/40 flex items-center justify-center text-blue-300 font-bold">
                    {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                </div>
                <div>
                    <p class="text-xs text-slate-400">Akun Anda Saat Ini:</p>
                    <p class="text-sm font-bold text-white">{{ Auth::user()->name }}</p>
                </div>
            </div>
            <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase {{ Auth::user()->role === 'admin' ? 'bg-purple-900/60 text-purple-300 border border-purple-700' : (Auth::user()->role === 'petugas' ? 'bg-amber-900/60 text-amber-300 border border-amber-700' : 'bg-blue-900/60 text-blue-300 border border-blue-700') }}">
                {{ Auth::user()->role }}
            </span>
        </div>
        @endauth

        <!-- Switch Demo Account Quick Actions -->
        <div class="bg-slate-900/50 border border-slate-700/50 rounded-xl p-4 mb-6 text-left">
            <p class="text-xs font-semibold text-slate-400 mb-2 uppercase tracking-wider text-center">Ganti Akun Instan (Uji Coba Cepat):</p>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                <a href="{{ route('demo.switch', 'mahasiswa') }}" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-blue-600/20 hover:bg-blue-600/30 text-blue-300 border border-blue-500/40 rounded-lg text-xs font-semibold transition">
                    🎓 Mahasiswa
                </a>
                <a href="{{ route('demo.switch', 'petugas') }}" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-amber-600/20 hover:bg-amber-600/30 text-amber-300 border border-amber-500/40 rounded-lg text-xs font-semibold transition">
                    🛡️ Petugas
                </a>
                <a href="{{ route('demo.switch', 'admin') }}" class="flex items-center justify-center gap-1.5 px-3 py-2 bg-purple-600/20 hover:bg-purple-600/30 text-purple-300 border border-purple-500/40 rounded-lg text-xs font-semibold transition">
                    ⚡ Admin
                </a>
            </div>
        </div>

        <!-- Tombol Navigasi -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            @auth
                @if(Auth::user()->role === 'admin')
                    <a href="{{ route('admin.dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg transition">
                        Dashboard Admin
                    </a>
                @elseif(Auth::user()->role === 'petugas')
                    <a href="{{ route('petugas.dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold rounded-xl shadow-lg transition">
                        Dashboard Layanan Kampus
                    </a>
                @else
                    <a href="{{ route('mahasiswa.dashboard') }}" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg transition">
                        Dashboard Mahasiswa
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white text-sm font-semibold rounded-xl shadow-lg transition">
                    Masuk Akun
                </a>
            @endauth

            <a href="{{ url('/') }}" class="w-full sm:w-auto px-5 py-2.5 bg-slate-700 hover:bg-slate-600 text-slate-200 text-sm font-semibold rounded-xl transition">
                Kembali ke Beranda
            </a>
        </div>
    </div>
</body>
</html>
