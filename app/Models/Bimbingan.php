<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bimbingan extends Model
{
    protected $table = 'bimbingan';

    protected $guarded = [];

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

    public function jadwal()
    {
        return $this->hasMany(Jadwal::class, 'bimbingan_id');
    }
}
