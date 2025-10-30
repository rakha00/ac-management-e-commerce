<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HargaUnitACHistory extends Model
{
    use HasFactory;

    protected $table = 'harga_unit_ac_history';

    protected $fillable = [
        'unit_ac_id',
        'harga_dealer',
        'harga_ecommerce',
        'harga_retail',
        'karyawan_id',
    ];

    protected function casts(): array
    {
        return [
            'harga_dealer' => 'integer',
            'harga_ecommerce' => 'integer',
            'harga_retail' => 'integer',
        ];
    }

    public function unitAC(): BelongsTo
    {
        return $this->belongsTo(UnitAC::class, 'unit_ac_id');
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'karyawan_id');
    }
}
