<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimoni extends Model
{
    protected $table = 'testimoni';

    protected $guarded = [];

    protected $appends = ['is_active', 'name', 'message', 'photo'];

    public function getIsActiveAttribute(): ?bool
    {
        return isset($this->attributes['aktif']) ? (bool) $this->attributes['aktif'] : null;
    }

    public function setIsActiveAttribute($value): void
    {
        $this->attributes['aktif'] = $value;
    }

    public function getNameAttribute(): ?string
    {
        return $this->attributes['nama'] ?? null;
    }

    public function setNameAttribute($value): void
    {
        $this->attributes['nama'] = $value;
    }

    public function getMessageAttribute(): ?string
    {
        return $this->attributes['pesan'] ?? null;
    }

    public function setMessageAttribute($value): void
    {
        $this->attributes['pesan'] = $value;
    }

    public function getPhotoAttribute(): ?string
    {
        return $this->attributes['foto'] ?? null;
    }

    public function setPhotoAttribute($value): void
    {
        $this->attributes['foto'] = $value;
    }

    public function scopeActive($query)
    {
        return $query->where('aktif', true);
    }
}
