@php
	/**
	 * @var \Illuminate\Database\Eloquent\Model $record
	 * @var string $relationName
	 * @var string $totalFieldName
	 * @var string $sisaLabel
	 */
	$items = $record->{$relationName}()
		->orderByDesc('tanggal_cicilan')
		->get(['tanggal_cicilan', 'nominal_cicilan']);

	$total = (int) ($record->{$totalFieldName} ?? 0);
	$paid = (int) $record->{$relationName}()->sum('nominal_cicilan');
	$sisa = max($total - $paid, 0);
@endphp

<div class="space-y-3">
	<div class="overflow-x-auto">
		<table class="min-w-full text-sm fi-ta-table">
			<thead class="fi-ta-header">
				<tr>
					<th class="px-3 py-2 text-left">Tanggal</th>
					<th class="px-3 py-2 text-left">Nominal</th>
				</tr>
			</thead>
			<tbody class="fi-ta-body">
				@forelse ($items as $it)
					<tr class="border-t">
						<td class="px-3 py-2">{{ optional($it->tanggal_cicilan)->format('Y-m-d') }}</td>
						<td class="px-3 py-2">Rp {{ number_format((int) $it->nominal_cicilan, 0, ',', '.') }}</td>
					</tr>
				@empty
					<tr>
						<td class="px-3 py-4 text-gray-500" colspan="2">Belum ada cicilan</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>

	<div class="font-medium">
		{{ $sisaLabel }}: Rp {{ number_format($sisa, 0, ',', '.') }}
	</div>
</div>