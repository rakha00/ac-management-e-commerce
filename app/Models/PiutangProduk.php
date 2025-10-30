<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PiutangProduk extends Model
{
    use SoftDeletes;

    protected $table = 'piutang_produk';

    protected $fillable = [
        'no_invoice',
        'transaksi_produk_id',
        'total_piutang',
        'status_pembayaran',
        'jatuh_tempo',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'total_piutang' => 'integer',
            'jatuh_tempo' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function getSisaPiutangAttribute(): int
    {
        $totalCicilan = $this->piutangProdukCicilanDetail()->sum('nominal_cicilan');

        return max($this->total_piutang - (int) $totalCicilan, 0);
    }

    public function piutangProdukCicilanDetail(): HasMany
    {
        return $this->hasMany(PiutangProdukCicilanDetail::class);
    }

    public function transaksiProduk(): BelongsTo
    {
        return $this->belongsTo(TransaksiProduk::class);
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
