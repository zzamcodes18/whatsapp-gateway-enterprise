<?php
/**
 * WhatsApp Gateway Enterprise — WHMCS Hooks
 *
 * Mendaftarkan hook untuk SEMUA event penting WHMCS dan mengirim
 * notifikasi WhatsApp via NotificationManager.
 *
 * Hook yang tercakup:
 *  - ClientAdd, ClientChangePassword, ClientLogin
 *  - InvoiceCreated, InvoicePaymentReminder, InvoiceOverdueNotice,
 *    InvoicePaid, InvoiceRefunded, InvoiceCancelled
 *  - OrderNew (via ShoppingCartCheckoutCompletePage), OrderPaid
 *  - TicketOpen, TicketUserReply, TicketClose
 *  - DomainRegister, DomainRenewal, DomainExpired
 *  - AddonActivated, ServiceSuspension, ServiceUnsuspension, ServiceTerminated
 *  - ClientAreaPage — inject floating WhatsApp support button
 */

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use WHMCS\Database\Capsule;

/*
 * hooks.php dimuat WHMCS pada SETIAP request, terpisah dari wagateway.php.
 * Karena itu semua dependensi (helpers + class) harus di-require di sini.
 * Guard function_exists/class_exists mencegah fatal error redeclare jika
 * wagateway.php sudah lebih dulu memuat file yang sama dalam request yang sama.
 */
if (!function_exists('wagateway_normalize_phone')) {
    require_once __DIR__ . '/includes/helpers.php';
}
if (!class_exists('GatewayApiClient')) {
    require_once __DIR__ . '/includes/GatewayApiClient.php';
}
if (!class_exists('NotificationManager')) {
    require_once __DIR__ . '/includes/NotificationManager.php';
}

/**
 * Ambil konfigurasi module (cache statis per-request).
 */
function wagateway_module_config()
{
    static $config = null;
    if ($config !== null) {
        return $config;
    }

    $config = [];
    try {
        $rows = Capsule::table('tbladdonmodules')
            ->where('module', 'wagateway')
            ->get();
        foreach ($rows as $row) {
            $config[$row->setting] = $row->value;
        }
    } catch (\Exception $e) {
        // Module belum aktif / tabel tidak ada
    }

    // Tambahkan system URL untuk tombol client area
    $config['systemurl'] = \WHMCS\Config\Setting::getValue('SystemURL');

    return $config;
}

/**
 * Factory NotificationManager.
 */
function wagateway_manager()
{
    return new NotificationManager(wagateway_module_config());
}

/**
 * Ambil nomor telepon klien (tblclients.phonenumber) + data dasar.
 */
function wagateway_client_data($clientId)
{
    static $cache = [];
    if (isset($cache[$clientId])) {
        return $cache[$clientId];
    }

    $client = Capsule::table('tblclients')->where('id', $clientId)->first();
    $cache[$clientId] = $client ?: null;
    return $cache[$clientId];
}

/**
 * Placeholder dasar klien.
 */
function wagateway_client_vars($client)
{
    return [
        'firstname' => $client->firstname ?? '',
        'lastname'  => $client->lastname ?? '',
        'email'     => $client->email ?? '',
        'company'   => \WHMCS\Config\Setting::getValue('CompanyName'),
    ];
}

/**
 * Link invoice (pakai source system URL agar aman).
 */
function wagateway_invoice_link($invoiceId)
{
    $systemUrl = \WHMCS\Config\Setting::getValue('SystemURL');
    return $systemUrl . '/viewinvoice.php?id=' . $invoiceId;
}

/* =========================================================
 * CLIENT EVENTS
 * ======================================================= */

add_hook('ClientAdd', 1, function ($vars) {
    $client = wagateway_client_data($vars['userid']);
    if (!$client) {
        return;
    }
    $manager = wagateway_manager();
    $manager->notify('ClientAdd', $client->phonenumber, wagateway_client_vars($client) + [
        'clientarea_link' => $vars['systemurl'] ?? wagateway_module_config()['systemurl'],
    ]);
});

add_hook('ClientChangePassword', 1, function ($vars) {
    $client = wagateway_client_data($vars['userid']);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('ClientChangePassword', $client->phonenumber, wagateway_client_vars($client));
});

