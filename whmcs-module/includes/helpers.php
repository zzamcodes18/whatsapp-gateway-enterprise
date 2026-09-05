<?php
/**
 * Helper functions — WhatsApp Gateway WHMCS Module
 */

if (!defined('WHMCS')) {
    die('This file cannot be accessed directly');
}

use WHMCS\Database\Capsule;

/**
 * Daftar event WHMCS yang didukung + labelnya.
 */
function wagateway_supported_events()
{
    return [
        // Invoice & Billing
        'InvoiceCreated'              => 'Invoice Dibuat',
        'InvoicePaymentReminder'      => 'Reminder Pembayaran Invoice',
        'InvoiceOverdueNotice'        => 'Invoice Jatuh Tempo',
        'InvoicePaid'                 => 'Pembayaran Berhasil',
        'InvoiceRefunded'             => 'Invoice Direfund',
        'InvoiceCancelled'            => 'Invoice Dibatalkan',
        // Order & Product
        'OrderNew'                    => 'Order Baru',
        'OrderPaid'                   => 'Order Dibayar',
        'OrderCancelled'              => 'Order Dibatalkan',
        // Client
        'ClientAdd'                   => 'Klien Baru Mendaftar',
        'ClientChangePassword'        => 'Password Diubah',
        'ClientLogin'                 => 'Klien Login',
        // Ticket
        'TicketOpen'                  => 'Tiket Support Baru',
        'TicketUserReply'             => 'Balasan Tiket Klien',
        'TicketClose'                 => 'Tiket Ditutup',
        // Domain
        'DomainRegister'              => 'Domain Diregistrasi',
        'DomainRenewal'               => 'Domain Diperpanjang',
        'DomainExpired'               => 'Domain Kedaluwarsa',
        // Addon & Suspension
        'AddonActivated'              => 'Addon Diaktifkan',
        'ServiceSuspension'           => 'Layanan Disuspend',
        'ServiceUnsuspension'         => 'Layanan Di-unsuspend',
        'ServiceTerminated'           => 'Layanan Diterminasi',
    ];
}

/**
 * Template pesan default per event (Bahasa Indonesia, siap pakai).
 * Placeholder yang tersedia: {firstname}, {lastname}, {email}, {company},
 * {invoiceid}, {invoicenum}, {total}, {duedate}, {orderid}, {servicename},
 * {domain}, {ticketid}, {subject}, {invoice_link}, {clientarea_link}
 */
