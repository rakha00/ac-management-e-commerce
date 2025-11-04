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
        'harga_dealer',
        'harga_ecommerce',
        'harga_retail',
        'jumlah_keluar',
        'harga_modal',
        'harga_jual',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'transaksi_produk_id' => 'integer',
            'unit_ac_id' => 'integer',
            'harga_dealer' => 'integer',
            'harga_ecommerce' => 'integer',
            'harga_retail' => 'integer',
            'harga_modal' => 'integer',
            'harga_jual' => 'integer',
            'jumlah_keluar' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    protected static function boot()
    {
        parent::boot();

        // Populate denormalized fields from UnitAC before creation.
        static::creating(function (TransaksiProdukDetail $detail) {
            if (
                $detail->unit_ac_id && (

                    $detail->harga_dealer === null ||
                    $detail->harga_ecommerce === null ||
                    $detail->harga_retail === null
                )
            ) {
                $unit = UnitAC::withTrashed()->find($detail->unit_ac_id);
                if ($unit) {
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
                            $detail->harga_dealer = $unit->current_harga_dealer ?? 0;
                            $detail->harga_ecommerce = $unit->current_harga_ecommerce ?? 0;
                            $detail->harga_retail = $unit->current_harga_retail ?? 0;
                        }
                    } else {
                        $detail->harga_dealer = $unit->current_harga_dealer ?? 0;
                        $detail->harga_ecommerce = $unit->current_harga_ecommerce ?? 0;
                        $detail->harga_retail = $unit->current_harga_retail ?? 0;
                    }
                }
            }
        });

        static::created(function (TransaksiProdukDetail $detail) {
            $detail->updateStokUnitAC((int) $detail->jumlah_keluar, 'out');
        });

        // Handle stock adjustments on update.
        static::updating(function (TransaksiProdukDetail $detail) {
            $oldJumlah = (int) $detail->getOriginal('jumlah_keluar');
            $newJumlah = (int) $detail->jumlah_keluar;
            $oldUnitACId = $detail->getOriginal('unit_ac_id');
            $newUnitACId = $detail->unit_ac_id;

            if ($oldUnitACId != $newUnitACId) {
                if ($oldUnitACId) {
                    $detail->updateStokUnitAC($oldJumlah, 'revert', $oldUnitACId);
                }
                if ($newUnitACId) {
                    $detail->updateStokUnitAC($newJumlah, 'out', $newUnitACId);
                }
            } else {
                $diff = $newJumlah - $oldJumlah;
                if ($diff !== 0) {
                    $detail->updateStokUnitAC(abs($diff), $diff > 0 ? 'out' : 'revert', $newUnitACId);
                }
            }
        });

        // Revert stock on soft delete.
        static::deleted(function (TransaksiProdukDetail $detail) {
            $detail->updateStokUnitAC((int) $detail->jumlah_keluar, 'revert');
        });

        // Re-apply stock on restore.
        static::restored(function (TransaksiProdukDetail $detail) {
            $detail->updateStokUnitAC((int) $detail->jumlah_keluar, 'out');
        });
    }

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
