<?php

namespace App\Exports;

use App\Models\SparepartKeluar;
use App\Models\SparepartKeluarDetail;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class SparepartKeluarExport extends ExcelExport implements WithHeadings, WithMapping
{
    protected static $lastParentId = null;

    protected static array $filters = [];

    public function __construct(string $name = 'SparepartKeluarExport')
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

        $sparepartKeluar = SparepartKeluar::query();

        if (! empty($filters['date_from']['value'])) {
            $sparepartKeluar->whereDate('tanggal_keluar', '>=', $filters['date_from']['value']);
        }

        if (! empty($filters['date_until']['value'])) {
            $sparepartKeluar->whereDate('tanggal_keluar', '<=', $filters['date_until']['value']);
        }

        $sparepartKeluarIds = $sparepartKeluar->pluck('id');

        $query = SparepartKeluarDetail::with('sparepart', 'sparepartKeluar.konsumen', 'sparepartKeluar.detailSparepartKeluar')
            ->whereIn('sparepart_keluar_id', $sparepartKeluarIds);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nomor Invoice',
            'Konsumen',
            'Tanggal',
            'Nama Sparepart',
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
        $currentParentId = $record->sparepartKeluar->id;

        $tanggal = Carbon::parse($record->sparepartKeluar->tanggal_keluar)->format('d-m-Y');
        $subTotalModal = $record->sparepartKeluar->detailSparepartKeluar->sum(fn ($detail) => $detail->harga_modal * $detail->jumlah_keluar);
        $subTotalPenjualan = $record->sparepartKeluar->detailSparepartKeluar->sum(fn ($detail) => $detail->harga_jual * $detail->jumlah_keluar);
        $subTotalKeuntungan = $record->sparepartKeluar->detailSparepartKeluar->sum(fn ($detail) => ($detail->harga_jual - $detail->harga_modal) * $detail->jumlah_keluar);

        if (self::$lastParentId === $currentParentId) {
            $nomorInvoice = '';
            $konsumen = '';
            $tanggalFormatted = '';
            $subTotalModalValue = '';
            $subTotalPenjualanValue = '';
            $subTotalKeuntunganValue = '';
        } else {
            $nomorInvoice = $record->sparepartKeluar->nomor_invoice;
            $konsumen = $record->sparepartKeluar?->konsumen?->nama ?? '-';
            $tanggalFormatted = $tanggal;
            $subTotalModalValue = $subTotalModal;
            $subTotalPenjualanValue = $subTotalPenjualan;
            $subTotalKeuntunganValue = $subTotalKeuntungan;

            self::$lastParentId = $currentParentId;
        }

        return [
            $nomorInvoice,
            $konsumen,
            $tanggalFormatted,
            $record->sparepart->nama_sparepart,
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
            'F' => '"Rp"#,##0_-',
            'G' => '"Rp"#,##0_-',
            'H' => '"Rp"#,##0_-',
            'I' => '"Rp"#,##0_-',
            'J' => '"Rp"#,##0_-',
        ];
    }
}
