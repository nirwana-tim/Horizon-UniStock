<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\StudentSizeHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SizeMonitorController extends Controller
{
    public function index(Request $request): View|JsonResponse
    {
        $histories = StudentSizeHistory::with([
            'sizeItem.sizeProfile.student',
            'sizeItem.item',
            'changedByUser',
        ])
            ->when($request->input('q'), function ($query, $search) {
                $search = str_replace(['%', '_'], ['\%', '\_'], $search);
                $query->where(function ($q) use ($search) {
                    $q->whereHas('sizeItem.sizeProfile.student', function ($sq) use ($search) {
                        $sq->where('name', 'like', "%{$search}%")
                           ->orWhere('nim', 'like', "%{$search}%");
                    })->orWhereHas('sizeItem.item', function ($iq) use ($search) {
                        $iq->where('name', 'like', "%{$search}%");
                    });
                });
            })
            ->latest('changed_at')
            ->paginate(20);

        if ($request->ajax()) {
            $html = view('distribution.size-monitor._table', compact('histories'))->render();
            $pagination = view('components.alpine-pagination', ['paginator' => $histories])->render();
            return response()->json(compact('html', 'pagination'));
        }

        return view('distribution.size-monitor.index', compact('histories'));
    }
}
