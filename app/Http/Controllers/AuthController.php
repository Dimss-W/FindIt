<?php

namespace App\Http\Controllers;

use App\Models\Kampus;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return $this->redirectBasedOnRole(Auth::user());
        }

        $campuses = Kampus::where('status', 'aktif')->orderBy('nama_kampus')->get();

        return view('auth.login', compact('campuses'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $loginInput = trim($request->input('login'));
        $passwordInput = $request->input('password');

        // Cari user berdasarkan email ATAU nim
        $user = User::where('email', $loginInput)
            ->orWhere('nim', $loginInput)
            ->first();

        if ($user) {
            $isPasswordValid = Hash::check($passwordInput, $user->password);

            // Cek juga jika password adalah tanggal lahir (format: DDMMYYYY, YYYY-MM-DD, dsb.)
            if (!$isPasswordValid && $user->tanggal_lahir) {
                $tglFormats = [
                    $user->tanggal_lahir->format('dmY'),       // contoh: 14052004
                    $user->tanggal_lahir->format('Y-m-d'),     // contoh: 2004-05-14
                    $user->tanggal_lahir->format('d-m-Y'),     // contoh: 14-05-2004
                    $user->tanggal_lahir->format('d/m/Y'),     // contoh: 14/05/2004
                ];

                if (in_array(trim($passwordInput), $tglFormats)) {
                    $isPasswordValid = true;
                }
            }

            if ($isPasswordValid) {
                if (!$user->is_active) {
                    return back()->with('error', 'Akun Anda dinonaktifkan oleh administrator.');
                }

                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();

                return $this->redirectBasedOnRole($user);
            }
        }

        return back()->withErrors([
            'login' => 'NIM / Email atau kata sandi / tanggal lahir yang Anda masukkan salah.',
        ])->onlyInput('login');
    }

    public function showRegister()
    {
        return redirect()->route('login')->with('warning', 'Pendaftaran akun mahasiswa dilakukan oleh Bagian Administrasi Kampus UBSI menggunakan NIM dan Tanggal Lahir.');
    }

    public function register(Request $request)
    {
        return redirect()->route('login')->with('warning', 'Pendaftaran mandiri dinonaktifkan.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda berhasil keluar.');
    }

    public function profile()
    {
        $user = Auth::user();
        $campuses = Kampus::where('status', 'aktif')->orderBy('nama_kampus')->get();
        return view('profile.index', compact('user', 'campuses'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'no_telp' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
        ];

        if ($user->isMahasiswa()) {
            $rules['nim'] = ['required', 'string', 'max:30', 'unique:users,nim,' . $user->id];
            $rules['kampus_id'] = ['required', 'exists:kampus,id'];
            $rules['tanggal_lahir'] = ['nullable', 'date'];
        }

        $validated = $request->validate($rules);

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);

        return back()->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(6)],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'Password berhasil diubah.');
    }

    private function redirectBasedOnRole(User $user)
    {
        return match ($user->role) {
            'admin' => redirect()->intended(route('admin.dashboard')),
            'petugas' => redirect()->intended(route('petugas.dashboard')),
            default => redirect()->intended(route('mahasiswa.dashboard')),
        };
    }
}
