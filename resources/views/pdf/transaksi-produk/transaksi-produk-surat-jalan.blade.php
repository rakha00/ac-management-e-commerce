<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transaksi Produk Surat Jalan - PT Global Servis Int</title>
</head>

<body style="font-family: Arial, sans-serif; font-size: 11pt; margin: 0; padding: 30px 40px; color: #000;">

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                @include('pdf.components.company-header')

                @include('pdf.components.customer-info', ['konsumen' => $transaksiProduk->konsumen])
            </td>

            <td style="width: 40%; vertical-align: top; padding-left: 20px;">
                <!-- Surat Jalan Info -->
                <div
                    style="font-weight: bold; font-size: 16pt; margin-bottom: 10px; margin-top: 54px; text-align: right;">
                    SURAT JALAN</div>
                <div style="border: 1px solid #0072bc; font-size: 10pt; line-height: 1.5;">
                    <table style="width: 100%;">
                        <tr>
                            <td style="width:45%;">Date</td>
                            <td>: {{ \Carbon\Carbon::parse($transaksiProduk->tanggal_transaksi)->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td>No. Surat Jalan</td>
                            <td>: {{ $transaksiProduk->nomor_surat_jalan }}</td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt;">
        <thead>
            <tr>
                <th
                    style="background-color: #0072bc; color: white; font-weight: bold; padding: 8px; border: 1px solid #0072bc; text-align: center; width: 10%;">
                    No.</th>
                <th
                    style="background-color: #0072bc; color: white; font-weight: bold; padding: 8px; border: 1px solid #0072bc; text-align: center; width: 70%;">
                    Nama Barang</th>
                <th
                    style="background-color: #0072bc; color: white; font-weight: bold; padding: 8px; border: 1px solid #0072bc; text-align: center; width: 20%;">
                    Qty</th>
            </tr>
        </thead>
        <tbody>
            @php
                $maxItems = 13; // Maximum items per page
                $totalItems = $transaksiProduk->transaksiProdukDetail->count();
                $remainingItems = $totalItems - $maxItems;
                $currentItemNumber = 0; // Global counter for sequential numbering
                $totalPages = ceil($totalItems / $maxItems);
            @endphp

            @foreach ($transaksiProduk->transaksiProdukDetail->take($maxItems) as $item)
                @php
                    $currentItemNumber++;
                @endphp
                <tr>
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; vertical-align: top; text-align: center; border-bottom: {{ $currentItemNumber == $maxItems ? '1px solid #0072bc' : 'none' }}">
                        {{ $currentItemNumber }}
                    </td>
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; vertical-align: top; text-align: left; border-bottom: {{ $currentItemNumber == $maxItems ? '1px solid #0072bc' : 'none' }}">
                        {{ $item->unitAC->nama_unit ?? 'N/A' }}
                    </td>
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; vertical-align: top; text-align: center; border-bottom: {{ $currentItemNumber == $maxItems ? '1px solid #0072bc' : 'none' }}">
                        {{ $item->jumlah_keluar }}
                    </td>
                </tr>
            @endforeach

            @if ($remainingItems > 0)
                <tr>
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: center; font-style: italic; color: #666;">
                        ...
                    </td>
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: left; font-style: italic; color: #666;">
                        +{{ $remainingItems }} artikel lainnya (Lihat halaman berikutnya)
                    </td>
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: center; font-style: italic; color: #666;">
                        ...
                    </td>
                </tr>
            @else
                @for ($i = 0; $i < $maxItems - $totalItems; $i++)
                    <tr>
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: {{ $i == $maxItems - $totalItems - 1 ? '1px solid #0072bc' : 'none' }}; vertical-align: top; text-align: center;">
                            &nbsp;
                        </td>
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: {{ $i == $maxItems - $totalItems - 1 ? '1px solid #0072bc' : 'none' }}; vertical-align: top; text-align: left;">
                            &nbsp;
                        </td>
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: {{ $i == $maxItems - $totalItems - 1 ? '1px solid #0072bc' : 'none' }}; vertical-align: top; text-align: center;">
                            &nbsp;
                        </td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <!-- Show footer directly on first page if only 1 page -->
    @if ($totalPages == 1)
        <table style="width: 100%; margin-top: 50px; font-size: 11pt">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                    <div style="margin-bottom: 60px;">
                        Customer<br />
                        <br />
                        <br />
                        <br />
                        <br />
                        ( ........................... )
                    </div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                    <div style="margin-bottom: 60px;">
                        Teknisi<br />
                        <br />
                        <br />
                        <br />
                        <br />
                        ( ........................... )
                    </div>
                </td>
            </tr>
        </table>
    @endif

    <!-- Generate additional pages if needed -->
    @for ($page = 2; $page <= $totalPages; $page++)
        <div style="page-break-before: always;"></div>

        <!-- Page {{ $page }} Header -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    @include('pdf.components.company-header')

                    @include('pdf.components.customer-info', ['konsumen' => $transaksiProduk->konsumen])
                </td>

                <td style="width: 40%; vertical-align: top; padding-left: 20px;">
                    <!-- Surat Jalan Info -->
                    <div
                        style="font-weight: bold; font-size: 16pt; margin-bottom: 10px; margin-top: 54px; text-align: right;">
                        SURAT JALAN (Halaman {{ $page }})</div>
                    <div style="border: 1px solid #0072bc; font-size: 10pt; line-height: 1.5;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width:45%;">Date</td>
                                <td>: {{ \Carbon\Carbon::parse($transaksiProduk->tanggal_transaksi)->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td>No. Surat Jalan</td>
                                <td>: {{ $transaksiProduk->nomor_surat_jalan }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Page {{ $page }} Table -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt;">
            <thead>
                <tr>
                    <th
                        style="background-color: #0072bc; color: white; font-weight: bold; padding: 8px; border: 1px solid #0072bc; text-align: center; width: 10%;">
                        No.</th>
                    <th
                        style="background-color: #0072bc; color: white; font-weight: bold; padding: 8px; border: 1px solid #0072bc; text-align: center; width: 70%;">
                        Nama Barang</th>
                    <th
                        style="background-color: #0072bc; color: white; font-weight: bold; padding: 8px; border: 1px solid #0072bc; text-align: center; width: 20%;">
                        Qty</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $pageStart = ($page - 1) * $maxItems;
                    $pageItems = $transaksiProduk->transaksiProdukDetail->skip($pageStart)->take($maxItems);
                @endphp

                @foreach ($pageItems as $item)
                    @php
                        $currentItemNumber++;
                    @endphp
                    <tr>
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: center;">
                            {{ $currentItemNumber }}
                        </td>
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: left;">
                            {{ $item->unitAC->nama_unit ?? 'N/A' }}
                        </td>
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: center;">
                            {{ $item->jumlah_keluar }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endfor

    <!-- Summary page (only show if there are multiple pages) -->
    @if ($totalPages > 1)
        <div style="page-break-before: always;"></div>

        <!-- Summary Header -->
        <table style="width: 100%; border-collapse: collapse; margin-bottom: 15px;">
            <tr>
                <td style="width: 60%; vertical-align: top;">
                    @include('pdf.components.company-header')

                    @include('pdf.components.customer-info', ['konsumen' => $transaksiProduk->konsumen])
                </td>

                <td style="width: 40%; vertical-align: top; padding-left: 20px;">
                    <!-- Surat Jalan Summary Info -->
                    <div
                        style="font-weight: bold; font-size: 16pt; margin-bottom: 10px; margin-top: 54px; text-align: right;">
                        SURAT JALAN SUMMARY</div>
                    <div style="border: 1px solid #0072bc; font-size: 10pt; line-height: 1.5;">
                        <table style="width: 100%;">
                            <tr>
                                <td style="width:45%;">Date</td>
                                <td>: {{ \Carbon\Carbon::parse($transaksiProduk->tanggal_transaksi)->format('d/m/Y') }}
                                </td>
                            </tr>
                            <tr>
                                <td>No. Surat Jalan</td>
                                <td>: {{ $transaksiProduk->nomor_surat_jalan }}</td>
                            </tr>
                            <tr>
                                <td>Total Items</td>
                                <td>: {{ $totalItems }}</td>
                            </tr>
                            <tr>
                                <td>Total Pages</td>
                                <td>: {{ $totalPages }}</td>
                            </tr>
                        </table>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Summary Footer -->
        <table style="width: 100%; margin-top: 50px; font-size: 11pt">
            <tr>
                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                    <div style="margin-bottom: 60px;">
                        Customer<br />
                        <br />
                        <br />
                        <br />
                        <br />
                        ( ........................... )
                    </div>
                </td>
                <td style="width: 50%; text-align: center; vertical-align: bottom;">
                    <div style="margin-bottom: 60px;">
                        Teknisi<br />
                        <br />
                        <br />
                        <br />
                        <br />
                        ( ........................... )
                    </div>
                </td>
            </tr>
        </table>
    @endif

</body>

</html>
