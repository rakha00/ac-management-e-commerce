<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Transaksi Produk Invoice - PT Global Servis Int</title>
</head>

<body style="font-family: Arial, sans-serif; font-size: 11pt; margin: 0; padding: 30px 40px; color: #000;">

    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                @include('pdf.components.company-header')

                @include('pdf.components.customer-info', ['konsumen' => $transaksiProduk->konsumen])
            </td>

            <td style="width: 40%; vertical-align: top; padding-left: 20px;">
                @include('pdf.components.invoice-header', [
                    'title' => 'INVOICE',
                    'tanggal_transaksi' => $transaksiProduk->tanggal_transaksi,
                    'nomor_invoice' => $transaksiProduk->nomor_invoice,
                ])
            </td>
        </tr>
    </table>

    <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt;">
        @include('pdf.components.table-header')
        <tbody>
            @php
                $maxItems = 14;
                $totalItems = $transaksiProduk->transaksiProdukDetail->count();
                $remainingItems = $totalItems - $maxItems;
                $currentItemNumber = 0; // Global counter for sequential numbering

                // Calculate subtotal for ALL items (not just first page)
                $subTotal = $transaksiProduk->transaksiProdukDetail->sum(function ($item) {
                    return $item->jumlah_keluar * $item->harga_jual;
                });

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
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; vertical-align: top; text-align: center; border-bottom: {{ $currentItemNumber == $maxItems ? '1px solid #0072bc' : 'none' }}">
                        Unit</td>
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; vertical-align: top; text-align: right; border-bottom: {{ $currentItemNumber == $maxItems ? '1px solid #0072bc' : 'none' }}">
                        {{ number_format($item->harga_jual, 0, ',', '.') }}
                    </td>
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; vertical-align: top; text-align: right; border-bottom: {{ $currentItemNumber == $maxItems ? '1px solid #0072bc' : 'none' }}">
                        {{ number_format($item->jumlah_keluar * $item->harga_jual, 0, ',', '.') }}
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
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: center; font-style: italic; color: #666;">
                        ...
                    </td>
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: right; font-style: italic; color: #666;">
                        ...
                    </td>
                    <td
                        style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: right; font-style: italic; color: #666;">
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
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: {{ $i == $maxItems - $totalItems - 1 ? '1px solid #0072bc' : 'none' }}; vertical-align: top; text-align: center;">
                            &nbsp;
                        </td>
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: {{ $i == $maxItems - $totalItems - 1 ? '1px solid #0072bc' : 'none' }}; vertical-align: top; text-align: right;">
                            &nbsp;
                        </td>
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: {{ $i == $maxItems - $totalItems - 1 ? '1px solid #0072bc' : 'none' }}; vertical-align: top; text-align: right;">
                            &nbsp;
                        </td>
                    </tr>
                @endfor
            @endif
        </tbody>
    </table>

    <!-- Show total, terms and conditions directly on first page if only 1 page -->
    @if ($totalPages == 1)
        <table style="width: 100%; font-size: 10pt;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding-right: 10px">
                        @include('pdf.components.terms-conditions')
                    </td>

                    <td style="width: 33.3%; vertical-align: top; padding-left: 10px">
                        @include('pdf.components.calculation-table', [
                            'subTotal' => $subTotal,
                            'totalLabel' => 'Balance Due',
                            'showBorderTop' => false,
                        ])
                    </td>
                </tr>
            </tbody>
        </table>

        @include('pdf.components.footer')
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
                    @include('pdf.components.invoice-header', [
                        'title' => 'INVOICE (Halaman ' . $page . ')',
                        'tanggal_transaksi' => $transaksiProduk->tanggal_transaksi,
                        'nomor_invoice' => $transaksiProduk->nomor_invoice,
                    ])
                </td>
            </tr>
        </table>

        <!-- Page {{ $page }} Table -->
        <table style="width: 100%; border-collapse: collapse; margin-top: 15px; font-size: 10pt;">
            @include('pdf.components.table-header')
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
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: center;">
                            Unit</td>
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: right;">
                            {{ number_format($item->harga_jual, 0, ',', '.') }}
                        </td>
                        <td
                            style="padding: 8px; border-left: 1px solid #0072bc; border-right: 1px solid #0072bc; border-bottom: 1px solid #0072bc; vertical-align: top; text-align: right;">
                            {{ number_format($item->jumlah_keluar * $item->harga_jual, 0, ',', '.') }}
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
                    @include('pdf.components.invoice-header-summary', [
                        'title' => 'INVOICE SUMMARY',
                        'tanggal_transaksi' => $transaksiProduk->tanggal_transaksi,
                        'nomor_invoice' => $transaksiProduk->nomor_invoice,
                        'totalItems' => $totalItems,
                        'totalPages' => $totalPages,
                    ])
                </td>
            </tr>
        </table>

        <table style="width: 100%; font-size: 10pt;">
            <tbody>
                <tr>
                    <td style="width: 60%; vertical-align: top; padding-right: 10px">
                        @include('pdf.components.terms-conditions')
                    </td>

                    <td style="width: 33.3%; vertical-align: top; padding-left: 10px">
                        @include('pdf.components.calculation-table', [
                            'subTotal' => $subTotal,
                            'totalLabel' => 'Balance Due',
                            'showBorderTop' => true,
                        ])
                    </td>
                </tr>
            </tbody>
        </table>

        @include('pdf.components.footer')
    @endif

</body>

</html>
