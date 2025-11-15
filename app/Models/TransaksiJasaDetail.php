<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiJasaDetail extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_jasa_detail';

    protected $fillable = [
        'transaksi_jasa_id',
        'jenis_jasa',
        'qty',
        'harga_jasa',
        'keterangan_jasa',
        'pengeluaran_jasa',
        'keterangan_pengeluaran',
        'subtotal_pendapatan',
        'subtotal_keuntungan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'transaksi_jasa_id' => 'integer',
            'qty' => 'integer',
            'harga_jasa' => 'integer',
            'pengeluaran_jasa' => 'integer',
            'subtotal_pendapatan' => 'integer',
            'subtotal_keuntungan' => 'integer',
            'created_by' => 'string',
            'updated_by' => 'string',
            'deleted_by' => 'string',
        ];
    }

    public function transaksiJasa(): BelongsTo
    {
        return $this->belongsTo(TransaksiJasa::class, 'transaksi_jasa_id');
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

        static::saving(function (TransaksiJasaDetail $detail) {
            $qty = (int) ($detail->qty ?? 0);
            $hargaJasa = (float) ($detail->harga_jasa ?? 0);
            $pengeluaranJasa = (float) ($detail->pengeluaran_jasa ?? 0);

            $detail->subtotal_pendapatan = $qty * $hargaJasa;
            $detail->subtotal_keuntungan = $detail->subtotal_pendapatan - $pengeluaranJasa;
        });
    }
}
