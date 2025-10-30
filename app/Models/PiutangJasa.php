<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PiutangJasa extends Model
{
    use SoftDeletes;

    protected $table = 'piutang_jasa';

    protected $fillable = [
        'transaksi_jasa_id',
        'total_piutang',
        'status_pembayaran',
        'jatuh_tempo',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    public function transaksiJasa(): BelongsTo
    {
        return $this->belongsTo(TransaksiJasa::class);
    }

    protected function casts(): array
    {
        return [
            'total_piutang' => 'integer',
            'jatuh_tempo' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function piutangJasaCicilanDetail(): HasMany
    {
        return $this->hasMany(PiutangJasaCicilanDetail::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function getSisaPiutangAttribute(): int
    {
        $totalCicilan = $this->piutangJasaCicilanDetail->sum('nominal_cicilan');

        return max(0, $this->total_piutang - $totalCicilan);
    }
}
