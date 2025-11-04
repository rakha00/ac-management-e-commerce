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
        'nomor_sparepart_masuk',
        'tanggal_masuk',
        'distributor_sparepart_id',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'distributor_sparepart_id' => 'integer',
            'tanggal_masuk' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Generate sequential number per date with format PREFIX-YYYYMMDD-####.
     */
    public static function generateSequentialNumber(string $date, string $prefix = 'SM'): string
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
}
