<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * SparepartMasukDetail represents individual incoming sparepart lines (stock in).
 * - Updates Sparepart stock_masuk and stock_akhir via model events
 * - Triggers parent total recalculation after changes
 * - Uses soft deletes; restore/force delete will adjust stock accordingly
 */
class SparepartMasukDetail extends Model
{
    use SoftDeletes;

    protected $table = 'sparepart_masuk_details';

    protected $fillable = [
        'sparepart_masuk_id',
        'sparepart_id',
        'kode_sparepart',
        'nama_sparepart',
        'jumlah_masuk',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_masuk' => 'integer',
        ];
    }

    // ============ RELATIONSHIPS ============
    public function sparepartMasuk(): BelongsTo
    {
        return $this->belongsTo(SparepartMasuk::class, 'sparepart_masuk_id');
    }

    public function sparepart(): BelongsTo
    {
        return $this->belongsTo(Sparepart::class, 'sparepart_id');
    }

    // ============ MODEL EVENTS ============
    protected static function boot()
    {
        parent::boot();

        // Before creating, ensure denormalized fields are populated from Sparepart.
        static::creating(function (SparepartMasukDetail $detail) {
            if ($detail->sparepart_id && (empty($detail->kode_sparepart) || empty($detail->nama_sparepart))) {
                $sp = Sparepart::find($detail->sparepart_id);
                if ($sp) {
                    if (empty($detail->kode_sparepart)) {
                        $detail->kode_sparepart = $sp->kode_sparepart;
                    }
                    if (empty($detail->nama_sparepart)) {
                        $detail->nama_sparepart = $sp->nama_sparepart;
                    }
                }
            }
        });

        // When a new detail is created
        static::created(function (SparepartMasukDetail $detail) {
            $detail->updateStockSparepart((int) $detail->jumlah_masuk, 'in');
            $detail->recalculateParent();
        });

        // Before a detail is updated (handle change in qty or sparepart)
        static::updating(function (SparepartMasukDetail $detail) {
            $oldJumlah = (int) $detail->getOriginal('jumlah_masuk');
            $newJumlah = (int) $detail->jumlah_masuk;
            $oldSparepartId = $detail->getOriginal('sparepart_id');
            $newSparepartId = $detail->sparepart_id;

            // If the Sparepart has changed, revert from old and apply to new
            if ($oldSparepartId != $newSparepartId) {
                if ($oldSparepartId) {
                    $detail->updateStockSparepart($oldJumlah, 'revert', $oldSparepartId);
                }
                if ($newSparepartId) {
                    $detail->updateStockSparepart($newJumlah, 'in', $newSparepartId);
                }
            } else {
                // Sparepart unchanged, adjust by difference
                $diff = $newJumlah - $oldJumlah;
                if ($diff !== 0) {
                    $detail->updateStockSparepart(abs($diff), $diff > 0 ? 'in' : 'revert', $newSparepartId);
                }
            }

            // Keep denormalized fields in sync if sparepart_id changed
            if ($oldSparepartId != $newSparepartId && $newSparepartId) {
                $sp = Sparepart::find($newSparepartId);
                if ($sp) {
                    $detail->kode_sparepart = $sp->kode_sparepart;
                    $detail->nama_sparepart = $sp->nama_sparepart;
                }
            }
        });

        // After update, keep parent totals in sync
        static::updated(function (SparepartMasukDetail $detail) {
            $detail->recalculateParent();
        });

        // On soft delete, revert stock (return stock back)
        static::deleted(function (SparepartMasukDetail $detail) {
            $detail->updateStockSparepart((int) $detail->jumlah_masuk, 'revert');
            $detail->recalculateParent();
        });

        // On restore, re-apply stock in
        static::restored(function (SparepartMasukDetail $detail) {
            $detail->updateStockSparepart((int) $detail->jumlah_masuk, 'in');
            $detail->recalculateParent();
        });
    }

    // ============ HELPERS ============
    /**
     * Update Sparepart stocks for 'in' (incoming) or 'revert' (undo incoming).
     * - 'in': increment stock_masuk, increment stock_akhir
     * - 'revert': decrement stock_masuk, decrement stock_akhir
     */
    private function updateStockSparepart(int $jumlah, string $action, ?int $sparepartId = null): void
    {
        $sp = Sparepart::find($sparepartId ?? $this->sparepart_id);

        if (!$sp || $jumlah <= 0) {
            return;
        }

        if ($action === 'in') {
            $sp->increment('stock_masuk', $jumlah);
            $sp->increment('stock_akhir', $jumlah);
        } else {
            $sp->decrement('stock_masuk', $jumlah);
            $sp->decrement('stock_akhir', $jumlah);
        }
    }

    /**
     * Recalculate parent totals based on current non-trashed details.
     */
    private function recalculateParent(): void
    {
        $parent = $this->sparepartMasuk;
        if ($parent instanceof SparepartMasuk) {
            $parent->recalcFromDetails();
        }
    }
}
