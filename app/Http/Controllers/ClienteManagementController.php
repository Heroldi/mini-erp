<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ClienteManagementController extends Controller
{
    public function index(Request $request): View
    {
        $authUser = $request->user();

        if (! $authUser->isAtendente() && ! $authUser->isAdmin()) {
            abort(403);
        }

        $usuariosQuery = User::with('role')
            ->whereHas('role', function ($query) {
                $query->where('nome', 'cliente');
            })
            ->when($request->filled('nome'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->nome . '%');
            })
            ->when($request->filled('cpf'), function ($query) use ($request) {
                $query->where('cpf', 'like', '%' . $request->cpf . '%');
            })
            ->when($request->filled('email'), function ($query) use ($request) {
                $query->where('email', 'like', '%' . $request->email . '%');
            })
            ->orderBy('name');

        $usuarios = $usuariosQuery
            ->paginate(10)
            ->appends($request->query());

        return view('clientes.index', compact('usuarios'));
    }

    public function create(Request $request): View
    {
        $authUser = $request->user();

        if (! $authUser->isAtendente() && ! $authUser->isAdmin()) {
            abort(403);
        }

        return view('clientes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $authUser = $request->user();

        if (! $authUser->isAtendente() && ! $authUser->isAdmin()) {
            abort(403);
        }

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['required', 'string', 'size:11', 'unique:users,cpf'],
            'telefone' => ['required', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date'],
        ]);

        $roleCliente = Role::where('nome', 'cliente')->firstOrFail();

        $senhaProvisoria = Str::password(
            length: 16,
            letters: true,
            numbers: true,
            symbols: true,
            spaces: false
        );

        User::create([
            'name' => $dados['name'],
            'email' => $dados['email'],
            'cpf' => $dados['cpf'],
            'telefone' => $dados['telefone'],
            'data_nascimento' => $dados['data_nascimento'],
            'role_id' => $roleCliente->id,
            'ativo' => true,
            'password' => Hash::make($senhaProvisoria),
            'must_change_password' => true,
        ]);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente cadastrado com sucesso.')
            ->with('generated_password', $senhaProvisoria)
            ->with('generated_email', $dados['email']);
    }

    public function edit(Request $request, User $usuario): View
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);

        $podeEditarCpf = $authUser->isAdmin();
        $podeEditarAtivo = true;

        return view('clientes.edit', compact(
            'usuario',
            'podeEditarCpf',
            'podeEditarAtivo'
        ));
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);

        if ($authUser->isAtendente()) {
            $dados = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'telefone' => ['required', 'string', 'max:20'],
                'data_nascimento' => ['required', 'date'],
                'ativo' => ['required', 'boolean'],
            ]);

            $usuario->update([
                'name' => $dados['name'],
                'telefone' => $dados['telefone'],
                'data_nascimento' => $dados['data_nascimento'],
                'ativo' => $dados['ativo'],
            ]);

            return redirect()
                ->route('clientes.index')
                ->with('success', 'Cliente atualizado com sucesso.');
        }

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required',
                'string',
                'size:11',
                Rule::unique('users', 'cpf')->ignore($usuario->id),
            ],
            'telefone' => ['required', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date'],
            'ativo' => ['required', 'boolean'],
        ]);

        $usuario->update([
            'name' => $dados['name'],
            'cpf' => $dados['cpf'],
            'telefone' => $dados['telefone'],
            'data_nascimento' => $dados['data_nascimento'],
            'ativo' => $dados['ativo'],
        ]);

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Cliente atualizado com sucesso.');
    }

    private function garanteQuePodeGerenciar(User $authUser, User $usuario): void
    {
        if (! $usuario->isCliente()) {
            abort(404);
        }

        if ($authUser->isAdmin()) {
            return;
        }

        if ($authUser->isAtendente()) {
            return;
        }

        abort(403);
    }
}