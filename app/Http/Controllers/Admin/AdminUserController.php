<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount(['devices', 'messages'])->latest();

        if ($request->filled('search')) {
            $search = '%'.$request->query('search').'%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('phone_number', 'like', $search);
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->query('role'));
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->query('status') === 'active');
        }

        $users = $query->paginate(12)->withQueryString();

        return view('admin.users.index', [
            'users' => $users,
            'plans' => Plan::active()->orderBy('sort_order')->orderBy('price')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:user,admin'],
            'password' => ['required', Password::min(8)],
            'device_limit' => ['required', 'integer', 'min:0', 'max:100000'],
            'daily_message_limit' => ['required', 'integer', 'min:0', 'max:100000'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
            'device_limit' => $validated['device_limit'],
            'daily_message_limit' => $validated['daily_message_limit'],
            'is_active' => true,
        ]);

        // Admin otomatis dapat plan Admin permanen (unlimited)
        if ($user->isAdmin()) {
            $adminPlan = \App\Models\Plan::where('slug', 'admin')->where('is_active', true)->first();
            if ($adminPlan) {
                $user->assignPlan($adminPlan, auth()->user()->email, 'Auto-assign plan Admin saat pembuatan akun admin');
            }
        }

        auth()->user()->logActivity('admin.user_create', "Admin membuat pengguna baru: {$user->email}");

        return back()->with('success', "Pengguna {$user->name} berhasil ditambahkan!");
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone_number' => ['nullable', 'string', 'max:30'],
            'role' => ['required', 'in:user,admin'],
            'device_limit' => ['required', 'integer', 'min:0', 'max:100000'],
            'daily_message_limit' => ['required', 'integer', 'min:0', 'max:100000'],
            'is_active' => ['required', 'boolean'],
            'password' => ['nullable', Password::min(8)],
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
            'role' => $validated['role'],
            'device_limit' => $validated['device_limit'],
            'daily_message_limit' => $validated['daily_message_limit'],
            'is_active' => $validated['is_active'],
        ];

        if (! empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $wasAdmin = $user->isAdmin();

        $user->update($updateData);

        // Promosi ke admin: otomatis dapat plan Admin permanen (unlimited)
        if (! $wasAdmin && $user->isAdmin()) {
            $adminPlan = \App\Models\Plan::where('slug', 'admin')->where('is_active', true)->first();
            if ($adminPlan) {
                $user->assignPlan($adminPlan, auth()->user()->email, 'Auto-assign plan Admin saat promosi role ke admin');
            }
        }

        // Demosi dari admin ke user: plan kembali ke plan default (Free)
        if ($wasAdmin && ! $user->isAdmin()) {
            $defaultPlan = \App\Models\Plan::where('is_default', true)->where('is_active', true)->first();
            if ($defaultPlan) {
                $user->assignPlan($defaultPlan, auth()->user()->email, 'Auto-assign plan default saat demosi role admin ke user');
            } else {
                // Tidak ada plan default: lepas plan sepenuhnya (kembali ke limit manual)
                $user->subscriptions()->where('status', 'active')->update(['status' => 'cancelled']);
                $user->update([
                    'plan_id' => null,
                    'plan_expires_at' => null,
                ]);
            }
        }

        auth()->user()->logActivity('admin.user_update', "Admin memperbarui data pengguna: {$user->email}");

        return back()->with('success', "Data pengguna {$user->name} berhasil diperbarui!");
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menonaktifkan akun sendiri.']);
        }

        $user->update(['is_active' => ! $user->is_active]);

        $status = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        auth()->user()->logActivity('admin.user_status', "Status pengguna {$user->email} diubah menjadi {$status}");

        return back()->with('success', "Akun {$user->name} berhasil {$status}.");
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun sendiri.']);
        }

        $name = $user->name;
        $user->delete();

        auth()->user()->logActivity('admin.user_delete', "Admin menghapus pengguna: {$name}");

        return back()->with('success', "Pengguna {$name} berhasil dihapus.");
    }
}
