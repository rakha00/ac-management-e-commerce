<?php

namespace App\Models;

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
        'merk_id',
        'pk',
        'tipe_ac_id',
        'keterangan',
        'path_foto_produk',
        'harga_dealer',
        'harga_ecommerce',
        'harga_retail',
        'stok_awal',
        'stok_akhir',
        'stok_masuk',
        'stok_keluar',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'merk_id' => 'integer',
            'tipe_ac_id' => 'integer',
            'path_foto_produk' => 'array',
            'harga_dealer' => 'integer',
            'harga_ecommerce' => 'integer',
            'harga_retail' => 'integer',
            'stok_awal' => 'integer',
            'stok_akhir' => 'integer',
            'stok_masuk' => 'integer',
            'stok_keluar' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function merk(): BelongsTo
    {
        return $this->belongsTo(Merk::class, 'merk_id');
    }

    public function tipeAC(): BelongsTo
    {
        return $this->belongsTo(TipeAC::class, 'tipe_ac_id');
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

        static::updating(function (UnitAC $model) {
            $model->stok_akhir = ($model->stok_awal ?? 0) + ($model->stok_masuk ?? 0) - ($model->stok_keluar ?? 0);
        });

        static::created(function (UnitAC $model) {
            $model->hargaHistory()->create([
                'harga_dealer' => $model->harga_dealer,
                'harga_ecommerce' => $model->harga_ecommerce,
                'harga_retail' => $model->harga_retail,
                'updated_by' => Auth::id(),
            ]);
        });

        static::updated(function (UnitAC $model) {
            if ($model->isDirty('harga_dealer') || $model->isDirty('harga_ecommerce') || $model->isDirty('harga_retail')) {
                $model->hargaHistory()->create([
                    'harga_dealer' => $model->harga_dealer,
                    'harga_ecommerce' => $model->harga_ecommerce,
                    'harga_retail' => $model->harga_retail,
                    'updated_by' => Auth::id(),
                ]);
            }
        });
    }

    public function getTotalStokMasuk(?string $tanggalAwal, ?string $tanggalAkhir): int
    {
        $query = $this->hasMany(BarangMasukDetail::class, 'unit_ac_id')
            ->join('barang_masuk', 'barang_masuk.id', '=', 'barang_masuk_detail.barang_masuk_id');

        if (! empty($tanggalAwal)) {
            $query->whereDate('barang_masuk.tanggal', '>=', $tanggalAwal);
        }

        if (! empty($tanggalAkhir)) {
            $query->whereDate('barang_masuk.tanggal', '<=', $tanggalAkhir);
        }

        return $query->sum('jumlah_barang_masuk');
    }

    public function getTotalStokKeluar(?string $tanggalAwal, ?string $tanggalAkhir): int
    {
        $query = $this->hasMany(TransaksiProdukDetail::class, 'unit_ac_id')
            ->join('transaksi_produk', 'transaksi_produk.id', '=', 'transaksi_produk_detail.transaksi_produk_id');

        if (! empty($tanggalAwal)) {
            $query->whereDate('transaksi_produk.tanggal_transaksi', '>=', $tanggalAwal);
        }

        if (! empty($tanggalAkhir)) {
            $query->whereDate('transaksi_produk.tanggal_transaksi', '<=', $tanggalAkhir);
        }

        return $query->sum('jumlah_keluar');
    }

    public function getCalculatedStokAkhir(?string $tanggalAwal, ?string $tanggalAkhir): int
    {
        $stokMasuk = $this->getTotalStokMasuk($tanggalAwal, $tanggalAkhir);
        $stokKeluar = $this->getTotalStokKeluar($tanggalAwal, $tanggalAkhir);

        return $this->stok_awal + $stokMasuk - $stokKeluar;
    }
}
