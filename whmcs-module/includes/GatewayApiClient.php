<?php
/**
 * GatewayApiClient — HTTP client untuk WhatsApp Gateway Enterprise API v1
 *
 * Endpoint yang digunakan:
 *  - POST /api/v1/messages/send-text
 *  - POST /api/v1/messages/send-button
 *  - GET  /api/v1/devices
 */

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

class GatewayApiClient
{
    private $baseUrl;
    private $apiKey;
    private $timeout = 15;

    public function __construct($baseUrl, $apiKey, $timeout = 15)
    {
        $this->baseUrl = rtrim((string) $baseUrl, '/');
        $this->apiKey  = (string) $apiKey;
        if ($timeout > 0) {
            $this->timeout = $timeout;
        }
    }

    /**
     * Cek koneksi: ambil daftar device.
     */
    public function getDevices()
    {
        return $this->request('GET', '/api/v1/devices');
    }

    /**
     * Kirim pesan teks.
     */
    public function sendText($deviceId, $phone, $message)
    {
        return $this->request('POST', '/api/v1/messages/send-text', [
            'device_id' => $deviceId,
            'phone'     => $phone,
            'message'   => $message,
        ]);
    }

    /**
     * Kirim pesan dengan tombol (interactive message).
     */
    public function sendButton($deviceId, $phone, $body, array $buttons, $footer = '')
    {
        $payload = [
            'device_id' => $deviceId,
            'phone'     => $phone,
            'body'      => $body,
            'buttons'   => array_values($buttons),
        ];
        if ($footer !== '') {
            $payload['footer'] = $footer;
        }
        return $this->request('POST', '/api/v1/messages/send-button', $payload);
    }

    /**
     * Eksekusi HTTP request via cURL.
     */
    private function request($method, $path, array $data = [])
    {
        if ($this->baseUrl === '' || $this->apiKey === '') {
            return ['success' => false, 'message' => 'API URL / API Key belum dikonfigurasi di module settings.'];
        }

        $url = $this->baseUrl . $path;
        $ch  = curl_init();

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-Key: ' . $this->apiKey,
        ];

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_CONNECTTIMEOUT => 10,
            CURLOPT_HTTPHEADER     => $headers,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $body     = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($body === false) {
            return ['success' => false, 'message' => 'cURL error: ' . $curlErr];
        }

        $json = json_decode($body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'message' => 'Invalid JSON response (HTTP ' . $httpCode . ')'];
        }

        // Laravel API: 200/201 = sukses
        if ($httpCode >= 200 && $httpCode < 300) {
            return ['success' => true, 'data' => $json['data'] ?? $json, 'raw' => $json];
        }

        $message = $json['message'] ?? ($json['error'] ?? 'HTTP ' . $httpCode);
        return ['success' => false, 'message' => $message, 'code' => $httpCode];
    }
}
