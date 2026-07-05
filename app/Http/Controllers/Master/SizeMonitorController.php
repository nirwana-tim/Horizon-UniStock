<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\StudentSizeHistory;
use Illuminate\View\View;

class SizeMonitorController extends Controller
{
    public function index(): View
    {
        $histories = StudentSizeHistory::with([
            'sizeItem.sizeProfile.student',
            'sizeItem.item',
            'changedByUser',
        ])
            ->latest('changed_at')
            ->paginate(20);

        return view('distribution.size-monitor.index', compact('histories'));
    }
}
