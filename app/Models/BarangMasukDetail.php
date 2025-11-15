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
        'jumlah_barang_masuk',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'barang_masuk_id' => 'integer',
            'unit_ac_id' => 'integer',
            'jumlah_barang_masuk' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function barangMasuk(): BelongsTo
    {
        return $this->belongsTo(BarangMasuk::class);
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

        // Set created_by  when creating
        static::creating(function (self $barangMasukDetail): void {
            if (auth()->check()) {
                $barangMasukDetail->created_by = auth()->id();
                $barangMasukDetail->updated_by = auth()->id();
            }
        });

        // Set updated_by when updating
        static::updating(function (self $barangMasukDetail): void {
            if (auth()->check()) {
                $barangMasukDetail->updated_by = auth()->id();
            }
        });

        // On soft delete: delete related BarangMasukDetail first; FK is SET NULL so barang_masuk_id becomes null automatically.
        static::deleting(function (self $barangMasukDetail): void {
            if (! $barangMasukDetail->isForceDeleting()) {
                if (auth()->check()) {
                    $barangMasukDetail->deleted_by = auth()->id();
                    $barangMasukDetail->save(); // Save the model to persist the deleted_by value
                }
            }
        });

        static::saving(function ($detail) {
            $originalQuantity = $detail->getOriginal('jumlah_barang_masuk');
            $originalUnitId = $detail->getOriginal('unit_ac_id');

            $newQuantity = $detail->jumlah_barang_masuk;
            $newUnitId = $detail->unit_ac_id;

            if ($detail->exists) {
                if ($originalUnitId !== $newUnitId) {
                    $detail->updateUnitACStock($originalQuantity, 'decrement', $originalUnitId);
                    $detail->updateUnitACStock($newQuantity, 'increment', $newUnitId);
                } else {
                    $difference = $newQuantity - $originalQuantity;
                    if ($difference > 0) {
                        $detail->updateUnitACStock($difference, 'increment');
                    } elseif ($difference < 0) {
                        $detail->updateUnitACStock(abs($difference), 'decrement');
                    }
                }
            } else {
                $detail->updateUnitACStock($newQuantity, 'increment');
            }
        });

        static::softDeleted(function ($detail) {
            $detail->updateUnitACStock($detail->getOriginal('jumlah_barang_masuk'), 'decrement', $detail->getOriginal('unit_ac_id'));
        });

        static::restored(function ($detail) {
            $detail->updateUnitACStock($detail->jumlah_barang_masuk, 'increment', $detail->unit_ac_id);
        });
    }

    private function updateUnitACStock(int $quantity, string $action, ?int $unitId = null)
    {
        $unitAC = UnitAC::find($unitId ?? $this->unit_ac_id);

        if (! $unitAC) {
            return;
        }

        if ($action === 'increment') {
            $unitAC->increment('stok_masuk', $quantity);
        } else {
            $unitAC->decrement('stok_masuk', $quantity);
        }
    }
}
