<?php
/**
 * ProvisioningService — auto-provisioning akun WhatsApp Gateway Enterprise
 * untuk klien WHMCS saat order pertama.
 *
 * Menggunakan API internal panel:
 *  - POST /api/v1/provision  (dibuat khusus untuk integrasi WHMCS)
 *
 * Body: { email, name, password, plan_slug }
 * Response: { user_id, api_key (lpk_...), webhook_secret }
 */

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

class ProvisioningService
{
    private $client;

    public function __construct(array $moduleConfig)
    {
        $this->client = new GatewayApiClient(
            $moduleConfig['apiUrl'] ?? '',
            $moduleConfig['apiKey'] ?? ''
        );
    }

    /**
     * Buat akun gateway untuk klien WHMCS.
     *
     * @param array $clientData  ['email' => ..., 'name' => ..., 'password' => ...]
     * @return array ['success' => bool, 'api_key' => ..., 'message' => ...]
     */
    public function provisionAccount(array $clientData)
    {
        $result = $this->client->request('POST', '/api/v1/provision', [
            'email'    => $clientData['email'],
            'name'     => $clientData['name'],
            'password' => $clientData['password'],
        ]);

        if (!empty($result['success'])) {
            $data = $result['data'] ?? [];
            return [
                'success' => true,
                'api_key' => $data['api_key'] ?? '',
                'user_id' => $data['user_id'] ?? null,
                'message' => 'Akun gateway berhasil dibuat.',
            ];
        }

        return [
            'success' => false,
            'message' => $result['message'] ?? 'Provisioning gagal.',
        ];
    }
}
