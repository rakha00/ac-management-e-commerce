<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PiutangJasa extends Model
{
    use SoftDeletes;

    protected $table = 'piutang_jasa';

    protected $fillable = [
        'no_kode_jasa',
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

    public function detailPiutangJasaCicilan(): HasMany
    {
        return $this->hasMany(DetailPiutangJasaCicilan::class);
    }
}
