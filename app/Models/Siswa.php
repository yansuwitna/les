<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Facades\Crypt;

class Siswa extends Authenticatable
{
    use Notifiable;

    protected $table = 'siswa';

    protected $guarded = [];

    protected $appends = ['peran', 'role', 'name', 'username', 'foto_url', 'encrypted_id'];

    public function getEncryptedIdAttribute(): string
    {
        return Crypt::encryptString((string)$this->id);
    }

    protected $hidden = [
        'kata_sandi',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'kata_sandi' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    public function getPeranAttribute(): string
    {
        return 'ortu';
    }

    public function getRoleAttribute(): string
    {
        return 'ortu';
    }

    public function getUsernameAttribute(): ?string
    {
        return $this->attributes['nomor_siswa'] ?? null;
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function getStudentNumberAttribute(): ?string
    {
        return $this->attributes['nomor_siswa'] ?? null;
    }

    public function setStudentNumberAttribute($value): void
    {
        $this->attributes['nomor_siswa'] = $value;
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->attributes['kata_sandi'] ?? null;
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['kata_sandi'] = $value;
    }

    public function getPhoneAttribute(): ?string
    {
        return $this->attributes['no_hp'] ?? null;
    }

    public function setPhoneAttribute($value): void
    {
        $this->attributes['no_hp'] = $value;
    }

    public function getAddressAttribute(): ?string
    {
        return $this->attributes['alamat'] ?? null;
    }

    public function setAddressAttribute($value): void
    {
        $this->attributes['alamat'] = $value;
    }

    public function getPhotoAttribute(): ?string
    {
        return $this->attributes['foto'] ?? null;
    }

    public function setPhotoAttribute($value): void
    {
        $this->attributes['foto'] = $value;
    }

    public function getFotoUrlAttribute(): ?string
    {
        if (!empty($this->foto)) {
            if (str_starts_with($this->foto, 'http://') || str_starts_with($this->foto, 'https://')) {
                return $this->foto;
            }
            return asset('storage/' . $this->foto);
        }
        return null;
    }

    // Compatibility getters
    public function getPenggunaAttribute()
    {
        return (object)[
            'nama' => $this->attributes['nama_wali'] ?? ('Wali dari ' . $this->attributes['nama']),
            'username' => $this->attributes['nomor_siswa'] ?? null,
            'email' => null,
        ];
    }

    public function getUserAttribute()
    {
        return $this->getPenggunaAttribute();
    }

    public function bimbingan()
    {
        return $this->hasMany(Bimbingan::class, 'siswa_id');
    }

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'siswa_id');
    }

    public function getParentAttribute()
    {
        return $this->getPenggunaAttribute();
    }
}
