<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Principle extends Model
{
    protected $fillable = [
        'nama',
        'sales',
        'no_hp',
        'remarks',
    ];

    public function barangMasuks()
    {
        return $this->hasMany(BarangMasuk::class, 'principle_subdealer_id');
    }
}
