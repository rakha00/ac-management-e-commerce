<!DOCTYPE html>
<html>

<head>
    <title>Invoice Sparepart Keluar</title>
    <style>
        body {
            font-family: sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }

        .container {
            padding: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 8px;
            vertical-align: top;
        }

        .no-border {
            border: none;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .header-company {
            /* font-size: 18pt; */
            /* font-weight: bold; */
            margin-bottom: 5px;
        }

        .header-address {
            font-size: 10pt;
            margin-bottom: 20px;
        }

        .invoice-details {
            width: 100%;
            margin-bottom: 20px;
            margin-top: 20px;
        }

        .invoice-details td {
            padding: 2px 5px;
            border: none;
            vertical-align: top;
        }

        .product-table th {
            background-color: #f2f2f2;
        }

        .total-row td {
            font-weight: bold;
        }

        .signature-section {
            margin-top: 40px;
            width: 100%;
        }

        .signature-section td {
            width: 33%;
            text-align: center;
            border: none;
            padding-top: 20px;
        }

        .bank-details {
            margin-top: 20px;
            font-size: 9pt;
        }

        .bank-details p {
            margin: 2px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <table>
            <tr>
                <td class="no-border" style="width: 60%;">
                    <div class="header-company">
                        <img src="{{ public_path('img/GSI-landscape.png') }}" alt="GLOBAL SERVICE int."
                            style="width: 160px;">
                    </div>
                    <div class="header-address">
                        Jl. Cempaka Raya No. 25 Bintaro<br>
                        Telp. (021) 7363352, 73887561<br>
                        Fax. (021) 73887561<br>
                        Hp. 0878.8215.8459
                    </div>
                </td>
                <td class="no-border" style="width: 40%;">
                    <table class="invoice-details">
                        <tr>
                            <td style="width: 30%;">TGL.</td>
                            <td style="width: 70%;">
                                {{ \Carbon\Carbon::parse($sparepartKeluar->tanggal_keluar)->format('d F Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">KEPADA YTH :</td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $sparepartKeluar->konsumen->nama }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $sparepartKeluar->konsumen->alamat }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $sparepartKeluar->konsumen->telepon }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <p style="font-weight: bold;">NO. {{ $sparepartKeluar->nomor_invoice }}</p>

        <table class="product-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Banyaknya</th>
                    <th style="width: 50%;">Nama Barang</th>
                    <th style="width: 20%;">Harga</th>
                    <th style="width: 20%;">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPenjualan = 0;
                @endphp
                @foreach($sparepartKeluar->detailSparepartKeluar as $detail)
                    <tr>
                        <td class="text-center">{{ $detail->jumlah_keluar }}</td>
                        <td>{{ $detail->sparepart->nama_sparepart }}</td>
                        <td class="text-right">Rp {{ number_format($detail->harga_jual, 0, ',', '.') }}</td>
                        <td class="text-right">Rp
                            {{ number_format($detail->harga_jual * $detail->jumlah_keluar, 0, ',', '.') }}
                        </td>
                    </tr>
                    @php
                        $totalPenjualan += $detail->harga_jual * $detail->jumlah_keluar;
                    @endphp
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total-row">
                    <td colspan="3" class="text-right">Total Rp.</td>
                    <td class="text-right">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>

        <div class="bank-details">
            <p><strong>Pembayaran Via Transfer :</strong></p>
            <p>a/n. PT. GLOBAL SERVIS INT</p>
            <p>MANDIRI : 10.100.123.5555.6</p>
            <p>BRI : 0524-0100-0338-304</p>
            <p>BCA : 4500-526-926</p>
        </div>

        <table class="signature-section">
            <tr>
                <td>Customer,</td>
                <td>Teknisi,</td>
                <td>Hormat kami,</td>
            </tr>
            <tr>
                <td><br><br><br></td>
                <td><br><br><br></td>
                <td><br><br><br></td>
            </tr>
            <tr>
                <td>(.........................)</td>
                <td>(.........................)</td>
                <td>(.........................)</td>
            </tr>
        </table>
    </div>
</body>

</html>