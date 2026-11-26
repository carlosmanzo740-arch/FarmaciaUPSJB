<!doctype html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Reporte de ventas ({{ $from }} - {{ $to }})</title>
  <style>
    body {
      font-family: DejaVu Sans, sans-serif;
      font-size: 11px;
      color: #1f2937;
      margin: 20px;
    }
    h1 {
      font-size: 18px;
      margin: 0 0 4px 0;
    }
    .sub {
      color: #6b7280;
      font-size: 11px;
      margin-bottom: 16px;
    }
    .summary-table {
      width: 100%;
      border-collapse: separate;
      border-spacing: 8px 0;
      margin: 0 -8px 18px -8px;
    }
    .summary-box {
      border: 1px solid #d1d5db;
      border-radius: 6px;
      padding: 8px 10px;
      background: #f9fafb;
    }
    .summary-label {
      font-size: 9px;
      color: #6b7280;
      text-transform: uppercase;
    }
    .summary-value {
      font-size: 13px;
      font-weight: bold;
      margin-top: 3px;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
    }
    .data-table th,
    .data-table td {
      border: 1px solid #d1d5db;
      padding: 6px 8px;
    }
    .data-table th {
      background: #f3f4f6;
      font-size: 10px;
      text-transform: uppercase;
      text-align: left;
    }
    .text-end {
      text-align: right !important;
    }
    .text-center {
      text-align: center !important;
    }
    tfoot th {
      background: #f9fafb;
    }
  </style>
</head>
<body>
  <h1>Reporte de ventas — FARM SJB</h1>
  <div class="sub">
    Rango: {{ \Carbon\Carbon::parse($from)->format('d/m/Y') }} al {{ \Carbon\Carbon::parse($to)->format('d/m/Y') }}
  </div>

  <table class="summary-table">
    <tr>
      <td style="width: 25%; padding: 0;">
        <div class="summary-box">
          <div class="summary-label">N.° de ventas</div>
          <div class="summary-value">{{ $summary['count'] }}</div>
        </div>
      </td>
      <td style="width: 25%; padding: 0;">
        <div class="summary-box">
          <div class="summary-label">Total vendido</div>
          <div class="summary-value">S/ {{ number_format($summary['total'], 2) }}</div>
        </div>
      </td>
      <td style="width: 25%; padding: 0;">
        <div class="summary-box">
          <div class="summary-label">IGV</div>
          <div class="summary-value">S/ {{ number_format($summary['igv'], 2) }}</div>
        </div>
      </td>
      <td style="width: 25%; padding: 0;">
        <div class="summary-box">
          <div class="summary-label">Venta promedio</div>
          <div class="summary-value">S/ {{ number_format($summary['average'], 2) }}</div>
        </div>
      </td>
    </tr>
  </table>

  <table class="data-table">
    <thead>
      <tr>
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
          <td colspan="6" class="text-center">No hay ventas con esos filtros.</td>
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
</body>
</html>