<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        \App\Models\Admin::buatDefaultJikaKosong();

        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        \App\Models\Admin::buatDefaultJikaKosong();

        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::guard('admin')->check()) {
            return redirect()->intended(route('admin.dashboard', absolute: false));
        } elseif (Auth::guard('guru')->check()) {
            return redirect()->intended(route('guru.dashboard', absolute: false));
        } else {
            return redirect()->intended(route('ortu.dashboard', absolute: false));
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();
        Auth::guard('guru')->logout();
        Auth::guard('siswa')->logout();
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
