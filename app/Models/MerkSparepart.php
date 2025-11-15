<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerkSparepart extends Model
{
    protected $table = 'merk_spareparts';

    protected $fillable = ['merk_spareparts'];

    public function spareparts()
    {
        return $this->hasMany(Sparepart::class, 'merk_spareparts_id');
    }
}
