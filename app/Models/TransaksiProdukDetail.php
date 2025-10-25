<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * TransaksiProdukDetail represents individual product transaction lines (stock out).
 * - Updates UnitAC stock_keluar and stock_akhir via model events
 * - Triggers parent totals recalculation after changes
 * - Uses soft deletes; restore/force delete will adjust stock accordingly
 */
class TransaksiProdukDetail extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_produk_details';

    protected $fillable = [
        'transaksi_produk_id',
        'unit_ac_id',
        'sku',
        'nama_unit',
        'harga_dealer',
        'harga_ecommerce',
        'harga_retail',
        'jumlah_keluar',
        'harga_modal',
        'harga_jual',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'harga_dealer' => 'decimal:2',
            'harga_ecommerce' => 'decimal:2',
            'harga_retail' => 'decimal:2',
            'harga_modal' => 'decimal:2',
            'harga_jual' => 'decimal:2',
            'jumlah_keluar' => 'integer',
        ];
    }

    // ============ RELATIONSHIPS ============
    public function transaksiProduk(): BelongsTo
    {
        return $this->belongsTo(TransaksiProduk::class, 'transaksi_produk_id');
    }

    public function unitAc(): BelongsTo
    {
        return $this->belongsTo(UnitAC::class, 'unit_ac_id');
    }

    // ============ MODEL EVENTS ============
    protected static function boot()
    {
        parent::boot();

        // Before creating, ensure denormalized fields are populated from UnitAC.
        static::creating(function (TransaksiProdukDetail $detail) {
            // Fill denormalized SKU, name, and pricing if missing
            if ($detail->unit_ac_id && (empty($detail->sku) || empty($detail->nama_unit) || $detail->harga_dealer === null || $detail->harga_ecommerce === null || $detail->harga_retail === null)) {
                $unit = UnitAC::find($detail->unit_ac_id);
                if ($unit) {
                    if (empty($detail->sku)) {
                        $detail->sku = $unit->sku;
                    }
                    if (empty($detail->nama_unit)) {
                        $detail->nama_unit = $unit->nama_merk;
                    }
                    if ($detail->harga_dealer === null) {
                        $detail->harga_dealer = $unit->harga_dealer;
                    }
                    if ($detail->harga_ecommerce === null) {
                        $detail->harga_ecommerce = $unit->harga_ecommerce;
                    }
                    if ($detail->harga_retail === null) {
                        $detail->harga_retail = $unit->harga_retail;
                    }
                }
            }
        });

        // When a new detail is created
        static::created(function (TransaksiProdukDetail $detail) {
            $detail->updateStockUnitAc((int) $detail->jumlah_keluar, 'out');
            $detail->recalculateParent();
        });

        // Before a detail is updated (handle change in qty or unit)
        static::updating(function (TransaksiProdukDetail $detail) {
            $oldJumlah = (int) $detail->getOriginal('jumlah_keluar');
            $newJumlah = (int) $detail->jumlah_keluar;
            $oldUnitAcId = $detail->getOriginal('unit_ac_id');
            $newUnitAcId = $detail->unit_ac_id;

            // If the Unit AC has changed, revert from old and apply to new
            if ($oldUnitAcId != $newUnitAcId) {
                if ($oldUnitAcId) {
                    $detail->updateStockUnitAc($oldJumlah, 'revert', $oldUnitAcId);
                }
                if ($newUnitAcId) {
                    $detail->updateStockUnitAc($newJumlah, 'out', $newUnitAcId);
                }
            } else {
                // Unit unchanged, adjust by difference
                $diff = $newJumlah - $oldJumlah;
                if ($diff !== 0) {
                    $detail->updateStockUnitAc(abs($diff), $diff > 0 ? 'out' : 'revert', $newUnitAcId);
                }
            }
        });

        // After update, keep parent totals in sync
        static::updated(function (TransaksiProdukDetail $detail) {
            $detail->recalculateParent();
        });

        // On soft delete, revert stock (return stock back)
        static::deleted(function (TransaksiProdukDetail $detail) {
            $detail->updateStockUnitAc((int) $detail->jumlah_keluar, 'revert');
            $detail->recalculateParent();
        });

        // On restore, re-apply stock out
        static::restored(function (TransaksiProdukDetail $detail) {
            $detail->updateStockUnitAc((int) $detail->jumlah_keluar, 'out');
            $detail->recalculateParent();
        });
    }

    // ============ HELPERS ============
    /**
     * Update UnitAC stocks for 'out' (sale) or 'revert' (undo sale).
     * - 'out': increment stock_keluar, decrement stock_akhir
     * - 'revert': decrement stock_keluar, increment stock_akhir
     */
    private function updateStockUnitAc(int $jumlah, string $action, ?int $unitAcId = null): void
    {
        $unitAc = UnitAC::find($unitAcId ?? $this->unit_ac_id);

        if (!$unitAc || $jumlah <= 0) {
            return;
        }

        if ($action === 'out') {
            $unitAc->increment('stock_keluar', $jumlah);
            $unitAc->decrement('stock_akhir', $jumlah);
        } else {
            $unitAc->decrement('stock_keluar', $jumlah);
            $unitAc->increment('stock_akhir', $jumlah);
        }
    }

    /**
     * Recalculate parent totals based on current non-trashed details.
     */
    private function recalculateParent(): void
    {
        $parent = $this->transaksiProduk;
        if ($parent instanceof TransaksiProduk) {
            // Delegate to parent's method for consistent aggregation
            $parent->recalcFromDetails();
        }
    }
}
