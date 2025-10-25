<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * SparepartKeluarDetail represents individual outgoing sparepart lines (stock out).
 * - Updates Sparepart stock_keluar and stock_akhir via model events
 * - Triggers parent totals recalculation after changes
 * - Uses soft deletes; restore/force delete will adjust stock accordingly
 */
class SparepartKeluarDetail extends Model
{
    use SoftDeletes;

    protected $table = 'sparepart_keluar_details';

    protected $fillable = [
        'sparepart_keluar_id',
        'sparepart_id',
        'kode_sparepart',
        'nama_sparepart',
        'jumlah_keluar',
        'harga_modal',
        'harga_jual',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_keluar' => 'integer',
            'harga_modal' => 'decimal:2',
            'harga_jual' => 'decimal:2',
        ];
    }

    // ============ RELATIONSHIPS ============
    public function sparepartKeluar(): BelongsTo
    {
        return $this->belongsTo(SparepartKeluar::class, 'sparepart_keluar_id');
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
        static::creating(function (SparepartKeluarDetail $detail) {
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
        static::created(function (SparepartKeluarDetail $detail) {
            $detail->updateStockSparepart((int) $detail->jumlah_keluar, 'out');
            $detail->recalculateParent();
        });

        // Before a detail is updated (handle change in qty or sparepart)
        static::updating(function (SparepartKeluarDetail $detail) {
            $oldJumlah = (int) $detail->getOriginal('jumlah_keluar');
            $newJumlah = (int) $detail->jumlah_keluar;
            $oldSparepartId = $detail->getOriginal('sparepart_id');
            $newSparepartId = $detail->sparepart_id;

            // If the Sparepart has changed, revert from old and apply to new
            if ($oldSparepartId != $newSparepartId) {
                if ($oldSparepartId) {
                    $detail->updateStockSparepart($oldJumlah, 'revert', $oldSparepartId);
                }
                if ($newSparepartId) {
                    $detail->updateStockSparepart($newJumlah, 'out', $newSparepartId);
                }
            } else {
                // Sparepart unchanged, adjust by difference
                $diff = $newJumlah - $oldJumlah;
                if ($diff !== 0) {
                    $detail->updateStockSparepart(abs($diff), $diff > 0 ? 'out' : 'revert', $newSparepartId);
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
        static::updated(function (SparepartKeluarDetail $detail) {
            $detail->recalculateParent();
        });

        // On soft delete, revert stock (return stock back)
        static::deleted(function (SparepartKeluarDetail $detail) {
            $detail->updateStockSparepart((int) $detail->jumlah_keluar, 'revert');
            $detail->recalculateParent();
        });

        // On restore, re-apply stock out
        static::restored(function (SparepartKeluarDetail $detail) {
            $detail->updateStockSparepart((int) $detail->jumlah_keluar, 'out');
            $detail->recalculateParent();
        });
    }

    // ============ HELPERS ============
    /**
     * Update Sparepart stocks for 'out' (sale) or 'revert' (undo sale).
     * - 'out': increment stock_keluar, decrement stock_akhir
     * - 'revert': decrement stock_keluar, increment stock_akhir
     */
    private function updateStockSparepart(int $jumlah, string $action, ?int $sparepartId = null): void
    {
        $sp = Sparepart::find($sparepartId ?? $this->sparepart_id);

        if (!$sp || $jumlah <= 0) {
            return;
        }

        if ($action === 'out') {
            $sp->increment('stock_keluar', $jumlah);
            $sp->decrement('stock_akhir', $jumlah);
        } else {
            $sp->decrement('stock_keluar', $jumlah);
            $sp->increment('stock_akhir', $jumlah);
        }
    }

    /**
     * Recalculate parent totals based on current non-trashed details.
     */
    private function recalculateParent(): void
    {
        $parent = $this->sparepartKeluar;
        if ($parent instanceof SparepartKeluar) {
            $parent->recalcFromDetails();
        }
    }
}
