<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PiutangJasaCicilanDetail extends Model
{
    use SoftDeletes;

    protected $table = 'piutang_jasa_cicilan_detail';

    protected $fillable = [
        'piutang_jasa_id',
        'nominal_cicilan',
        'tanggal_cicilan',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'nominal_cicilan' => 'integer',
            'tanggal_cicilan' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function piutangJasa(): BelongsTo
    {
        return $this->belongsTo(PiutangJasa::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
