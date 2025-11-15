<div style="padding: 6px 0px; font-size: 10pt; line-height: 1.4;">
    <div style="background-color: #0072bc; color: white; font-weight: bold; padding: 5px 10px; font-size: 10pt;">
        Customers :</div>
    {{ $konsumen->nama ?? 'N/A' }}<br>
    {{ $konsumen->alamat ?? 'N/A' }}<br>
    {{ $konsumen->telepon ?? 'N/A' }}
</div>