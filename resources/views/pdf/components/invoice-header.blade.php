<div style="font-weight: bold; font-size: 16pt; margin-bottom: 10px; margin-top: 54px; text-align: right;">
    {{ $title ?? 'INVOICE' }}</div>
<div style="border: 1px solid #0072bc; font-size: 8pt; line-height: 1.5;">
    <table style="width: 100%;">
        <tr>
            <td style="width:45%;">Date</td>
            <td>: {{ \Carbon\Carbon::parse($tanggal_transaksi)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td>No. Invoice</td>
            <td>: {{ $nomor_invoice }}</td>
        </tr>
    </table>
</div>
