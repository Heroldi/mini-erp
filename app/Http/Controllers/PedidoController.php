<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Pedido;
use App\Models\Cliente;
use App\Models\Produto;
use App\Models\ItemPedido;

class PedidoController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('nome')->get();

        $clienteId = request('cliente_id'); // vem do GET ?cliente_id=...

        $pedidosQuery = Pedido::with('cliente')
            ->withCount('itens')
            ->orderByDesc('data_pedido');

        if ($clienteId) {
            $pedidosQuery->where('cliente_id', $clienteId);
        }

        $pedidos = $pedidosQuery
            ->paginate(10)
            ->appends(request()->query()); // mantém o filtro na paginação

        return view('pedidos.index', compact('pedidos', 'clientes', 'clienteId'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $produtos = Produto::orderBy('nome')->get();

        return view('pedidos.create', compact('clientes', 'produtos'));
    }

    public function store(Request $request)
    {
        $dados = $request->validate([
        'cliente_id'  => ['required', 'exists:clientes,id'],
        'data_pedido' => ['required', 'date'],
        'status'      => ['required', 'string', 'max:50'],

        // itens obrigatórios
        'itens' => ['required', 'array', 'min:1'],
        'itens.*.produto_id' => ['required', 'exists:produtos,id', 'distinct'],
        'itens.*.quantidade' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($dados) {
            $pedido = Pedido::create([
                'cliente_id'  => $dados['cliente_id'],
                'data_pedido' => $dados['data_pedido'],
                'status'      => $dados['status'],
                'valor_total' => 0,
            ]);

            $produtoIds = collect($dados['itens'])->pluck('produto_id')->unique()->values();
            $produtos = Produto::whereIn('id', $produtoIds)->get()->keyBy('id');

            $total = 0;

            foreach ($dados['itens'] as $item) {
                $produto = $produtos[$item['produto_id']];
                $quantidade = (int) $item['quantidade'];

                $precoUnitario = (float) $produto->preco;
                $subtotal = round($precoUnitario * $quantidade, 2);

                ItemPedido::create([
                    'pedido_id'      => $pedido->id,
                    'produto_id'     => $produto->id,
                    'quantidade'     => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'subtotal'       => $subtotal,
                ]);

                $total += $subtotal;
            }

            $pedido->update(['valor_total' => $total]);
        });

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido criado com sucesso.');
    }

    public function show(Pedido $pedido)
    {
        $pedido->load(['cliente', 'itens.produto']);

        return view('pedidos.show', compact('pedido'));
    }

    public function edit(Pedido $pedido)
    {
        $clientes = Cliente::orderBy('nome')->get();
        $produtos = Produto::orderBy('nome')->get();

        // importante: carregar itens e produto pra preencher a tela
        $pedido->load(['itens', 'itens.produto']);

        return view('pedidos.edit', compact('pedido', 'clientes', 'produtos'));
    }

    public function update(Request $request, Pedido $pedido)
    {
        $dados = $request->validate([
        'cliente_id'  => ['required', 'exists:clientes,id'],
        'data_pedido' => ['required', 'date'],
        'status'      => ['required', 'string', 'max:50'],

        'itens' => ['required', 'array', 'min:1'],
        'itens.*.produto_id' => ['required', 'exists:produtos,id', 'distinct'],
        'itens.*.quantidade' => ['required', 'integer', 'min:1'],
        ]);

        DB::transaction(function () use ($pedido, $dados) {
            // atualiza o cabeçalho do pedido
            $pedido->update([
                'cliente_id'  => $dados['cliente_id'],
                'data_pedido' => $dados['data_pedido'],
                'status'      => $dados['status'],
            ]);

            // remove os itens antigos para salvar os atuais
            $pedido->itens()->delete();

            // busca todos os produtos envolvidos de uma vez
            $produtoIds = collect($dados['itens'])->pluck('produto_id')->unique()->values();
            $produtos = Produto::whereIn('id', $produtoIds)->get()->keyBy('id');

            // recria os itens e recalcula total
            $total = 0;

            foreach ($dados['itens'] as $item) {
                $produto = $produtos[$item['produto_id']];
                $quantidade = (int) $item['quantidade'];

                $precoUnitario = (float) $produto->preco;
                $subtotal = round($precoUnitario * $quantidade, 2);

                ItemPedido::create([
                    'pedido_id'      => $pedido->id,
                    'produto_id'     => $produto->id,
                    'quantidade'     => $quantidade,
                    'preco_unitario' => $precoUnitario,
                    'subtotal'       => $subtotal,
                ]);

                $total += $subtotal;
            }

            $pedido->update(['valor_total' => $total]);
        });

        return redirect()
            ->route('pedidos.show', $pedido)
            ->with('success', 'Pedido atualizado com sucesso.');
    }

    public function destroy(Pedido $pedido)
    {
        $pedido->delete();

        return redirect()
            ->route('pedidos.index')
            ->with('success', 'Pedido removido com sucesso.');
    }
}
