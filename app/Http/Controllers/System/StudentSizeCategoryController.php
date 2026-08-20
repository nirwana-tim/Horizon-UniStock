<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Models\AppSetting;
use App\Models\ItemCategory;
use App\Services\AuditService;
use App\Services\StudentSizeCategoryService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudentSizeCategoryController extends Controller
{
    public function show(StudentSizeCategoryService $service): View
    {
        return view('system.student-size-categories', [
            'categories' => ItemCategory::query()->orderBy('code')->get(),
            'bajuCodes' => $service->bajuCategoryCodes(),
            'sepatuCodes' => $service->sepatuCategoryCodes(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'baju_category_codes' => ['nullable', 'array'],
            'baju_category_codes.*' => ['string', 'exists:item_categories,code'],
            'sepatu_category_codes' => ['nullable', 'array'],
            'sepatu_category_codes.*' => ['string', 'exists:item_categories,code'],
        ]);

        $baju = array_values(array_unique($validated['baju_category_codes'] ?? []));
        $sepatu = array_values(array_unique($validated['sepatu_category_codes'] ?? []));

        $bajuRow = AppSetting::set('student-size.baju_category_codes', $baju, auth()->id());
        AppSetting::set('student-size.sepatu_category_codes', $sepatu, auth()->id());

        AuditService::log('update_student_size_categories', AppSetting::class, $bajuRow->id, null, [
            'baju_category_codes' => $baju,
            'sepatu_category_codes' => $sepatu,
        ]);

        return redirect()->route('system.student-size.show')
            ->with('success', 'Konfigurasi ukuran berhasil disimpan.');
    }
}
