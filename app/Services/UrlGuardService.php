<?php

namespace App\Services;

use Illuminate\Validation\ValidationException;

/**
 * Guard SSRF: validasi URL eksternal sebelum server melakukan request.
 * Memblokir IP private/loopback/link-local/metadata & scheme non-HTTP(S).
 */
class UrlGuardService
{
    /**
     * Assert URL aman untuk di-request oleh server (webhook target, media URL, dll).
     *
     * @throws ValidationException
     */
    public static function assertSafeUrl(string $url, string $field = 'target_url'): void
    {
        $parsed = parse_url($url);

        if (! isset($parsed['scheme']) || ! in_array(strtolower($parsed['scheme']), ['http', 'https'], true)) {
            throw ValidationException::withMessages([$field => 'URL hanya boleh menggunakan scheme http:// atau https://.']);
        }

        if (! isset($parsed['host']) || $parsed['host'] === '') {
            throw ValidationException::withMessages([$field => 'URL tidak valid: host tidak ditemukan.']);
        }

        $host = strtolower($parsed['host']);

        // Blokir hostname berbahaya
        $blockedHosts = ['localhost', 'metadata.google.internal', 'instance-data'];
        if (in_array($host, $blockedHosts, true)) {
            throw ValidationException::withMessages([$field => 'Hostname internal tidak diizinkan.']);
        }

        // Blokir literal IPv6 (mis. [::1])
        if (str_starts_with($host, '[')) {
            throw ValidationException::withMessages([$field => 'Alamat IP internal tidak diizinkan.']);
        }

        // Blokir IP literal private/reserved (IPv4 & IPv4-mapped)
        if (filter_var($host, FILTER_VALIDATE_IP)) {
            if (! filter_var($host, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw ValidationException::withMessages([$field => 'Alamat IP private/reserved tidak diizinkan.']);
            }

            return;
        }

        // Hostname dinamis: resolve DNS lalu cek semua IP hasil resolusi
        $records = @dns_get_record($host, DNS_A | DNS_AAAA);
        if ($records === false || $records === []) {
            // Biarkan Http client yang menangani host tidak resolvable
            return;
        }

        foreach ($records as $record) {
            $ip = $record['ip'] ?? $record['ipv6'] ?? null;
            if ($ip === null) {
                continue;
            }
            if (! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                throw ValidationException::withMessages([$field => 'Domain ini me-resolve ke alamat IP internal yang tidak diizinkan.']);
            }
        }
    }
}
