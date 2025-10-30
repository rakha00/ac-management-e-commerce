<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class TransaksiProduk extends Model
{
    use SoftDeletes;

    protected $table = 'transaksi_produk';

    protected $fillable = [
        'tanggal_transaksi',
        'nomor_invoice',
        'nomor_surat_jalan',
        'sales_karyawan_id',
        'sales_nama',
        'toko_konsumen',
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

    /**
     * Always set tanggal_transaksi to current date if not provided.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function (TransaksiProduk $model) {
            // Ensure transaction date exists
            $date = $model->tanggal_transaksi ? Carbon::parse($model->tanggal_transaksi)->toDateString() : Carbon::now()->toDateString();

            // Auto-fill sales_nama from Karyawan when provided
            if ($model->sales_karyawan_id && empty($model->sales_nama)) {
                $sales = Karyawan::find($model->sales_karyawan_id);
                if ($sales && ($sales->jabatan === 'sales' || empty($sales->jabatan))) {
                    $model->sales_nama = $sales->nama;
                }
            }

            $model->nomor_invoice = self::generateNomorInvoice($date);
            $model->nomor_surat_jalan = self::generateNomorSuratJalan($date);
        });

        static::updating(function (TransaksiProduk $model) {
            // If sales_karyawan_id changes, keep sales_nama in sync
            if ($model->isDirty('sales_karyawan_id')) {
                $sales = $model->sales_karyawan_id ? Karyawan::find($model->sales_karyawan_id) : null;
                $model->sales_nama = $sales ? $sales->nama : null;
            }
        });
    }

    public static function generateNomorInvoice(string $date): string
    {
        $tanggal = Carbon::parse($date);
        $ymd = $tanggal->format('Ymd');

        $lastInvoice = self::whereDate('tanggal_transaksi', $tanggal)
            ->get()
            ->map(function ($item) {
                if (preg_match('/INV-(\d{8})-(\d{4})$/', $item->nomor_invoice, $matches)) {
                    return (int) $matches[2];
                }

                return 0;
            })
            ->max();

        $nextInvoice = ($lastInvoice ?? 0) + 1;

        return "INV-{$ymd}-".str_pad($nextInvoice, 0, '0', STR_PAD_LEFT);
    }

    public static function generateNomorSuratJalan(string $date): string
    {
        $tanggal = Carbon::parse($date);
        $ymd = $tanggal->format('Ymd');

        $lastSuratJalan = self::whereDate('tanggal_transaksi', $tanggal)
            ->get()
            ->map(function ($item) {
                if (preg_match('/SJ-(\d{8})-(\d{4})$/', $item->nomor_surat_jalan, $matches)) {
                    return (int) $matches[2];
                }

                return 0;
            })
            ->max();

        $nextSuratJalan = ($lastSuratJalan ?? 0) + 1;

        return "SJ-{$ymd}-".str_pad($nextSuratJalan, 0, '0', STR_PAD_LEFT);
    }
}
