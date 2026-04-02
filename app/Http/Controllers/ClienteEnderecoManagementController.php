<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClienteEnderecoManagementController extends Controller
{
    public function index(Request $request, User $usuario): View
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);

        $enderecos = $usuario->enderecos()
            ->orderByDesc('is_principal')
            ->orderByDesc('ativo')
            ->orderBy('id')
            ->paginate(10)
            ->appends($request->query());

        return view('clientes.enderecos.index', compact('usuario', 'enderecos'));
    }

    public function store(Request $request, User $usuario): RedirectResponse
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);

        $dados = $this->validarDados($request);

        $ativo = (bool) $dados['ativo'];
        $isPrincipal = (bool) $dados['is_principal'];

        DB::transaction(function () use ($usuario, $dados, $ativo, $isPrincipal) {
            $temEnderecos = $usuario->enderecos()->exists();
            $temEnderecoAtivo = $usuario->enderecos()->where('ativo', true)->exists();

            if (! $temEnderecos || ! $temEnderecoAtivo) {
                $ativo = true;
                $isPrincipal = true;
            }

            if ($isPrincipal) {
                $ativo = true;
            }

            if ($isPrincipal) {
                $usuario->enderecos()->update([
                    'is_principal' => false,
                ]);
            }

            $usuario->enderecos()->create([
                'cep' => $dados['cep'],
                'rua' => $dados['rua'],
                'numero' => $dados['numero'],
                'complemento' => $dados['complemento'],
                'bairro' => $dados['bairro'],
                'cidade' => $dados['cidade'],
                'uf' => strtoupper($dados['uf']),
                'ativo' => $ativo,
                'is_principal' => $isPrincipal,
            ]);

            $this->sincronizarPrincipalidade($usuario);
        });

        return redirect()
            ->route('clientes.enderecos.index', $usuario)
            ->with('success', 'Endereço cadastrado com sucesso.');
    }

    public function update(Request $request, User $usuario, Endereco $endereco): RedirectResponse
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);
        $this->garanteEnderecoDoCliente($usuario, $endereco);

        $dados = $this->validarDados($request);

        $ativo = (bool) $dados['ativo'];
        $isPrincipal = (bool) $dados['is_principal'];

        DB::transaction(function () use ($usuario, $endereco, $dados, $ativo, $isPrincipal) {
            if ($isPrincipal) {
                $ativo = true;
            }

            if (! $ativo) {
                $isPrincipal = false;
            }

            if ($isPrincipal) {
                $usuario->enderecos()
                    ->where('id', '!=', $endereco->id)
                    ->update([
                        'is_principal' => false,
                    ]);
            }

            $endereco->update([
                'cep' => $dados['cep'],
                'rua' => $dados['rua'],
                'numero' => $dados['numero'],
                'complemento' => $dados['complemento'],
                'bairro' => $dados['bairro'],
                'cidade' => $dados['cidade'],
                'uf' => strtoupper($dados['uf']),
                'ativo' => $ativo,
                'is_principal' => $isPrincipal,
            ]);

            $this->sincronizarPrincipalidade($usuario);
        });

        return redirect()
            ->route('clientes.enderecos.index', $usuario)
            ->with('success', 'Endereço atualizado com sucesso.');
    }

    public function setPrincipal(Request $request, User $usuario, Endereco $endereco): RedirectResponse
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);
        $this->garanteEnderecoDoCliente($usuario, $endereco);

        if (! $endereco->ativo) {
            return redirect()
                ->route('clientes.enderecos.index', $usuario)
                ->withErrors([
                    'endereco' => 'Somente endereços ativos podem ser definidos como principal.',
                ]);
        }

        DB::transaction(function () use ($usuario, $endereco) {
            $usuario->enderecos()->update([
                'is_principal' => false,
            ]);

            $endereco->update([
                'is_principal' => true,
            ]);

            $this->sincronizarPrincipalidade($usuario);
        });

        return redirect()
            ->route('clientes.enderecos.index', $usuario)
            ->with('success', 'Endereço principal definido com sucesso.');
    }

    public function toggleAtivo(Request $request, User $usuario, Endereco $endereco): RedirectResponse
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);
        $this->garanteEnderecoDoCliente($usuario, $endereco);

        DB::transaction(function () use ($usuario, $endereco) {
            $novoAtivo = ! $endereco->ativo;

            $endereco->update([
                'ativo' => $novoAtivo,
                'is_principal' => $novoAtivo ? $endereco->is_principal : false,
            ]);

            $this->sincronizarPrincipalidade($usuario);
        });

        $mensagem = $endereco->fresh()->ativo
            ? 'Endereço ativado com sucesso.'
            : 'Endereço inativado com sucesso.';

        return redirect()
            ->route('clientes.enderecos.index', $usuario)
            ->with('success', $mensagem);
    }

    private function validarDados(Request $request): array
    {
        return $request->validate([
            'cep' => ['required', 'string', 'max:9'],
            'rua' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'uf' => ['required', 'string', 'size:2'],
            'ativo' => ['required', 'boolean'],
            'is_principal' => ['required', 'boolean'],
        ]);
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

    private function garanteEnderecoDoCliente(User $usuario, Endereco $endereco): void
    {
        if ($endereco->user_id !== $usuario->id) {
            abort(404);
        }
    }

    private function sincronizarPrincipalidade(User $usuario): void
    {
        $usuario->enderecos()
            ->where('ativo', false)
            ->update([
                'is_principal' => false,
            ]);

        $ativosIds = $usuario->enderecos()
            ->where('ativo', true)
            ->orderBy('id')
            ->pluck('id');

        if ($ativosIds->isEmpty()) {
            $usuario->enderecos()->update([
                'is_principal' => false,
            ]);

            return;
        }

        $principaisIds = $usuario->enderecos()
            ->where('ativo', true)
            ->where('is_principal', true)
            ->orderBy('id')
            ->pluck('id');

        if ($principaisIds->isEmpty()) {
            $usuario->enderecos()->update([
                'is_principal' => false,
            ]);

            $usuario->enderecos()
                ->where('id', $ativosIds->first())
                ->update([
                    'is_principal' => true,
                ]);

            return;
        }

        $principalId = $principaisIds->first();

        $usuario->enderecos()
            ->where('id', '!=', $principalId)
            ->update([
                'is_principal' => false,
            ]);
    }
}