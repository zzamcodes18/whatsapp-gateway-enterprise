<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Device;
use App\Models\Message;
use App\Models\SystemSetting;
use App\Models\User;
use App\Services\WaEngineService;

class AdminDashboardController extends Controller
{
    public function __construct(protected WaEngineService $engineService) {}

    public function index()
    {
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();

        $totalDevices = Device::count();
        $connectedDevices = Device::where('status', 'connected')->count();

        $todayMessages = Message::whereDate('created_at', today())->count();
        $totalMessages = Message::count();

        $botServerDeviceId = SystemSetting::get('otp_server_device_id');
        $botDevice = $botServerDeviceId ? Device::with('user')->find($botServerDeviceId) : null;

        $recentUsers = User::latest()->take(5)->get();
        $recentLogs = ActivityLog::with('user')->latest()->take(8)->get();
        $engineHealth = $this->engineService->checkHealth();

        return view('admin.dashboard.index', [
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'totalDevices' => $totalDevices,
            'connectedDevices' => $connectedDevices,
            'todayMessages' => $todayMessages,
            'totalMessages' => $totalMessages,
            'botDevice' => $botDevice,
            'recentUsers' => $recentUsers,
            'recentLogs' => $recentLogs,
            'engineOnline' => ($engineHealth['status'] ?? '') === 'ok',
            'engineHealth' => $engineHealth,
        ]);
    }
}
