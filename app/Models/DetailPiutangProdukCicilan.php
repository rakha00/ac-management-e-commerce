<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPiutangProdukCicilan extends Model
{
    use SoftDeletes;

    protected $table = 'detail_piutang_produk_cicilan';

    protected $fillable = [
        'piutang_produk_id',
        'nominal_cicilan',
        'tanggal_cicilan',
    ];

    protected function casts(): array
    {
        return [
            'nominal_cicilan' => 'integer',
            'tanggal_cicilan' => 'date',
        ];
    }

    public function piutangProduk(): BelongsTo
    {
        return $this->belongsTo(PiutangProduk::class);
    }
}
