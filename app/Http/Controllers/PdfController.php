<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use App\Models\SparepartKeluar;
use App\Models\TransaksiJasa;
use App\Models\TransaksiProduk;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;

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

    public function generateSlipGaji(Karyawan $record)
    {
        $bulan = request('bulan', now()->month);
        $tahun = request('tahun', now()->year);

        // Selected period for display (e.g. "Desember 2025")
        $selectedDate = Carbon::create($tahun, $bulan, 1);
        $periode = $selectedDate->translatedFormat('F Y');

        // Data derived from previous month (e.g. November 2025)
        $dataDate = $selectedDate->copy()->subMonth();
        $dari = $dataDate->startOfMonth()->toDateString();
        $sampai = $dataDate->endOfMonth()->toDateString();

        $total_gaji = $record->getTotalGaji($dari, $sampai);

        $query = $record->karyawanPenghasilanDetail();

        if ($dari) {
            $query->whereDate('tanggal', '>=', $dari);
        }

        if ($sampai) {
            $query->whereDate('tanggal', '<=', $sampai);
        }

        $details = $query->get();

        $data = [
            'karyawan' => $record,
            'details' => $details,
            'gaji_pokok' => $record->gaji_pokok,
            'total_gaji' => $total_gaji,
            'periode' => $periode,
            'tanggal_cetak' => now(),
        ];

        $pdf = Pdf::loadView('pdf.karyawan.slip-gaji', $data);

        return $pdf->stream('slip-gaji-'.$record->nama.'-'.$periode.'.pdf');
    }

    public function generateBulkSlipGajiZip()
    {
        $bulan = request('bulan', now()->month);
        $tahun = request('tahun', now()->year);

        // Display Period (e.g. December 2025)
        $selectedDate = Carbon::create($tahun, $bulan, 1);
        $periodeLabel = $selectedDate->translatedFormat('F Y');
        $periodeFilename = $selectedDate->format('F_Y');

        // Data Period (Previous Month, e.g. November 2025)
        $dataDate = $selectedDate->copy()->subMonth();
        $dari = $dataDate->startOfMonth()->toDateString();
        $sampai = $dataDate->endOfMonth()->toDateString();

        // Get all active employees
        $karyawans = Karyawan::whereNull('deleted_at')->get();

        if ($karyawans->isEmpty()) {
            return back()->with('error', 'Tidak ada data karyawan.');
        }

        // Create Zip Archive
        $zip = new \ZipArchive;
        $fileName = 'Slip_Gaji_Batch_'.$periodeFilename.'_'.now()->timestamp.'.zip';
        $path = storage_path('app/public/'.$fileName);

        if ($zip->open($path, \ZipArchive::CREATE) === true) {
            foreach ($karyawans as $karyawan) {
                // Calculation Logic (Same as single slip)
                $total_gaji = $karyawan->getTotalGaji($dari, $sampai);
                $query = $karyawan->karyawanPenghasilanDetail();
                if ($dari) {
                    $query->whereDate('tanggal', '>=', $dari);
                }
                if ($sampai) {
                    $query->whereDate('tanggal', '<=', $sampai);
                }
                $details = $query->get();

                // Row counting for compact mode
                $rowCount = 1;
                foreach ($details as $d) {
                    if ($d->lembur > 0) {
                        $rowCount++;
                    }
                    if ($d->bonus > 0) {
                        $rowCount++;
                    }
                    if ($d->kasbon > 0) {
                        $rowCount++;
                    }
                    if ($d->potongan > 0) {
                        $rowCount++;
                    }
                }
                $isCompact = $rowCount > 5;

                $data = [
                    'karyawan' => $karyawan,
                    'details' => $details,
                    'gaji_pokok' => $karyawan->gaji_pokok,
                    'total_gaji' => $total_gaji,
                    'periode' => $periodeLabel,
                    'tanggal_cetak' => now(),
                    'isCompact' => $isCompact,
                ];

                $pdf = Pdf::loadView('pdf.karyawan.slip-gaji', $data);

                // Add PDF to ZIP
                // Filename: Slip_Gaji_Nama_Periode.pdf
                // Sanitize name for filename
                $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $karyawan->nama);
                $pdfName = 'Slip_Gaji_'.$safeName.'_'.$periodeFilename.'.pdf';

                $zip->addFromString($pdfName, $pdf->output());
            }
            $zip->close();
        }

        return response()->download($path)->deleteFileAfterSend(true);
    }
}
