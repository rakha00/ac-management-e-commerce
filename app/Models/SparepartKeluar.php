<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class SparepartKeluar extends Model
{
    use SoftDeletes;

    protected $table = 'sparepart_keluar';

    protected $fillable = [
        'tanggal_keluar',
        'nomor_invoice',
        'konsumen_id',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_keluar' => 'date',
            'konsumen_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'konsumen_id');
    }

    public function detailSparepartKeluar(): HasMany
    {
        return $this->hasMany(SparepartKeluarDetail::class, 'sparepart_keluar_id');
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

    public function getTotalModal(): int
    {
        return $this->detailSparepartKeluar->sum(function ($detail) {
            return $detail->jumlah_keluar * $detail->harga_modal;
        });
    }

    public function getTotalPenjualan(): int
    {
        return $this->detailSparepartKeluar->sum(function ($detail) {
            return $detail->jumlah_keluar * $detail->harga_jual;
        });
    }

    public function getTotalKeuntungan(): int
    {
        return $this->getTotalPenjualan() - $this->getTotalModal();
    }

    public static function generateSequentialNumber(string $date, string $prefix = 'INV-SP'): string
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
