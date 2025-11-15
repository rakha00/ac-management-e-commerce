<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class BarangMasuk extends Model
{
    use SoftDeletes;

    protected $table = 'barang_masuk';

    protected $fillable = [
        'principal_id',
        'tanggal',
        'nomor_barang_masuk',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'principal_id' => 'integer',
            'tanggal' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function barangMasukDetail(): HasMany
    {
        return $this->hasMany(BarangMasukDetail::class);
    }

    public function principal(): BelongsTo
    {
        return $this->belongsTo(Principal::class);
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

    protected static function booted(): void
    {
        // Set created_by  when creating
        static::creating(function (self $barangMasuk): void {
            if (auth()->check()) {
                $barangMasuk->created_by = auth()->id();
                $barangMasuk->updated_by = auth()->id();
            }
        });

        // Set updated_by when updating
        static::updating(function (self $barangMasuk): void {
            if (auth()->check()) {
                $barangMasuk->updated_by = auth()->id();
            }
        });

        // On soft delete: delete related BarangMasukDetail first; FK is SET NULL so barang_masuk_id becomes null automatically.
        static::deleting(function (self $barangMasuk): void {
            if (! $barangMasuk->isForceDeleting()) {
                if (auth()->check()) {
                    $barangMasuk->deleted_by = auth()->id();
                    $barangMasuk->save(); // Save the model to persist the deleted_by value
                }
            }
        });

        // On permanent delete: ensure related BarangMasukDetail is removed if still present (defensive)
        static::forceDeleted(function (self $barangMasuk): void {
            $barangMasuk->barangMasukDetail()->delete();
        });
    }

    public static function generateNomorBarangMasuk(string $date, string $prefix = 'BM'): string
    {
        $ymd = Carbon::parse($date)->format('Ymd');

        // Include trashed to avoid reusing numbers that exist in soft-deleted rows
        $builder = static::withTrashed()->whereDate('tanggal', Carbon::parse($date));

        // Column for numbering
        $column = 'nomor_barang_masuk';

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
