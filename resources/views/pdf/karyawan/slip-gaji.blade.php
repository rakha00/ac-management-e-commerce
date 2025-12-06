<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Slip Gaji</title>
    <style>
        @page {
            size: 210mm 148mm; /* A5 Landscape */
            margin: 10mm; /* Standard margin */
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 10pt;
            color: #111;
            margin: 0;
            padding: 0;
            position: relative;
            height: 100%;
        }
        /* Compact Mode Overrides */
        body.compact {
            font-size: 8pt;
        }
        body.compact .header {
            margin-bottom: 10px;
            padding-bottom: 5px;
        }
        body.compact .header h1 {
            font-size: 14pt;
        }
        body.compact .info-table {
            margin-bottom: 10px;
        }
        body.compact .salary-table th, 
        body.compact .salary-table td {
            padding: 4px 5px;
        }
        body.compact .total-row td {
            padding: 5px 5px;
            font-size: 10pt;
        }
        body.compact .signature-line {
            margin-top: 30px;
        }

        .header {
            display: flex;
            align-items: center;
            border-bottom: 2px solid #d32f2f;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header img {
            height: 35px;
            margin-right: 15px;
            vertical-align: middle;
        }
        .header h1 {
            display: inline;
            font-size: 16pt;
            margin: 0;
            color: #d32f2f;
            text-transform: uppercase;
            vertical-align: middle;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table {
            margin-bottom: 20px;
        }
        .info-table td {
            padding: 4px 0;
        }
        .label {
            width: 100px;
            font-weight: bold;
            color: #555;
        }
        .salary-table th {
            text-align: left;
            padding: 8px 5px;
            background-color: #f5f5f5;
            text-transform: uppercase;
            font-size: 9pt;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #ddd;
        }
        .salary-table td {
            padding: 8px 5px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .total-row td {
            font-weight: bold;
            font-size: 11pt;
            color: #000;
            border-top: 2px solid #111;
            border-bottom: none;
            background-color: #fff;
            padding: 10px 5px;
        }
        .footer {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 100%;
            text-align: right;
        }
        .signature {
            display: inline-block;
            text-align: center;
            width: 200px;
        }
        .signature-line {
            margin-top: 60px;
            border-top: 1px solid #000;
            padding-top: 5px;
            font-weight: bold;
        }
        /* Utility */
        .text-red { color: #d32f2f; }
    </style>
</head>
@php
    // Calculate total actual rows to be rendered
    $rowCount = 1; // Start with 1 for Gaji Pokok
    if(isset($details)) {
        foreach($details as $detail) {
            if($detail->lembur > 0) $rowCount++;
            if($detail->bonus > 0) $rowCount++;
            if($detail->kasbon > 0) $rowCount++;
            if($detail->potongan > 0) $rowCount++;
        }
    }
    // Threshold: if more than 5 rows (besides standard UI elements), switch to compact
    // A5 Landscape is tight. 
    $isCompact = $rowCount > 3; 
@endphp
<body class="{{ $isCompact ? 'compact' : '' }}">
    <div class="header">
        <img src="{{ public_path('img/GSI.png') }}" alt="GSI Logo">
        <h1>Global Service Int.</h1>
    </div>

    <table class="info-table">
        <tr>
            <td class="label">NAMA</td>
            <td width="10">:</td>
            <td><strong>{{ $karyawan->nama }}</strong></td>
            <td class="text-right">PERIODE: <strong>{{ strtoupper($periode) }}</strong></td>
        </tr>
        <tr>
            <td class="label">JABATAN</td>
            <td>:</td>
            <td>{{ $karyawan->jabatan }}</td>
            <td></td>
        </tr>
    </table>

    <table class="salary-table">
        <thead>
            <tr>
                <th>KETERANGAN</th>
                <th class="text-right">JUMLAH (RP)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Gaji Pokok</td>
                <td class="text-right">{{ number_format($karyawan->gaji_pokok, 0, ',', '.') }}</td>
            </tr>
            @if(isset($details) && $details->count() > 0)
                @foreach($details as $detail)
                    @php
                        $date = \Carbon\Carbon::parse($detail->tanggal)->format('d/m/Y');
                    @endphp
                    @if($detail->lembur > 0)
                    <tr>
                        <td>Lembur ({{ $date }})</td>
                        <td class="text-right">{{ number_format($detail->lembur, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($detail->bonus > 0)
                    <tr>
                        <td>Bonus ({{ $date }})</td>
                        <td class="text-right">{{ number_format($detail->bonus, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($detail->kasbon > 0)
                    <tr>
                        <td>Kasbon ({{ $date }})</td>
                        <td class="text-right text-red">-{{ number_format($detail->kasbon, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                    @if($detail->potongan > 0)
                    <tr>
                        <td>Potongan ({{ $date }})</td>
                        <td class="text-right text-red">-{{ number_format($detail->potongan, 0, ',', '.') }}</td>
                    </tr>
                    @endif
                @endforeach
            @endif
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td>TOTAL DITERIMA</td>
                <td class="text-right">{{ number_format($total_gaji, 0, ',', '.') }}</td>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <div class="signature">
            <p>Jakarta, {{ \Carbon\Carbon::parse($tanggal_cetak)->translatedFormat('d F Y') }}</p>
            <p>Penerima</p>
            <div class="signature-line">{{ $karyawan->nama }}</div>
        </div>
    </div>
</body>
</html>
