<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminPlanController extends Controller
{
    public function index()
    {
        $plans = Plan::withCount('users')->orderBy('sort_order')->orderBy('price')->get();

        return view('admin.plans.index', [
            'plans' => $plans,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:plans,name'],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'device_limit' => ['required', 'integer', 'min:0', 'max:100000'],
            'daily_message_limit' => ['required', 'integer', 'min:0', 'max:10000000'],
            'monthly_message_limit' => ['required', 'integer', 'min:0', 'max:100000000'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        // Jika plan ini dijadikan default, nonaktifkan default pada plan lain
        if (!empty($validated['is_default'])) {
            Plan::query()->update(['is_default' => false]);
        }

        Plan::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']) . '-' . strtolower(Str::random(4)),
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'device_limit' => $validated['device_limit'],
            'daily_message_limit' => $validated['daily_message_limit'],
            'monthly_message_limit' => $validated['monthly_message_limit'],
            'is_active' => $validated['is_active'] ?? true,
            'is_default' => $validated['is_default'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        auth()->user()->logActivity('admin.plan_create', "Admin membuat paket baru: {$validated['name']}");

        return back()->with('success', "Paket {$validated['name']} berhasil dibuat!");
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', 'unique:plans,name,'.$plan->id],
            'description' => ['nullable', 'string', 'max:500'],
            'price' => ['required', 'integer', 'min:0', 'max:100000000'],
            'duration_days' => ['required', 'integer', 'min:1', 'max:3650'],
            'device_limit' => ['required', 'integer', 'min:0', 'max:100000'],
            'daily_message_limit' => ['required', 'integer', 'min:0', 'max:10000000'],
            'monthly_message_limit' => ['required', 'integer', 'min:0', 'max:100000000'],
            'is_active' => ['nullable', 'boolean'],
            'is_default' => ['nullable', 'boolean'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:1000'],
        ]);

        // Jika plan ini dijadikan default, nonaktifkan default pada plan lain
        if (!empty($validated['is_default'])) {
            Plan::query()->where('id', '!=', $plan->id)->update(['is_default' => false]);
        }

        $plan->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'price' => $validated['price'],
            'duration_days' => $validated['duration_days'],
            'device_limit' => $validated['device_limit'],
            'daily_message_limit' => $validated['daily_message_limit'],
            'monthly_message_limit' => $validated['monthly_message_limit'],
            'is_active' => $validated['is_active'] ?? false,
            'is_default' => $validated['is_default'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
        ]);

        auth()->user()->logActivity('admin.plan_update', "Admin memperbarui paket: {$validated['name']}");

        return back()->with('success', "Paket {$validated['name']} berhasil diperbarui!");
    }

    public function destroy(Plan $plan)
    {
        // Plan sistem (Admin) tidak boleh dihapus
        if (in_array($plan->slug, ['admin'])) {
            return back()->withErrors(['error' => 'Paket sistem (Admin) tidak dapat dihapus.']);
        }

        $usersCount = $plan->users()->count();
        $planName = $plan->name;

        // Lepaskan user dari plan ini sebelum hapus (nullOnDelete)
        $plan->users()->update([
            'plan_id' => null,
            'plan_expires_at' => null,
        ]);

        $plan->delete();

        auth()->user()->logActivity('admin.plan_delete', "Admin menghapus paket: {$planName} ({$usersCount} user dilepas)");

        return back()->with('success', "Paket {$planName} berhasil dihapus! {$usersCount} user yang terkait telah dilepas dari paket ini.");
    }

    /**
     * Assign plan ke user tertentu (dari halaman manajemen user).
     */
    public function assign(Request $request, User $user)
    {
        $validated = $request->validate([
            'plan_id' => ['required', 'exists:plans,id'],
        ]);

        $plan = Plan::findOrFail($validated['plan_id']);

        $user->assignPlan($plan, auth()->user()->email);

        auth()->user()->logActivity('admin.plan_assign', "Admin menetapkan paket {$plan->name} untuk user {$user->email}");

        return back()->with('success', "User {$user->name} berhasil ditetapkan ke paket {$plan->name} (aktif {$plan->duration_days} hari).");
    }

    /**
     * Hapus plan dari user (kembali ke limit manual).
     */
    public function revoke(User $user)
    {
        if ($user->plan_id) {
            $user->subscriptions()->where('status', 'active')->update(['status' => 'cancelled']);

            $user->update([
                'plan_id' => null,
                'plan_expires_at' => null,
            ]);

            auth()->user()->logActivity('admin.plan_revoke', "Admin mencabut paket dari user {$user->email}");

            return back()->with('success', "Paket user {$user->name} berhasil dicabut. User kembali menggunakan limit manual.");
        }

        return back()->with('error', 'User ini tidak memiliki paket aktif.');
    }
}
