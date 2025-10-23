<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PiutangProduk extends Model
{
    use SoftDeletes;

    protected $table = 'piutang_produk';

    protected $fillable = [
        'no_invoice',
        'total_piutang',
        'sisa_piutang',
        'status_pembayaran',
        'jatuh_tempo',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'total_piutang' => 'integer',
            'sisa_piutang' => 'integer',
            'jatuh_tempo' => 'date',
        ];
    }

    public function detailPiutangProdukCicilan(): HasMany
    {
        return $this->hasMany(DetailPiutangProdukCicilan::class);
    }
}
