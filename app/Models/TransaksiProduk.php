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
        'tanggal_transaksi',
        'nomor_invoice',
        'nomor_surat_jalan',
        'sales_karyawan_id',
        'konsumen_id',
        'keterangan',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_transaksi' => 'date',
            'created_by' => 'integer',
            'updated_by' => 'integer',
        ];
    }

    public function transaksiProdukDetail(): HasMany
    {
        return $this->hasMany(TransaksiProdukDetail::class, 'transaksi_produk_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function konsumen(): BelongsTo
    {
        return $this->belongsTo(Konsumen::class, 'konsumen_id');
    }

    public function salesKaryawan(): BelongsTo
    {
        return $this->belongsTo(Karyawan::class, 'sales_karyawan_id');
    }

    /**
     * Always set tanggal_transaksi to current date if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (TransaksiProduk $model) {
            // Ensure transaction date exists
            $date = $model->tanggal_transaksi ? Carbon::parse($model->tanggal_transaksi)->toDateString() : Carbon::now()->toDateString();

            $model->nomor_invoice = self::generateNomorInvoice($date);
            $model->nomor_surat_jalan = self::generateNomorSuratJalan($date);
        });
    }

    public static function generateNomorInvoice(string $date): string
    {
        $tanggal = Carbon::parse($date);
        $ymd = $tanggal->format('Ymd');

        $lastInvoice = self::whereDate('tanggal_transaksi', $tanggal)
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

        $lastSuratJalan = self::whereDate('tanggal_transaksi', $tanggal)
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
