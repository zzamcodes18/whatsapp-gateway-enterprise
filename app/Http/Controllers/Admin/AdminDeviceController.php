<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\User;
use App\Services\WaEngineService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminDeviceController extends Controller
{
    public function __construct(protected WaEngineService $engineService) {}

    public function index(Request $request)
    {
        $query = Device::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('phone_number', 'like', $search)
                    ->orWhere('session_id', 'like', $search)
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', $search)
                            ->orWhere('email', 'like', $search);
                    });
            });
        }

        $devices = $query->paginate(15)->withQueryString();
        $engineHealth = $this->engineService->checkHealth();
        $allUsers = User::orderBy('name')->get(['id', 'name', 'email']);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'devices' => $devices,
            ]);
        }

        return view('admin.devices.index', [
            'devices' => $devices,
            'allUsers' => $allUsers,
            'engineOnline' => ($engineHealth['status'] ?? '') === 'ok',
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:100'],
            'connection_type' => ['required', 'in:qr,pairing_code'],
            'phone_number' => ['nullable', 'string', 'max:25'],
        ]);

        $cleanPhone = null;
        if (! empty($validated['phone_number'])) {
            $cleanPhone = preg_replace('/[^0-9]/', '', $validated['phone_number']);
            if (str_starts_with($cleanPhone, '08')) {
                $cleanPhone = '62'.substr($cleanPhone, 1);
            }
        }

        // UUIDv4 Session ID
        $sessionId = (string) Str::uuid();

        $device = Device::create([
            'user_id' => $validated['user_id'],
            'session_id' => $sessionId,
            'name' => $validated['name'],
            'phone_number' => $cleanPhone ?? ($validated['phone_number'] ?? null),
            'connection_type' => $validated['connection_type'],
            'status' => 'connecting',
        ]);

        auth()->user()->logActivity('admin.device_create', "Admin membuat device baru '{$device->name}' (User ID: {$device->user_id})");

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

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Device '{$device->name}' berhasil ditambahkan.",
                'device' => $device->fresh()->load('user'),
            ]);
        }

        return back()->with('success', "Perangkat '{$device->name}' berhasil ditambahkan.");
    }

    public function update(Request $request, Device $device)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'name' => ['required', 'string', 'max:100'],
            'phone_number' => ['nullable', 'string', 'max:25'],
            'status' => ['required', 'in:disconnected,connecting,qr_ready,pairing_ready,connected'],
        ]);

        $device->update([
            'user_id' => $validated['user_id'],
            'name' => $validated['name'],
            'phone_number' => $validated['phone_number'] ?? $device->phone_number,
            'status' => $validated['status'],
        ]);

        auth()->user()->logActivity('admin.device_update', "Admin mengedit device '{$device->name}' (ID: {$device->id})");

        if ($request->expectsJson() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Device '{$device->name}' berhasil diperbarui.",
                'device' => $device->fresh()->load('user'),
            ]);
        }

        return back()->with('success', "Device '{$device->name}' berhasil diperbarui.");
    }

    public function forceDisconnect(Device $device)
    {
        $this->engineService->logoutSession($device->session_id);

        $device->update([
            'status' => 'disconnected',
            'qr_code' => null,
            'pairing_code' => null,
        ]);

        auth()->user()->logActivity('admin.device_disconnect', "Admin memaksa disconnect device '{$device->name}'");

        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Koneksi device '{$device->name}' berhasil diputuskan.",
            ]);
        }

        return back()->with('success', "Koneksi device '{$device->name}' berhasil diputuskan.");
    }

    public function restart(Device $device)
    {
        $device->update([
            'status' => 'connecting',
            'qr_code' => null,
            'pairing_code' => null,
        ]);

        $this->engineService->startSession(
            $device->session_id,
            $device->connection_type,
            $device->phone_number,
            true
        );

        auth()->user()->logActivity('admin.device_restart', "Admin me-restart sesi device '{$device->name}'");

        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Sesi device '{$device->name}' sedang dimulai ulang.",
            ]);
        }

        return back()->with('success', "Sesi device '{$device->name}' sedang dimulai ulang.");
    }

    public function destroy(Device $device)
    {
        $this->engineService->logoutSession($device->session_id);

        $name = $device->name;
        $device->delete();

        auth()->user()->logActivity('admin.device_delete', "Admin menghapus device '{$name}'");

        if (request()->expectsJson() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Device '{$name}' berhasil dihapus.",
            ]);
        }

        return back()->with('success', "Device '{$name}' berhasil dihapus.");
    }
}
