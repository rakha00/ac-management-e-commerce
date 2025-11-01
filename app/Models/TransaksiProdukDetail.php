<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiProdukDetail extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_produk_detail';

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
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'harga_dealer' => 'integer',
            'harga_ecommerce' => 'integer',
            'harga_retail' => 'integer',
            'harga_modal' => 'integer',
            'harga_jual' => 'integer',
            'jumlah_keluar' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function transaksiProduk(): BelongsTo
    {
        return $this->belongsTo(TransaksiProduk::class, 'transaksi_produk_id');
    }

    public function unitAC()
    {
        return $this->belongsTo(UnitAC::class, 'unit_ac_id')->withTrashed();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Before creating, ensure denormalized fields are populated from UnitAC.
        static::creating(function (TransaksiProdukDetail $detail) {
            // Fill denormalized SKU, name, and pricing if missing
            if ($detail->unit_ac_id && (empty($detail->sku) || empty($detail->nama_unit) || $detail->harga_dealer === null || $detail->harga_ecommerce === null || $detail->harga_retail === null)) {
                $unit = UnitAC::withTrashed()->find($detail->unit_ac_id);
                if ($unit) {
                    if (empty($detail->sku)) {
                        $detail->sku = $unit->sku;
                    }
                    if (empty($detail->nama_unit)) {
                        $detail->nama_unit = $unit->nama_unit;
                    }

                    // Retrieve historical prices
                    if ($detail->transaksiProduk) {
                        $hargaHistory = $unit->hargaHistory()
                            ->where('created_at', '<=', $detail->transaksiProduk->created_at)
                            ->latest()
                            ->first();

                        if ($hargaHistory) {
                            $detail->harga_dealer = $hargaHistory->harga_dealer;
                            $detail->harga_ecommerce = $hargaHistory->harga_ecommerce;
                            $detail->harga_retail = $hargaHistory->harga_retail;
                        } else {
                            // Fallback to current prices if no history found
                            $detail->harga_dealer = $unit->current_harga_dealer ?? 0;
                            $detail->harga_ecommerce = $unit->current_harga_ecommerce ?? 0;
                            $detail->harga_retail = $unit->current_harga_retail ?? 0;
                        }
                    } else {
                        // Fallback to current prices if no TransaksiProduk (should not happen)
                        $detail->harga_dealer = $unit->current_harga_dealer ?? 0;
                        $detail->harga_ecommerce = $unit->current_harga_ecommerce ?? 0;
                        $detail->harga_retail = $unit->current_harga_retail ?? 0;
                    }
                }
            }
        });

        // When a new detail is created
        static::created(function (TransaksiProdukDetail $detail) {
            $detail->updateStokUnitAC((int) $detail->jumlah_keluar, 'out');
        });

        // Before a detail is updated (handle change in qty or unit)
        static::updating(function (TransaksiProdukDetail $detail) {
            $oldJumlah = (int) $detail->getOriginal('jumlah_keluar');
            $newJumlah = (int) $detail->jumlah_keluar;
            $oldUnitACId = $detail->getOriginal('unit_ac_id');
            $newUnitACId = $detail->unit_ac_id;

            // If the Unit AC has changed, revert from old and apply to new
            if ($oldUnitACId != $newUnitACId) {
                if ($oldUnitACId) {
                    $detail->updateStokUnitAC($oldJumlah, 'revert', $oldUnitACId);
                }
                if ($newUnitACId) {
                    $detail->updateStokUnitAC($newJumlah, 'out', $newUnitACId);
                }
            } else {
                // Unit unchanged, adjust by difference
                $diff = $newJumlah - $oldJumlah;
                if ($diff !== 0) {
                    $detail->updateStokUnitAC(abs($diff), $diff > 0 ? 'out' : 'revert', $newUnitACId);
                }
            }
        });

        // On soft delete, revert stok (return stok back)
        static::deleted(function (TransaksiProdukDetail $detail) {
            $detail->updateStokUnitAC((int) $detail->jumlah_keluar, 'revert');
        });

        // On restore, re-apply stok out
        static::restored(function (TransaksiProdukDetail $detail) {
            $detail->updateStokUnitAC((int) $detail->jumlah_keluar, 'out');
        });
    }

    /**
     * Update stok UnitAC based on the transaction detail.
     */
    private function updateStokUnitAC(int $jumlah, string $action, ?int $unitACId = null): void
    {
        $unitAC = UnitAC::find($unitACId ?? $this->unit_ac_id);

        if (! $unitAC || $jumlah <= 0) {
            return;
        }

        if ($action === 'out') {
            $unitAC->increment('stok_keluar', $jumlah);
            $unitAC->decrement('stok_akhir', $jumlah);
        } else {
            $unitAC->decrement('stok_keluar', $jumlah);
            $unitAC->increment('stok_akhir', $jumlah);
        }
    }
}
