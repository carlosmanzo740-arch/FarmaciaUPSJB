<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Medicine;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $salesToday = Sale::whereDate('date', today())->sum('total');

        $salesMonth = Sale::whereBetween('date', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ])->sum('total');

        $customers = Customer::count();

        $lowStock = Medicine::where('stock', '<=', 10)->count();

        $startDate = now()->subDays(6)->startOfDay();
        $endDate = now()->endOfDay();

        $salesByDay = Sale::selectRaw('DATE(date) as day, SUM(total) as total')
            ->whereBetween('date', [$startDate, $endDate])
            ->groupBy('day')
            ->pluck('total', 'day');

        $days = [];
        $totals = [];
        for ($i = 0; $i < 7; $i++) {
            $currentDate = now()->subDays(6 - $i);
            $key = $currentDate->toDateString();
            $days[] = $currentDate->format('d/m');
            $totals[] = (float) ($salesByDay[$key] ?? 0);
        }

        $byCategory = DB::table('detail_sales')
            ->join('medicines', 'detail_sales.medicine_id', '=', 'medicines.id')
            ->join('categories', 'medicines.category_id', '=', 'categories.id')
            ->join('sales', 'detail_sales.sale_id', '=', 'sales.id')
            ->whereBetween('sales.date', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->select('categories.name', DB::raw('SUM(detail_sales.subtotal) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->get();

        $topMedicines = DB::table('detail_sales')
            ->join('medicines', 'detail_sales.medicine_id', '=', 'medicines.id')
            ->join('sales', 'detail_sales.sale_id', '=', 'sales.id')
            ->whereBetween('sales.date', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])
            ->select('medicines.name', DB::raw('SUM(detail_sales.amount) as units'))
            ->groupBy('medicines.name')
            ->orderByDesc('units')
            ->limit(5)
            ->get();

        return view('principal.dashboard.index', compact(
            'salesToday',
            'salesMonth',
            'customers',
            'lowStock',
            'days',
            'totals',
            'byCategory',
            'topMedicines'
        ));
    }
}
