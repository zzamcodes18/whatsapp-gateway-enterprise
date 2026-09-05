<?php
/**
 * Admin area views — WhatsApp Gateway WHMCS Module
 * Dipanggil dari wagateway_output().
 *
 * Variabel tersedia: $vars (module config), $activeTab
 */

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use WHMCS\Database\Capsule;

$events   = wagateway_supported_events();
$defaults = wagateway_default_templates();
$baseUrl  = $vars['apiUrl'] ?? '';
$apiKey   = $vars['apiKey'] ?? '';
$deviceId = $vars['deviceId'] ?? '';
$token    = generate_token('WHMCS.admin.default');

function wagateway_status_badge($ok)
{
    return $ok
        ? '<span style="color:#16a34a;font-weight:700;">● Aktif</span>'
        : '<span style="color:#dc2626;font-weight:700;">● Belum diisi</span>';
}
?>

<div class="wagateway-admin">

<?php if ($activeTab === 'dashboard' || $activeTab === 'test_connection' || $activeTab === 'send_test'): ?>
    <!-- ============ DASHBOARD ============ -->
    <h2>📱 WhatsApp Gateway Enterprise — Dashboard</h2>

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(280px,1fr));gap:16px;margin:20px 0;">
        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;">
            <h3 style="margin:0 0 12px;font-size:14px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Status Koneksi</h3>
            <table class="table table-striped" style="margin:0;">
                <tr><td>API URL</td><td><?= wagateway_status_badge($baseUrl !== '') ?></td></tr>
                <tr><td>API Key</td><td><?= wagateway_status_badge($apiKey !== '') ?></td></tr>
                <tr><td>Device ID</td><td><?= wagateway_status_badge($deviceId !== '') ?></td></tr>
            </table>
            <form method="post" style="margin-top:14px;">
                <input type="hidden" name="action" value="test_connection">
                <input type="hidden" name="token" value="<?= $token ?>">
                <button type="submit" class="btn btn-primary">🔌 Test Koneksi</button>
            </form>
        </div>

        <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:20px;">
            <h3 style="margin:0 0 12px;font-size:14px;color:#64748b;text-transform:uppercase;letter-spacing:.05em;">Kirim Test Message</h3>
            <form method="post">
                <input type="hidden" name="action" value="send_test">
                <input type="hidden" name="token" value="<?= $token ?>">
                <div class="form-group">
                    <label>Nomor WhatsApp</label>
                    <input type="text" name="phone" class="form-control" placeholder="08123456789" required>
                </div>
                <div class="form-group">
                    <label>Pesan</label>
                    <textarea name="message" class="form-control" rows="3" required>✅ Tes notifikasi WhatsApp Gateway dari WHMCS!</textarea>
                </div>
                <button type="submit" class="btn btn-success">📤 Kirim Test</button>
            </form>
        </div>

        <div style="background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border-radius:12px;padding:20px;">
            <h3 style="margin:0 0 12px;font-size:14px;color:#94a3b8;text-transform:uppercase;letter-spacing:.05em;">Statistik Notifikasi</h3>
            <?php
            $total = Capsule::table('mod_wagateway_logs')->count();
            $sent  = Capsule::table('mod_wagateway_logs')->where('status', 'sent')->count();
            $fail  = Capsule::table('mod_wagateway_logs')->where('status', 'failed')->count();
            ?>
            <div style="font-size:32px;font-weight:800;"><?= $total ?></div>
            <div style="color:#94a3b8;margin-bottom:12px;">Total notifikasi</div>
            <div style="display:flex;gap:16px;">
                <span style="color:#4ade80;">✔ <?= $sent ?> terkirim</span>
                <span style="color:#f87171;">✘ <?= $fail ?> gagal</span>
            </div>
        </div>
    </div>

    <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:12px;padding:16px 20px;">
        <strong>💡 Langkah berikutnya:</strong>
        <ol style="margin:8px 0 0;">
            <li>Lengkapi API URL, API Key, dan Device ID di <a href="addonmodules.php?module=wagateway">konfigurasi module</a>.</li>
            <li>Atur template pesan di tab <a href="addonmodules.php?module=wagateway&action=templates">Template Notifikasi</a>.</li>
            <li>Klik <em>Test Koneksi</em> lalu <em>Kirim Test</em> untuk memastikan semuanya berjalan.</li>
        </ol>
    </div>

<?php elseif ($activeTab === 'templates'): ?>
    <!-- ============ TEMPLATE EDITOR ============ -->
    <h2>✏️ Template Notifikasi WhatsApp</h2>
    <p style="color:#64748b;">Gunakan placeholder seperti <code>{firstname}</code>, <code>{invoicenum}</code>, <code>{total}</code>, <code>{duedate}</code>, <code>{invoice_link}</code>, <code>{servicename}</code>, <code>{ticketid}</code>, <code>{subject}</code>, <code>{domain}</code>, <code>{orderid}</code>, <code>{email}</code>, <code>{company}</code>. Format WhatsApp didukung: <code>*tebal*</code>, <code>_miring_</code>, emoji.</p>

    <form method="post">
        <input type="hidden" name="action" value="save_templates">
        <input type="hidden" name="token" value="<?= $token ?>">

        <?php foreach ($events as $event => $label): ?>
            <?php $tpl = wagateway_get_template($event); ?>
            <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:16px 20px;margin-bottom:14px;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px;">
                    <strong style="font-size:15px;"><?= htmlspecialchars($label) ?></strong>
                    <label style="margin:0;display:flex;align-items:center;gap:6px;cursor:pointer;">
                        <input type="checkbox" name="templates[<?= $event ?>][enabled]" <?= $tpl['enabled'] ? 'checked' : '' ?>>
                        Aktif
                    </label>
                </div>
                <textarea name="templates[<?= $event ?>][message]" class="form-control" rows="4" style="font-family:monospace;font-size:12px;"><?= htmlspecialchars($tpl['message']) ?></textarea>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary" style="padding:10px 28px;">💾 Simpan Semua Template</button>
    </form>

