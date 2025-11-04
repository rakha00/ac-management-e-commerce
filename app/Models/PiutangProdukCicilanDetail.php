<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class PiutangProdukCicilanDetail extends Model
{
    use SoftDeletes;

    protected $table = 'piutang_produk_cicilan_detail';

    protected $fillable = [
        'piutang_produk_id',
        'nominal_cicilan',
        'tanggal_cicilan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'piutang_produk_id' => 'integer',
            'nominal_cicilan' => 'integer',
            'tanggal_cicilan' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function piutangProduk(): BelongsTo
    {
        return $this->belongsTo(PiutangProduk::class);
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

        // Otomatis hitung sisa piutang saat data cicilan berubah
        static::created(fn (PiutangProdukCicilanDetail $detail) => $detail->piutangProduk->recalculatePaymentStatus());
        static::updated(fn (PiutangProdukCicilanDetail $detail) => $detail->piutangProduk->recalculatePaymentStatus());
        static::deleted(fn (PiutangProdukCicilanDetail $detail) => $detail->piutangProduk->recalculatePaymentStatus());
        static::restored(fn (PiutangProdukCicilanDetail $detail) => $detail->piutangProduk->recalculatePaymentStatus());
    }
}
