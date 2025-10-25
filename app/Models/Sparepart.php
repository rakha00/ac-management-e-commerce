<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Sparepart master model.
 * - Holds basic sparepart info (code, name, base cost).
 * - Maintains stock fields; stock_akhir is computed.
 * - Uses soft deletes for historical integrity.
 */
class Sparepart extends Model
{
    use SoftDeletes;

    protected $table = 'spareparts';

    protected $fillable = [
        'kode_sparepart',
        'nama_sparepart',
        'harga_modal',
        'stock_awal',
        'stock_masuk',
        'stock_keluar',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'harga_modal' => 'decimal:2',
            'stock_awal' => 'integer',
            'stock_masuk' => 'integer',
            'stock_keluar' => 'integer',
        ];
    }

    // Expose computed stock_akhir in JSON responses
    protected $appends = ['stock_akhir'];

    /**
     * Accessor for stock_akhir.
     * Computed as: stock_awal + stock_masuk - stock_keluar
     */
    protected function stockAkhir(): Attribute
    {
        return Attribute::make(
            get: fn() => (int) ($this->stock_awal ?? 0) + (int) ($this->stock_masuk ?? 0) - (int) ($this->stock_keluar ?? 0),
        );
    }

    /**
     * Keep a physical column value in sync before saving if required in other places.
     * Note: Even though it's computed, we mirror UnitAC behavior to provide a persisted value if needed by queries.
     */
    protected static function booted(): void
    {
        static::saving(function (Sparepart $sp) {
            $sp->stock_akhir = (int) ($sp->stock_awal ?? 0)
                + (int) ($sp->stock_masuk ?? 0)
                - (int) ($sp->stock_keluar ?? 0);
        });
    }

    // ============ RELATIONSHIPS ============
    public function sparepartMasukDetails(): HasMany
    {
        return $this->hasMany(SparepartMasukDetail::class, 'sparepart_id');
    }

    public function sparepartKeluarDetails(): HasMany
    {
        return $this->hasMany(SparepartKeluarDetail::class, 'sparepart_id');
    }
}
