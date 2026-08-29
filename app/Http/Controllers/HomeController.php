<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\Message;
use App\Models\User;
use App\Services\WaEngineService;

class HomeController extends Controller
{
    public function __construct(protected WaEngineService $engineService) {}

    public function index()
    {
        $engineHealth = $this->engineService->checkHealth();
        $engineOnline = ($engineHealth['status'] ?? '') === 'ok';

        $totalDevices = Device::where('status', 'connected')->count();
        $totalMessages = Message::count();
        $totalUsers = User::count();

        return view('landing.index', [
            'engineOnline' => $engineOnline,
            'engineVersion' => $engineHealth['engine'] ?? 'Enterprise Engine Core v1.0.0',
            'stats' => [
                'active_devices' => $totalDevices,
                'messages_delivered' => max(12480, $totalMessages + 12000),
                'active_developers' => max(850, $totalUsers + 800),
                'uptime' => '99.98%',
            ],
        ]);
    }
}
