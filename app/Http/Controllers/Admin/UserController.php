<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('role:admin'),
        ];
    }

    public function index()
    {
        $users = User::with('roles')->orderBy('name')->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'creci' => ['nullable', 'string', 'max:50'],
            'default_commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'role' => ['required', 'exists:roles,name'],
            'active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            ...collect($data)->except(['password', 'role'])->toArray(),
            'password' => Hash::make($data['password']),
            'email_verified_at' => now(),
        ]);

        $user->assignRole($data['role']);

        return redirect()->route('admin.users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'string', 'min:8'],
            'phone' => ['nullable', 'string', 'max:30'],
            'creci' => ['nullable', 'string', 'max:50'],
            'default_commission_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'role' => ['required', 'exists:roles,name'],
            'active' => ['nullable', 'boolean'],
        ]);

        $user->update([
            ...collect($data)->except(['password', 'role'])->toArray(),
            'active' => $request->boolean('active'),
        ]);

        if (! empty($data['password'])) {
            $user->update(['password' => Hash::make($data['password'])]);
        }

        $user->syncRoles([$data['role']]);

        return back()->with('success', 'Usuário atualizado.');
    }

    public function destroy(User $user)
    {
        abort_if($user->id === auth()->id(), 403, 'Você não pode remover seu próprio usuário.');

        $user->delete();

        return redirect()->route('admin.users.index')->with('success', 'Usuário removido.');
    }
}
