<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reporte de ventas ({{ $from }} - {{ $to }})</title>
  <link rel="stylesheet" href="{{ asset('css/bootstrap.css') }}">
  <style>
    @media print {
      .no-print { display: none !important; }
      body { background: #fff !important; }
    }
  </style>
</head>
<body class="bg-light p-4">
  <div class="container bg-white p-4 shadow-sm rounded-3">
    <div class="d-flex justify-content-between align-items-center border-bottom pb-3 mb-4">
      <div>
        <h1 class="h4 fw-bold mb-1">Reporte de ventas — FARM SJB</h1>
        <div class="text-secondary small">Rango: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}</div>
      </div>
      <div class="d-flex gap-2 no-print">
        <button type="button" onclick="window.print()" class="btn btn-danger btn-sm text-white">
          <i class="bi bi-printer me-1"></i> Guardar / Imprimir PDF
        </button>
        <button type="button" onclick="window.close()" class="btn btn-light btn-sm">Cerrar</button>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-3">
        <div class="border rounded p-3">
          <small class="text-secondary d-block">N.° de ventas</small>
          <strong class="fs-5">{{ $summary['count'] }}</strong>
        </div>
      </div>
      <div class="col-3">
        <div class="border rounded p-3">
          <small class="text-secondary d-block">Total vendido</small>
          <strong class="fs-5">S/ {{ number_format($summary['total'], 2) }}</strong>
        </div>
      </div>
      <div class="col-3">
        <div class="border rounded p-3">
          <small class="text-secondary d-block">IGV</small>
          <strong class="fs-5">S/ {{ number_format($summary['igv'], 2) }}</strong>
        </div>
      </div>
      <div class="col-3">
        <div class="border rounded p-3">
          <small class="text-secondary d-block">Venta promedio</small>
          <strong class="fs-5">S/ {{ number_format($summary['average'], 2) }}</strong>
        </div>
      </div>
    </div>

    <table class="table table-sm table-bordered align-middle mb-0">
      <thead class="table-light">
        <tr class="text-uppercase small">
          <th>N.°</th>
          <th>Fecha</th>
          <th>Cliente</th>
          <th>Vendedor</th>
          <th class="text-end">IGV</th>
          <th class="text-end">Total</th>
        </tr>
      </thead>
      <tbody>
        @forelse ($sales as $sale)
          <tr>
            <td>{{ str_pad($sale->id, 6, '0', STR_PAD_LEFT) }}</td>
            <td>{{ $sale->date->format('d/m/Y H:i') }}</td>
            <td>{{ $sale->customer->name }}</td>
            <td>{{ $sale->user->name }}</td>
            <td class="text-end">S/ {{ number_format($sale->igv, 2) }}</td>
            <td class="text-end">S/ {{ number_format($sale->total, 2) }}</td>
          </tr>
        @empty
          <tr>
            <td colspan="6" class="text-center text-secondary py-3">No hay ventas con esos filtros.</td>
          </tr>
        @endforelse
      </tbody>
      @if ($sales->isNotEmpty())
        <tfoot>
          <tr>
            <th colspan="4" class="text-end">Totales</th>
            <th class="text-end">S/ {{ number_format($summary['igv'], 2) }}</th>
            <th class="text-end">S/ {{ number_format($summary['total'], 2) }}</th>
          </tr>
        </tfoot>
      @endif
    </table>
  </div>

  <script>
    window.addEventListener('load', () => {
      setTimeout(() => window.print(), 250);
    });
  </script>
</body>
</html>