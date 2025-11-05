<?php

namespace App\Http\Controllers;

use App\Models\SparepartKeluar;
use App\Models\TransaksiJasa;
use App\Models\TransaksiProduk;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generateTransaksiProdukInvoice(TransaksiProduk $record)
    {
        $data = [
            'transaksiProduk' => $record,
        ];

        $pdf = Pdf::loadView('pdf.transaksi-produk-invoice', $data);

        return $pdf->download($record->nomor_invoice.'.pdf');
    }

    public function generateTransaksiProdukSuratJalan(TransaksiProduk $record)
    {
        $data = [
            'transaksiProduk' => $record,
        ];

        $pdf = Pdf::loadView('pdf.transaksi-produk-surat-jalan', $data);

        return $pdf->download($record->nomor_surat_jalan.'.pdf');
    }

    public function generateSparepartKeluarInvoice(SparepartKeluar $record)
    {
        $data = [
            'sparepartKeluar' => $record,
        ];

        $pdf = Pdf::loadView('pdf.sparepart-keluar-invoice', $data);

        return $pdf->download($record->nomor_invoice.'.pdf');
    }

    public function generateTransaksiJasaInvoice(TransaksiJasa $record)
    {
        $data = [
            'transaksiJasa' => $record,
        ];

        $pdf = Pdf::loadView('pdf.transaksi-jasa-invoice', $data);

        return $pdf->download($record->nomor_invoice_jasa.'.pdf');
    }

    public function generateTransaksiJasaSuratJalan(TransaksiJasa $record)
    {
        $data = [
            'transaksiJasa' => $record,
        ];

        $pdf = Pdf::loadView('pdf.transaksi-jasa-surat-jalan', $data);

        return $pdf->download($record->nomor_surat_jalan_jasa.'.pdf');
    }
}
