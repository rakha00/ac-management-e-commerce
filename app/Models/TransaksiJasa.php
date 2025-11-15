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
        'nomor_invoice_jasa',
        'nomor_surat_jalan_jasa',
        'teknisi_karyawan_id',
        'helper_karyawan_id',
        'konsumen_id',
        'garansi_hari',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'teknisi_karyawan_id' => 'integer',
            'helper_karyawan_id' => 'integer',
            'konsumen_id' => 'integer',
            'tanggal_transaksi' => 'date',
            'garansi_hari' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'konsumen_id');
    }

    public function teknisi(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'teknisi_karyawan_id')->where('jabatan', 'teknisi');
    }

    public function helper(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'helper_karyawan_id')->where('jabatan', 'helper');
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

    public function deletedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public static function generateSequentialNumber(string $date, string $column, string $prefix): string
    {
        $ymd = Carbon::parse($date)->format('Ymd');

        // Include trashed to avoid reusing numbers that exist in soft-deleted rows
        $builder = static::withTrashed()->whereDate('tanggal_transaksi', Carbon::parse($date));

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
