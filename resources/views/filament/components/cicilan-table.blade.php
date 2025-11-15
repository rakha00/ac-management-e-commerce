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

<style>
	.cicilan-container {
		display: flex;
		flex-direction: column;
		gap: 12px;
	}

	.cicilan-table-wrapper {
		overflow-x: auto;
	}

	.cicilan-table {
		width: 100%;
		min-width: 100%;
		font-size: 14px;
		border-collapse: collapse;
	}

	.cicilan-header th {
		padding: 8px 12px;
		text-align: left;
		font-weight: 600;
		background-color: #f9fafb;
		border-bottom: 1px solid #e5e7eb;
	}

	.dark .cicilan-header th {
		background-color: #1f2937;
		border-bottom-color: #374151;
	}

	.cicilan-body tr {
		border-top: 1px solid #e5e7eb;
	}

	.dark .cicilan-body tr {
		border-top-color: #374151;
	}

	.cicilan-body td {
		padding: 8px 12px;
	}

	.cicilan-empty {
		color: #6b7280;
	}

	.dark .cicilan-empty {
		color: #9ca3af;
	}

	.cicilan-sisa {
		font-weight: 500;
		padding: 8px 12px;
		background-color: #f9fafb;
		border-radius: 6px;
		border: 1px solid #e5e7eb;
	}

	.dark .cicilan-sisa {
		background-color: #1f2937;
		border-color: #374151;
	}
</style>

<div class="cicilan-container">
	<div class="cicilan-table-wrapper">
		<table class="cicilan-table fi-ta-table">
			<thead class="cicilan-header fi-ta-header">
				<tr>
					<th>Tanggal</th>
					<th>Nominal</th>
				</tr>
			</thead>
			<tbody class="cicilan-body fi-ta-body">
				@forelse ($items as $it)
					<tr>
						<td>{{ optional($it->tanggal_cicilan)->format('Y-m-d') }}</td>
						<td>Rp {{ number_format((int) $it->nominal_cicilan, 0, ',', '.') }}</td>
					</tr>
				@empty
					<tr>
						<td class="cicilan-empty" colspan="2">Belum ada cicilan</td>
					</tr>
				@endforelse
			</tbody>
		</table>
	</div>
	<div class="cicilan-sisa">
		{{ $sisaLabel }}: Rp {{ number_format($sisa, 0, ',', '.') }}
	</div>
</div>