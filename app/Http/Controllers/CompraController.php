<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class CompraController extends Controller
{
    public function index(Request $request): View
    {
        $authUser = $request->user();

        $this->garantirCliente($authUser);

        $busca = $request->string('busca')->value();

        $produtosQuery = Produto::where('ativo', true)
            ->orderBy('nome');

        if ($busca) {
            $produtosQuery->where(function ($query) use ($busca) {
                $query->where('nome', 'like', "%{$busca}%")
                    ->orWhere('descricao', 'like', "%{$busca}%");
            });
        }

        $produtos = $produtosQuery
            ->paginate(12)
            ->appends($request->query());

        return view('compras.index', compact('produtos', 'busca'));
    }

    public function checkout(Request $request): View|RedirectResponse
    {
        $authUser = $request->user();

        $this->garantirCliente($authUser);

        $itens = session('compra.itens', []);

        if (empty($itens)) {
            return redirect()
                ->route('compras.index')
                ->withErrors([
                    'itens' => 'Selecione pelo menos um produto para continuar.',
                ]);
        }

        $produtos = $this->buscarProdutosAtivosIndexados($itens);

        $itensSelecionados = $this->montarResumoDosItens($itens, $produtos);
        $total = round($itensSelecionados->sum('subtotal'), 2);

        $enderecos = $authUser->enderecos()
            ->where('ativo', true)
            ->orderByDesc('is_principal')
            ->orderBy('id')
            ->get();

        return view('compras.checkout', [
            'itensSelecionados' => $itensSelecionados,
            'total' => $total,
            'enderecos' => $enderecos,
        ]);
    }

    public function prepareCheckout(Request $request): RedirectResponse
    {
        $authUser = $request->user();

        $this->garantirCliente($authUser);

        $itens = $this->validarItensDaCompra($request);

        $produtoIds = collect($itens)
            ->pluck('produto_id')
            ->filter()
            ->unique()
            ->values();

        $produtos = $this->buscarProdutosAtivosIndexados($itens);

        if ($produtos->count() !== $produtoIds->count()) {
            return back()
                ->withErrors([
                    'itens' => 'Um ou mais produtos selecionados são inválidos ou estão inativos.',
                ])
                ->withInput();
        }

        session(['compra.itens' => $itens]);

        return redirect()->route('compras.checkout');
    }

    public function store(Request $request): RedirectResponse
    {
        $authUser = $request->user();

        $this->garantirCliente($authUser);

        $itens = session('compra.itens', []);

        if (empty($itens)) {
            return redirect()
                ->route('compras.index')
                ->withErrors([
                    'itens' => 'Sua compra não possui itens válidos.',
                ]);
        }

        $produtoIds = collect($itens)
            ->pluck('produto_id')
            ->filter()
            ->unique()
            ->values();

        $produtos = $this->buscarProdutosAtivosIndexados($itens);

        if ($produtos->count() !== $produtoIds->count()) {
            session()->forget('compra.itens');

            return redirect()
                ->route('compras.index')
                ->withErrors([
                    'itens' => 'Um ou mais produtos da sua compra foram inativados ou ficaram inválidos antes da finalização. Selecione os itens novamente.',
                ]);
        }

        $dados = $request->validate([
            'address_mode' => ['required', Rule::in(['existing', 'new'])],
            'endereco_id' => ['nullable', 'integer'],

            'novo_endereco' => ['nullable', 'array'],
            'novo_endereco.cep' => ['nullable', 'required_if:address_mode,new', 'string', 'max:9'],
            'novo_endereco.rua' => ['nullable', 'required_if:address_mode,new', 'string', 'max:255'],
            'novo_endereco.numero' => ['nullable', 'required_if:address_mode,new', 'string', 'max:20'],
            'novo_endereco.complemento' => ['nullable', 'string', 'max:255'],
            'novo_endereco.bairro' => ['nullable', 'required_if:address_mode,new', 'string', 'max:255'],
            'novo_endereco.cidade' => ['nullable', 'required_if:address_mode,new', 'string', 'max:255'],
            'novo_endereco.uf' => ['nullable', 'required_if:address_mode,new', 'string', 'size:2'],
        ]);

        $pedido = DB::transaction(function () use ($authUser, $dados, $itens, $produtos) {
            $endereco = $this->resolverEnderecoDaCompra($authUser, $dados);

            $pedido = Pedido::create([
                'user_id' => $authUser->id,
                'data_pedido' => now()->toDateString(),
                'status' => 'aberto',
                'valor_total' => 0,

                'entrega_cep' => $endereco->cep,
                'entrega_rua' => $endereco->rua,
                'entrega_numero' => $endereco->numero,
                'entrega_complemento' => $endereco->complemento,
                'entrega_bairro' => $endereco->bairro,
                'entrega_cidade' => $endereco->cidade,
                'entrega_uf' => $endereco->uf,
            ]);

            $total = 0;

            foreach ($itens as $item) {
                $produto = $produtos[$item['produto_id']];
                $quantidade = (int) $item['quantidade'];

                $precoUnitario = (float) $produto->preco;
                $subtotal = round($precoUnitario * $quantidade, 2);

                $pedido->itens()->create([
                    'produto_id' => $produto->id,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'subtotal' => $subtotal,
                ]);

                $total += $subtotal;
            }

            $pedido->update([
                'valor_total' => round($total, 2),
            ]);

            return $pedido;
        });

        session()->forget('compra.itens');

        return redirect()
            ->route('pedidos.show', $pedido)
            ->with('success', 'Pedido criado com sucesso.');
    }

    private function garantirCliente(?User $authUser): void
    {
        if (! $authUser || ! $authUser->isCliente()) {
            abort(403);
        }
    }

    private function validarItensDaCompra(Request $request): array
    {
        $itensNormalizados = $this->normalizarItens($request->input('itens', []));

        $request->merge([
            'itens' => $itensNormalizados,
        ]);

        $dados = $request->validate([
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'integer', 'distinct'],
            'itens.*.quantidade' => ['required', 'integer', 'min:1'],
        ]);

        return $dados['itens'];
    }

    private function normalizarItens(mixed $itens): array
    {
        return collect(is_array($itens) ? $itens : [])
            ->filter(function ($item) {
                if (! is_array($item)) {
                    return false;
                }

                $produtoId = $item['produto_id'] ?? null;
                $quantidade = (int) ($item['quantidade'] ?? 0);

                return filled($produtoId) && $quantidade > 0;
            })
            ->map(function ($item) {
                return [
                    'produto_id' => (int) $item['produto_id'],
                    'quantidade' => (int) $item['quantidade'],
                ];
            })
            ->groupBy('produto_id')
            ->map(function ($grupo, $produtoId) {
                return [
                    'produto_id' => (int) $produtoId,
                    'quantidade' => $grupo->sum('quantidade'),
                ];
            })
            ->values()
            ->all();
    }

    private function buscarProdutosAtivosIndexados(array $itens): Collection
    {
        $produtoIds = collect($itens)
            ->pluck('produto_id')
            ->unique()
            ->values();

        $produtos = Produto::whereIn('id', $produtoIds)
            ->where('ativo', true)
            ->get()
            ->keyBy('id');

        if ($produtos->count() !== $produtoIds->count()) {
            throw ValidationException::withMessages([
                'itens' => 'Um ou mais produtos selecionados não estão mais disponíveis.',
            ]);
        }

        return $produtos;
    }

    private function montarResumoDosItens(array $itens, Collection $produtos): Collection
    {
        return collect($itens)
            ->map(function ($item) use ($produtos) {
                $produto = $produtos[$item['produto_id']];
                $quantidade = (int) $item['quantidade'];
                $precoUnitario = (float) $produto->preco;
                $subtotal = round($precoUnitario * $quantidade, 2);

                return [
                    'produto' => $produto,
                    'quantidade' => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'subtotal' => $subtotal,
                ];
            })
            ->values();
    }

    private function resolverEnderecoDaCompra(User $authUser, array $dados): Endereco
    {
        if ($dados['address_mode'] === 'existing') {
            $endereco = Endereco::where('id', $dados['endereco_id'] ?? 0)
                ->where('user_id', $authUser->id)
                ->where('ativo', true)
                ->first();

            if (! $endereco) {
                throw ValidationException::withMessages([
                    'endereco_id' => 'Selecione um endereço válido e ativo.',
                ]);
            }

            return $endereco;
        }

        return $this->criarNovoEnderecoParaCompra(
            $authUser,
            $dados['novo_endereco'] ?? []
        );
    }

    private function criarNovoEnderecoParaCompra(User $authUser, array $dados): Endereco
    {
        $temPrincipalAtivo = $authUser->enderecos()
            ->where('ativo', true)
            ->where('is_principal', true)
            ->exists();

        return $authUser->enderecos()->create([
            'cep' => $dados['cep'],
            'rua' => $dados['rua'],
            'numero' => $dados['numero'],
            'complemento' => $dados['complemento'] ?? null,
            'bairro' => $dados['bairro'],
            'cidade' => $dados['cidade'],
            'uf' => $dados['uf'],
            'ativo' => true,
            'is_principal' => ! $temPrincipalAtivo,
        ]);
    }
}