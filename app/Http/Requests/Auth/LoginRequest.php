<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string'],
            'password' => ['nullable', 'string'],
            'kata_sandi' => ['nullable', 'string'],
            'role' => ['nullable', 'string', 'in:admin,guru,ortu,siswa'],
            'peran' => ['nullable', 'string', 'in:admin,guru,ortu,siswa'],
        ];
    }

    public function messages(): array
    {
        return [
            'username.required' => 'Identitas masuk (Username / NIK / Nomor Siswa) wajib diisi.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
            'password.required' => 'Kata sandi wajib diisi.',
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        $identifier = trim((string) $this->input('username'));
        $password = (string) ($this->input('kata_sandi') ?: $this->input('password'));
        $role = $this->input('peran') ?: $this->input('role') ?: 'admin';

        if (empty($password)) {
            throw ValidationException::withMessages([
                'kata_sandi' => 'Kata sandi wajib diisi untuk melanjutkan masuk.',
                'password' => 'Kata sandi wajib diisi untuk melanjutkan masuk.',
            ]);
        }

        $authenticated = false;

        if ($role === 'admin') {
            \App\Models\Admin::buatDefaultJikaKosong();

            $authenticated = Auth::guard('admin')->attempt(['username' => $identifier, 'password' => $password], $this->boolean('remember'));
            if (!$authenticated) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'username' => 'Username admin atau kata sandi tidak cocok. Silakan periksa kembali (Akun awal: admin / admin).',
                ]);
            }
        } elseif ($role === 'guru') {
            $authenticated = Auth::guard('guru')->attempt(['nik' => $identifier, 'password' => $password], $this->boolean('remember'));
            if (!$authenticated) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'username' => '16 digit NIK atau kata sandi guru tidak sesuai. Pastikan NIK terdaftar atau hubungi administrator.',
                ]);
            }
            if (!Auth::guard('guru')->user()->aktif) {
                Auth::guard('guru')->logout();
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'username' => 'Akun guru Anda saat ini berstatus nonaktif. Silakan hubungi administrator bimbingan belajar.',
                ]);
            }
        } elseif ($role === 'ortu' || $role === 'siswa') {
            $authenticated = Auth::guard('siswa')->attempt(['nomor_siswa' => $identifier, 'password' => $password], $this->boolean('remember'));
            if (!$authenticated) {
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'username' => '10 digit nomor siswa atau kata sandi tidak cocok. Pastikan nomor siswa terdaftar atau hubungi pengajar.',
                ]);
            }
            if (!Auth::guard('siswa')->user()->aktif) {
                Auth::guard('siswa')->logout();
                RateLimiter::hit($this->throttleKey());
                throw ValidationException::withMessages([
                    'username' => 'Akun siswa ini saat ini berstatus nonaktif. Silakan hubungi administrator bimbingan belajar.',
                ]);
            }
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    public function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'username' => "Terlalu banyak percobaan masuk yang gagal. Silakan tunggu {$seconds} detik sebelum mencoba lagi.",
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->string('username')).'|'.$this->ip());
    }
}
