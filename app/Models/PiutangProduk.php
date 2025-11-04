<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PiutangProduk extends Model
{
    use SoftDeletes;

    protected $table = 'piutang_produk';

    protected $fillable = [
        'transaksi_produk_id',
        'total_piutang',
        'sisa_piutang',
        'status_pembayaran',
        'jatuh_tempo',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'transaksi_produk_id' => 'integer',
            'total_piutang' => 'integer',
            'sisa_piutang' => 'integer',
            'jatuh_tempo' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function piutangProdukCicilanDetail(): HasMany
    {
        return $this->hasMany(PiutangProdukCicilanDetail::class);
    }

    public function transaksiProduk(): BelongsTo
    {
        return $this->belongsTo(TransaksiProduk::class);
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

    public static function calculateTotalPiutang(int $transaksiProdukId): int
    {
        $transaksiProduk = TransaksiProduk::find($transaksiProdukId);

        if (! $transaksiProduk) {
            return 0;
        }

        return (int) $transaksiProduk->transaksiProdukDetail->sum(function ($detail) {
            return $detail->jumlah_keluar * $detail->harga_jual;
        });
    }

    public function recalculatePaymentStatus(): void
    {
        $totalCicilan = $this->piutangProdukCicilanDetail()->sum('nominal_cicilan');
        $totalPiutang = (int) ($this->total_piutang ?? 0);
        $sisa = max($totalPiutang - (int) $totalCicilan, 0);

        $status = 'belum lunas';
        if ($sisa <= 0 && $totalPiutang > 0) {
            $status = 'sudah lunas';
        } elseif ($sisa < $totalPiutang && $sisa > 0) {
            $status = 'tercicil';
        }

        $this->forceFill([
            'status_pembayaran' => $status,
            'sisa_piutang' => $sisa,
        ])->save();
    }
}