add_hook('ClientLogin', 1, function ($vars) {
    $client = wagateway_client_data($vars['userid']);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('ClientLogin', $client->phonenumber, wagateway_client_vars($client) + [
        'login_time' => date('d M Y H:i') . ' WIB',
    ]);
});

/* =========================================================
 * INVOICE EVENTS
 * ======================================================= */

add_hook('InvoiceCreated', 1, function ($vars) {
    $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
    if (!$invoice) {
        return;
    }
    $client = wagateway_client_data($invoice->userid);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('InvoiceCreated', $client->phonenumber, wagateway_client_vars($client) + [
        'invoiceid'     => $invoice->id,
        'invoicenum'    => $invoice->invoicenum ?: $invoice->id,
        'total'         => formatCurrency($invoice->total)->toText(),
        'duedate'       => date('d M Y', strtotime($invoice->duedate)),
        'invoice_link'  => wagateway_invoice_link($invoice->id),
    ]);
});

add_hook('InvoicePaymentReminder', 1, function ($vars) {
    $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
    if (!$invoice) {
        return;
    }
    $client = wagateway_client_data($invoice->userid);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('InvoicePaymentReminder', $client->phonenumber, wagateway_client_vars($client) + [
        'invoiceid'     => $invoice->id,
        'invoicenum'    => $invoice->invoicenum ?: $invoice->id,
        'total'         => formatCurrency($invoice->total)->toText(),
        'duedate'       => date('d M Y', strtotime($invoice->duedate)),
        'invoice_link'  => wagateway_invoice_link($invoice->id),
    ]);
});

add_hook('InvoiceOverdueNotice', 1, function ($vars) {
    $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
    if (!$invoice) {
        return;
    }
    $client = wagateway_client_data($invoice->userid);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('InvoiceOverdueNotice', $client->phonenumber, wagateway_client_vars($client) + [
        'invoiceid'     => $invoice->id,
        'invoicenum'    => $invoice->invoicenum ?: $invoice->id,
        'total'         => formatCurrency($invoice->total)->toText(),
        'duedate'       => date('d M Y', strtotime($invoice->duedate)),
        'invoice_link'  => wagateway_invoice_link($invoice->id),
    ]);
});

add_hook('InvoicePaid', 1, function ($vars) {
    $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
    if (!$invoice) {
        return;
    }
    $client = wagateway_client_data($invoice->userid);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('InvoicePaid', $client->phonenumber, wagateway_client_vars($client) + [
        'invoiceid'     => $invoice->id,
        'invoicenum'    => $invoice->invoicenum ?: $invoice->id,
        'total'         => formatCurrency($invoice->total)->toText(),
        'invoice_link'  => wagateway_invoice_link($invoice->id),
    ]);
});

add_hook('InvoiceRefunded', 1, function ($vars) {
    $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
    if (!$invoice) {
        return;
    }
    $client = wagateway_client_data($invoice->userid);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('InvoiceRefunded', $client->phonenumber, wagateway_client_vars($client) + [
        'invoiceid'     => $invoice->id,
        'invoicenum'    => $invoice->invoicenum ?: $invoice->id,
        'total'         => formatCurrency($invoice->total)->toText(),
    ]);
});

add_hook('InvoiceCancelled', 1, function ($vars) {
    $invoice = Capsule::table('tblinvoices')->where('id', $vars['invoiceid'])->first();
    if (!$invoice) {
        return;
    }
    $client = wagateway_client_data($invoice->userid);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('InvoiceCancelled', $client->phonenumber, wagateway_client_vars($client) + [
        'invoiceid'     => $invoice->id,
        'invoicenum'    => $invoice->invoicenum ?: $invoice->id,
    ]);
});

/* =========================================================
 * ORDER EVENTS
 * ======================================================= */

// Order baru: hook ShoppingCartCheckoutCompletePage memberi orderid
add_hook('ShoppingCartCheckoutCompletePage', 1, function ($vars) {
    if (empty($vars['orderid'])) {
        return;
    }
    $order = Capsule::table('tblorders')->where('id', $vars['orderid'])->first();
    if (!$order) {
        return;
    }
    $client = wagateway_client_data($order->userid);
    if (!$client) {
        return;
    }
    $service = Capsule::table('tblhosting')
        ->join('tblproducts', 'tblhosting.packageid', '=', 'tblproducts.id')
        ->where('tblhosting.orderid', $order->id)
        ->value('tblproducts.name');

    wagateway_manager()->notify('OrderNew', $client->phonenumber, wagateway_client_vars($client) + [
        'orderid'    => $order->id,
        'servicename' => $service ?: 'Layanan Anda',
    ]);
});

