<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemDepartmentRequest;
use App\Models\ItemDepartment;
use App\Services\Master\ItemDepartmentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ItemDepartmentController extends Controller
{
    public function __construct(
        protected ItemDepartmentService $departmentService
    ) {}

    public function index(): View
    {
        $departments = ItemDepartment::withCount('items')->orderBy('code')->paginate(15);

        return view('master.item-department.index', compact('departments'));
    }

    public function create(): View
    {
        return view('master.item-department.create');
    }

    public function store(ItemDepartmentRequest $request): RedirectResponse
    {
        $this->departmentService->store($request->validated());

        return redirect()->route('master.item-department.index')->with('success', 'Departemen item berhasil ditambahkan.');
    }

    public function show(ItemDepartment $itemDepartment): View
    {
        return view('master.item-department.show', compact('itemDepartment'));
    }

    public function edit(ItemDepartment $itemDepartment): View
    {
        return view('master.item-department.edit', compact('itemDepartment'));
    }

    public function update(ItemDepartmentRequest $request, ItemDepartment $itemDepartment): RedirectResponse
    {
        $this->departmentService->update($itemDepartment, $request->validated());

        return redirect()->route('master.item-department.index')->with('success', 'Departemen item berhasil diperbarui.');
    }

    public function destroy(ItemDepartment $itemDepartment): RedirectResponse
    {
        $this->departmentService->destroy($itemDepartment);

        return redirect()->route('master.item-department.index')->with('success', 'Departemen item berhasil dihapus.');
    }
}
