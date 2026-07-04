<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\DistributionSchedule;
use App\Services\GpmService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GpmController extends Controller
{
    public function __construct(
        private GpmService $service
    ) {}

    public function index(Request $request): View
    {
        $period = $request->input('period');
        $periods = DistributionSchedule::whereNotNull('period')->distinct()->orderBy('period', 'desc')->pluck('period', 'period');

        $gpmData = $this->service->calculateGpm($period);
        $gpmByCategory = $this->service->getGpmByCategory($period);

        $totalQty = $gpmData->sum('qty_sold');
        $total_hpp = $gpmData->sum('total_hpp');
        $total_selling = $gpmData->sum('total_selling_price');
        $total_laba_rugi = $total_selling - $total_hpp;

        return view('finance.gpm', compact(
            'period',
            'periods',
            'gpmData',
            'gpmByCategory',
            'totalQty',
            'total_hpp',
            'total_selling',
            'total_laba_rugi'
        ));
    }
}
