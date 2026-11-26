<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function sales(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        [$from, $to, $sales] = $this->getFilteredData($request);

        $summary = [
            'count' => $sales->count(),
            'total' => $sales->sum('total'),
            'igv' => $sales->sum('igv'),
            'average' => $sales->avg('total') ?? 0,
        ];

        $chart = $sales->groupBy(fn ($sale) => $sale->date->format('d/m'))
            ->map(fn ($group) => round($group->sum('total'), 2));

        $customers = Customer::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('principal.reports.sales', compact(
            'sales',
            'summary',
            'chart',
            'from',
            'to',
            'customers',
            'users',
            'categories'
        ));
    }

    public function exportExcel(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        [$from, $to, $sales] = $this->getFilteredData($request);

        $filename = 'reporte_ventas_' . $from . '_a_' . $to . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $totalIgv = $sales->sum('igv');
        $totalVenta = $sales->sum('total');

        $callback = function () use ($sales, $from, $to, $totalIgv, $totalVenta) {
            echo "\xEF\xBB\xBF";
            echo "<table border='1'>";
            echo "<tr><th colspan='6'>Reporte de ventas ({$from} al {$to})</th></tr>";
            echo "<tr>
                    <th>N.°</th>
                    <th>Fecha</th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th>IGV</th>
                    <th>Total</th>
                  </tr>";

            foreach ($sales as $sale) {
                $num = str_pad($sale->id, 6, '0', STR_PAD_LEFT);
                $fecha = $sale->date->format('d/m/Y H:i');
                $cliente = e($sale->customer->name);
                $vendedor = e($sale->user->name);
                $igv = number_format($sale->igv, 2, '.', '');
                $total = number_format($sale->total, 2, '.', '');

                echo "<tr>
                        <td>{$num}</td>
                        <td>{$fecha}</td>
                        <td>{$cliente}</td>
                        <td>{$vendedor}</td>
                        <td>{$igv}</td>
                        <td>{$total}</td>
                      </tr>";
            }

            echo "<tr>
                    <th colspan='4'>Totales</th>
                    <th>" . number_format($totalIgv, 2, '.', '') . "</th>
                    <th>" . number_format($totalVenta, 2, '.', '') . "</th>
                  </tr>";
            echo "</table>";
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf(Request $request)
    {
        $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'customer_id' => ['nullable', 'exists:customers,id'],
            'user_id' => ['nullable', 'exists:users,id'],
            'category_id' => ['nullable', 'exists:categories,id'],
        ]);

        [$from, $to, $sales] = $this->getFilteredData($request);

        $summary = [
            'count' => $sales->count(),
            'total' => $sales->sum('total'),
            'igv' => $sales->sum('igv'),
            'average' => $sales->avg('total') ?? 0,
        ];

        return view('principal.reports.pdf', compact('sales', 'summary', 'from', 'to'));
    }

    private function getFilteredData(Request $request): array
    {
        $from = $request->input('from', now()->startOfMonth()->toDateString());
        $to = $request->input('to', now()->toDateString());

        $sales = Sale::with(['customer', 'user'])
            ->whereBetween('date', [$from . ' 00:00:00', $to . ' 23:59:59'])
            ->when($request->customer_id, fn ($q, $id) => $q->where('customer_id', $id))
            ->when($request->user_id, fn ($q, $id) => $q->where('user_id', $id))
            ->when($request->category_id, fn ($q, $id) => $q->whereHas('details.medicine', fn ($m) => $m->where('category_id', $id)))
            ->orderBy('date')
            ->get();

        return [$from, $to, $sales];
    }
}