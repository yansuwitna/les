<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kegiatan extends Model
{
    protected $table = 'kegiatan';

    protected $guarded = [];

    public function target()
    {
        return $this->belongsTo(Target::class, 'target_id');
    }
}
