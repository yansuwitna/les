<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Target extends Model
{
    protected $table = 'target';

    protected $guarded = [];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function materi()
    {
        return $this->belongsTo(Materi::class, 'materi_id');
    }

    public function kegiatan()
    {
        return $this->hasMany(Kegiatan::class, 'target_id')->orderBy('nomor_urut', 'asc');
    }

    public function bintang()
    {
        return $this->hasMany(Bintang::class, 'target_id');
    }
}
