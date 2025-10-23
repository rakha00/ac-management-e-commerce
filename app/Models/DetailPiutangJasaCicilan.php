<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPiutangJasaCicilan extends Model
{
    use SoftDeletes;

    protected $table = 'detail_piutang_jasa_cicilan';

    protected $fillable = [
        'piutang_jasa_id',
        'nominal_cicilan',
        'tanggal_cicilan',
    ];

    protected function casts(): array
    {
        return [
            'nominal_cicilan' => 'integer',
            'tanggal_cicilan' => 'date',
        ];
    }

    public function piutangJasa(): BelongsTo
    {
        return $this->belongsTo(PiutangJasa::class);
    }
}