// Order dibayar (via InvoicePaid → cek apakah invoice terkait order)
add_hook('InvoicePaid', 2, function ($vars) {
    $order = Capsule::table('tblorders')->where('invoiceid', $vars['invoiceid'])->first();
    if (!$order) {
        return;
    }
    $client = wagateway_client_data($order->userid);
    if (!$client) {
        return;
    }
    $service = Capsule::table('tblhosting')
        ->join('tblproducts', 'tblproducts.id', '=', 'tblhosting.packageid')
        ->where('tblhosting.orderid', $order->id)
        ->value('tblproducts.name');

    wagateway_manager()->notify('OrderPaid', $client->phonenumber, wagateway_client_vars($client) + [
        'orderid'     => $order->id,
        'servicename' => $service ?: 'Layanan Anda',
        'clientarea_link' => wagateway_module_config()['systemurl'] . '/clientarea.php',
    ]);
});

/* =========================================================
 * TICKET EVENTS
 * ======================================================= */

add_hook('TicketOpen', 1, function ($vars) {
    $client = wagateway_client_data($vars['userid']);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('TicketOpen', $client->phonenumber, wagateway_client_vars($client) + [
        'ticketid' => $vars['ticketid'],
        'subject'  => $vars['subject'] ?? '',
    ]);
});

add_hook('TicketUserReply', 1, function ($vars) {
    $client = wagateway_client_data($vars['userid']);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('TicketUserReply', $client->phonenumber, wagateway_client_vars($client) + [
        'ticketid' => $vars['ticketid'],
        'subject'  => $vars['subject'] ?? '',
    ]);
});

add_hook('TicketClose', 1, function ($vars) {
    $ticket = Capsule::table('tbltickets')->where('id', $vars['ticketid'])->first();
    if (!$ticket) {
        return;
    }
    $client = wagateway_client_data($ticket->userid);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('TicketClose', $client->phonenumber, wagateway_client_vars($client) + [
        'ticketid' => $ticket->id,
        'subject'  => $ticket->title,
    ]);
});

/* =========================================================
 * DOMAIN EVENTS
 * ======================================================= */

add_hook('DomainRegister', 1, function ($vars) {
    $client = wagateway_client_data($vars['userid']);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('DomainRegister', $client->phonenumber, wagateway_client_vars($client) + [
        'domain' => $vars['domain'] ?? '',
    ]);
});

add_hook('DomainRenewal', 1, function ($vars) {
    $client = wagateway_client_data($vars['userid']);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('DomainRenewal', $client->phonenumber, wagateway_client_vars($client) + [
        'domain' => $vars['domain'] ?? '',
    ]);
});

add_hook('DomainExpired', 1, function ($vars) {
    $client = wagateway_client_data($vars['userid']);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('DomainExpired', $client->phonenumber, wagateway_client_vars($client) + [
        'domain' => $vars['domain'] ?? '',
    ]);
});

/* =========================================================
 * SERVICE / ADDON EVENTS
 * ======================================================= */

add_hook('AddonActivated', 1, function ($vars) {
    $client = wagateway_client_data($vars['userid']);
    if (!$client) {
        return;
    }
    wagateway_manager()->notify('AddonActivated', $client->phonenumber, wagateway_client_vars($client));
});

add_hook('ServiceSuspension', 1, function ($vars) {
    $service = Capsule::table('tblhosting')->where('id', $vars['serviceid'])->first();
    if (!$service) {
        return;
    }
    $client = wagateway_client_data($service->userid);
    if (!$client) {
        return;
    }
    $product = Capsule::table('tblproducts')->where('id', $service->packageid)->value('name');

    wagateway_manager()->notify('ServiceSuspension', $client->phonenumber, wagateway_client_vars($client) + [
        'servicename' => $product ?: 'Layanan Anda',
        'invoice_link' => wagateway_invoice_link($service->id),
    ]);
});

