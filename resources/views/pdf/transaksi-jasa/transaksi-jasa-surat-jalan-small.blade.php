<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Transaksi Produk Surat Jalan - PT Global Servis Int</title>
    <style>
        @page {
            size: 9.5in 5.5in;
            margin: 0.5in 0.75in;
        }
    </style>
</head>

<body style="font-family: Arial, sans-serif; font-size: 9pt; line-height: 1.2; margin: 0; padding: 0;">

    <div style="width: 100%; box-sizing: border-box;">

        <table style="width: 100%; border-collapse: collapse;">
            <tr>
                <td style="width: 50%; vertical-align: top; padding: 1px;">
                    <strong style="font-size: 30pt; display: block;">GLOBAL SERVIS INT</strong>
                    Jl. Ir H. Juanda No.20A, Cemp. Putih, Kec. Ciputat Timur, Kota Tangerang Selatan<br>
                    Telp. (021) 74778082<br>
                    Hp. 0856-8564-3257<br>
                    Follow Instagram @pt_globalserviceint
                </td>

                <td style="width: 30%; vertical-align: top; padding: 1px;">
                    <table style="width: 100%; border-collapse: collapse; border: 1px solid #000;">
                        <tr>
                            <td style="width: 25%; border-right: 1px solid #000; padding: 3px;">TGL</td>
                            <td style="padding: 3px;">
                                {{ \Carbon\Carbon::parse($transaksiJasa->tanggal_transaksi)->format('d F Y') }}
                            </td>
                        </tr>
                    </table>

                    <div style="border: 1px solid #000; border-top: none; padding: 3px;">
                        KEPADA YTH :
                        <div style="margin-top: 3px;">
                            {{ $transaksiJasa->konsumen->nama }}<br>
                            {{ $transaksiJasa->konsumen->alamat }}<br>
                            {{ $transaksiJasa->konsumen->telepon }}
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="vertical-align: top; padding: 1px; padding-top: 5px;">NO. SURAT JALAN :
                    {{ $transaksiJasa->nomor_surat_jalan_jasa }}
                </td>
                <td style="vertical-align: top; padding: 1px;"></td>
            </tr>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 8px;">
            <thead>
                <tr>
                    <th
                        style="width: 20%; border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                        Banyaknya</th>
                    <th
                        style="width: 80%; border: 1px solid #000; padding: 3px; text-align: left; background-color: #f2f2f2;">
                        Nama Barang</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $item_count = $transaksiJasa->detailTransaksiJasa->count();
                    $items = $transaksiJasa->detailTransaksiJasa;
                    // Add placeholder rows if item count is 8 or less
                    $placeholder_needed = max(0, 8 - $item_count);
                    $is_last_item = false;
                @endphp
                @forelse($items as $index => $detail)
                    @php
                        $is_last_item = $index === $items->count() - 1;
                    @endphp
                    <tr>
                        <td
                            style="border: 1px solid #000; padding: 3px; text-align: left; vertical-align: top; border-bottom: {{ $is_last_item && $placeholder_needed == 0 ? '1px solid #000' : 'none' }}; border-top: none;">
                            {{ $detail->qty }} Unit
                        </td>
                        <td
                            style="border: 1px solid #000; padding: 3px; text-align: left; vertical-align: top; border-bottom: {{ $is_last_item && $placeholder_needed == 0 ? '1px solid #000' : 'none' }}; border-top: none;">
                            {{ $detail->jenis_jasa }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td style="border: 1px solid #000; padding: 3px; text-align: left; vertical-align: top; border-bottom: 1px solid #000;"
                            colspan="2">Tidak ada data</td>
                    </tr>
                @endforelse

                @if ($item_count > 0 && $placeholder_needed > 0)
                    @for ($i = 0; $i < $placeholder_needed; $i++)
                        <tr>
                            <td
                                style="border: 1px solid #000; padding: 3px; text-align: left; vertical-align: top; border-bottom: {{ $i == $placeholder_needed - 1 ? '1px solid #000' : 'none' }}; border-top: none;">
                                &nbsp;
                            </td>
                            <td
                                style="border: 1px solid #000; padding: 3px; text-align: left; vertical-align: top; border-bottom: {{ $i == $placeholder_needed - 1 ? '1px solid #000' : 'none' }}; border-top: none;">
                                &nbsp;
                            </td>
                        </tr>
                    @endfor
                @endif
            </tbody>
        </table>

        <table style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <tr>
                <td style="width: 35%; text-align: center; vertical-align: top;">
                    Customer,
                    <span style="display: block; margin-top: 40px;">(............................)</span>
                </td>

                <td style="width: 35%; text-align: center; vertical-align: top;">
                    Hormat kami,

                    <div style="margin-top: 8px;">
                        <img src="{{ public_path('img/GSI.png') }}" alt="GLOBAL SERVIS INT"
                            style="width: 60px; height: 60px;">
                    </div>
                </td>
            </tr>
        </table>

    </div>
</body>

</html>