<?php elseif ($activeTab === 'logs'): ?>
    <!-- ============ LOGS ============ -->
    <h2>📜 Riwayat Notifikasi</h2>
    <?php
    $logs = Capsule::table('mod_wagateway_logs')
        ->orderBy('created_at', 'desc')
        ->limit(100)
        ->get();
    ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Waktu</th>
                <th>Event</th>
                <th>Nomor</th>
                <th>Pesan</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($logs->isEmpty()): ?>
                <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:24px;">Belum ada notifikasi terkirim.</td></tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td style="white-space:nowrap;"><?= htmlspecialchars($log->created_at) ?></td>
                        <td><span class="label label-default"><?= htmlspecialchars($log->event) ?></span></td>
                        <td>+<?= htmlspecialchars($log->phone) ?></td>
                        <td style="max-width:380px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"><?= htmlspecialchars(mb_substr($log->message, 0, 80)) ?>…</td>
                        <td>
                            <?php if ($log->status === 'sent'): ?>
                                <span class="label label-success">Terkirim</span>
                            <?php elseif ($log->status === 'failed'): ?>
                                <span class="label label-danger">Gagal</span>
                            <?php else: ?>
                                <span class="label label-default"><?= htmlspecialchars($log->status) ?></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

<?php elseif ($activeTab === 'docs'): ?>
    <!-- ============ PANDUAN ============ -->
    <h2>📖 Panduan Pemasangan Module</h2>
    <div style="background:#fff;border:1px solid #e2e8f0;border-radius:12px;padding:24px;max-width:860px;line-height:1.7;">
        <h3>1. Persiapan Panel Gateway</h3>
        <ol>
            <li>Login ke panel <strong>WhatsApp Gateway Enterprise</strong>.</li>
            <li>Buka menu <strong>Devices</strong>, pastikan device WhatsApp Anda <em>Connected</em>. Catat <strong>Device ID</strong>-nya.</li>
            <li>Buka menu <strong>API Keys</strong> → <em>Buat API Key baru</em> → salin token <code>lpk_...</code>.</li>
        </ol>

        <h3>2. Instalasi di WHMCS</h3>
        <ol>
            <li>Upload folder <code>wagateway</code> ke <code>modules/addons/</code> pada instalasi WHMCS Anda.</li>
            <li>Pastikan struktur akhirnya: <code>modules/addons/wagateway/wagateway.php</code></li>
            <li>Masuk WHMCS Admin → <strong>System Settings → Addon Modules</strong>.</li>
            <li>Cari <strong>WhatsApp Gateway Enterprise</strong> → klik <em>Activate</em>.</li>
            <li>Klik <em>Configure</em> → isi:
                <ul>
                    <li><strong>Gateway API URL</strong>: URL panel gateway (contoh: <code>https://wa.domain.com</code>)</li>
                    <li><strong>API Key</strong>: token <code>lpk_...</code></li>
                    <li><strong>Device ID</strong>: ID device yang terhubung</li>
                    <li><strong>Nomor Support WhatsApp</strong>: untuk floating button (opsional)</li>
                </ul>
            </li>
            <li>Beri akses ke admin group, lalu Save.</li>
        </ol>

        <h3>3. Verifikasi</h3>
        <ol>
            <li>Buka tab <strong>Dashboard</strong> module → klik <em>Test Koneksi</em> (harus muncul jumlah device).</li>
            <li>Gunakan <em>Kirim Test Message</em> ke nomor Anda sendiri.</li>
            <li>Atur template di tab <strong>Template Notifikasi</strong> sesuai kebutuhan.</li>
        </ol>

        <h3>4. Troubleshooting</h3>
        <table class="table table-bordered">
            <tr><th>Masalah</th><th>Solusi</th></tr>
            <tr><td>Koneksi gagal / 401</td><td>Cek API Key & URL. Pastikan API key aktif di panel gateway.</td></tr>
            <tr><td>Device tidak ditemukan</td><td>Pastikan Device ID benar dan device status <em>Connected</em> di panel.</td></tr>
            <tr><td>Notifikasi tidak terkirim</td><td>Cek tab <em>Riwayat Notifikasi</em> untuk alasan gagal (quota, nomor kosong, dll).</td></tr>
            <tr><td>Nomor tidak terdeteksi</td><td>Isi nomor telepon klien di profil WHMCS (format 08xx atau 62xxx).</td></tr>
        </table>
    </div>

<?php else: ?>
    <div class="alert alert-danger">Tab tidak dikenal.</div>
<?php endif; ?>

</div>
