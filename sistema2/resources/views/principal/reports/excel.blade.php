<table>
  <thead>
    <tr>
      <th colspan="6">Reporte de ventas — FARM SJB ({{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }})</th>
    </tr>
    <tr>
      <th>N.°</th>
      <th>Fecha</th>
      <th>Cliente</th>
      <th>Vendedor</th>
      <th>IGV (S/)</th>
      <th>Total (S/)</th>
    </tr>
  </thead>
  <tbody>
    @forelse ($sales as $sale)
      <tr>
        <td>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
        <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
        <td>{{ $sale->customer->name }}</td>
        <td>{{ $sale->user->name }}</td>
        <td>{{ number_format($sale->igv, 2, '.', '') }}</td>
        <td>{{ number_format($sale->total, 2, '.', '') }}</td>
      </tr>
    @empty
      <tr>
        <td colspan="6">No hay ventas con esos filtros.</td>
      </tr>
    @endforelse
  </tbody>
  @if ($sales->isNotEmpty())
    <tfoot>
      <tr>
        <th colspan="4">Totales</th>
        <th>{{ number_format($summary['igv'], 2, '.', '') }}</th>
        <th>{{ number_format($summary['total'], 2, '.', '') }}</th>
      </tr>
    </tfoot>
  @endif
</table>