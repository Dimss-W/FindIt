<?php

namespace App\Services;

use App\Models\LaporanBarang;
use App\Models\Notifikasi;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class SmartMatchingService
{
    /**
     * Hitung persentase kecocokan antara dua laporan barang.
     * Bobot:
     * - Kampus: 30%
     * - Kategori: 20%
     * - Nama Barang: 20%
     * - Lokasi: 15%
     * - Tanggal: 15%
     */
    public function calculateMatch(LaporanBarang $a, LaporanBarang $b): array
    {
        // 1. Kampus (30%)
        $campusScore = ($a->kampus_id === $b->kampus_id) ? 30.0 : 0.0;

        // 2. Kategori (20%)
        $categoryScore = ($a->kategori_id === $b->kategori_id) ? 20.0 : 0.0;

        // 3. Nama Barang (20%)
        $nameSim = $this->calculateTextSimilarity($a->nama_barang, $b->nama_barang);
        $nameScore = round(($nameSim / 100) * 20, 1);

        // 4. Lokasi Kejadian (15%)
        $locSim = $this->calculateTextSimilarity($a->lokasi_kejadian, $b->lokasi_kejadian);
        $locScore = round(($locSim / 100) * 15, 1);

        // 5. Tanggal Kejadian (15%)
        $diffDays = 999;
        if ($a->tanggal_kejadian && $b->tanggal_kejadian) {
            $dateA = Carbon::parse($a->tanggal_kejadian);
            $dateB = Carbon::parse($b->tanggal_kejadian);
            $diffDays = abs($dateA->diffInDays($dateB, false));
        }

        if ($diffDays === 0) {
            $dateScore = 15.0;
        } elseif ($diffDays <= 2) {
            $dateScore = 12.0;
        } elseif ($diffDays <= 5) {
            $dateScore = 8.0;
        } elseif ($diffDays <= 7) {
            $dateScore = 4.0;
        } else {
            $dateScore = 0.0;
        }

        $totalScore = min(100, round($campusScore + $categoryScore + $nameScore + $locScore + $dateScore));

        return [
            'total' => (int) $totalScore,
            'breakdown' => [
                'kampus' => $campusScore,
                'kategori' => $categoryScore,
                'nama' => $nameScore,
                'lokasi' => $locScore,
                'tanggal' => $dateScore,
            ],
            'details' => [
                'same_campus' => $a->kampus_id === $b->kampus_id,
                'same_category' => $a->kategori_id === $b->kategori_id,
                'name_similarity' => round($nameSim),
                'location_similarity' => round($locSim),
                'date_diff_days' => $diffDays,
            ]
        ];
    }

    /**
     * Hitung kemiripan teks menggunakan kombinasi kata perkata & similar_text
     */
    private function calculateTextSimilarity(string $textA, string $textB): float
    {
        $cleanA = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $textA)));
        $cleanB = strtolower(trim(preg_replace('/[^a-zA-Z0-9\s]/', ' ', $textB)));

        if ($cleanA === $cleanB) {
            return 100.0;
        }

        // similar_text
        similar_text($cleanA, $cleanB, $simPerc);

        // Word tokens overlap (Jaccard)
        $wordsA = array_filter(explode(' ', $cleanA));
        $wordsB = array_filter(explode(' ', $cleanB));

        if (empty($wordsA) || empty($wordsB)) {
            return $simPerc;
        }

        $intersect = count(array_intersect($wordsA, $wordsB));
        $union = count(array_unique(array_merge($wordsA, $wordsB)));
        $jaccardPerc = ($union > 0) ? ($intersect / $union) * 100 : 0;

        return max($simPerc, $jaccardPerc);
    }

    /**
     * Cari kemungkinan kecocokan untuk suatu laporan
     */
    public function findMatchesFor(LaporanBarang $target, int $threshold = 40, ?int $limit = 10): array
    {
        // Jika target adalah HILANG, pasangannya adalah DITEMUKAN
        $targetOpposite = ($target->jenis_laporan === 'HILANG') ? 'DITEMUKAN' : 'HILANG';

        $query = LaporanBarang::with(['kampus', 'kategori', 'user'])
            ->where('jenis_laporan', $targetOpposite)
            ->where('id', '!=', $target->id);

        if ($targetOpposite === 'DITEMUKAN') {
            $query->whereIn('status', ['MENUNGGU VERIFIKASI', 'BARANG DIAMANKAN', 'SIAP DIAMBIL']);
        } else {
            $query->whereIn('status', ['SEDANG DICARI', 'ADA KEMUNGKINAN COCOK']);
        }

        // Prioritaskan kampus yang sama terlebih dahulu, tetapi boleh periksa bila ada di kampus lain
        $candidates = $query->get();

        $results = [];
        foreach ($candidates as $candidate) {
            $match = $this->calculateMatch($target, $candidate);
            if ($match['total'] >= $threshold) {
                $results[] = [
                    'item' => $candidate,
                    'score' => $match['total'],
                    'breakdown' => $match['breakdown'],
                    'details' => $match['details'],
                ];
            }
        }

        // Sort descending by score
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        if ($limit && count($results) > $limit) {
            $results = array_slice($results, 0, $limit);
        }

        return $results;
    }

    /**
     * Kirim notifikasi otomatis jika ditemukan kecocokan tinggi (>= 60%)
     */
    public function notifyOnHighMatch(LaporanBarang $newReport): void
    {
        $matches = $this->findMatchesFor($newReport, 60, 3);

        foreach ($matches as $match) {
            $matchedItem = $match['item'];
            $score = $match['score'];

            // Jika laporan baru adalah DITEMUKAN, beri notif ke pelapor HILANG
            if ($newReport->jenis_laporan === 'DITEMUKAN' && $matchedItem->jenis_laporan === 'HILANG') {
                $matchedItem->update(['status' => 'ADA KEMUNGKINAN COCOK']);

                Notifikasi::create([
                    'user_id' => $matchedItem->user_id,
                    'judul' => '⚡ Kemungkinan Barang Ditemukan (' . $score . '% Cocok)',
                    'pesan' => "Barang yang sesuai dengan laporan kehilangan Anda \"{$matchedItem->nama_barang}\" kemungkinan telah ditemukan di {$newReport->kampus->nama_kampus}. Segera cek dan ajukan klaim jika milik Anda.",
                    'link' => route('mahasiswa.laporan.detail', $newReport->id),
                    'is_read' => false,
                ]);

                try {
                    WhatsAppService::notifyMatchFound($matchedItem, $newReport, (int) $score);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('WhatsApp Match notification failed: ' . $e->getMessage());
                }
            }
            // Jika laporan baru adalah HILANG, dan ada barang DITEMUKAN yang cocok
            elseif ($newReport->jenis_laporan === 'HILANG' && $matchedItem->jenis_laporan === 'DITEMUKAN') {
                $newReport->update(['status' => 'ADA KEMUNGKINAN COCOK']);

                Notifikasi::create([
                    'user_id' => $newReport->user_id,
                    'judul' => '⚡ Kemungkinan Barang Ditemukan (' . $score . '% Cocok)',
                    'pesan' => "Ada barang ditemukan \"{$matchedItem->nama_barang}\" di {$matchedItem->kampus->nama_kampus} yang cocok dengan laporan kehilangan Anda. Cek detail dan ajukan klaim.",
                    'link' => route('mahasiswa.laporan.detail', $matchedItem->id),
                    'is_read' => false,
                ]);

                try {
                    WhatsAppService::notifyMatchFound($newReport, $matchedItem, (int) $score);
                } catch (\Throwable $e) {
                    \Illuminate\Support\Facades\Log::warning('WhatsApp Match notification failed: ' . $e->getMessage());
                }
            }
        }
    }
}
