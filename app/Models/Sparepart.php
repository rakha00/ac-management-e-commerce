<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Sparepart extends Model
{
    use SoftDeletes;

    protected $table = 'spareparts';

    protected $fillable = [
        'path_foto_sparepart',
        'kode_sparepart',
        'nama_sparepart',
        'harga_modal',
        'harga_ecommerce',
        'stok_awal',
        'stok_masuk',
        'stok_keluar',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'path_foto_sparepart' => 'array',
            'harga_modal' => 'integer',
            'harga_ecommerce' => 'integer',
            'stok_awal' => 'integer',
            'stok_masuk' => 'integer',
            'stok_keluar' => 'integer',
            'created_by' => 'string',
            'updated_by' => 'string',
            'deleted_by' => 'string',
        ];
    }

    public function sparepartMasukDetails(): HasMany
    {
        return $this->hasMany(SparepartMasukDetail::class, 'sparepart_id');
    }

    public function sparepartKeluarDetails(): HasMany
    {
        return $this->hasMany(SparepartKeluarDetail::class, 'sparepart_id');
    }

    public function hargaHistory(): HasMany
    {
        return $this->hasMany(HargaSparepartHistory::class, 'sparepart_id');
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

    protected static function booted(): void
    {
        static::creating(function (Sparepart $model) {
            $model->stok_akhir = ($model->stok_awal ?? 0) + ($model->stok_masuk ?? 0) - ($model->stok_keluar ?? 0);
        });

        static::updating(function (Sparepart $model) {
            $model->stok_akhir = ($model->stok_awal ?? 0) + ($model->stok_masuk ?? 0) - ($model->stok_keluar ?? 0);
        });

        static::created(function (Sparepart $model) {
            $model->hargaHistory()->create([
                'harga_modal' => $model->harga_modal,
                'harga_ecommerce' => $model->harga_ecommerce,
                'updated_by' => Auth::id(),
            ]);
        });

        static::updated(function (Sparepart $model) {
            if ($model->isDirty('harga_modal') || $model->isDirty('harga_ecommerce')) {
                $model->hargaHistory()->create([
                    'harga_modal' => $model->harga_modal,
                    'harga_ecommerce' => $model->harga_ecommerce,
                    'updated_by' => Auth::id(),
                ]);
            }
        });
    }

    public function getTotalStokMasuk(?string $tanggalAwal, ?string $tanggalAkhir): int
    {
        $query = SparepartMasukDetail::where('sparepart_id', $this->id)
            ->join('sparepart_masuk', 'sparepart_masuk.id', '=', 'sparepart_masuk_detail.sparepart_masuk_id');

        if (! empty($tanggalAwal)) {
            $query->whereDate('sparepart_masuk.tanggal_masuk', '>=', $tanggalAwal);
        }

        if (! empty($tanggalAkhir)) {
            $query->whereDate('sparepart_masuk.tanggal_masuk', '<=', $tanggalAkhir);
        }

        return $query->sum('jumlah_masuk');
    }

    public function getTotalStokKeluar(?string $tanggalAwal, ?string $tanggalAkhir): int
    {
        $query = SparepartKeluarDetail::where('sparepart_id', $this->id)
            ->join('sparepart_keluar', 'sparepart_keluar.id', '=', 'sparepart_keluar_detail.sparepart_keluar_id');

        if (! empty($tanggalAwal)) {
            $query->whereDate('sparepart_keluar.tanggal_keluar', '>=', $tanggalAwal);
        }

        if (! empty($tanggalAkhir)) {
            $query->whereDate('sparepart_keluar.tanggal_keluar', '<=', $tanggalAkhir);
        }

        return $query->sum('jumlah_keluar');
    }

    public function getCalculatedStokAkhir(?string $tanggalAwal, ?string $tanggalAkhir): int
    {
        $stokAwal = $this->stok_awal;
        $stokMasuk = $this->getTotalStokMasuk($tanggalAwal, $tanggalAkhir);
        $stokKeluar = $this->getTotalStokKeluar($tanggalAwal, $tanggalAkhir);

        return $stokAwal + $stokMasuk - $stokKeluar;
    }
}
