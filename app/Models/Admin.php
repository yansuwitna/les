<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'admin';

    protected $fillable = [
        'nama',
        'name',
        'username',
        'email',
        'foto',
        'kata_sandi',
        'password',
    ];

    protected $appends = ['peran', 'role', 'name', 'foto_url'];

    protected $hidden = [
        'kata_sandi',
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'kata_sandi' => 'hashed',
        ];
    }

    public function getAuthPasswordName(): string
    {
        return 'kata_sandi';
    }

    public function getPeranAttribute(): string
    {
        return 'admin';
    }

    public function getRoleAttribute(): string
    {
        return 'admin';
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function getPasswordAttribute(): ?string
    {
        return $this->attributes['kata_sandi'] ?? null;
    }

    public function setPasswordAttribute($value): void
    {
        $this->attributes['kata_sandi'] = $value;
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

    protected static function boot()
    {
        parent::boot();

        // Pastikan kolom foto tersedia pada tabel admin jika belum ada
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('admin') && !\Illuminate\Support\Facades\Schema::hasColumn('admin', 'foto')) {
                \Illuminate\Support\Facades\Schema::table('admin', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('foto')->nullable()->after('email');
                });
            }
        } catch (\Throwable $e) {
            // Abaikan jika migrasi sedang berjalan
        }
    }

    /**
     * Buat akun admin default (username: admin, password: admin) jika tabel admin kosong.
     */
    public static function buatDefaultJikaKosong(): ?self
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('admin') && !static::exists()) {
                return static::create([
                    'nama' => 'Administrator',
                    'username' => 'admin',
                    'email' => 'admin@les.com',
                    'kata_sandi' => 'admin',
                ]);
            }
        } catch (\Throwable $e) {
            // Abaikan pengecualian saat proses migrasi awal atau koneksi belum siap
        }

        return null;
    }
}
