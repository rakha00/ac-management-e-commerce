<div style="margin-top: -4px; margin-right: -3px;">
    <table style="width: 100%; border-collapse: collapse; border: 1px solid #0072bc;">
        <tr>
            <td
                style="width: 50.4%; border-right: 1px solid #0072bc; padding: 8px; text-align: left; font-weight: bold;">
                {{ $totalLabel ?? 'Total' }}
            </td>
            <td style="padding: 8px; text-align: right; font-weight: bold;">
                {{ number_format($subTotal, 0, ',', '.') }}
            </td>
        </tr>
    </table>
</div>
