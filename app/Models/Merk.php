<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merk extends Model
{
    protected $table = 'merk';

    protected $fillable = ['merk'];

    public function unitAC()
    {
        return $this->hasMany(UnitAC::class, 'merk_id');
    }
}
