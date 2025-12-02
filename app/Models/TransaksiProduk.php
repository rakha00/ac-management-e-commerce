<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransaksiProduk extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_produk';

    protected $fillable = [
        'nomor_invoice',
        'nomor_surat_jalan',
        'tanggal_transaksi',
        'sales_karyawan_id',
        'konsumen_id',
        'keterangan',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'date',
            'sales_karyawan_id' => 'integer',
            'konsumen_id' => 'integer',
            'created_by' => 'integer',
            'updated_by' => 'integer',
            'deleted_by' => 'integer',
        ];
    }

    public function transaksiProdukDetail(): HasMany
    {
        return $this->hasMany(TransaksiProdukDetail::class, 'transaksi_produk_id');
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'konsumen_id');
    }

    public function salesKaryawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'sales_karyawan_id')->where('jabatan', 'sales');
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

    protected static function boot()
    {
        parent::boot();

        // Set created_by  when creating
        static::creating(function (self $transaksiProduk): void {
            if (auth()->check()) {
                $transaksiProduk->created_by = auth()->id();
                $transaksiProduk->updated_by = auth()->id();
            }
        });

        // Set updated_by when updating
        static::updating(function (self $transaksiProduk): void {
            if (auth()->check()) {
                $transaksiProduk->updated_by = auth()->id();
            }
        });

        // Set deleted_by when soft deleting
        static::deleting(function (self $transaksiProduk): void {
            if (! $transaksiProduk->isForceDeleting()) {
                if (auth()->check()) {
                    $transaksiProduk->deleted_by = auth()->id();
                    $transaksiProduk->save(); // Save the model to persist the deleted_by value
                }
            }
        });
    }

    public static function generateNomorInvoice(string $date): string
    {
        $tanggal = Carbon::parse($date);
        $ymd = $tanggal->format('Ymd');

        $lastInvoice = self::withTrashed()
            ->where('nomor_invoice', 'like', "INV-{$ymd}-%")
            ->get()
            ->map(function ($item) {
                if (preg_match('/INV-(\d{8})-(\d{2})$/', $item->nomor_invoice, $matches)) {
                    return (int) $matches[2];
                }

                return 0;
            })
            ->max();

        $nextInvoice = ($lastInvoice ?? 0) + 1;

        return "INV-{$ymd}-".str_pad($nextInvoice, 2, '0', STR_PAD_LEFT);
    }

    public static function generateNomorSuratJalan(string $date): string
    {
        $tanggal = Carbon::parse($date);
        $ymd = $tanggal->format('Ymd');

        $lastSuratJalan = self::withTrashed()
            ->where('nomor_surat_jalan', 'like', "SJ-{$ymd}-%")
            ->get()
            ->map(function ($item) {
                if (preg_match('/SJ-(\d{8})-(\d{2})$/', $item->nomor_surat_jalan, $matches)) {
                    return (int) $matches[2];
                }

                return 0;
            })
            ->max();

        $nextSuratJalan = ($lastSuratJalan ?? 0) + 1;

        return "SJ-{$ymd}-".str_pad($nextSuratJalan, 2, '0', STR_PAD_LEFT);
    }
}
