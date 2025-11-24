<?php

namespace App\Http\Controllers;

use App\Models\SparepartKeluar;
use App\Models\TransaksiJasa;
use App\Models\TransaksiProduk;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generateTransaksiProdukInvoice(TransaksiProduk $record, $format_type = 'a4')
    {
        $data = [
            'transaksiProduk' => $record,
            'format_type' => $format_type,
        ];

        if ($format_type === 'a4') {
            $pdf = Pdf::loadView('pdf.transaksi-produk.transaksi-produk-invoice', $data);
        } else {
            $pdf = Pdf::loadView('pdf.transaksi-produk.transaksi-produk-invoice-small', $data);
        }

        return $pdf->stream($record->nomor_invoice.'.pdf');
    }

    public function generateTransaksiProdukSuratJalan(TransaksiProduk $record, $format_type = 'a4')
    {
        $data = [
            'transaksiProduk' => $record,
            'format_type' => $format_type,
        ];

        if ($format_type === 'a4') {
            $pdf = Pdf::loadView('pdf.transaksi-produk.transaksi-produk-surat-jalan', $data);
        } else {
            $pdf = Pdf::loadView('pdf.transaksi-produk.transaksi-produk-surat-jalan-small', $data);
        }

        return $pdf->stream($record->nomor_surat_jalan.'.pdf');
    }

    public function generateSparepartKeluarInvoice(SparepartKeluar $record, $format_type = 'a4')
    {
        $data = [
            'sparepartKeluar' => $record,
            'format_type' => $format_type,
        ];

        if ($format_type === 'a4') {
            $pdf = Pdf::loadView('pdf.sparepart-keluar.sparepart-keluar-invoice', $data);
        } else {
            $pdf = Pdf::loadView('pdf.sparepart-keluar.sparepart-keluar-invoice-small', $data);
        }

        return $pdf->stream($record->nomor_invoice.'.pdf');
    }

    public function generateTransaksiJasaInvoice(TransaksiJasa $record, $format_type = 'a4')
    {
        $data = [
            'transaksiJasa' => $record,
            'format_type' => $format_type,
        ];

        if ($format_type === 'a4') {
            $pdf = Pdf::loadView('pdf.transaksi-jasa.transaksi-jasa-invoice', $data);
        } else {
            $pdf = Pdf::loadView('pdf.transaksi-jasa.transaksi-jasa-invoice-small', $data);
        }

        return $pdf->stream($record->nomor_invoice.'.pdf');
    }

    public function generateTransaksiJasaSuratJalan(TransaksiJasa $record, $format_type = 'a4')
    {
        $data = [
            'transaksiJasa' => $record,
            'format_type' => $format_type,
        ];

        if ($format_type === 'a4') {
            $pdf = Pdf::loadView('pdf.transaksi-jasa.transaksi-jasa-surat-jalan', $data);
        } else {
            $pdf = Pdf::loadView('pdf.transaksi-jasa.transaksi-jasa-surat-jalan-small', $data);
        }

        return $pdf->stream($record->nomor_surat_jalan.'.pdf');
    }
}
