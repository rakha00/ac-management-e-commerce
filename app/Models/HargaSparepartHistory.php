<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HargaSparepartHistory extends Model
{
    protected $table = 'harga_sparepart_history';

    protected $fillable = [
        'sparepart_id',
        'harga_modal',
        'harga_ecommerce',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'harga_modal' => 'integer',
            'harga_ecommerce' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
