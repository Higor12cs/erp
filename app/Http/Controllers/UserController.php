<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->where('tenant_id', Auth::user()->tenant_id)
            ->when(request('search'), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'filters' => request()->only(['search']),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Users/Create');
    }

    public function store(UserRequest $request): RedirectResponse
    {
        User::create($request->validated());

        return to_route('users.index')->with('success', 'Usuário criado com sucesso!');
    }

    public function edit(User $user): Response
    {
        if ($user->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        return Inertia::render('Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        if ($user->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $user->update($request->validated());

        return to_route('users.index')->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->tenant_id !== Auth::user()->tenant_id) {
            abort(403);
        }

        $user->delete();

        return to_route('users.index')->with('success', 'Usuário excluído com sucesso!');
    }
}
