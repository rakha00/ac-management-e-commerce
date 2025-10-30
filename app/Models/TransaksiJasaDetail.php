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
        'jenis_data',
        'qty',
        'harga_jasa',
        'keterangan_jasa',
        'pengeluaran_jasa',
        'keterangan_pengeluaran',
        'subtotal_pendapatan',
        'subtotal_keuntungan',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'qty' => 'integer',
            'harga_jasa' => 'integer',
            'pengeluaran_jasa' => 'integer',
            'subtotal_pendapatan' => 'integer',
            'subtotal_keuntungan' => 'integer',
            'created_by' => 'string',
            'updated_by' => 'string',
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

    /**
     * Boot the model to handle events.
     */
    protected static function boot()
    {
        parent::boot();

        // After create/update/delete/restore, recalc parent totals
        static::created(function (TransaksiJasaDetail $detail) {
            $detail->recalculateParent();
        });

        static::updated(function (TransaksiJasaDetail $detail) {
            $detail->recalculateParent();
        });

        static::deleted(function (TransaksiJasaDetail $detail) {
            $detail->recalculateParent();
        });

        static::restored(function (TransaksiJasaDetail $detail) {
            $detail->recalculateParent();
        });

        static::saving(function (TransaksiJasaDetail $detail) {
            $qty = (int) ($detail->qty ?? 0);
            $hargaJasa = (float) ($detail->harga_jasa ?? 0);
            $pengeluaranJasa = (float) ($detail->pengeluaran_jasa ?? 0);

            $detail->subtotal_pendapatan = $qty * $hargaJasa;
            $detail->subtotal_keuntungan = $detail->subtotal_pendapatan - $pengeluaranJasa;
        });
    }

    /**
     * Recalculate parent totals based on current non-trashed details.
     */
    private function recalculateParent(): void
    {
        $parent = $this->transaksiJasa;
        if ($parent instanceof TransaksiJasa) {
            // Delegate to parent's method for consistent aggregation
            $parent->recalcFromDetails();
        }
    }
}
