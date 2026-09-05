# WhatsApp Gateway Enterprise — Module WHMCS

Addon module WHMCS untuk mengirim notifikasi WhatsApp otomatis untuk **23 event WHMCS** via WhatsApp Gateway Enterprise API.

## Fitur

- ✅ **23 Event Notifikasi** — Invoice (dibuat, reminder, jatuh tempo, lunas, refund, batal), Order (baru, dibayar, batal), Klien (registrasi, ganti password, login), Tiket (baru, balasan, ditutup), Domain (registrasi, perpanjangan, kedaluwarsa), Layanan (addon, suspend, unsuspend, terminasi)
- 🔘 **Tombol CTA Interaktif** — Notifikasi invoice dikirim dengan tombol "Bayar Sekarang" & "Client Area" (interactive message)
- � **23 Event Notifikasi** — Invoice, order, tiket, domain, suspend/unsuspend, dan lainnya
- ✏️ **Template Editor** — Edit pesan per event dari admin WHMCS dengan merge field (`{firstname}`, `{invoicenum}`, `{total}`, dll)
- 📜 **Log Notifikasi** — Riwayat semua notifikasi tersimpan di database WHMCS
- 🔌 **Test Koneksi & Test Kirim** — Verifikasi integrasi langsung dari admin area

## Persyaratan

- WHMCS 8.x atau lebih baru
- PHP 7.4+ dengan ekstensi `curl`
- Akun WhatsApp Gateway Enterprise dengan device terhubung + API Key aktif

## Instalasi

1. **Siapkan panel gateway**: pastikan device WhatsApp *Connected*, catat **Device ID**, dan buat **API Key** (`lpk_...`) di panel.
2. **Upload module**: ekstrak ZIP, upload folder `wagateway` ke `modules/addons/` WHMCS.
   Struktur akhir: `modules/addons/wagateway/wagateway.php`
3. **Aktifkan**: WHMCS Admin → *System Settings → Addon Modules* → **WhatsApp Gateway Enterprise** → *Activate*.
4. **Konfigurasi** (*Configure*):
   - **Gateway API URL** — URL panel gateway, contoh `https://wa.domain-anda.com`
   - **API Key** — token `lpk_...`
   - **Device ID** — ID device yang terhubung
   - Beri akses ke admin group → *Save*
5. **Verifikasi**: buka module → tab *Dashboard* → *Test Koneksi* → *Kirim Test Message*.

## Struktur File

```
wagateway/
├── wagateway.php                  # Entry point addon (config, activate, output)
├── hooks.php                      # Hook semua event WHMCS (notifikasi otomatis)
├── includes/
│   ├── helpers.php                # Template default, normalisasi nomor, logging
│   ├── GatewayApiClient.php       # HTTP client API v1 (send-text, send-button, devices)
│   ├── NotificationManager.php    # Orkestrasi template → render → kirim → log
│   └── ProvisioningService.php    # Auto-provisioning akun gateway (opsional)
└── views/
    └── admin.php                  # Admin UI (dashboard, template editor, logs, panduan)
```

## Merge Field Template

| Placeholder | Tersedia di event |
|---|---|
| `{firstname}` `{lastname}` `{email}` `{company}` | Semua event |
| `{invoiceid}` `{invoicenum}` `{total}` `{duedate}` `{invoice_link}` | Event invoice & suspend |
| `{orderid}` `{servicename}` | Event order & layanan |
| `{ticketid}` `{subject}` | Event tiket |
| `{domain}` | Event domain |
| `{clientarea_link}` | ClientAdd, OrderPaid |
| `{login_time}` | ClientLogin |

Format WhatsApp didukung di template: `*tebal*`, `_miring_`, `~coret~`, emoji.

## Troubleshooting

| Masalah | Solusi |
|---|---|
| Koneksi gagal / 401 | Cek API Key & URL. Pastikan API key aktif di panel gateway. |
| Device tidak ditemukan | Pastikan Device ID benar dan device *Connected* di panel. |
| Notifikasi tidak terkirim | Cek tab *Riwayat Notifikasi* untuk alasan (quota, nomor kosong, dll). |
| Nomor tidak terdeteksi | Isi nomor telepon klien di profil WHMCS (format `08xx` atau `62xxx`). |

## Dukungan

Dibuat oleh **zzamcode** untuk WhatsApp Gateway Enterprise.
