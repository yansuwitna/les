<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = Auth::guard('admin')->user()
            ?? Auth::guard('guru')->user()
            ?? Auth::guard('siswa')->user()
            ?? $request->user();

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'userData' => $user,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = Auth::guard('admin')->user()
            ?? Auth::guard('guru')->user()
            ?? Auth::guard('siswa')->user()
            ?? $request->user();

        $data = $request->validated();
        $nama = $data['nama'] ?? $data['name'] ?? null;
        if ($nama) {
            $user->nama = $nama;
        }

        $role = $user->peran ?? $user->role ?? null;
        if ($user instanceof \App\Models\Admin || $role === 'admin' || isset($user->username)) {
            if (!empty($data['username'])) {
                $user->username = trim($data['username']);
            }
            if (!empty($data['email'])) {
                $user->email = trim($data['email']);
            }
        }

        if ($user instanceof \App\Models\Guru || $role === 'guru') {
            if (isset($data['telepon'])) {
                $user->telepon = $data['telepon'];
            }
            if (isset($data['email'])) {
                $user->email = $data['email'];
            }
        }

        if ($user instanceof \App\Models\Siswa || $role === 'ortu' || $role === 'siswa') {
            if (isset($data['nama_wali'])) {
                $user->nama_wali = $data['nama_wali'];
            }
            if (isset($data['telepon_wali'])) {
                $user->telepon_wali = $data['telepon_wali'];
            }
        }

        if ($request->hasFile('foto')) {
            if (!empty($user->foto) && \Illuminate\Support\Facades\Storage::disk('public')->exists($user->foto)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->foto);
            }
            $user->foto = $request->file('foto')->store('foto_profil', 'public');
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
