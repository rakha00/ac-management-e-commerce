<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HargaSparepartHistory extends Model
{
    use HasFactory;

    protected $table = 'harga_sparepart_history';

    protected $fillable = [
        'sparepart_id',
        'karyawan_id',
        'harga_modal',
    ];

    protected function casts(): array
    {
        return [
            'harga_modal' => 'integer',
        ];
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function karyawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class);
    }
}
