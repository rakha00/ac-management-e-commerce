<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class UnitAC extends Model
{
    use SoftDeletes;

    protected $table = 'unit_ac';

    protected $fillable = [
        'sku',
        'nama_unit',
        'path_foto_produk',
        'harga_dealer',
        'harga_ecommerce',
        'harga_retail',
        'stok_awal',
        'stok_masuk',
        'stok_keluar',
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
            'stok_awal' => 'integer',
            'stok_akhir' => 'integer',
            'stok_masuk' => 'integer',
            'stok_keluar' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'path_foto_produk' => 'array',
        ];
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function hargaHistory(): HasMany
    {
        return $this->hasMany(HargaUnitACHistory::class, 'unit_ac_id');
    }

    protected static function booted(): void
    {
        static::creating(function (UnitAC $model) {
            $model->stok_akhir = ($model->stok_awal ?? 0) + ($model->stok_masuk ?? 0) - ($model->stok_keluar ?? 0);
        });

        static::created(function (UnitAC $model) {
            $model->hargaHistory()->create([
                'harga_dealer' => $model->harga_dealer,
                'harga_ecommerce' => $model->harga_ecommerce,
                'harga_retail' => $model->harga_retail,
                'karyawan_id' => Auth::id(),
            ]);
        });

        static::updating(function (UnitAC $model) {
            $model->stok_akhir = ($model->stok_awal ?? 0) + ($model->stok_masuk ?? 0) - ($model->stok_keluar ?? 0);
        });

        static::updated(function (UnitAC $model) {
            if ($model->isDirty('harga_dealer') || $model->isDirty('harga_ecommerce') || $model->isDirty('harga_retail')) {
                $model->hargaHistory()->create([
                    'harga_dealer' => $model->harga_dealer,
                    'harga_ecommerce' => $model->harga_ecommerce,
                    'harga_retail' => $model->harga_retail,
                    'karyawan_id' => Auth::id(),
                ]);
            }
        });
    }

    /**
     * Accessor to always return computed stok_akhir, ensuring consistency on read.
     */
    protected function stokAkhir(): Attribute
    {
        return Attribute::make(
            get: function ($value, array $attributes) {
                $stokAwal = (int) ($attributes['stok_awal'] ?? 0);
                $stokMasuk = (int) ($attributes['stok_masuk'] ?? 0);
                $stokKeluar = (int) ($attributes['stok_keluar'] ?? 0);

                return $stokAwal + $stokMasuk - $stokKeluar;
            },
            set: function ($value) {
                // Persist as integer if explicitly set; will be overwritten on save by the formula.
                return (int) $value;
            }
        );
    }
}
