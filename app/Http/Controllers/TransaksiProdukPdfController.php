<?php

namespace App\Http\Controllers;

use App\Models\TransaksiProduk;
use Barryvdh\DomPDF\Facade\Pdf;

class TransaksiProdukPdfController extends Controller
{
    public function generateInvoice(TransaksiProduk $record)
    {
        $data = [
            'transaksiProduk' => $record,
        ];

        $pdf = Pdf::loadView('pdf.transaksi-produk-invoice', $data);

        return $pdf->download($record->nomor_invoice.'.pdf');
    }

    public function generateSuratJalan(TransaksiProduk $record)
    {
        $data = [
            'transaksiProduk' => $record,
        ];

        $pdf = Pdf::loadView('pdf.transaksi-produk-surat-jalan', $data);

        return $pdf->download($record->nomor_surat_jalan.'.pdf');
    }
}
