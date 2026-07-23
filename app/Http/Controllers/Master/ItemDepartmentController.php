<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemDepartmentRequest;
use App\Models\ItemDepartment;
use App\Services\Master\ItemDepartmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemDepartmentController extends Controller
{
    public function __construct(
        protected ItemDepartmentService $departmentService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = ItemDepartment::withCount('items');

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($search) {
                $q->where('label', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('code')->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('master.item-department._table', compact('data'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $data])->render(),
            ]);
        }

        return view('master.item-department.index', compact('data'));
    }

    public function create(): View
    {
        return view('master.item-department.create');
    }

    public function store(ItemDepartmentRequest $request): RedirectResponse
    {
        $this->departmentService->store($request->validated());

        return redirect()->route('master-data.item-department.index')->with('success', 'Departemen item berhasil ditambahkan.');
    }

    public function show(ItemDepartment $itemDepartment): View
    {
        $itemDepartment->load(['items.category']);

        return view('master.item-department.show', compact('itemDepartment'));
    }

    public function edit(ItemDepartment $itemDepartment): View
    {
        return view('master.item-department.edit', compact('itemDepartment'));
    }

    public function update(ItemDepartmentRequest $request, ItemDepartment $itemDepartment): RedirectResponse
    {
        $this->departmentService->update($itemDepartment, $request->validated());

        return redirect()->route('master-data.item-department.index')->with('success', 'Departemen item berhasil diperbarui.');
    }

    public function destroy(ItemDepartment $itemDepartment): RedirectResponse
    {
        $this->departmentService->destroy($itemDepartment);

        return redirect()->route('master-data.item-department.index')->with('success', 'Departemen item berhasil dihapus.');
    }
}
