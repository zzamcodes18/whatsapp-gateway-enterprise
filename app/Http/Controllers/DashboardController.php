<?php

namespace App\Http\Controllers;

use App\Services\WaEngineService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(protected WaEngineService $engineService) {}

    public function index()
    {
        $user = Auth::user();

        $devices = $user->devices()->latest()->get();
        $totalDevices = $devices->count();
        $connectedDevices = $devices->where('status', 'connected')->count();

        $todayMessages = $user->messages()
            ->whereDate('created_at', today())
            ->count();

        $totalMessages = $user->messages()->count();
        $successfulMessages = $user->messages()->whereIn('status', ['sent', 'delivered', 'read'])->count();
        $deliveryRate = $totalMessages > 0 ? round(($successfulMessages / $totalMessages) * 100, 1) : 100;

        $recentMessages = $user->messages()->with('device')->latest()->take(6)->get();
        $recentLogs = $user->activityLogs()->latest()->take(5)->get();
        $apiKeysCount = $user->apiKeys()->where('is_active', true)->count();

        $engineHealth = $this->engineService->checkHealth();
        $engineOnline = ($engineHealth['status'] ?? '') === 'ok';

        return view('dashboard.index', [
            'user' => $user,
            'devices' => $devices,
            'totalDevices' => $totalDevices,
            'connectedDevices' => $connectedDevices,
            'todayMessages' => $todayMessages,
            'totalMessages' => $totalMessages,
            'deliveryRate' => $deliveryRate,
            'recentMessages' => $recentMessages,
            'recentLogs' => $recentLogs,
            'apiKeysCount' => $apiKeysCount,
            'engineOnline' => $engineOnline,
            'engineHealth' => $engineHealth,
        ]);
    }
}
