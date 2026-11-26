@extends('template.administrador')

@section('title', 'Dashboard')
@section('name_section', 'Dashboard')
@section('description', 'Resumen de estadísticas')
@section('item_section', 'Inicio')
@section('section', 'Dashboard')
@section('module', 'Principal')

@section('content')
  <div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small">Ventas de hoy</div>
            <div class="fs-4 fw-bold">S/ {{ number_format($salesToday, 2) }}</div>
          </div>
          <i class="bi bi-cash-coin fs-2 text-primary"></i>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small">Ventas del mes</div>
            <div class="fs-4 fw-bold">S/ {{ number_format($salesMonth, 2) }}</div>
          </div>
          <i class="bi bi-graph-up-arrow fs-2 text-success"></i>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small">Clientes registrados</div>
            <div class="fs-4 fw-bold">{{ $customers }}</div>
          </div>
          <i class="bi bi-people fs-2 text-info"></i>
        </div>
      </div>
    </div>

    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body d-flex align-items-center justify-content-between">
          <div>
            <div class="text-muted small">Stock bajo (<= 10)</div>
            <div class="fs-4 fw-bold">{{ $lowStock }}</div>
          </div>
          <i class="bi bi-exclamation-triangle fs-2 text-warning"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-3">
    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white fw-semibold">Ventas últimos 7 días</div>
        <div class="card-body">
          <div style="height: 260px;">
            <canvas
              id="chartDays"
              data-labels="{{ json_encode($days) }}"
              data-values="{{ json_encode($totals) }}"
            ></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12 col-lg-6">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-header bg-white fw-semibold">Ventas del mes por categoría</div>
        <div class="card-body">
          <div style="height: 260px;">
            <canvas
              id="chartCategories"
              data-labels="{{ json_encode($byCategory->pluck('name')) }}"
              data-values="{{ json_encode($byCategory->pluck('total')) }}"
            ></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="card border-0 shadow-sm">
        <div class="card-header bg-white fw-semibold">Top 5 medicamentos más vendidos del mes</div>
        <div class="card-body">
          <div style="height: 260px;">
            <canvas
              id="chartTop"
              data-labels="{{ json_encode($topMedicines->pluck('name')) }}"
              data-values="{{ json_encode($topMedicines->pluck('units')) }}"
            ></canvas>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
  <script src="{{ asset('js/chart.umd.min.js') }}"></script>
  <script>
    const daysEl = document.getElementById('chartDays');
    new Chart(daysEl, {
      type: 'line',
      data: {
        labels: JSON.parse(daysEl.dataset.labels || '[]'),
        datasets: [{
          label: 'Total (S/)',
          data: JSON.parse(daysEl.dataset.values || '[]'),
          borderColor: '#0d6efd',
          backgroundColor: 'rgba(13, 110, 253, 0.15)',
          tension: 0.3,
          fill: true
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });

    const catEl = document.getElementById('chartCategories');
    new Chart(catEl, {
      type: 'doughnut',
      data: {
        labels: JSON.parse(catEl.dataset.labels || '[]'),
        datasets: [{
          data: JSON.parse(catEl.dataset.values || '[]'),
          backgroundColor: ['#0d6efd', '#198754', '#ffc107', '#dc3545', '#6f42c1', '#20c997']
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    });

    const topEl = document.getElementById('chartTop');
    new Chart(topEl, {
      type: 'bar',
      data: {
        labels: JSON.parse(topEl.dataset.labels || '[]'),
        datasets: [{
          label: 'Unidades vendidas',
          data: JSON.parse(topEl.dataset.values || '[]'),
          backgroundColor: '#198754'
        }]
      },
      options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: false
      }
    });
  </script>
@endpush