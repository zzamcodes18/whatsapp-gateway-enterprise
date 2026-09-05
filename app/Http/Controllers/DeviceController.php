<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Services\WaEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DeviceController extends Controller
{
    public function __construct(protected WaEngineService $engineService) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $devices = $user->devices()->latest()->paginate(12);
        $engineHealth = $this->engineService->checkHealth();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'devices' => $devices,
                'engineOnline' => ($engineHealth['status'] ?? '') === 'ok',
            ]);
        }

        return view('devices.index', [
            'devices' => $devices,
            'engineOnline' => ($engineHealth['status'] ?? '') === 'ok',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'connection_type' => ['required', 'in:qr,pairing_code'],
            'phone_number' => ['nullable', 'string', 'max:25'],
        ]);

        $user = Auth::user();

        if (! $user->canCreateDevice()) {
            $msg = "Batas maksimum perangkat Anda tercapai ({$user->device_limit} unit). Silakan hubungi admin untuk meningkatkan kuota.";
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $msg], 403);
            }

            return back()->withErrors(['limit' => $msg]);
        }

        // Format clean phone number if pairing code is selected
        $cleanPhone = null;
        if ($validated['connection_type'] === 'pairing_code') {
            if (empty($validated['phone_number'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Nomor WhatsApp wajib diisi jika menggunakan metode Pairing Code.',
                ], 422);
            }
            $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone_number']);
            if (str_starts_with($cleanPhone, '08')) {
                $cleanPhone = '62'.substr($cleanPhone, 1);
            }
        }

        // Generate UUIDv4 for connection / session ID
        $sessionId = (string) Str::uuid();

        $device = $user->devices()->create([
            'session_id' => $sessionId,
            'name' => $validated['name'],
            'phone_number' => $cleanPhone ?? ($validated['phone_number'] ?? null),
            'connection_type' => $validated['connection_type'],
            'status' => 'connecting',
        ]);

        $user->logActivity('device.create', "Membuat device baru '{$device->name}' (Metode: {$device->connection_type})", [
            'device_id' => $device->id,
            'session_id' => $sessionId,
        ]);

        // Start session on Baileys Engine
        $engineResult = $this->engineService->startSession(
            $sessionId,
            $validated['connection_type'],
            $cleanPhone
        );

        if (! empty($engineResult['data']['qr'])) {
            $device->update([
                'qr_code' => $engineResult['data']['qr'],
                'status' => 'qr_ready',
            ]);
        }

        if (! empty($engineResult['data']['pairingCode'])) {
            $device->update([
                'pairing_code' => $engineResult['data']['pairingCode'],
                'status' => 'pairing_ready',
            ]);
        }

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Device berhasil diinisialisasi.',
                'device' => $device->fresh(),
                'engine' => $engineResult,
            ]);
        }

        return redirect()->route('devices.index')->with('success', 'Device berhasil ditambahkan.');
    }

    public function update(Request $request, Device $device)
    {
        $this->authorizeAccess($device);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:25'],
        ]);

        $device->update([
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'] ?? $device->phone_number,
        ]);

        Auth::user()->logActivity('device.update', "Memperbarui informasi device '{$device->name}'");

        if ($request->expectsJson() || $request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Perangkat '{$device->name}' berhasil diperbarui.",
                'device' => $device->fresh(),
            ]);
        }

        return back()->with('success', "Perangkat '{$device->name}' berhasil diperbarui.");
    }

    public function show(Device $device)
    {
        $this->authorizeAccess($device);
        $engineStatus = $this->engineService->getSessionStatus($device->session_id);

        $recentMessages = $device->messages()->latest()->take(10)->get();

        return view('devices.show', [
            'device' => $device,
            'engineStatus' => $engineStatus,
            'recentMessages' => $recentMessages,
        ]);
    }

    /**
     * Stop sesi device — matikan koneksi tanpa menghapus kredensial.
     */
    public function stop(Device $device)
    {
        $this->authorizeAccess($device);

        $result = $this->engineService->stopSession($device->session_id);

        $device->update([
            'status' => 'stopped',
            'qr_code' => null,
            'pairing_code' => null,
        ]);

        Auth::user()->logActivity('device.stop', "Menghentikan sesi device '{$device->name}' (kredensial tersimpan)");

        if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi device berhasil dihentikan. Kredensial tetap tersimpan.',
                'device' => $device->fresh(),
                'engine' => $result,
            ]);
        }

        return back()->with('success', 'Sesi device berhasil dihentikan. Kredensial tetap tersimpan — gunakan Start untuk mengaktifkan kembali.');
    }

    /**
     * Start ulang sesi device yang dihentikan.
     */
    public function start(Device $device)
    {
        $this->authorizeAccess($device);

        $result = $this->engineService->startStoppedSession($device->session_id);

        if (empty($result['success'])) {
            $msg = $result['message'] ?? 'Gagal memulai sesi.';
            if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }

            return back()->withErrors(['engine' => $msg]);
        }

        $device->update([
            'status' => 'connecting',
            'qr_code' => null,
            'pairing_code' => null,
        ]);

        Auth::user()->logActivity('device.start', "Memulai ulang sesi device '{$device->name}'");

        if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi device sedang dimulai ulang.',
                'device' => $device->fresh(),
            ]);
        }

        return back()->with('success', 'Sesi device sedang dimulai ulang.');
    }

    /**
     * Toggle fitur device (always online, typing indicator, auto read, block calls).
     */
    public function updateFeatures(Request $request, Device $device)
    {
        $this->authorizeAccess($device);

        $validated = $request->validate([
            'always_online'    => ['nullable', 'boolean'],
            'typing_indicator' => ['nullable', 'boolean'],
            'auto_read'        => ['nullable', 'boolean'],
            'block_calls'      => ['nullable', 'boolean'],
        ]);

        if (empty(array_filter($validated, fn ($v) => ! is_null($v)))) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada fitur yang diubah.',
            ], 422);
        }

        $device->fill($validated)->save();

        // Sinkronkan ke engine (best-effort, hanya jika sesi aktif)
        $engineResult = ['success' => false, 'skipped' => true];
        if ($device->status === 'connected') {
            $engineResult = $this->engineService->updateSessionFeatures($device->session_id, [
                'alwaysOnline'    => $device->always_online,
                'typingIndicator' => $device->typing_indicator,
                'autoRead'        => $device->auto_read,
                'blockCalls'      => $device->block_calls,
            ]);
        }

        $enabledFeatures = array_keys(array_filter($validated, fn ($v) => $v));
        Auth::user()->logActivity('device.features', "Mengubah fitur device '{$device->name}': ".($enabledFeatures ? implode(', ', $enabledFeatures) : 'semua dimatikan'), [
            'device_id' => $device->id,
            'features' => $validated,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Fitur device berhasil diperbarui.',
            'device' => $device->fresh(),
            'engine' => $engineResult,
        ]);
    }

    /**
     * Console logs device (dari engine, filter per session).
     */
    public function consoleLogs(Device $device)
    {
        $this->authorizeAccess($device);

        $limit = (int) request()->query('limit', 100);
        $limit = max(10, min($limit, 300));

        $result = $this->engineService->getConsoleLogs($device->session_id, $limit);

        return response()->json([
            'success' => ! empty($result['success']),
            'logs' => $result['data'] ?? [],
            'message' => $result['message'] ?? null,
        ]);
    }

    public function status(Device $device)
    {
        $this->authorizeAccess($device);

        $engineStatus = $this->engineService->getSessionStatus($device->session_id);

        if (! empty($engineStatus['data'])) {
            $data = $engineStatus['data'];
            $updates = [];

            if (! empty($data['status'])) {
                $updates['status'] = $data['status'];
            }
            if (! empty($data['qr'])) {
                $updates['qr_code'] = $data['qr'];
            }
            if (! empty($data['pairingCode'])) {
                $updates['pairing_code'] = $data['pairingCode'];
            }
            if (! empty($data['info'])) {
                $updates['metadata'] = $data['info'];
                if (! empty($data['info']['phone'])) {
                    $updates['phone_number'] = $data['info']['phone'];
                }
                if ($data['status'] === 'connected' && ! $device->connected_at) {
                    $updates['connected_at'] = now();
                }
            }

            if (! empty($updates)) {
                $device->update($updates);
            }
        }

        return response()->json([
            'success' => true,
            'device' => $device->fresh(),
            'engine' => $engineStatus,
        ]);
    }

    public function disconnect(Device $device)
    {
        $this->authorizeAccess($device);

        $this->engineService->logoutSession($device->session_id);

        $device->update([
            'status' => 'disconnected',
            'qr_code' => null,
            'pairing_code' => null,
        ]);

        Auth::user()->logActivity('device.disconnect', "Memutuskan & menghapus sesi WhatsApp device '{$device->name}'");

        if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Sesi WhatsApp berhasil diputuskan & dihapus. Device harus di-pairing ulang.',
                'device' => $device->fresh(),
            ]);
        }

        return back()->with('success', 'Sesi WhatsApp berhasil diputuskan & dihapus. Device harus di-pairing ulang.');
    }

    public function destroy(Device $device)
    {
        $this->authorizeAccess($device);

        // Terminate session on engine
        $this->engineService->logoutSession($device->session_id);

        $deviceName = $device->name;
        $device->delete();

        Auth::user()->logActivity('device.delete', "Menghapus device '{$deviceName}'");

        if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Device '{$deviceName}' berhasil dihapus.",
            ]);
        }

        return redirect()->route('devices.index')->with('success', 'Device berhasil dihapus.');
    }

    protected function authorizeAccess(Device $device): void
    {
        if ($device->user_id !== Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }
    }
}
