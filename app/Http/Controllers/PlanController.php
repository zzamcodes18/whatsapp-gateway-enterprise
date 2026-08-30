<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    /**
     * Halaman upgrade plan untuk user.
     * Menampilkan daftar paket yang tersedia beserta paket aktif user.
     *
     * CATATAN: Sistem pembayaran belum terintegrasi.
     * Untuk sementara, upgrade dilakukan manual via admin.
     */
    public function index()
    {
        $user = auth()->user();

        $plans = Plan::active()
            ->where('slug', '!=', 'admin')
            ->orderBy('sort_order')
            ->orderBy('price')
            ->get();

        $currentPlan = $user->plan;

        return view('plans.index', compact('plans', 'currentPlan'));
    }
}
