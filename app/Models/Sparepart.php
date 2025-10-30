<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
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
        'kode_sparepart',
        'nama_sparepart',
        'harga_modal',
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
            'harga_modal' => 'integer',
            'stok_awal' => 'integer',
            'stok_masuk' => 'integer',
            'stok_keluar' => 'integer',
            'created_by' => 'string',
            'updated_by' => 'string',
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
        return $this->hasMany(HargaSparepartHistory::class, 'sparepart_id');
    }

    protected $appends = ['stok_akhir'];

    /**
     * Accessor for stok_akhir.
     * Computed as: stok_awal + stok_masuk - stok_keluar
     */
    protected function stokAkhir(): Attribute
    {
        return Attribute::make(
            get: fn () => (int) ($this->stok_awal ?? 0) + (int) ($this->stok_masuk ?? 0) - (int) ($this->stok_keluar ?? 0),
        );
    }

    /**
     * Keep a physical column value in sync before saving if required in other places.
     * Note: Even though it's computed, we mirror UnitAC behavior to provide a persisted value if needed by queries.
     */
    protected static function booted(): void
    {
        static::saving(function (Sparepart $sp) {
            $sp->stok_akhir = (int) ($sp->stok_awal ?? 0)
                + (int) ($sp->stok_masuk ?? 0)
                - (int) ($sp->stok_keluar ?? 0);

            if ($sp->isDirty('harga_modal') && $sp->exists) {
                $sp->hargaHistory()->create([
                    'harga_modal' => $sp->harga_modal,
                    'karyawan_id' => Auth::id(),
                ]);
            }
        });
    }
}
