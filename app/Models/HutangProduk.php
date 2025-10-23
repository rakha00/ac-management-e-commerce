<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class HutangProduk extends Model
{
    use SoftDeletes;

    protected $table = 'hutang_produk';

    protected $fillable = [
        'no_unit_masuk',
        'total_hutang',
        'nama_principle',
        'status_pembayaran',
        'jatuh_tempo',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'total_hutang' => 'integer',
            'jatuh_tempo' => 'date',
        ];
    }

    public function detailHutangProdukCicilan(): HasMany
    {
        return $this->hasMany(DetailHutangProdukCicilan::class);
    }
}
