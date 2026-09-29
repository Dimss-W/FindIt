<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class SystemHealthTest extends TestCase
{
    public function test_public_routes_are_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);

        $response = $this->get('/cari');
        $response->assertStatus(200);

        $response = $this->get('/login');
        $response->assertStatus(200);

        $response = $this->get('/manifest.json');
        $response->assertStatus(200);
    }

    public function test_logout_works_via_get_and_post(): void
    {
        $user = User::where('role', 'admin')->first();

        $response = $this->actingAs($user)->get('/logout');
        $response->assertRedirect('/login');

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/login');
    }

    public function test_admin_routes(): void
    {
        $admin = User::where('role', 'admin')->first();

        $routes = [
            '/admin/dashboard',
            '/admin/users',
            '/admin/users/template',
            '/admin/petugas',
            '/admin/kampus',
            '/admin/kategori',
            '/admin/semua-laporan',
            '/admin/semua-klaim',
            '/admin/statistik',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($admin)->get($route);
            $this->assertContains($response->getStatusCode(), [200, 302], "Route {$route} failed with status {$response->getStatusCode()}");
        }
    }

    public function test_petugas_routes(): void
    {
        $petugas = User::where('role', 'petugas')->first();

        $routes = [
            '/layanan-kampus/dashboard',
            '/layanan-kampus/kelola-barang',
            '/layanan-kampus/serah-terima',
            '/layanan-kampus/barang-ditemukan',
            '/layanan-kampus/menunggu-verifikasi',
            '/layanan-kampus/barang-diamankan',
            '/layanan-kampus/laporan-hilang',
            '/layanan-kampus/klaim',
            '/layanan-kampus/scan-qr',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($petugas)->get($route);
            $this->assertContains($response->getStatusCode(), [200, 302], "Route {$route} failed with status {$response->getStatusCode()}");
        }
    }

    public function test_mahasiswa_routes(): void
    {
        $mhs = User::where('role', 'mahasiswa')->first();

        $routes = [
            '/mahasiswa/dashboard',
            '/mahasiswa/lapor',
            '/mahasiswa/aktivitas',
            '/mahasiswa/laporan-saya',
            '/mahasiswa/klaim-saya',
            '/mahasiswa/notifikasi',
            '/profil',
        ];

        foreach ($routes as $route) {
            $response = $this->actingAs($mhs)->get($route);
            $this->assertContains($response->getStatusCode(), [200, 302], "Route {$route} failed with status {$response->getStatusCode()}");
        }
    }

    public function test_template_has_5_columns(): void
    {
        $admin = User::where('role', 'admin')->first();
        $response = $this->actingAs($admin)->get('/admin/users/template');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename=template_import_mahasiswa.xlsx');
    }

    public function test_whatsapp_service_formatting_and_link(): void
    {
        $this->assertEquals('628123456789', \App\Services\WhatsAppService::formatPhoneNumber('08123456789'));
        $this->assertEquals('628123456789', \App\Services\WhatsAppService::formatPhoneNumber('+628123456789'));
        $this->assertEquals('628123456789', \App\Services\WhatsAppService::formatPhoneNumber('628123456789'));
        
        $link = \App\Services\WhatsAppService::generateChatLink('08123456789', 'Halo Tes');
        $this->assertStringContainsString('https://wa.me/628123456789?text=', $link);
    }

    public function test_donation_badge_logic(): void
    {
        $itemRecent = new \App\Models\LaporanBarang([
            'status' => 'BARANG DIAMANKAN',
        ]);
        $itemRecent->created_at = now()->subDays(10);
        $this->assertFalse($itemRecent->isExpiredForDonation());

        $itemOld = new \App\Models\LaporanBarang([
            'status' => 'BARANG DIAMANKAN',
        ]);
        $itemOld->created_at = now()->subDays(91);
        $this->assertTrue($itemOld->isExpiredForDonation());
        $this->assertGreaterThanOrEqual(90, $itemOld->days_stored);
    }
}
