<?php
/**
 * NotificationManager — mengirim notifikasi WhatsApp berdasarkan event WHMCS.
 *
 * Alur:
 *  1. Hook WHMCS memanggil notify($event, $phone, $mergeVars)
 *  2. Manager cek template enabled + ambil pesan dari DB
 *  3. Render placeholder, kirim via GatewayApiClient
 *  4. Catat ke mod_wagateway_logs
 */

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

class NotificationManager
{
    private $config;
    private $client;

    public function __construct(array $moduleConfig)
    {
        $this->config = $moduleConfig;
        $this->client = new GatewayApiClient(
            $moduleConfig['apiUrl'] ?? '',
            $moduleConfig['apiKey'] ?? ''
        );
    }

    /**
     * Kirim notifikasi event ke client.
     *
     * @param string $event      Nama event (key dari wagateway_supported_events)
     * @param string $phone      Nomor tujuan (akan dinormalisasi)
     * @param array  $mergeVars  Placeholder => value
     */
    public function notify($event, $phone, array $mergeVars = [])
    {
        $phone = wagateway_normalize_phone($phone);
        if ($phone === '') {
            wagateway_log($event, $phone, '', 'skipped', ['reason' => 'phone empty']);
            return ['success' => false, 'message' => 'Nomor telepon kosong.'];
        }

        $template = wagateway_get_template($event);
        if (!$template['enabled'] || trim($template['message']) === '') {
            wagateway_log($event, $phone, $template['message'], 'disabled');
            return ['success' => false, 'message' => 'Event tidak aktif / template kosong.'];
        }

        $message = wagateway_render_template($template['message'], $mergeVars);
        return $this->sendDirect($phone, $message, $event);
    }

    /**
     * Kirim pesan langsung (tanpa template), dengan tombol CTA jika dikonfigurasi.
     */
    public function sendDirect($phone, $message, $event = 'direct')
    {
        $phone = wagateway_normalize_phone($phone);
        $deviceId = $this->config['deviceId'] ?? '';

        if ($deviceId === '') {
            wagateway_log($event, $phone, $message, 'failed', ['reason' => 'device_id empty']);
            return ['success' => false, 'message' => 'Device ID belum diisi di module settings.'];
        }

        // Event invoice: tambahkan tombol "Bayar Sekarang" jika ada link invoice
        $result = null;
        if (preg_match('#https?://\S+#', $message, $m) && $this->shouldUseButtons($event)) {
            $link   = $m[0];
            $body   = preg_replace('#\s*' . preg_quote($link, '#') . '#', '', $message);
            $result = $this->client->sendButton(
                $deviceId,
                $phone,
                trim($body),
                [
                    ['type' => 'url', 'text' => '🔗 Bayar Sekarang', 'url' => $link],
                    ['type' => 'url', 'text' => '🏠 Client Area', 'url' => $this->config['systemurl'] ?? ''],
                ],
                'WhatsApp Gateway Enterprise'
            );
        }

        // Fallback / default: kirim teks biasa
        if ($result === null || empty($result['success'])) {
            $result = $this->client->sendText($deviceId, $phone, $message);
        }

        $status = !empty($result['success']) ? 'sent' : 'failed';
        wagateway_log($event, $phone, $message, $status, $result);

        return $result;
    }

    /**
     * Event mana yang layak pakai tombol (ada link pembayaran/client area).
     */
    private function shouldUseButtons($event)
    {
        return in_array($event, [
            'InvoiceCreated',
            'InvoicePaymentReminder',
            'InvoiceOverdueNotice',
            'ServiceSuspension',
            'OrderPaid',
            'ClientAdd',
        ], true);
    }
}
