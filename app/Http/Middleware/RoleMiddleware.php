<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect()->route('login')->with('error', 'Akun Anda dinonaktifkan oleh administrator. Silakan hubungi admin.');
        }

        // Parse daftar role yang diizinkan (mendukung format array maupun string koma 'admin,petugas')
        $allowedRoles = [];
        foreach ($roles as $roleItem) {
            foreach (explode(',', $roleItem) as $subRole) {
                $allowedRoles[] = trim($subRole);
            }
        }

        // Super Admin selalu memiliki izin supervisi penuh untuk membuka semua modul
        if ($user->role === 'admin') {
            return $next($request);
        }

        if (!in_array($user->role, $allowedRoles)) {
            $roleTarget = strtoupper(implode(' atau ', $allowedRoles));
            $roleAktif = strtoupper($user->role);
            abort(403, "Akses ditolak. Anda saat ini login sebagai {$roleAktif} ({$user->name}), sedangkan halaman ini khusus untuk peran {$roleTarget}. Silakan ganti/masuk dengan akun {$roleTarget} untuk mengakses halaman ini.");
        }

        return $next($request);
    }
}
