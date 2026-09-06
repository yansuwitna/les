<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bintang extends Model
{
    protected $table = 'bintang';

    protected $guarded = [];

    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'siswa_id');
    }

    public function target()
    {
        return $this->belongsTo(Target::class, 'target_id');
    }
}