add_hook('ServiceUnsuspension', 1, function ($vars) {
    $service = Capsule::table('tblhosting')->where('id', $vars['serviceid'])->first();
    if (!$service) {
        return;
    }
    $client = wagateway_client_data($service->userid);
    if (!$client) {
        return;
    }
    $product = Capsule::table('tblproducts')->where('id', $service->packageid)->value('name');

    wagateway_manager()->notify('ServiceUnsuspension', $client->phonenumber, wagateway_client_vars($client) + [
        'servicename' => $product ?: 'Layanan Anda',
    ]);
});

add_hook('ServiceTerminated', 1, function ($vars) {
    $service = Capsule::table('tblhosting')->where('id', $vars['serviceid'])->first();
    if (!$service) {
        return;
    }
    $client = wagateway_client_data($service->userid);
    if (!$client) {
        return;
    }
    $product = Capsule::table('tblproducts')->where('id', $service->packageid)->value('name');

    wagateway_manager()->notify('ServiceTerminated', $client->phonenumber, wagateway_client_vars($client) + [
        'servicename' => $product ?: 'Layanan Anda',
    ]);
});

/* =========================================================
 * SUPPORT BUTTON — floating WhatsApp button di client area
 * ======================================================= */

add_hook('ClientAreaHeadOutput', 1, function ($vars) {
    $config = wagateway_module_config();
    if (empty($config['supportNumber'])) {
        return '';
    }

    $waNumber  = wagateway_normalize_phone($config['supportNumber']);
    $waMessage = rawurlencode($config['supportMessage'] ?? 'Halo support, saya butuh bantuan');
    $waUrl     = 'https://wa.me/' . $waNumber . '?text=' . $waMessage;

    return <<<HTML
<style>
.wa-support-fab {
    position: fixed;
    bottom: 24px;
    right: 24px;
    z-index: 9999;
    display: flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, #25D366, #128C7E);
    color: #fff !important;
    padding: 14px 20px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 14px;
    text-decoration: none;
    box-shadow: 0 8px 24px rgba(37, 211, 102, 0.4);
    transition: transform .2s ease, box-shadow .2s ease;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}
.wa-support-fab:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 12px 32px rgba(37, 211, 102, 0.55);
    color: #fff !important;
    text-decoration: none;
}
.wa-support-fab svg {
    width: 22px;
    height: 22px;
    fill: #fff;
    flex-shrink: 0;
}
@media (max-width: 480px) {
    .wa-support-fab { padding: 12px 16px; font-size: 13px; }
}
</style>
<a href="{$waUrl}" target="_blank" rel="noopener noreferrer" class="wa-support-fab" aria-label="Chat WhatsApp Support">
    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
        <path d="M16 2.7C8.7 2.7 2.8 8.6 2.8 15.9c0 2.3.6 4.6 1.8 6.6L2.7 29.3l7-1.8c1.9 1 4.1 1.6 6.3 1.6 7.3 0 13.2-5.9 13.2-13.2S23.3 2.7 16 2.7zm0 24.1c-2 0-3.9-.5-5.6-1.5l-.4-.2-4.1 1.1 1.1-4-.3-.4c-1.1-1.8-1.7-3.8-1.7-5.9C5 9.9 9.9 5 16 5s11 4.9 11 11-4.9 10.8-11 10.8zm6.1-8.2c-.3-.2-1.9-1-2.2-1.1-.3-.1-.5-.2-.7.2-.2.3-.8 1.1-1 1.3-.2.2-.4.2-.7.1-.3-.2-1.4-.5-2.6-1.6-1-.9-1.6-1.9-1.8-2.3-.2-.3 0-.5.1-.7l.5-.6c.2-.2.2-.3.3-.6.1-.2 0-.4 0-.6-.1-.2-.7-1.8-1-2.4-.3-.6-.5-.5-.7-.5h-.6c-.2 0-.6.1-.9.4-.3.3-1.1 1.1-1.1 2.7s1.2 3.1 1.3 3.3c.2.2 2.3 3.5 5.5 4.9.8.3 1.4.5 1.8.7.8.2 1.5.2 2 .1.6-.1 1.9-.8 2.2-1.5.3-.8.3-1.4.2-1.5-.1-.2-.3-.3-.6-.4z"/>
    </svg>
    Chat Support
</a>
HTML;
});
