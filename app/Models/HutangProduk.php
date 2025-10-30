<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HutangProduk extends Model
{
    use SoftDeletes;

    protected $table = 'hutang_produk';

    protected $fillable = [
        'barang_masuk_id',
        'total_hutang',
        'status_pembayaran',
        'jatuh_tempo',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    public function barangMasuk(): BelongsTo
    {
        return $this->belongsTo(BarangMasuk::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    protected function casts(): array
    {
        return [
            'total_hutang' => 'integer',
            'jatuh_tempo' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function hutangProdukCicilanDetail(): HasMany
    {
        return $this->hasMany(HutangProdukCicilanDetail::class);
    }
}
