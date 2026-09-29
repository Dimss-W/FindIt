<?php

namespace App\Services;

use App\Models\LaporanBarang;
use Carbon\Carbon;

class AutoMatchingService
{
    /**
     * Cari rekomendasi kecocokan otomatis untuk sebuah laporan.
     *
     * @param LaporanBarang $laporan
     * @param int $limit
     * @return array
     */
    public static function findMatches(LaporanBarang $laporan, int $limit = 5): array
    {
        $targetJenis = $laporan->jenis_laporan === 'HILANG' ? 'DITEMUKAN' : 'HILANG';

        // Hanya cari laporan yang belum selesai/dikembalikan
        $candidates = LaporanBarang::with(['kategori', 'kampus', 'user'])
            ->where('jenis_laporan', $targetJenis)
            ->whereNotIn('status', ['DITOLAK', 'DIKEMBALIKAN'])
            ->where('id', '!=', $laporan->id)
            ->get();

        $results = [];

        foreach ($candidates as $candidate) {
            $score = 0;
            $reasons = [];

            // 1. Kategori Sama (Bobot 40 poin)
            if ($laporan->kategori_id === $candidate->kategori_id) {
                $score += 40;
                $reasons[] = 'Kategori ' . ($laporan->kategori?->nama_kategori ?? 'Sama');
            }

            // 2. Kampus Sama (Bobot 30 poin)
            if ($laporan->kampus_id === $candidate->kampus_id) {
                $score += 30;
                $reasons[] = 'Lokasi ' . ($laporan->kampus?->nama_kampus ?? 'Kampus Sama');
            }

            // 3. Rentang Tanggal (Bobot 15 poin)
            if ($laporan->tanggal_kejadian && $candidate->tanggal_kejadian) {
                $tglTarget = Carbon::parse($laporan->tanggal_kejadian);
                $tglCandidate = Carbon::parse($candidate->tanggal_kejadian);
                $diffDays = abs($tglTarget->diffInDays($tglCandidate));

                if ($diffDays <= 2) {
                    $score += 15;
                    $reasons[] = 'Waktu sangat dekat (' . ($diffDays === 0 ? 'Hari yang sama' : $diffDays . ' hari') . ')';
                } elseif ($diffDays <= 7) {
                    $score += 10;
                    $reasons[] = 'Waktu berdekatan (' . $diffDays . ' hari)';
                } elseif ($diffDays <= 14) {
                    $score += 5;
                    $reasons[] = 'Rentang waktu 2 minggu';
                }
            }

            // 4. Kemiripan Teks Nama Barang & Deskripsi (Bobot 15 poin)
            $textSimilarity = self::calculateTextSimilarity(
                $laporan->nama_barang . ' ' . $laporan->deskripsi,
                $candidate->nama_barang . ' ' . $candidate->deskripsi
            );

            if ($textSimilarity >= 0.5) {
                $score += 15;
                $reasons[] = 'Ciri-ciri & deskripsi sangat mirip';
            } elseif ($textSimilarity >= 0.25) {
                $score += 10;
                $reasons[] = 'Kata kunci barang mirip';
            } elseif ($textSimilarity >= 0.1) {
                $score += 5;
                $reasons[] = 'Ada kemiripan istilah';
            }

            // Minimal skor 45% untuk dianggap rekomendasi yang relevan
            if ($score >= 45) {
                $results[] = [
                    'laporan' => $candidate,
                    'score' => min(100, $score),
                    'reasons' => $reasons,
                ];
            }
        }

        // Urutkan skor tertinggi ke terendah
        usort($results, fn ($a, $b) => $b['score'] <=> $a['score']);

        return array_slice($results, 0, $limit);
    }

    /**
     * Hitung koefisien kemiripan kata antara dua teks sederhana.
     */
    private static function calculateTextSimilarity(string $str1, string $str2): float
    {
        $words1 = self::tokenize($str1);
        $words2 = self::tokenize($str2);

        if (empty($words1) || empty($words2)) {
            return 0.0;
        }

        $intersection = array_intersect($words1, $words2);
        $union = array_unique(array_merge($words1, $words2));

        if (empty($union)) {
            return 0.0;
        }

        return count($intersection) / count($union);
    }

    /**
     * Tokenisasi kata kunci (hapus stop words umum bahasa Indonesia).
     */
    private static function tokenize(string $str): array
    {
        $clean = strtolower(preg_replace('/[^\p{L}\p{N}\s]/u', ' ', $str));
        $tokens = preg_split('/\s+/', $clean, -1, PREG_SPLIT_NO_EMPTY);

        $stopWords = [
            'yang', 'di', 'dan', 'ini', 'itu', 'pada', 'untuk', 'dengan', 'ada', 
            'dari', 'saya', 'ke', 'adalah', 'atau', 'dalam', 'bisa', 'sudah', 
            'hilang', 'ditemukan', 'warna', 'berwarna', 'merk', 'tipe', 'sebuah'
        ];

        return array_values(array_filter($tokens, fn ($t) => strlen($t) >= 3 && !in_array($t, $stopWords)));
    }
}
