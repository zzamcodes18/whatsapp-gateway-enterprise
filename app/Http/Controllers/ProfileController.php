<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Tampilkan halaman edit profil pengguna.
     */
    public function edit(Request $request): View
    {
        /** @var User $user */
        $user = $request->user();
        $user->loadCount(['devices', 'messages']);

        return view('profile.index', [
            'user' => $user,
        ]);
    }

    /**
     * Perbarui informasi profil (nama, email, nomor telepon).
     */
    public function updateInformation(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'phone_number' => ['nullable', 'string', 'max:30'],
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone_number' => $validated['phone_number'] ?? null,
        ]);

        $user->logActivity('user.profile_update', 'Memperbarui informasi profil pribadi');

        return redirect()->back()->with('success', 'Informasi profil berhasil diperbarui!');
    }

    /**
     * Perbarui foto profil / avatar pengguna.
     */
    public function updateAvatar(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($request->boolean('remove_avatar')) {
            if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $user->avatar);
                Storage::disk('public')->delete($oldPath);
            }
            $user->update(['avatar' => null]);
            $user->logActivity('user.avatar_remove', 'Menghapus foto profil');

            return redirect()->back()->with('success', 'Foto profil berhasil dihapus!');
        }

        $request->validate([
            'avatar' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:2048'],
        ]);

        // Hapus foto terdahulu jika berupa file di storage public
        if ($user->avatar && str_starts_with($user->avatar, '/storage/')) {
            $oldPath = str_replace('/storage/', '', $user->avatar);
            Storage::disk('public')->delete($oldPath);
        }

        $file = $request->file('avatar');
        $mime = $file->getMimeType() ?: 'image/png';
        $base64Data = 'data:' . $mime . ';base64,' . base64_encode(file_get_contents($file->getRealPath()));

        $user->update(['avatar' => $base64Data]);
        $user->logActivity('user.avatar_update', 'Memperbarui foto profil baru');

        return redirect()->back()->with('success', 'Foto profil berhasil diperbarui!');
    }

    /**
     * Perbarui kata sandi / password akun.
     */
    public function updatePassword(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', Password::min(8)->mixedCase()->numbers(), 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors([
                'current_password' => 'Password saat ini yang Anda masukkan salah.',
            ]);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        $user->logActivity('user.password_update', 'Mengubah password kata sandi akun');

        return redirect()->back()->with('success', 'Password kata sandi akun Anda berhasil diperbarui!');
    }
}