function wagateway_default_templates()
{
    return [
        'InvoiceCreated'          => "Halo {firstname} 👋\n\nInvoice #{invoicenum} telah dibuat.\n\n💰 Total: {total}\n📅 Jatuh tempo: {duedate}\n\nBayar sekarang:\n{invoice_link}\n\nTerima kasih! 🙏",
        'InvoicePaymentReminder'  => "Halo {firstname} 👋\n\nIni pengingat untuk invoice #{invoicenum} yang belum dibayar.\n\n💰 Total: {total}\n📅 Jatuh tempo: {duedate}\n\nBayar sekarang:\n{invoice_link}\n\nTerima kasih! 🙏",
        'InvoiceOverdueNotice'    => "⚠️ Halo {firstname},\n\nInvoice #{invoicenum} telah *JATUH TEMPO*.\n\n💰 Total: {total}\n\nMohon segera lakukan pembayaran agar layanan tidak terganggu:\n{invoice_link}\n\nTerima kasih! 🙏",
        'InvoicePaid'             => "✅ Pembayaran Berhasil!\n\nHalo {firstname}, terima kasih! Pembayaran untuk invoice #{invoicenum} sebesar {total} telah kami terima.\n\nSelamat menikmati layanan! 🎉",
        'InvoiceRefunded'         => "Halo {firstname},\n\nInvoice #{invoicenum} telah direfund sebesar {total}. Dana akan kembali sesuai metode pembayaran dalam 1-14 hari kerja.",
        'InvoiceCancelled'        => "Halo {firstname},\n\nInvoice #{invoicenum} telah dibatalkan. Jika ini keliru, silakan hubungi kami.",
        'OrderNew'                => "🛒 Order Diterima!\n\nHalo {firstname}, order Anda untuk *{servicename}* telah kami terima (Order #{orderid}).\n\nKami akan memproses order Anda sesegera mungkin.",
        'OrderPaid'               => "🎉 Order Berhasil Dibayar!\n\nHalo {firstname}, order *{servicename}* (#{orderid}) telah dibayar dan sedang diproses.\n\nDetail order:\n{clientarea_link}",
        'OrderCancelled'          => "Halo {firstname},\n\nOrder #{orderid} untuk {servicename} telah dibatalkan. Jika ada pertanyaan, silakan hubungi kami.",
        'ClientAdd'               => "🎉 Selamat Datang, {firstname}!\n\nAkun Anda di {company} berhasil dibuat.\n\nEmail: {email}\n\nSilakan login:\n{clientarea_link}\n\nTerima kasih telah bergabung! 🙏",
        'ClientChangePassword'    => "🔐 Halo {firstname},\n\nPassword akun Anda ({email}) baru saja diubah.\n\nJika ini BUKAN Anda, segera hubungi kami!",
        'ClientLogin'             => "👋 Halo {firstname},\n\nLogin ke akun Anda berhasil pada " . '{login_time}' . ".\n\nJika ini bukan Anda, segera amankan akun Anda.",
        'TicketOpen'              => "🎫 Tiket Baru #{ticketid}\n\nHalo {firstname}, tiket Anda \"{subject}\" telah kami terima.\n\nTim kami akan merespon sesegera mungkin.",
        'TicketUserReply'         => "💬 Balasan Tiket #{ticketid}\n\nHalo {firstname}, balasan Anda untuk tiket \"{subject}\" telah kami terima dan sedang ditindaklanjuti.",
        'TicketClose'             => "✅ Halo {firstname},\n\nTiket #{ticketid} \"{subject}\" telah ditutup. Jika masih ada kendala, silakan buka tiket baru.",
        'DomainRegister'          => "🌐 Halo {firstname},\n\nDomain *{domain}* berhasil diregistrasi! 🎉",
        'DomainRenewal'           => "🔄 Halo {firstname},\n\nDomain *{domain}* berhasil diperpanjang. Terima kasih! 🙏",
        'DomainExpired'           => "⚠️ Halo {firstname},\n\nDomain *{domain}* telah KEDALUWARSA. Segera perpanjang agar website & email tidak mati.",
        'AddonActivated'          => "✨ Halo {firstname},\n\nAddon untuk layanan Anda telah berhasil diaktifkan.",
        'ServiceSuspension'       => "⛔ Halo {firstname},\n\nLayanan *{servicename}* telah DISUSPEND karena invoice belum dibayar.\n\nAktifkan kembali dengan membayar:\n{invoice_link}",
        'ServiceUnsuspension'     => "✅ Halo {firstname},\n\nLayanan *{servicename}* telah diaktifkan kembali. Terima kasih! 🙏",
        'ServiceTerminated'       => "Halo {firstname},\n\nLayanan *{servicename}* telah diterminasi. Data akan dihapus sesuai kebijakan retensi kami.",
    ];
}

/**
 * Ambil template dari DB (fallback ke default).
 */
function wagateway_get_template($event)
{
    $row = Capsule::table('mod_wagateway_templates')->where('event', $event)->first();
    if ($row) {
        return ['message' => $row->message, 'enabled' => (bool) $row->enabled];
    }
    $defaults = wagateway_default_templates();
    return ['message' => $defaults[$event] ?? '', 'enabled' => false];
}

/**
 * Simpan template event.
 */
function wagateway_save_template($event, $message, $enabled)
{
    Capsule::table('mod_wagateway_templates')->updateOrInsert(
        ['event' => $event],
        [
            'message'    => $message,
            'enabled'    => $enabled ? 1 : 0,
            'updated_at' => date('Y-m-d H:i:s'),
        ]
    );
}

/**
 * Catat notifikasi ke log.
 */
function wagateway_log($event, $phone, $message, $status, $response = null)
{
    try {
        Capsule::table('mod_wagateway_logs')->insert([
            'event'       => $event,
            'phone'       => $phone,
            'message'     => mb_substr($message, 0, 1000),
            'status'      => $status,
            'response'    => $response ? mb_substr(json_encode($response), 0, 2000) : null,
            'created_at'  => date('Y-m-d H:i:s'),
            'updated_at'  => date('Y-m-d H:i:s'),
        ]);
    } catch (\Exception $e) {
        // Logging tidak boleh mengganggu flow WHMCS
    }
}

/**
 * Normalisasi nomor telepon ke format internasional (62xxx).
 */
function wagateway_normalize_phone($phone)
{
    $phone = preg_replace('/[^0-9]/', '', (string) $phone);
    if ($phone === '') {
        return '';
    }
    if (str_starts_with($phone, '08')) {
        $phone = '62' . substr($phone, 1);
    } elseif (str_starts_with($phone, '8')) {
        $phone = '62' . $phone;
    }
    return $phone;
}

/**
 * Render placeholder {xxx} dalam template.
 */
function wagateway_render_template($template, array $vars)
{
    $replace = [];
    foreach ($vars as $key => $value) {
        $replace['{' . $key . '}'] = (string) $value;
    }
    return strtr($template, $replace);
}
