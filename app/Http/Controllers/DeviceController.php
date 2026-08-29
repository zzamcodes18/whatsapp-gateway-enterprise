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

    public function restart(Device $device)
    {
        $this->authorizeAccess($device);

        $device->update([
            'status' => 'connecting',
            'qr_code' => null,
            'pairing_code' => null,
        ]);

        $this->engineService->startSession(
            $device->session_id,
            $device->connection_type,
            $device->phone_number,
            true // forceRestart
        );

        Auth::user()->logActivity('device.restart', "Memulai ulang koneksi device '{$device->name}'");

        if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Koneksi device sedang dimulai ulang.',
                'device' => $device->fresh(),
            ]);
        }

        return back()->with('success', 'Koneksi device sedang dimulai ulang.');
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

        Auth::user()->logActivity('device.disconnect', "Memutuskan koneksi device '{$device->name}'");

        if (request()->expectsJson() || request()->wantsJson() || request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Koneksi device berhasil diputuskan.',
                'device' => $device->fresh(),
            ]);
        }

        return back()->with('success', 'Koneksi device berhasil diputuskan.');
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
