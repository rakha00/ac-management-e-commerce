<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use App\Models\Karyawan;

/**
 * TransaksiProduk model for product transactions (stock out).
 * - Generates unique sequential invoice & surat jalan numbers per date
 * - Maintains aggregated totals based on details
 * - Uses soft deletes as requested
 */
class TransaksiProduk extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_produks';

    protected $fillable = [
        'tanggal_transaksi',
        'nomor_invoice',
        'nomor_surat_jalan',
        'sales_karyawan_id',
        'sales_nama',
        'toko_konsumen',
        'total_modal',
        'total_penjualan',
        'total_keuntungan',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'date',
            'total_modal' => 'decimal:2',
            'total_penjualan' => 'decimal:2',
            'total_keuntungan' => 'decimal:2',
        ];
    }

    // ============ RELATIONSHIPS ============
    public function salesKaryawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'sales_karyawan_id');
    }

    public function detailTransaksiProduk(): HasMany
    {
        return $this->hasMany(TransaksiProdukDetail::class, 'transaksi_produk_id');
    }

    // ============ MODEL EVENTS ============
    protected static function boot()
    {
        parent::boot();

        static::creating(function (TransaksiProduk $model) {
            // Ensure transaction date exists
            $date = $model->tanggal_transaksi ? Carbon::parse($model->tanggal_transaksi)->toDateString() : Carbon::now()->toDateString();
            $model->tanggal_transaksi = $date;

            // Auto-fill sales_nama from Karyawan when provided
            if ($model->sales_karyawan_id && empty($model->sales_nama)) {
                $sales = Karyawan::find($model->sales_karyawan_id);
                if ($sales && ($sales->jabatan === 'sales' || empty($sales->jabatan))) {
                    $model->sales_nama = $sales->nama;
                }
            }

            // Generate invoice & surat jalan if not set
            if (empty($model->nomor_invoice)) {
                $model->nomor_invoice = static::generateSequentialNumber($date, 'INV');
            }
            if (empty($model->nomor_surat_jalan)) {
                $model->nomor_surat_jalan = static::generateSequentialNumber($date, 'SJ');
            }
        });

        static::updating(function (TransaksiProduk $model) {
            // If sales_karyawan_id changes, keep sales_nama in sync
            if ($model->isDirty('sales_karyawan_id')) {
                $sales = $model->sales_karyawan_id ? Karyawan::find($model->sales_karyawan_id) : null;
                $model->sales_nama = $sales ? $sales->nama : null;
            }
        });

        static::saved(function (TransaksiProduk $model) {
            // Keep totals in sync whenever parent is saved
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

        // Include trashed to avoid reusing numbers that exist in soft-deleted rows (unique constraint applies)
        $builder = static::withTrashed()->whereDate('tanggal_transaksi', Carbon::parse($date));

        // Choose appropriate column based on prefix
        $column = $prefix === 'INV' ? 'nomor_invoice' : 'nomor_surat_jalan';

        // Get existing numbers for the given date and prefix
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
        $details = $this->detailTransaksiProduk()->get();

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

