<?php

namespace App\Exports;

use App\Models\BarangMasuk;
use App\Models\BarangMasukDetail;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class BarangMasukExport extends ExcelExport implements WithHeadings, WithMapping
{
    protected static $lastParentId = null;

    protected static array $filters = [];

    public function __construct(string $name = 'BarangMasukExport')
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

        $barangMasuk = BarangMasuk::query();

        if (! empty($filters['principal_id']['value'])) {
            $barangMasuk->where('principal_id', $filters['principal_id']['value']);
        }

        if (! empty($filters['date_range']['dari'])) {
            $barangMasuk->whereDate('tanggal', '>=', $filters['date_range']['dari']);
        }

        if (! empty($filters['date_range']['sampai'])) {
            $barangMasuk->whereDate('tanggal', '<=', $filters['date_range']['sampai']);
        }

        $barangMasukIds = $barangMasuk->pluck('id');

        $query = BarangMasukDetail::with('unitAC', 'barangMasuk.principal', 'barangMasuk.barangMasukDetail')
            ->whereIn('barang_masuk_id', $barangMasukIds);

        return $query;
    }

    public function headings(): array
    {
        return [
            'Nomor BM',
            'Principal',
            'Tanggal',
            'Total Qty',
            'SKU',
            'Nama Unit',
            'Jumlah',
        ];
    }

    public function map($record): array
    {
        $currentParentId = $record->barangMasuk->id;

        $tanggal = Carbon::parse($record->barangMasuk->tanggal)->format('d-m-Y');
        $totalQty = $record->barangMasuk->barangMasukDetail->sum('jumlah_barang_masuk');

        if (self::$lastParentId === $currentParentId) {
            // Kalau sama parent, kosongkan kolom parent supaya tidak duplikat
            $nomorBM = '';
            $principal = '';
            $tanggalFormatted = '';
            $totalQtyValue = '';
        } else {
            // Kalau beda parent, tampilkan data dan update lastParentId
            $nomorBM = $record->barangMasuk->nomor_barang_masuk;
            $principal = $record->barangMasuk->principal->nama;
            $tanggalFormatted = $tanggal;
            $totalQtyValue = $totalQty;

            self::$lastParentId = $currentParentId;
        }

        return [
            $nomorBM,
            $principal,
            $tanggalFormatted,
            $totalQtyValue,
            $record->unitAC->sku,
            $record->unitAC->nama_unit,
            $record->jumlah_barang_masuk,
        ];
    }
}
