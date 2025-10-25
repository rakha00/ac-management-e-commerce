<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TransaksiJasaDetail represents individual service transaction lines.
 * - Triggers parent totals recalculation after changes
 * - Uses soft deletes; restore/force delete will adjust totals accordingly
 */
class TransaksiJasaDetail extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_jasa_details';

    protected $fillable = [
        'transaksi_jasa_id',
        'jenis_data',
        'qty',
        'harga_jasa',
        'keterangan_jasa',
        'pengeluaran_jasa',
        'keterangan_pengeluaran',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'harga_jasa' => 'decimal:2',
            'pengeluaran_jasa' => 'decimal:2',
        ];
    }

    // ============ RELATIONSHIPS ============
    public function transaksiJasa(): BelongsTo
    {
        return $this->belongsTo(TransaksiJasa::class, 'transaksi_jasa_id');
    }

    // ============ MODEL EVENTS ============
    protected static function boot()
    {
        parent::boot();

        // After create/update/delete/restore, recalc parent totals
        static::created(function (TransaksiJasaDetail $detail) {
            $detail->recalculateParent();
        });

        static::updated(function (TransaksiJasaDetail $detail) {
            $detail->recalculateParent();
        });

        static::deleted(function (TransaksiJasaDetail $detail) {
            $detail->recalculateParent();
        });

        static::restored(function (TransaksiJasaDetail $detail) {
            $detail->recalculateParent();
        });
    }

    // ============ HELPERS ============
    /**
     * Recalculate parent totals based on current non-trashed details.
     */
    private function recalculateParent(): void
    {
        $parent = $this->transaksiJasa;
        if ($parent instanceof TransaksiJasa) {
            // Delegate to parent's method for consistent aggregation
            $parent->recalcFromDetails();
        }
    }
}
