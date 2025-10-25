<?php

// App/Models/BarangMasukDetail.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BarangMasukDetail extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'barang_masuk_id',
        'unit_ac_id',
        'sku',
        'nama_unit',
        'jumlah_barang_masuk',
        'remarks',
    ];

    protected $casts = [
        'jumlah_barang_masuk' => 'integer',
    ];

    // ============ RELATIONSHIPS ============
    public function barangMasuk()
    {
        return $this->belongsTo(BarangMasuk::class);
    }

    public function unitAc()
    {
        return $this->belongsTo(UnitAc::class);
    }

    // ============ MODEL EVENTS ============
    protected static function boot()
    {
        parent::boot();

        // Ketika data baru dibuat
        static::created(function ($barangMasukDetail) {
            $barangMasukDetail->updateStockUnitAc(
                $barangMasukDetail->jumlah_barang_masuk,
                'increment'
            );
        });

        // Sebelum data diupdate
        static::updating(function ($barangMasukDetail) {
            $oldJumlah = $barangMasukDetail->getOriginal('jumlah_barang_masuk');
            $newJumlah = $barangMasukDetail->jumlah_barang_masuk;
            $oldUnitAcId = $barangMasukDetail->getOriginal('unit_ac_id');
            $newUnitAcId = $barangMasukDetail->unit_ac_id;

            // Jika unit_ac_id berubah
            if ($oldUnitAcId != $newUnitAcId) {
                // Kurangi stock dari unit AC lama
                $barangMasukDetail->updateStockUnitAc($oldJumlah, 'decrement', $oldUnitAcId);
                // Tambah stock ke unit AC baru
                $barangMasukDetail->updateStockUnitAc($newJumlah, 'increment', $newUnitAcId);
            }
            // Jika hanya jumlah yang berubah
            else {
                $selisih = $newJumlah - $oldJumlah;
                if ($selisih != 0) {
                    $action = $selisih > 0 ? 'increment' : 'decrement';
                    $barangMasukDetail->updateStockUnitAc(abs($selisih), $action);
                }
            }
        });

        // Ketika di soft delete atau force delete
        static::deleted(function ($barangMasukDetail) {
            $barangMasukDetail->updateStockUnitAc(
                $barangMasukDetail->jumlah_barang_masuk,
                'decrement'
            );
        });

        // Ketika di restore dari soft delete
        static::restored(function ($barangMasukDetail) {
            $barangMasukDetail->updateStockUnitAc(
                $barangMasukDetail->jumlah_barang_masuk,
                'increment'
            );
        });
    }

    // ============ HELPER METHOD ============
    private function updateStockUnitAc(int $jumlah, string $action, ?int $unitAcId = null)
    {
        $unitAc = UnitAc::find($unitAcId ?? $this->unit_ac_id);

        if (! $unitAc) {
            return;
        }

        if ($action === 'increment') {
            $unitAc->increment('stock_masuk', $jumlah);
            $unitAc->increment('stock_akhir', $jumlah);
        } else {
            $unitAc->decrement('stock_masuk', $jumlah);
            $unitAc->decrement('stock_akhir', $jumlah);
        }
    }
}
