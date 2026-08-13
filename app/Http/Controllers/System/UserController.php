<?php

namespace App\Http\Controllers\System;

use App\Http\Controllers\Controller;
use App\Http\Requests\System\UserRequest;
use App\Models\User;
use App\Services\System\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserController extends Controller
{
    public function __construct(
        protected UserService $userService
    ) {}

    public function index(Request $request): View|JsonResponse
    {
        $query = $this->userService->manageableQuery()->with('roles');

        if ($role = $request->input('role')) {
            $query->role($role);
        }

        if ($request->input('status') === 'inactive') {
            $query->where('is_active', false);
        } elseif ($request->input('status') === 'active') {
            $query->where('is_active', true);
        }

        if ($search = $request->input('q')) {
            $search = $this->escapeLike($search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->orderBy('name')->paginate(20)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('system.users._table', compact('users'))->render(),
                'pagination' => view('components.alpine-pagination', ['paginator' => $users])->render(),
            ]);
        }

        return view('system.users.index', [
            'users' => $users,
            'roles' => $this->userService->allowedRoles(),
        ]);
    }

    public function create(): View
    {
        return view('system.users.create', [
            'roles' => $this->userService->allowedRoles(),
        ]);
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $this->userService->store($request->validated());

        return redirect()->route('admin.user.index')->with('success', 'Akun berhasil dibuat. Pengguna wajib mengganti password saat login pertama.');
    }

    public function edit(User $user): View
    {
        abort_unless($this->userService->manageableQuery()->whereKey($user->id)->exists(), 404);

        return view('system.users.edit', [
            'user' => $user->load('roles'),
            'roles' => $this->userService->allowedRoles(),
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        abort_unless($this->userService->manageableQuery()->whereKey($user->id)->exists(), 404);

        $this->userService->update($user, $request->validated());

        return redirect()->route('admin.user.index')->with('success', 'Akun berhasil diperbarui.');
    }

    public function toggleActive(Request $request, User $user): RedirectResponse
    {
        abort_unless($this->userService->manageableQuery()->whereKey($user->id)->exists(), 404);

        $activated = $this->userService->setActive($user, $request->boolean('active'))->is_active;

        return redirect()->route('admin.user.index')->with('success', $activated ? 'Akun berhasil diaktifkan.' : 'Akun berhasil dinonaktifkan.');
    }
}
