<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class ClientePedidoManagementController extends Controller
{
    public function index(Request $request, User $usuario): View
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);

        $pedidos = $usuario->pedidos()
            ->withCount('itens')
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderByDesc('id')
            ->paginate(10)
            ->appends($request->query());

        return view('clientes.pedidos.index', compact('usuario', 'pedidos'));
    }

    public function create(Request $request, User $usuario): View
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);

        $produtos = Produto::query()
            ->where('ativo', true)
            ->when($request->filled('busca'), function ($query) use ($request) {
                $busca = $request->input('busca');

                $query->where(function ($subQuery) use ($busca) {
                    $subQuery->where('nome', 'like', '%' . $busca . '%')
                        ->orWhere('descricao', 'like', '%' . $busca . '%');
                });
            })
            ->orderBy('nome')
            ->paginate(20)
            ->appends($request->query());

        $enderecos = $usuario->enderecos()
            ->where('ativo', true)
            ->orderByDesc('is_principal')
            ->orderBy('id')
            ->get();

        return view('clientes.pedidos.create', compact('usuario', 'produtos', 'enderecos'));
    }

    public function store(Request $request, User $usuario): RedirectResponse
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);

        $dados = $request->validate([
            'endereco_id' => ['required', 'integer'],
            'itens' => ['required', 'array', 'min:1'],
            'itens.*.produto_id' => ['required', 'integer'],
            'itens.*.quantidade' => ['required', 'integer', 'min:0'],
        ]);

        $endereco = $usuario->enderecos()
            ->where('id', $dados['endereco_id'])
            ->where('ativo', true)
            ->first();

        if (! $endereco) {
            return back()
                ->withErrors([
                    'endereco_id' => 'O endereço selecionado é inválido ou está inativo.',
                ])
                ->withInput();
        }

        $itens = collect($dados['itens'])
            ->map(function ($item) {
                return [
                    'produto_id' => (int) $item['produto_id'],
                    'quantidade' => (int) $item['quantidade'],
                ];
            })
            ->filter(function ($item) {
                return $item['quantidade'] > 0;
            })
            ->values()
            ->all();

        if (empty($itens)) {
            return back()
                ->withErrors([
                    'itens' => 'Informe ao menos um produto com quantidade maior que zero.',
                ])
                ->withInput();
        }

        $produtoIds = collect($itens)
            ->pluck('produto_id')
            ->unique()
            ->values();

        $produtos = Produto::query()
            ->whereIn('id', $produtoIds)
            ->where('ativo', true)
            ->get()
            ->keyBy('id');

        if ($produtos->count() !== $produtoIds->count()) {
            return back()
                ->withErrors([
                    'itens' => 'Um ou mais produtos selecionados são inválidos ou estão inativos.',
                ])
                ->withInput();
        }

        $pedido = DB::transaction(function () use ($usuario, $endereco, $itens, $produtos) {
            $pedido = Pedido::create([
                'user_id' => $usuario->id,
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

        return redirect()
            ->route('clientes.pedidos.show', [$usuario, $pedido])
            ->with('success', 'Pedido criado com sucesso.');
    }

    public function show(Request $request, User $usuario, Pedido $pedido): View
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);
        $this->garantePedidoDoCliente($usuario, $pedido);

        $pedido->load(['itens.produto']);

        return view('clientes.pedidos.show', compact('usuario', 'pedido'));
    }

    public function finalize(Request $request, User $usuario, Pedido $pedido): RedirectResponse
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);
        $this->garantePedidoDoCliente($usuario, $pedido);

        if ($pedido->status !== 'aberto') {
            return redirect()
                ->route('clientes.pedidos.show', [$usuario, $pedido])
                ->withErrors([
                    'pedido' => 'Somente pedidos abertos podem ser finalizados.',
                ]);
        }

        $pedido->update([
            'status' => 'finalizado',
        ]);

        return redirect()
            ->route('clientes.pedidos.show', [$usuario, $pedido])
            ->with('success', 'Pedido finalizado com sucesso.');
    }

    public function cancel(Request $request, User $usuario, Pedido $pedido): RedirectResponse
    {
        $authUser = $request->user();

        $this->garanteQuePodeGerenciar($authUser, $usuario);
        $this->garantePedidoDoCliente($usuario, $pedido);

        if ($pedido->status !== 'aberto') {
            return redirect()
                ->route('clientes.pedidos.show', [$usuario, $pedido])
                ->withErrors([
                    'pedido' => 'Somente pedidos abertos podem ser cancelados.',
                ]);
        }

        $pedido->update([
            'status' => 'cancelado',
        ]);

        return redirect()
            ->route('clientes.pedidos.show', [$usuario, $pedido])
            ->with('success', 'Pedido cancelado com sucesso.');
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

    private function garantePedidoDoCliente(User $usuario, Pedido $pedido): void
    {
        if ($pedido->user_id !== $usuario->id) {
            abort(404);
        }
    }
}