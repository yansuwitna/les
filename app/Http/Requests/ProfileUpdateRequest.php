<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $user = Auth::guard('admin')->user()
            ?? Auth::guard('guru')->user()
            ?? Auth::guard('siswa')->user()
            ?? $this->user();

        $userId = $user?->id;

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'nama' => ['required', 'string', 'max:255'],
            'username' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('admin', 'username')->ignore($userId),
            ],
            'email' => ['nullable', 'string', 'max:255'],
            'foto' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'telepon' => ['nullable', 'string', 'max:25'],
            'nama_wali' => ['nullable', 'string', 'max:255'],
            'telepon_wali' => ['nullable', 'string', 'max:25'],
        ];
    }
}
