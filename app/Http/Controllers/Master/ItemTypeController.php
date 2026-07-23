<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Http\Requests\ItemTypeRequest;
use App\Models\ItemType;
use App\Services\Master\ItemTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ItemTypeController extends Controller
{
    public function __construct(
        protected ItemTypeService $typeService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = ItemType::withCount('items');

        if ($search = $request->input('q')) {
            $search = str_replace(['%', '_'], ['\%', '\_'], $search);
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhere('label', 'like', "%{$search}%");
            });
        }

        $data = $query->orderBy('code')->paginate(20);

        if ($request->ajax()) {
            return response()->json([
                'html' => view('master.item-type._table', compact('data'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $data])->render(),
            ]);
        }

        return view('master.item-type.index', compact('data'));
    }

    public function create(): View
    {
        return view('master.item-type.create');
    }

    public function store(ItemTypeRequest $request): RedirectResponse
    {
        $this->typeService->store($request->validated());

        return redirect()->route('master-data.item-type.index')->with('success', 'Tipe item berhasil ditambahkan.');
    }

    public function show(ItemType $itemType): View
    {
        return view('master.item-type.show', compact('itemType'));
    }

    public function edit(ItemType $itemType): View
    {
        return view('master.item-type.edit', compact('itemType'));
    }

    public function update(ItemTypeRequest $request, ItemType $itemType): RedirectResponse
    {
        $this->typeService->update($itemType, $request->validated());

        return redirect()->route('master-data.item-type.index')->with('success', 'Tipe item berhasil diperbarui.');
    }

    public function destroy(ItemType $itemType): RedirectResponse
    {
        $this->typeService->destroy($itemType);

        return redirect()->route('master-data.item-type.index')->with('success', 'Tipe item berhasil dihapus.');
    }
}
