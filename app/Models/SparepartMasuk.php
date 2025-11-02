<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class SparepartMasuk extends Model
{
    use SoftDeletes;

    protected $table = 'sparepart_masuk';

    protected $fillable = [
        'tanggal_masuk',
        'nomor_sparepart_masuk',
        'distributor_sparepart_id',
        'distributor_nama',
        'total_qty',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'total_qty' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function distributor(): BelongsTo
    {
        return $this->belongsTo(DistributorSparepart::class, 'distributor_sparepart_id');
    }

    public function detailSparepartMasuk(): HasMany
    {
        return $this->hasMany(SparepartMasukDetail::class, 'sparepart_masuk_id');
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

        static::creating(function (SparepartMasuk $model) {
            // Ensure date exists
            $date = $model->tanggal_masuk ? Carbon::parse($model->tanggal_masuk)->toDateString() : Carbon::now()->toDateString();

            // Sync distributor_nama if distributor selected
            if ($model->distributor_sparepart_id && empty($model->distributor_nama)) {
                $dist = DistributorSparepart::find($model->distributor_sparepart_id);
                if ($dist) {
                    $model->distributor_nama = $dist->nama_distributor;
                }
            }

            // Generate nomor_sparepart_masuk if not set
            if (empty($model->nomor_sparepart_masuk)) {
                $model->nomor_sparepart_masuk = static::generateSequentialNumber($date, 'SM');
            }
        });

        static::updating(function (SparepartMasuk $model) {
            // Keep distributor name in sync when distributor changes
            if ($model->isDirty('distributor_sparepart_id')) {
                $dist = $model->distributor_sparepart_id ? DistributorSparepart::find($model->distributor_sparepart_id) : null;
                $model->distributor_nama = $dist ? $dist->nama_distributor : null;
            }
        });

        static::saved(function (SparepartMasuk $model) {
            // Maintain total qty in sync
            $model->recalcFromDetails();
        });
    }

    /**
     * Generate sequential number per date with format PREFIX-YYYYMMDD-####.
     */
    public static function generateSequentialNumber(string $date, string $prefix): string
    {
        $ymd = Carbon::parse($date)->format('Ymd');

        // Include trashed to avoid reusing numbers in soft-deleted rows
        $builder = static::withTrashed()->whereDate('tanggal_masuk', Carbon::parse($date));

        $column = 'nomor_sparepart_masuk';

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
     * Recalculate total_qty from non-trashed details.
     */
    public function recalcFromDetails(): void
    {
        $qty = (int) $this->detailSparepartMasuk()->sum('jumlah_masuk');

        $this->forceFill([
            'total_qty' => $qty,
        ])->saveQuietly();
    }
}
