<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class EnderecoController extends Controller
{
    public function index(Request $request): View
    {
        $authUser = $request->user();

        if (! $authUser || ! $authUser->isCliente()) {
            abort(403);
        }

        $authUser->load([
            'enderecos' => function ($query) {
                $query->orderByDesc('is_principal')
                    ->orderByDesc('ativo')
                    ->orderBy('created_at')
                    ->orderBy('id');
            },
        ]);

        return view('enderecos.index', [
            'user' => $authUser,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $authUser = $request->user();

        if (! $authUser) {
            return redirect()->route('login');
        }

        $this->garantirClienteLogado($authUser);

        $dados = $this->validarEndereco($request);

        $temPrincipalAtivo = $authUser->enderecos()
            ->where('ativo', true)
            ->where('is_principal', true)
            ->exists();

        $authUser->enderecos()->create([
            ...$dados,
            'ativo' => true,
            'is_principal' => ! $temPrincipalAtivo,
        ]);

        return back()->with('success', 'Endereço cadastrado com sucesso.');
    }

    public function update(Request $request, Endereco $endereco): RedirectResponse
    {
        $authUser = $request->user();

        if (! $authUser) {
            return redirect()->route('login');
        }

        $this->garantirClienteLogado($authUser);
        $this->garantirEnderecoDoUsuarioLogado($authUser, $endereco);

        $dados = $this->validarEndereco($request);

        $endereco->update($dados);

        return back()->with('success', 'Endereço atualizado com sucesso.');
    }

    public function setPrincipal(Request $request, Endereco $endereco): RedirectResponse
    {
        $authUser = $request->user();

        if (! $authUser) {
            return redirect()->route('login');
        }

        $this->garantirClienteLogado($authUser);
        $this->garantirEnderecoDoUsuarioLogado($authUser, $endereco);

        if (! $endereco->ativo) {
            return back()->withErrors([
                'endereco' => 'Apenas endereços ativos podem ser definidos como principal.',
            ]);
        }

        DB::transaction(function () use ($authUser, $endereco) {
            $authUser->enderecos()->update([
                'is_principal' => false,
            ]);

            $endereco->update([
                'is_principal' => true,
            ]);
        });

        return back()->with('success', 'Endereço principal definido com sucesso.');
    }

    public function toggleAtivo(Request $request, Endereco $endereco): RedirectResponse
    {
        $authUser = $request->user();

        if (! $authUser) {
            return redirect()->route('login');
        }

        $this->garantirClienteLogado($authUser);
        $this->garantirEnderecoDoUsuarioLogado($authUser, $endereco);

        $novoAtivo = ! $endereco->ativo;

        DB::transaction(function () use ($authUser, $endereco, $novoAtivo) {
            if ($novoAtivo) {
                $temPrincipalAtivo = $authUser->enderecos()
                    ->where('ativo', true)
                    ->where('is_principal', true)
                    ->exists();

                $endereco->update([
                    'ativo' => true,
                    'is_principal' => ! $temPrincipalAtivo,
                ]);

                return;
            }

            $eraPrincipal = $endereco->is_principal;

            $endereco->update([
                'ativo' => false,
                'is_principal' => false,
            ]);

            if (! $eraPrincipal) {
                return;
            }

            $novoPrincipal = $authUser->enderecos()
                ->where('ativo', true)
                ->orderBy('created_at')
                ->orderBy('id')
                ->first();

            if ($novoPrincipal) {
                $novoPrincipal->update([
                    'is_principal' => true,
                ]);
            }
        });

        $mensagem = $novoAtivo
            ? 'Endereço ativado com sucesso.'
            : 'Endereço inativado com sucesso.';

        return back()->with('success', $mensagem);
    }

    private function validarEndereco(Request $request): array
    {
        return $request->validate([
            'cep' => ['required', 'string', 'max:9'],
            'rua' => ['required', 'string', 'max:255'],
            'numero' => ['required', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:255'],
            'bairro' => ['required', 'string', 'max:255'],
            'cidade' => ['required', 'string', 'max:255'],
            'uf' => ['required', 'string', 'size:2'],
        ]);
    }

    private function garantirClienteLogado($authUser): void
    {
        if (! $authUser->isCliente()) {
            abort(403);
        }
    }

    private function garantirEnderecoDoUsuarioLogado($authUser, Endereco $endereco): void
    {
        if ($endereco->user_id !== $authUser->id) {
            abort(404);
        }
    }
}