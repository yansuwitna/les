<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pengaturan extends Model
{
    protected $table = 'pengaturan';

    protected $guarded = [];

    protected $appends = ['les_name', 'les_address', 'les_contact', 'logo_url', 'slide_url'];

    public function getLogoUrlAttribute(): ?string
    {
        if (!empty($this->logo_les)) {
            if (str_starts_with($this->logo_les, 'http://') || str_starts_with($this->logo_les, 'https://')) {
                return $this->logo_les;
            }
            return asset('storage/' . $this->logo_les);
        }
        return null;
    }

    public function getSlideUrlAttribute(): ?string
    {
        if (!empty($this->slide_les)) {
            if (str_starts_with($this->slide_les, 'http://') || str_starts_with($this->slide_les, 'https://')) {
                return $this->slide_les;
            }
            return asset('storage/' . $this->slide_les);
        }
        return null;
    }

    // Accessors for backward compatibility
    public function getLesNameAttribute(): ?string
    {
        return $this->attributes['nama_les'] ?? null;
    }

    public function setLesNameAttribute($value): void
    {
        $this->attributes['nama_les'] = $value;
    }

    public function getLesAddressAttribute(): ?string
    {
        return $this->attributes['alamat_les'] ?? null;
    }

    public function setLesAddressAttribute($value): void
    {
        $this->attributes['alamat_les'] = $value;
    }

    public function getLesContactAttribute(): ?string
    {
        return $this->attributes['kontak_les'] ?? null;
    }

    public function setLesContactAttribute($value): void
    {
        $this->attributes['kontak_les'] = $value;
    }
}
