<?php

namespace App\Exports;

use App\Models\SparepartMasuk;
use App\Models\SparepartMasukDetail;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class SparepartMasukExport extends ExcelExport implements WithHeadings, WithMapping
{
    protected static $lastParentId = null;

    protected static array $filters = [];

    public function __construct(string $name = 'SparepartMasukExport')
    {
        parent::__construct($name);
    }

    public static function withFilters(array $filters): self
    {
        static::$filters = $filters;

        return new static;
    }

    // TODO : Add filter for trashed
    public function query()
    {
        $filters = static::$filters;

        $sparepartMasuk = SparepartMasuk::query();

        if (! empty($filters['distributor_id']['value'])) {
            $sparepartMasuk->where('distributor_id', $filters['distributor_id']['value']);
        }

        if (! empty($filters['date_range']['dari'])) {
            $sparepartMasuk->whereDate('tanggal_masuk', '>=', $filters['date_range']['dari']);
        }

        if (! empty($filters['date_range']['sampai'])) {
            $sparepartMasuk->whereDate('tanggal_masuk', '<=', $filters['date_range']['sampai']);
        }

        $sparepartMasukIds = $sparepartMasuk->pluck('id');

        $query = SparepartMasukDetail::with('sparepart', 'sparepartMasuk.distributor', 'sparepartMasuk.detailSparepartMasuk')
            ->whereIn('sparepart_masuk_id', $sparepartMasukIds);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nomor SM',
            'Distributor',
            'Tanggal',
            'Total Qty',
            'Kode Sparepart',
            'Nama Sparepart',
            'Jumlah',
        ];
    }

    public function map($record): array
    {
        $currentParentId = $record->sparepartMasuk->id;

        $tanggal = Carbon::parse($record->sparepartMasuk->tanggal_masuk)->format('d-m-Y');
        $totalQty = $record->sparepartMasuk->detailSparepartMasuk->sum('jumlah_masuk');

        if (self::$lastParentId === $currentParentId) {
            // Kalau sama parent, kosongkan kolom parent supaya tidak duplikat
            $nomorSM = '';
            $distributor = '';
            $tanggalFormatted = '';
            $totalQtyValue = '';
        } else {
            // Kalau beda parent, tampilkan data dan update lastParentId
            $nomorSM = $record->sparepartMasuk->nomor_sparepart_masuk;
            $distributor = $record->sparepartMasuk->distributor->nama_distributor ?? '-';
            $tanggalFormatted = $tanggal;
            $totalQtyValue = $totalQty;

            self::$lastParentId = $currentParentId;
        }

        return [
            $nomorSM,
            $distributor,
            $tanggalFormatted,
            $totalQtyValue,
            $record->sparepart->kode_sparepart ?? '-',
            $record->sparepart->nama_sparepart ?? '-',
            $record->jumlah_masuk,
        ];
    }
}
