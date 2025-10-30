<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangMasukDetail extends Model
{
    use SoftDeletes;

    protected $table = 'barang_masuk_detail';

    protected $fillable = [
        'barang_masuk_id',
        'unit_ac_id',
        'sku',
        'nama_unit',
        'jumlah_barang_masuk',
        'harga_dealer',
        'harga_ecommerce',
        'harga_retail',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_barang_masuk' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'harga_dealer' => 'integer',
            'harga_ecommerce' => 'integer',
            'harga_retail' => 'integer',
        ];
    }

    public function barangMasuk(): BelongsTo
    {
        return $this->belongsTo(BarangMasuk::class);
    }

    public function unitAC()
    {
        return $this->belongsTo(UnitAC::class)->withTrashed();
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
     * Always update stok_unit_ac when creating/updating BarangMasukDetail.
     */
    protected static function boot()
    {
        parent::boot();

        // When new record is created/updated
        static::saving(function ($barangMasukDetail) {
            // Get original values before changes
            $originalJumlah = $barangMasukDetail->getOriginal('jumlah_barang_masuk');
            $originalUnitACId = $barangMasukDetail->getOriginal('unit_ac_id');

            $newJumlah = $barangMasukDetail->jumlah_barang_masuk;
            $newUnitACId = $barangMasukDetail->unit_ac_id;

            if ($barangMasukDetail->exists) {
                // Existing record - handle changes

                // Check if unit_ac_id changed
                if ($originalUnitACId !== $newUnitACId) {
                    // Remove from old unit
                    $barangMasukDetail->updateStokUnitAC($originalJumlah, 'decrement', $originalUnitACId);
                    // Add to new unit
                    $barangMasukDetail->updateStokUnitAC($newJumlah, 'increment', $newUnitACId);
                } else {
                    // Same unit, handle quantity changes
                    $difference = $newJumlah - $originalJumlah;

                    if ($difference > 0) {
                        // Increment the difference
                        $barangMasukDetail->updateStokUnitAC($difference, 'increment');
                    } elseif ($difference < 0) {
                        // Decrement the absolute difference
                        $barangMasukDetail->updateStokUnitAC(abs($difference), 'decrement');
                    }
                    // If difference = 0, do nothing
                }
            } else {
                // New record - increment the full amount
                $barangMasukDetail->updateStokUnitAC($newJumlah, 'increment');
            }

            // Retrieve historical prices
            $unitAC = UnitAC::find($barangMasukDetail->unit_ac_id);
            if ($unitAC && $barangMasukDetail->barangMasuk) {
                $hargaHistory = $unitAC->hargaHistory()
                    ->where('created_at', '<=', $barangMasukDetail->barangMasuk->created_at)
                    ->latest()
                    ->first();

                if ($hargaHistory) {
                    $barangMasukDetail->harga_dealer = $hargaHistory->harga_dealer;
                    $barangMasukDetail->harga_ecommerce = $hargaHistory->harga_ecommerce;
                    $barangMasukDetail->harga_retail = $hargaHistory->harga_retail;
                } else {
                    // Fallback to current prices if no history found (should not happen if prices are always recorded)
                    $barangMasukDetail->harga_dealer = $unitAC->current_harga_dealer ?? 0;
                    $barangMasukDetail->harga_ecommerce = $unitAC->current_harga_ecommerce ?? 0;
                    $barangMasukDetail->harga_retail = $unitAC->current_harga_retail ?? 0;
                }
            }
        });

        // When soft deleted or force deleted
        static::softDeleted(function ($barangMasukDetail) {
            // Get the original values at time of deletion
            $originalJumlah = $barangMasukDetail->getOriginal('jumlah_barang_masuk');
            $originalUnitACId = $barangMasukDetail->getOriginal('unit_ac_id');

            // Decrement from the original unit
            $barangMasukDetail->updateStokUnitAC($originalJumlah, 'decrement', $originalUnitACId);
        });

        // When restored from soft delete
        static::restored(function ($barangMasukDetail) {
            // Get the current values at time of restoration
            $currentJumlah = $barangMasukDetail->jumlah_barang_masuk;
            $currentUnitACId = $barangMasukDetail->unit_ac_id;

            // Increment to the current unit
            $barangMasukDetail->updateStokUnitAC($currentJumlah, 'increment', $currentUnitACId);
        });
    }

    /**
     * Update stok_unit_ac based on the action (increment or decrement).
     */
    private function updateStokUnitAC(int $jumlah, string $action, ?int $unitACId = null)
    {
        $unitAC = UnitAC::find($unitACId ?? $this->unit_ac_id);

        if (! $unitAC) {
            return;
        }

        if ($action === 'increment') {
            $unitAC->increment('stok_masuk', $jumlah);
        } else {
            $unitAC->decrement('stok_masuk', $jumlah);
        }
    }
}
