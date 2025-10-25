<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * SparepartKeluar model for outgoing spareparts (stock out).
 * - Generates unique sequential "nomor_invoice" per date
 * - Syncs konsumen_nama from KonsumenSparepart
 * - Aggregates totals (modal, penjualan, keuntungan) from details
 * - Uses soft deletes
 */
class SparepartKeluar extends Model
{
    use SoftDeletes;

    protected $table = 'sparepart_keluars';

    protected $fillable = [
        'tanggal_keluar',
        'nomor_invoice',
        'konsumen_sparepart_id',
        'konsumen_nama',
        'total_modal',
        'total_penjualan',
        'total_keuntungan',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_keluar' => 'date',
            'total_modal' => 'decimal:2',
            'total_penjualan' => 'decimal:2',
            'total_keuntungan' => 'decimal:2',
        ];
    }

    // ============ RELATIONSHIPS ============
    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(KonsumenSparepart::class, 'konsumen_sparepart_id');
    }

    public function detailSparepartKeluar(): HasMany
    {
        return $this->hasMany(SparepartKeluarDetail::class, 'sparepart_keluar_id');
    }

    // ============ MODEL EVENTS ============
    protected static function boot()
    {
        parent::boot();

        static::creating(function (SparepartKeluar $model) {
            // Ensure date exists
            $date = $model->tanggal_keluar ? Carbon::parse($model->tanggal_keluar)->toDateString() : Carbon::now()->toDateString();
            $model->tanggal_keluar = $date;

            // Sync konsumen_nama if konsumen selected
            if ($model->konsumen_sparepart_id && empty($model->konsumen_nama)) {
                $cust = KonsumenSparepart::find($model->konsumen_sparepart_id);
                if ($cust) {
                    $model->konsumen_nama = $cust->nama_konsumen;
                }
            }

            // Generate nomor_invoice if not set
            if (empty($model->nomor_invoice)) {
                // Use INVSP prefix to distinguish from Produk invoices
                $model->nomor_invoice = static::generateSequentialNumber($date, 'INVSP');
            }
        });

        static::updating(function (SparepartKeluar $model) {
            // Keep konsumen name in sync when konsumen changes
            if ($model->isDirty('konsumen_sparepart_id')) {
                $cust = $model->konsumen_sparepart_id ? KonsumenSparepart::find($model->konsumen_sparepart_id) : null;
                $model->konsumen_nama = $cust ? $cust->nama_konsumen : null;
            }
        });

        static::saved(function (SparepartKeluar $model) {
            // Maintain totals in sync
            $model->recalcFromDetails();
        });
    }

    // ============ HELPERS ============
    /**
     * Generate sequential number per date with format PREFIX-YYYYMMDD-####.
     */
    public static function generateSequentialNumber(string $date, string $prefix): string
    {
        $ymd = Carbon::parse($date)->format('Ymd');

        // Include trashed to avoid reusing numbers in soft-deleted rows
        $builder = static::withTrashed()->whereDate('tanggal_keluar', Carbon::parse($date));

        $column = 'nomor_invoice';

        $existing = $builder
            ->where($column, 'like', "{$prefix}-{$ymd}-%")
            ->pluck($column)
            ->filter();

        $max = 0;
        foreach ($existing as $no) {
            $seq = static::extractSequence($no, $prefix, $ymd);
            if ($seq > $max) {
                $max = $seq;
            }
        }

        $next = $max + 1;
        $seqStr = str_pad((string) $next, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$ymd}-{$seqStr}";
    }

    /**
     * Extract sequence integer from formatted number.
     */
    protected static function extractSequence(?string $no, string $prefix, string $ymd): int
    {
        if (!$no) {
            return 0;
        }

        $pattern = '/^' . preg_quote($prefix, '/') . '-' . preg_quote($ymd, '/') . '-(\d{4})$/';
        if (preg_match($pattern, $no, $m)) {
            return (int) $m[1];
        }

        return 0;
    }

    /**
     * Recalculate totals from non-trashed details.
     */
    public function recalcFromDetails(): void
    {
        $details = $this->detailSparepartKeluar()->get();

        $totalModal = 0.0;
        $totalPenjualan = 0.0;

        foreach ($details as $d) {
            $qty = (int) ($d->jumlah_keluar ?? 0);
            $modal = (float) ($d->harga_modal ?? 0);
            $jual = (float) ($d->harga_jual ?? 0);

            $totalModal += $modal * $qty;
            $totalPenjualan += $jual * $qty;
        }

        $this->forceFill([
            'total_modal' => $totalModal,
            'total_penjualan' => $totalPenjualan,
            'total_keuntungan' => max($totalPenjualan - $totalModal, 0),
        ])->saveQuietly();
    }
}
