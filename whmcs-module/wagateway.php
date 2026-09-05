<?php
/**
 * WhatsApp Gateway Enterprise — WHMCS Provisioning & Notification Module
 *
 * Developer : Muhammad Zaki (jakisoft) & Muhammad Tsaqif Noor Az Zamil (zzamcode)
 * Version   : 1.0.0
 * Requires  : WHMCS 8.x+ / PHP 7.4+
 *
 * Fitur:
 *  - Notifikasi WhatsApp untuk SEMUA event WHMCS (invoice, order, ticket, domain, dll)
 *  - Auto-provisioning akun gateway + API key saat order pertama
 *  - Support button WhatsApp floating di client area
 *  - Template pesan yang bisa dikustomisasi per event dari admin area
 *  - Test koneksi & preview pesan dari admin area
 */

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use WHMCS\Database\Capsule;

require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/GatewayApiClient.php';
require_once __DIR__ . '/includes/NotificationManager.php';
require_once __DIR__ . '/includes/ProvisioningService.php';

/**
 * Konfigurasi module (ditampilkan di WHMCS Admin > Addon Modules).
 */
function wagateway_config()
{
    return [
        'name'        => 'WhatsApp Gateway Enterprise',
        'description' => 'Notifikasi WhatsApp untuk semua event WHMCS + auto-provisioning akun WhatsApp Gateway Enterprise.',
        'version'     => '1.0.0',
        'author'      => 'zzamcode & jakisoft',
        'language'    => 'indonesian',
        'fields'      => [
            'apiUrl' => [
                'FriendlyName' => 'Gateway API URL',
                'Type'         => 'text',
                'Description'  => 'URL panel WhatsApp Gateway, contoh: https://wa.domain-anda.com',
                'Default'      => '',
            ],
            'apiKey' => [
                'FriendlyName' => 'API Key',
                'Type'         => 'password',
                'Description'  => 'API key (lpk_...) dari panel gateway. Buat di menu API Keys.',
                'Default'      => '',
            ],
            'deviceId' => [
                'FriendlyName' => 'Device ID',
                'Type'         => 'text',
                'Description'  => 'ID device WhatsApp yang terhubung (lihat menu Devices di panel).',
                'Default'      => '',
            ],
            'enableProvisioning' => [
                'FriendlyName' => 'Auto-Provisioning',
                'Type'         => 'yesno',
                'Description'  => 'Buat akun gateway + API key otomatis saat order pertama (via API panel).',
                'Default'      => 'no',
            ],
        ],
    ];
}

/**
 * Output admin area module page.
 */
function wagateway_output($vars)
{
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 'dashboard';

    // Simpan template jika disubmit
    if ($action === 'save_templates' && isset($_POST['templates'])) {
        check_token('WHMCS.admin.default');
        foreach ($_POST['templates'] as $event => $data) {
            wagateway_save_template($event, $data['message'], isset($data['enabled']));
        }
        echo '<div class="alert alert-success">Template notifikasi berhasil disimpan.</div>';
        $action = 'templates';
    }

    // Test koneksi
    if ($action === 'test_connection') {
        check_token('WHMCS.admin.default');
        $client = new GatewayApiClient($vars['apiUrl'], $vars['apiKey']);
        $result = $client->getDevices();
        if (!empty($result['success'])) {
            echo '<div class="alert alert-success">Koneksi berhasil! ' . count($result['data']) . ' device terdeteksi.</div>';
        } else {
            echo '<div class="alert alert-danger">Koneksi gagal: ' . htmlspecialchars($result['message'] ?? 'Unknown error') . '</div>';
        }
    }

    // Kirim test message
    if ($action === 'send_test' && isset($_POST['phone'])) {
        check_token('WHMCS.admin.default');
        $manager = new NotificationManager($vars);
        $result  = $manager->sendDirect(
            $_POST['phone'],
            $_POST['message'],
            'Test dari WHMCS Admin'
        );
        if (!empty($result['success'])) {
            echo '<div class="alert alert-success">Test message terkirim ke +' . htmlspecialchars($_POST['phone']) . '!</div>';
        } else {
            echo '<div class="alert alert-danger">Gagal: ' . htmlspecialchars($result['message'] ?? 'Unknown') . '</div>';
        }
    }

    $activeTab = $action;
    include __DIR__ . '/views/admin.php';
}

/**
 * Sidebar admin area.
 */
function wagateway_sidebar($vars)
{
    return <<<HTML
<div class="header-sidebar">
    <div class="sidebar-header">WhatsApp Gateway</div>
    <ul class="nav nav-tabs nav-stacked">
        <li><a href="addonmodules.php?module=wagateway">Dashboard</a></li>
        <li><a href="addonmodules.php?module=wagateway&action=templates">Template Notifikasi</a></li>
        <li><a href="addonmodules.php?module=wagateway&action=logs">Riwayat Notifikasi</a></li>
        <li><a href="addonmodules.php?module=wagateway&action=docs">Panduan Pemasangan</a></li>
    </ul>
</div>
HTML;
}

/**
 * Aktivasi module: buat tabel template & log.
 */
function wagateway_activate()
{
    try {
        Capsule::schema()->create('mod_wagateway_templates', function ($table) {
            $table->increments('id');
            $table->string('event', 60)->unique();
            $table->text('message');
            $table->tinyInteger('enabled')->default(1);
            $table->timestamps();
        });

        Capsule::schema()->create('mod_wagateway_logs', function ($table) {
            $table->increments('id');
            $table->string('event', 60);
            $table->string('phone', 30);
            $table->text('message');
            $table->string('status', 20)->default('pending');
            $table->text('response')->nullable();
            $table->timestamps();
        });

        // Seed template default semua event
        foreach (wagateway_default_templates() as $event => $message) {
            wagateway_save_template($event, $message, true);
        }

        return [
            'status'      => 'success',
            'description' => 'Module WhatsApp Gateway Enterprise berhasil diaktifkan. Buka Addon Modules untuk konfigurasi.',
        ];
    } catch (\Exception $e) {
        return [
            'status'      => 'error',
            'description' => 'Gagal aktivasi: ' . $e->getMessage(),
        ];
    }
}

/**
 * Deaktivasi module.
 */
function wagateway_deactivate()
{
    try {
        Capsule::schema()->dropIfExists('mod_wagateway_templates');
        Capsule::schema()->dropIfExists('mod_wagateway_logs');
        return ['status' => 'success', 'description' => 'Module dinonaktifkan.'];
    } catch (\Exception $e) {
        return ['status' => 'error', 'description' => 'Gagal: ' . $e->getMessage()];
    }
}

/**
 * Upgrade module (versi mendatang).
 */
function wagateway_upgrade($vars)
{
    // Reserved untuk migrasi versi berikutnya
}
