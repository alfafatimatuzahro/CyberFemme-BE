<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeoLocationService
{
    /**
     * Dapatkan lokasi dari IP address.
     * Menggunakan ip-api.com (gratis, 45 req/menit).
     * Untuk production, ganti dengan layanan berbayar.
     */
    public function getLocation(string $ip): string
    {
        // Skip untuk local/private IP
        if (in_array($ip, ['127.0.0.1', '::1']) || $this->isPrivateIp($ip)) {
            return 'Local';
        }

        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,city,country,countryCode");

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return "{$data['city']}, {$data['country']}";
                }
            }
        } catch (\Exception $e) {
            Log::warning("GeoLocation lookup failed for IP {$ip}: " . $e->getMessage());
        }

        return 'Unknown';
    }

    /**
     * Cek apakah IP mencurigakan (di luar Indonesia).
     */
    public function isSuspiciousLocation(string $ip, string $expectedCountryCode = 'ID'): bool
    {
        if (in_array($ip, ['127.0.0.1', '::1']) || $this->isPrivateIp($ip)) {
            return false;
        }

        try {
            $response = Http::timeout(3)->get("http://ip-api.com/json/{$ip}?fields=status,countryCode");

            if ($response->successful()) {
                $data = $response->json();
                if ($data['status'] === 'success') {
                    return $data['countryCode'] !== $expectedCountryCode;
                }
            }
        } catch (\Exception $e) {
            Log::warning("GeoLocation check failed: " . $e->getMessage());
        }

        return false;
    }

    private function isPrivateIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE
        ) === false;
    }
}
