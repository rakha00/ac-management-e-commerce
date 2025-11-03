<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipeAC extends Model
{
    protected $table = 'tipe_ac';

    protected $fillable = ['tipe_ac'];

    public function unitAC()
    {
        return $this->hasMany(UnitAC::class, 'tipe_ac_id');
    }
}
