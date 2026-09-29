<?php

namespace App\Services;

use App\Models\Klaim;
use App\Models\LaporanBarang;
use App\Models\Pengembalian;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    /**
     * Standarisasi nomor telepon ke format internasional WhatsApp (628xxx)
     */
    public static function formatPhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        // Hapus karakter non-digit
        $clean = preg_replace('/[^0-9]/', '', $phone);

        // Jika berawalan '0', ganti dengan '62'
        if (str_starts_with($clean, '0')) {
            $clean = '62' . substr($clean, 1);
        }

        // Jika berawalan '8', tambahkan '62'
        if (str_starts_with($clean, '8')) {
            $clean = '62' . $clean;
        }

        return strlen($clean) >= 10 ? $clean : null;
    }

    /**
     * Buat Link Direct Chat wa.me (1-Click Chat Langsung dari Browser/HP)
     */
    public static function generateChatLink(?string $phone, string $message): ?string
    {
        $formatted = self::formatPhoneNumber($phone);
        if (!$formatted) {
            return null;
        }

        return 'https://wa.me/' . $formatted . '?text=' . urlencode($message);
    }

    /**
     * Kirim Pesan WhatsApp (Mendukung Gateway API Fonnte / Waba / Logging Fallback)
     */
    public static function sendMessage(?string $phone, string $message): array
    {
        $target = self::formatPhoneNumber($phone);
        if (!$target) {
            return [
                'success' => false,
                'message' => 'Nomor WhatsApp tidak valid atau kosong',
            ];
        }

        $apiToken = config('services.whatsapp.token') ?? env('WA_API_TOKEN');
        $apiUrl = config('services.whatsapp.url') ?? env('WA_API_URL', 'https://api.fonnte.com/send');

        // Jika API Token tersedia di .env, kirim via HTTP Gateway
        if (!empty($apiToken)) {
            try {
                $response = Http::withHeaders([
                    'Authorization' => $apiToken,
                ])->timeout(8)->post($apiUrl, [
                    'target' => $target,
                    'message' => $message,
                ]);

                if ($response->successful()) {
                    return [
                        'success' => true,
                        'gateway' => true,
                        'data' => $response->json(),
                    ];
                }
            } catch (\Exception $e) {
                Log::warning('WhatsApp Gateway error: ' . $e->getMessage());
            }
        }

        // Fallback: Catat ke Log Sistem & Berikan status sukses simulasi
        Log::info("[WA NOTIFIKASI FINDIT] Target: {$target} | Pesan: {$message}");

        return [
            'success' => true,
            'simulated' => true,
            'target' => $target,
            'chat_link' => self::generateChatLink($target, $message),
        ];
    }

    /**
     * 1. Template Pesan: Klaim Barang Disetujui
     */
    public static function notifyClaimApproved(Klaim $klaim): array
    {
        $user = $klaim->user;
        $laporan = $klaim->laporan;
        $kampus = $laporan?->kampus?->nama_kampus ?? 'Kampus UBSI Terdaftar';
        $noTiket = $klaim->kode_klaim ?? ('CLM-' . str_pad($klaim->id, 5, '0', STR_PAD_LEFT));

        $msg = "📢 *PEMBERITAHUAN KLAIM BARANG FINDIT UBSI*\n\n"
             . "Halo Sdr/i *{$user->name}* (NIM: {$user->nim}),\n\n"
             . "Klaim Anda untuk barang temuan berikut telah *DISETUJUI* oleh Admin Layanan Kampus:\n"
             . "📦 *Barang:* {$laporan->nama_barang}\n"
             . "🏢 *Lokasi Pengambilan:* Ruang Layanan {$kampus}\n"
             . "🎫 *Kode Tiket Klaim:* *{$noTiket}*\n\n"
             . "Silakan datang ke Bagian Layanan Kampus dengan membawa:\n"
             . "1. KTM / KTP Asli untuk verifikasi identitas.\n"
             . "2. Menunjukkan pesan atau Kode Tiket ini kepada petugas.\n\n"
             . "_Kuliah...? BSI Aja! • Sistem FINDIT UBSI_";

        return self::sendMessage($user->no_telp, $msg);
    }

    /**
     * 2. Template Pesan: Deteksi Smart Match Barang Temuan
     */
    public static function notifyMatchFound(LaporanBarang $laporanHilang, LaporanBarang $laporanTemuan, int $score = 90): array
    {
        $user = $laporanHilang->user;
        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }
        $kampus = $laporanTemuan->kampus?->nama_kampus ?? 'Kampus UBSI';

        $msg = "🔍 *NOTIFIKASI KECOCOKAN BARANG HILANG - FINDIT UBSI*\n\n"
             . "Halo Sdr/i *{$user->name}*,\n\n"
             . "Sistem FINDIT UBSI mendeteksi ada barang temuan baru dengan tingkat kecocokan *{$score}%* mirip dengan laporan kehilangan Anda:\n"
             . "📦 *Laporan Anda:* {$laporanHilang->nama_barang}\n"
             . "✨ *Barang Ditemukan:* {$laporanTemuan->nama_barang}\n"
             . "🏢 *Kampus:* {$kampus}\n"
             . "📍 *Lokasi:* {$laporanTemuan->lokasi_kejadian}\n\n"
             . "Silakan buka aplikasi FINDIT UBSI untuk melihat foto dan mengajukan klaim verifikasi kepemilikan.\n\n"
             . "_Sistem Informasi Lost & Found UBSI_";

        return self::sendMessage($user->no_telp, $msg);
    }

    /**
     * 3. Template Pesan: Bukti Serah Terima Fisik Barang (BAST) Selesai
     */
    public static function notifyHandoverSuccess(Pengembalian $pengembalian): array
    {
        $klaim = $pengembalian->klaim;
        $user = $klaim?->user;
        $laporan = $klaim?->laporan;
        $noBast = $pengembalian->nomor_pengembalian ?? ('BAST-' . date('Ymd') . '-' . str_pad($pengembalian->id, 4, '0', STR_PAD_LEFT));

        if (!$user) {
            return ['success' => false, 'message' => 'User tidak ditemukan'];
        }

        $msg = "✅ *BERITA ACARA SERAH TERIMA BARANG (BAST) - FINDIT UBSI*\n\n"
             . "Halo Sdr/i *{$user->name}*,\n\n"
             . "Barang temuan berupa *{$laporan?->nama_barang}* telah resmi diserahterimakan kepada Anda pada " . date('d/m/Y H:i') . " WIB.\n\n"
             . "📄 *Nomor BAST Resmi:* *{$noBast}*\n"
             . "Petugas: " . ($pengembalian->petugas?->name ?? 'Admin Layanan Kampus') . "\n\n"
             . "Terima kasih telah menggunakan sistem FINDIT UBSI. Simpan pesan ini sebagai bukti serah terima sah barang Anda.\n\n"
             . "_Universitas Bina Sarana Informatika_";

        return self::sendMessage($user->no_telp, $msg);
    }
}
