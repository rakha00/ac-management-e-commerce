<!DOCTYPE html>
<html>

<head>
    <title>Invoice Transaksi Produk</title>
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
                                {{ \Carbon\Carbon::parse($transaksiProduk->tanggal_transaksi)->format('d F Y') }}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2">KEPADA YTH :</td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $transaksiProduk->konsumen->nama }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $transaksiProduk->konsumen->alamat }}</td>
                        </tr>
                        <tr>
                            <td colspan="2">{{ $transaksiProduk->konsumen->telepon }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>

        <p style="font-weight: bold;">NO. {{ $transaksiProduk->nomor_surat_jalan }}</p>

        <table class="product-table">
            <thead>
                <tr>
                    <th style="width: 10%;">Banyaknya</th>
                    <th style="width: 90%;">Nama Barang</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totalPenjualan = 0;
                @endphp
                @foreach($transaksiProduk->transaksiProdukDetail as $detail)
                    <tr>
                        <td class="text-center">{{ $detail->jumlah_keluar . ' Unit' }}</td>
                        <td>{{ $detail->unitAC->nama_unit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

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