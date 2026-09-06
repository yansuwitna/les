<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class PasswordController extends Controller
{
    /**
     * Update the user's password.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = \Illuminate\Support\Facades\Auth::guard('admin')->user()
            ?? \Illuminate\Support\Facades\Auth::guard('guru')->user()
            ?? \Illuminate\Support\Facades\Auth::guard('siswa')->user()
            ?? $request->user();

        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        $currentHashed = $user->kata_sandi ?? $user->password;
        if (!Hash::check($validated['current_password'], $currentHashed)) {
            return back()->withErrors(['current_password' => 'Kata sandi saat ini tidak sesuai dengan data kami.']);
        }

        $user->password = Hash::make($validated['password']);
        $user->save();

        return back()->with('success', 'Kata sandi akun berhasil diperbarui.');
    }
}
