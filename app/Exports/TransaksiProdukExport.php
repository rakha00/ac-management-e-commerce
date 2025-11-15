<?php

namespace App\Exports;

use App\Models\TransaksiProduk;
use App\Models\TransaksiProdukDetail;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class TransaksiProdukExport extends ExcelExport implements WithHeadings, WithMapping
{
    protected static $lastParentId = null;

    protected static array $filters = [];

    public function __construct(string $name = 'TransaksiProdukExport')
    {
        parent::__construct($name);
    }

    public static function withFilters(array $filters): self
    {
        static::$filters = $filters;

        return new static;
    }

    public function query()
    {
        $filters = static::$filters;

        $transaksiProduk = TransaksiProduk::query();

        if (! empty($filters['sales_karyawan_id']['value'])) {
            $transaksiProduk->where('sales_karyawan_id', $filters['sales_karyawan_id']['value']);
        }

        if (! empty($filters['konsumen_id']['value'])) {
            $transaksiProduk->where('konsumen_id', $filters['konsumen_id']['value']);
        }

        if (! empty($filters['date_range']['dari'])) {
            $transaksiProduk->whereDate('tanggal_transaksi', '>=', $filters['date_range']['dari']);
        }

        if (! empty($filters['date_range']['sampai'])) {
            $transaksiProduk->whereDate('tanggal_transaksi', '<=', $filters['date_range']['sampai']);
        }

        $transaksiProdukIds = $transaksiProduk->pluck('id');

        $query = TransaksiProdukDetail::with('unitAC', 'transaksiProduk.salesKaryawan', 'transaksiProduk.konsumen', 'transaksiProduk.transaksiProdukDetail')
            ->whereIn('transaksi_produk_id', $transaksiProdukIds);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nomor Invoice',
            'Nomor Surat Jalan',
            'Sales',
            'Konsumen',
            'Tanggal',
            'SKU',
            'Nama Unit',
            'Jumlah Keluar',
            'Harga Modal',
            'Harga Jual',
            'Sub Total Modal',
            'Sub Total Penjualan',
            'Sub Total Keuntungan',
        ];
    }

    public function map($record): array
    {
        $currentParentId = $record->transaksiProduk->id;

        $tanggal = Carbon::parse($record->transaksiProduk->tanggal_transaksi)->format('d-m-Y');
        $subTotalModal = $record->transaksiProduk->transaksiProdukDetail->sum(fn ($detail) => $detail->harga_modal * $detail->jumlah_keluar);
        $subTotalPenjualan = $record->transaksiProduk->transaksiProdukDetail->sum(fn ($detail) => $detail->harga_jual * $detail->jumlah_keluar);
        $subTotalKeuntungan = $record->transaksiProduk->transaksiProdukDetail->sum(fn ($detail) => ($detail->harga_jual - $detail->harga_modal) * $detail->jumlah_keluar);

        if (self::$lastParentId === $currentParentId) {
            $nomorInvoice = '';
            $nomorSuratJalan = '';
            $sales = '';
            $konsumen = '';
            $tanggalFormatted = '';
            $subTotalModalValue = '';
            $subTotalPenjualanValue = '';
            $subTotalKeuntunganValue = '';
        } else {
            $nomorInvoice = $record->transaksiProduk->nomor_invoice;
            $nomorSuratJalan = $record->transaksiProduk->nomor_surat_jalan;
            $sales = $record->transaksiProduk?->salesKaryawan->nama ?? '-';
            $konsumen = $record->transaksiProduk?->konsumen?->nama ?? '-';
            $tanggalFormatted = $tanggal;
            $subTotalModalValue = $subTotalModal;
            $subTotalPenjualanValue = $subTotalPenjualan;
            $subTotalKeuntunganValue = $subTotalKeuntungan;

            self::$lastParentId = $currentParentId;
        }

        return [
            $nomorInvoice,
            $nomorSuratJalan,
            $sales,
            $konsumen,
            $tanggalFormatted,
            $record->unitAC->sku,
            $record->unitAC->nama_unit,
            $record->jumlah_keluar,
            $record->harga_modal,
            $record->harga_jual,
            $subTotalModalValue,
            $subTotalPenjualanValue,
            $subTotalKeuntunganValue,
        ];
    }

    public function columnFormats(): array
    {
        return [
            'I' => '"Rp"#,##0_-',
            'J' => '"Rp"#,##0_-',
            'K' => '"Rp"#,##0_-',
            'L' => '"Rp"#,##0_-',
            'M' => '"Rp"#,##0_-',
        ];
    }
}
