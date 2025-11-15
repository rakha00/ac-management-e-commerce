<?php

namespace App\Exports;

use App\Models\TransaksiJasa;
use App\Models\TransaksiJasaDetail;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class TransaksiJasaExport extends ExcelExport implements WithHeadings, WithMapping
{
    protected static $lastParentId = null;

    protected static array $filters = [];

    public function __construct(string $name = 'TransaksiJasaExport')
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

        $transaksiJasa = TransaksiJasa::query();

        if (! empty($filters['tanggal_transaksi']['dari'])) {
            $transaksiJasa->whereDate('tanggal_transaksi', '>=', $filters['tanggal_transaksi']['dari']);
        }

        if (! empty($filters['tanggal_transaksi']['sampai'])) {
            $transaksiJasa->whereDate('tanggal_transaksi', '<=', $filters['tanggal_transaksi']['sampai']);
        }

        if (! empty($filters['trashed'])) {
            if ($filters['trashed'] === 'with') {
                $transaksiJasa->withTrashed();
            } elseif ($filters['trashed'] === 'only') {
                $transaksiJasa->onlyTrashed();
            }
        }

        $transaksiJasaIds = $transaksiJasa->pluck('id');

        $query = TransaksiJasaDetail::with('transaksiJasa.teknisi', 'transaksiJasa.helper', 'transaksiJasa.konsumen', 'transaksiJasa.detailTransaksiJasa')
            ->whereIn('transaksi_jasa_id', $transaksiJasaIds);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nomor Invoice',
            'Nomor Surat Jalan',
            'Teknisi',
            'Helper',
            'Konsumen',
            'Tanggal',
            'Jenis Jasa',
            'Qty',
            'Harga Jasa',
            'Pengeluaran Jasa',
            'Sub Total Pendapatan',
            'Sub Total Keuntungan',
        ];
    }

    public function map($record): array
    {
        $currentParentId = $record->transaksiJasa->id;

        $tanggal = Carbon::parse($record->transaksiJasa->tanggal_transaksi)->format('d-m-Y');
        $subTotalPendapatan = $record->transaksiJasa->detailTransaksiJasa->sum(fn ($detail) => $detail->subtotal_pendapatan);
        $subTotalKeuntungan = $record->transaksiJasa->detailTransaksiJasa->sum(fn ($detail) => $detail->subtotal_keuntungan);

        if (self::$lastParentId === $currentParentId) {
            $nomorInvoice = '';
            $nomorSuratJalan = '';
            $teknisi = '';
            $helper = '';
            $konsumen = '';
            $tanggalFormatted = '';
            $subTotalPendapatanValue = '';
            $subTotalKeuntunganValue = '';
        } else {
            $nomorInvoice = $record->transaksiJasa->nomor_invoice_jasa;
            $nomorSuratJalan = $record->transaksiJasa->nomor_surat_jalan_jasa;
            $teknisi = $record->transaksiJasa?->teknisi?->nama ?? '-';
            $helper = $record->transaksiJasa?->helper?->nama ?? '-';
            $konsumen = $record->transaksiJasa?->konsumen?->nama ?? '-';
            $tanggalFormatted = $tanggal;
            $subTotalPendapatanValue = $subTotalPendapatan;
            $subTotalKeuntunganValue = $subTotalKeuntungan;

            self::$lastParentId = $currentParentId;
        }

        return [
            $nomorInvoice,
            $nomorSuratJalan,
            $teknisi,
            $helper,
            $konsumen,
            $tanggalFormatted,
            $record->jenis_jasa,
            $record->qty,
            $record->harga_jasa,
            $record->pengeluaran_jasa,
            $subTotalPendapatanValue,
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
        ];
    }
}
