<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ProfileCompletionController extends Controller
{
    public function edit(Request $request): View
    {
        $user = $request->user();

        $this->garantirUsuarioAutenticado($user);
        $this->garantirCliente($user);

        return view('auth.complete-profile', [
            'user' => $user->load([
                'enderecos' => function ($query) {
                    $query->orderByDesc('is_principal')
                        ->orderByDesc('ativo')
                        ->orderBy('created_at')
                        ->orderBy('id');
                },
            ]),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $this->garantirUsuarioAutenticado($user);
        $this->garantirCliente($user);

        $enderecosNormalizados = collect($request->input('enderecos', []))
            ->filter(function ($endereco) {
                if (! is_array($endereco)) {
                    return false;
                }

                return collect($endereco)->some(function ($valor) {
                    return filled($valor);
                });
            })
            ->values()
            ->all();

        $request->merge([
            'enderecos' => $enderecosNormalizados,
        ]);

        $dados = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => ['required', 'string', 'size:11'],
            'telefone' => ['required', 'string', 'max:20'],
            'data_nascimento' => ['required', 'date'],

            'enderecos' => ['nullable', 'array'],
            'enderecos.*.cep' => ['required', 'string', 'max:9'],
            'enderecos.*.rua' => ['required', 'string', 'max:255'],
            'enderecos.*.numero' => ['required', 'string', 'max:20'],
            'enderecos.*.complemento' => ['nullable', 'string', 'max:255'],
            'enderecos.*.bairro' => ['required', 'string', 'max:255'],
            'enderecos.*.cidade' => ['required', 'string', 'max:255'],
            'enderecos.*.uf' => ['required', 'string', 'size:2'],
        ]);

        DB::transaction(function () use ($user, $dados) {
            $user->update([
                'name' => $dados['name'],
                'cpf' => $dados['cpf'],
                'telefone' => $dados['telefone'],
                'data_nascimento' => $dados['data_nascimento'],
            ]);

            $enderecos = $dados['enderecos'] ?? [];

            if (empty($enderecos)) {
                return;
            }

            $temPrincipalAtivo = $user->enderecos()
                ->where('ativo', true)
                ->where('is_principal', true)
                ->exists();

            foreach ($enderecos as $endereco) {
                $user->enderecos()->create([
                    'cep' => $endereco['cep'],
                    'rua' => $endereco['rua'],
                    'numero' => $endereco['numero'],
                    'complemento' => $endereco['complemento'] ?? null,
                    'bairro' => $endereco['bairro'],
                    'cidade' => $endereco['cidade'],
                    'uf' => $endereco['uf'],
                    'ativo' => true,
                    'is_principal' => ! $temPrincipalAtivo,
                ]);

                if (! $temPrincipalAtivo) {
                    $temPrincipalAtivo = true;
                }
            }
        });

        return redirect()->route('dashboard')
            ->with('success', 'Cadastro completado com sucesso.');
    }

    private function garantirUsuarioAutenticado($user): void
    {
        if (! $user) {
            abort(403);
        }
    }

    private function garantirCliente($user): void
    {
        if (! $user->isCliente()) {
            abort(403);
        }
    }
}