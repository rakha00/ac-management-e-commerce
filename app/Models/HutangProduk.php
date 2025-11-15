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
        'sisa_hutang',
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
            'barang_masuk_id' => 'integer',
            'total_hutang' => 'integer',
            'sisa_hutang' => 'integer',
            'jatuh_tempo' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function hutangProdukCicilanDetail(): HasMany
    {
        return $this->hasMany(HutangProdukCicilanDetail::class);
    }

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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public static function calculateTotalHutang(int $barangMasukId): int
    {
        $barangMasuk = BarangMasuk::find($barangMasukId);

        if (! $barangMasuk) {
            return 0;
        }

        return (int) $barangMasuk->barangMasukDetail->sum(function ($detail) {
            return $detail->jumlah_barang_masuk * ($detail->unitAC->harga_dealer ?? 0);
        });
    }

    public function recalculatePaymentStatus(): void
    {
        $totalCicilan = $this->hutangProdukCicilanDetail()->sum('nominal_cicilan');
        $totalHutang = (int) ($this->total_hutang ?? 0);
        $sisa = max($totalHutang - (int) $totalCicilan, 0);

        $status = 'belum lunas';
        if ($sisa <= 0 && $totalHutang > 0) {
            $status = 'sudah lunas';
        } elseif ($sisa < $totalHutang && $sisa > 0) {
            $status = 'tercicil';
        }

        $this->forceFill([
            'status_pembayaran' => $status,
            'sisa_hutang' => $sisa,
        ])->save();
    }
}
