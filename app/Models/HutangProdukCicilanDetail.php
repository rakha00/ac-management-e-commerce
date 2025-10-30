<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class HutangProdukCicilanDetail extends Model
{
    use SoftDeletes;

    protected $table = 'hutang_produk_cicilan_detail';

    protected $fillable = [
        'hutang_produk_id',
        'nominal_cicilan',
        'tanggal_cicilan',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'nominal_cicilan' => 'integer',
            'tanggal_cicilan' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function hutangProduk(): BelongsTo
    {
        return $this->belongsTo(HutangProduk::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
