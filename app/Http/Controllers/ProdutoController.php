<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProdutoController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->input('status');

        $produtos = Produto::query()
            ->when($request->filled('busca'), function ($query) use ($request) {
                $busca = $request->input('busca');

                $query->where(function ($subQuery) use ($busca) {
                    $subQuery->where('nome', 'like', '%' . $busca . '%')
                        ->orWhere('descricao', 'like', '%' . $busca . '%');
                });
            })
            ->when(in_array($status, ['0', '1'], true), function ($query) use ($status) {
                $query->where('ativo', $status === '1');
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        return view('produtos.index', compact('produtos'));
    }

    public function create(): View
    {
        return view('produtos.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'preco' => ['required', 'numeric', 'min:0'],
        ]);

        Produto::create([
            ...$dados,
            'ativo' => true,
        ]);

        return redirect()
            ->route('produtos.index')
            ->with('success', 'Produto cadastrado com sucesso.');
    }

    public function edit(Produto $produto): View
    {
        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, Produto $produto): RedirectResponse
    {
        $dados = $request->validate([
            'nome' => ['required', 'string', 'max:255'],
            'descricao' => ['nullable', 'string'],
            'preco' => ['required', 'numeric', 'min:0'],
        ]);

        $produto->update($dados);

        return redirect()
            ->route('produtos.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    public function toggleAtivo(Produto $produto): RedirectResponse
    {
        $produto->update([
            'ativo' => ! $produto->ativo,
        ]);

        $mensagem = $produto->ativo
            ? 'Produto ativado com sucesso.'
            : 'Produto inativado com sucesso.';

        return redirect()
            ->route('produtos.index')
            ->with('success', $mensagem);
    }
}