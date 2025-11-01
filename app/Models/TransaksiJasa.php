<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class TransaksiJasa extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_jasa';

    protected $fillable = [
        'tanggal_transaksi',
        'kode_jasa',
        'teknisi_karyawan_id',
        'helper_karyawan_id',
        'teknisi_nama',
        'helper_nama',
        'konsumen_id',
        'nama_konsumen',
        'garansi_hari',
        'total_pendapatan_jasa',
        'total_pengeluaran_jasa',
        'total_keuntungan_jasa',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'date',
            'garansi_hari' => 'integer',
            'total_pendapatan_jasa' => 'integer',
            'total_pengeluaran_jasa' => 'integer',
            'total_keuntungan_jasa' => 'integer',
            'created_by' => 'string',
            'updated_by' => 'string',
        ];
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'konsumen_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'teknisi_karyawan_id');
    }

    public function helper(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'helper_karyawan_id');
    }

    public function detailTransaksiJasa(): HasMany
    {
        return $this->hasMany(TransaksiJasaDetail::class, 'transaksi_jasa_id');
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

        static::creating(function (TransaksiJasa $model) {
            // Ensure transaction date exists
            $date = $model->tanggal_transaksi ? Carbon::parse($model->tanggal_transaksi)->toDateString() : Carbon::now()->toDateString();

            // Auto-fill teknisi/helper names from Karyawan when provided
            if ($model->teknisi_karyawan_id && empty($model->teknisi_nama)) {
                $k = Karyawan::find($model->teknisi_karyawan_id);
                if ($k) {
                    $model->teknisi_nama = $k->nama;
                }
            }
            if ($model->helper_karyawan_id && empty($model->helper_nama)) {
                $k = Karyawan::find($model->helper_karyawan_id);
                if ($k) {
                    $model->helper_nama = $k->nama;
                }
            }

            // Auto-fill nama_konsumen from Konsumen when provided
            if ($model->konsumen_id && empty($model->nama_konsumen)) {
                $konsumen = Konsumen::find($model->konsumen_id);
                if ($konsumen) {
                    $model->nama_konsumen = $konsumen->nama;
                }
            }

            // Generate kode_jasa if not set
            if (empty($model->kode_jasa)) {
                $model->kode_jasa = static::generateSequentialNumber($date, 'KJ');
            }
        });

        static::updating(function (TransaksiJasa $model) {
            // Keep teknisi/helper names in sync if relation changes
            if ($model->isDirty('teknisi_karyawan_id')) {
                $k = $model->teknisi_karyawan_id ? Karyawan::find($model->teknisi_karyawan_id) : null;
                $model->teknisi_nama = $k ? $k->nama : null;
            }
            if ($model->isDirty('helper_karyawan_id')) {
                $k = $model->helper_karyawan_id ? Karyawan::find($model->helper_karyawan_id) : null;
                $model->helper_nama = $k ? $k->nama : null;
            }

            // Keep nama_konsumen in sync if relation changes
            if ($model->isDirty('konsumen_id')) {
                $konsumen = $model->konsumen_id ? Konsumen::find($model->konsumen_id) : null;
                $model->nama_konsumen = $konsumen ? $konsumen->nama : null;
            }
        });

        static::saved(function (TransaksiJasa $model) {
            // Keep totals in sync whenever parent is saved
            $model->recalcFromDetails();
        });
    }

    /**
     * Generate sequential number per date with format PREFIX-YYYYMMDD-####.
     */
    public static function generateSequentialNumber(string $date, string $prefix): string
    {
        $ymd = Carbon::parse($date)->format('Ymd');

        // Include trashed to avoid reusing numbers that exist in soft-deleted rows
        $builder = static::withTrashed()->whereDate('tanggal_transaksi', Carbon::parse($date));

        // Column for numbering
        $column = 'kode_jasa';

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
        $seqStr = str_pad((string) $next, 2, '0', STR_PAD_LEFT);

        return "{$prefix}-{$ymd}-{$seqStr}";
    }

    /**
     * Extract sequence integer from formatted number.
     */
    protected static function extractSequence(?string $no, string $prefix, string $ymd): int
    {
        if (! $no) {
            return 0;
        }

        $pattern = '/^'.preg_quote($prefix, '/').'-'.preg_quote($ymd, '/').'-(\d{2})$/';
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
        $details = $this->detailTransaksiJasa()->get();

        $totalPendapatan = 0.0;
        $totalPengeluaran = 0.0;

        foreach ($details as $d) {
            $qty = (int) ($d->qty ?? 0);
            $harga = (float) ($d->harga_jasa ?? 0);
            $biaya = (float) ($d->pengeluaran_jasa ?? 0);

            $totalPendapatan += $harga * $qty;
            $totalPengeluaran += $biaya;
        }

        $this->forceFill([
            'total_pendapatan_jasa' => $totalPendapatan,
            'total_pengeluaran_jasa' => $totalPengeluaran,
            'total_keuntungan_jasa' => max($totalPendapatan - $totalPengeluaran, 0),
        ])->saveQuietly();
    }
}
