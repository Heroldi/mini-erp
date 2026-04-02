<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EquipeManagementController extends Controller
{
    public function index(Request $request): View
    {
        $authUser = $request->user();

        $this->garantirAdmin($authUser);

        $rolesDisponiveis = $this->rolesInternasDisponiveis();

        $usuariosQuery = User::with('role')
            ->whereHas('role', function ($query) {
                $query->whereIn('nome', ['atendente', 'admin']);
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
            ->when($request->filled('role_id'), function ($query) use ($request, $rolesDisponiveis) {
                $roleId = (int) $request->role_id;

                if ($rolesDisponiveis->pluck('id')->contains($roleId)) {
                    $query->where('role_id', $roleId);
                }
            })
            ->when($request->filled('ativo'), function ($query) use ($request) {
                if (in_array($request->ativo, ['0', '1'], true)) {
                    $query->where('ativo', $request->ativo === '1');
                }
            })
            ->orderBy('name');

        $usuarios = $usuariosQuery
            ->paginate(10)
            ->appends($request->query());

        return view('equipe.index', compact('usuarios', 'rolesDisponiveis'));
    }

    public function create(Request $request): View
    {
        $authUser = $request->user();

        $this->garantirAdmin($authUser);

        $rolesDisponiveis = $this->rolesInternasDisponiveis();

        return view('equipe.create', compact('rolesDisponiveis'));
    }

    public function store(Request $request): RedirectResponse
    {
        $authUser = $request->user();

        $this->garantirAdmin($authUser);

        $rolesDisponiveis = $this->rolesInternasDisponiveis();
        $roleIdsPermitidos = $rolesDisponiveis->pluck('id')->all();

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['required', 'string', 'size:11', 'unique:users,cpf'],
            'telefone' => ['required', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date'],
            'role_id' => ['required', Rule::in($roleIdsPermitidos)],
        ]);

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
            'role_id' => $dados['role_id'],
            'ativo' => true,
            'password' => Hash::make($senhaProvisoria),
            'must_change_password' => true,
        ]);

        return redirect()
            ->route('equipe.index')
            ->with('success', 'Membro da equipe cadastrado com sucesso.')
            ->with('generated_password', $senhaProvisoria)
            ->with('generated_email', $dados['email']);
    }

    public function edit(Request $request, User $usuario): View
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);

        $rolesDisponiveis = $this->rolesInternasDisponiveis();

        $podeEditarRole = $authUser->id !== $usuario->id;
        $podeEditarAtivo = $authUser->id !== $usuario->id;

        return view('equipe.edit', compact(
            'usuario',
            'rolesDisponiveis',
            'podeEditarRole',
            'podeEditarAtivo'
        ));
    }

    public function update(Request $request, User $usuario): RedirectResponse
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);

        $rolesDisponiveis = $this->rolesInternasDisponiveis();
        $roleIdsPermitidos = $rolesDisponiveis->pluck('id')->all();

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
            'role_id' => ['required', Rule::in($roleIdsPermitidos)],
        ]);

        $updateData = [
            'name' => $dados['name'],
            'cpf' => $dados['cpf'],
            'telefone' => $dados['telefone'],
            'data_nascimento' => $dados['data_nascimento'],
        ];

        if ($authUser->id !== $usuario->id) {
            $updateData['ativo'] = $dados['ativo'];
            $updateData['role_id'] = $dados['role_id'];
        }

        $usuario->update($updateData);

        return redirect()
            ->route('equipe.index')
            ->with('success', 'Membro da equipe atualizado com sucesso.');
    }

    private function garantirAdmin(User $authUser): void
    {
        if (! $authUser->isAdmin()) {
            abort(403);
        }
    }

    private function garanteQuePodeGerenciar(User $authUser, User $usuario): void
    {
        $this->garantirAdmin($authUser);

        if (! $usuario->isInterno()) {
            abort(404);
        }
    }

    private function rolesInternasDisponiveis(): Collection
    {
        return Role::whereIn('nome', ['atendente', 'admin'])
            ->orderBy('nome')
            ->get();
    }
}