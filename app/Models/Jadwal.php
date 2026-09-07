<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jadwal extends Model
{
    protected $table = 'jadwal';

    protected $guarded = [];

    protected $appends = ['day', 'start_time', 'end_time'];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
        ];
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function bimbingan()
    {
        return $this->belongsTo(Bimbingan::class, 'bimbingan_id');
    }

    // Aliases for backward compatibility
    public function student()
    {
        return $this->siswa();
    }

    public function teacher()
    {
        return $this->guru();
    }

    public function getDayAttribute(): ?string
    {
        return $this->attributes['hari'] ?? null;
    }

    public function getStartTimeAttribute(): ?string
    {
        return $this->attributes['jam_mulai'] ?? null;
    }

    public function getEndTimeAttribute(): ?string
    {
        return $this->attributes['jam_selesai'] ?? null;
    }
}
