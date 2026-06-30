<?php

namespace App\Http\Controllers;

use App\Exports\DistributionReportExport;
use App\Exports\GpmReportExport;
use App\Exports\InventoryReportExport;
use App\Models\DistributionPeriod;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $periods = DistributionPeriod::orderBy('name', 'desc')->pluck('name', 'name');

        return view('reports.index', compact('periods'));
    }

    public function distribution(Request $request)
    {
        $request->validate([
            'period' => 'nullable|string',
        ]);

        $period = $request->input('period');
        $filename = 'Laporan_Distribusi' . ($period ? "_{$period}" : '') . '.xlsx';

        return Excel::download(new DistributionReportExport($period), $filename);
    }

    public function inventory(Request $request)
    {
        $filename = 'Laporan_Inventory.xlsx';

        return Excel::download(new InventoryReportExport(), $filename);
    }

    public function gpm(Request $request)
    {
        $request->validate([
            'period' => 'nullable|string',
        ]);

        $period = $request->input('period');
        $filename = 'Laporan_GPM' . ($period ? "_{$period}" : '') . '.xlsx';

        return Excel::download(new GpmReportExport($period), $filename);
    }
}
