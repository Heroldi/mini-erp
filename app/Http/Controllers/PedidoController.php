<?php

namespace App\Http\Controllers;

use App\Models\Endereco;
use App\Models\Pedido;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PedidoController extends Controller
{
    public function index(Request $request): View
    {
        $authUser = $request->user();

        $pedidosQuery = Pedido::with('user')
            ->withCount('itens')
            ->orderByDesc('data_pedido')
            ->orderByDesc('id');

        $clientes = collect();
        $userId = null;
        $status = $request->string('status')->value();
        $nome = $request->string('nome')->value();
        $cpf = $request->string('cpf')->value();

        if ($authUser->isCliente()) {
            $pedidosQuery->where('user_id', $authUser->id);
        } elseif ($authUser->isInterno()) {
            $clientes = User::whereHas('role', function ($query) {
                    $query->where('nome', 'cliente');
                })
                ->orderBy('name')
                ->get();

            $userId = $request->integer('user_id') ?: null;

            if ($userId) {
                $pedidosQuery->where('user_id', $userId);
            }

            if ($status) {
                $pedidosQuery->where('status', $status);
            }

            if ($nome) {
                $pedidosQuery->whereHas('user', function ($query) use ($nome) {
                    $query->where('name', 'like', '%' . $nome . '%');
                });
            }

            if ($cpf) {
                $pedidosQuery->whereHas('user', function ($query) use ($cpf) {
                    $query->where('cpf', 'like', '%' . $cpf . '%');
                });
            }
        } else {
            abort(403);
        }

        $pedidos = $pedidosQuery
            ->paginate(10)
            ->appends($request->query());

        return view('pedidos.index', compact('pedidos', 'clientes', 'userId', 'status', 'nome', 'cpf'));
    }

    public function show(Request $request, Pedido $pedido): View
    {
        $authUser = $request->user();

        $this->garantirQuePodeVisualizar($authUser, $pedido);

        $pedido->load(['user', 'itens.produto']);

        return view('pedidos.show', compact('pedido'));
    }

    public function cancel(Request $request, Pedido $pedido): RedirectResponse
    {
        $authUser = $request->user();

        $this->garantirQuePodeCancelar($authUser, $pedido);

        $pedido->update([
            'status' => 'cancelado',
        ]);

        return redirect()
            ->route('pedidos.show', $pedido)
            ->with('success', 'Pedido cancelado com sucesso.');
    }

    public function finalize(Request $request, Pedido $pedido): RedirectResponse
    {
        $authUser = $request->user();

        $this->garantirQuePodeFinalizar($authUser, $pedido);

        $pedido->update([
            'status' => 'finalizado',
        ]);

        return redirect()
            ->route('pedidos.show', $pedido)
            ->with('success', 'Pedido finalizado com sucesso.');
    }

    private function garantirQuePodeVisualizar(User $authUser, Pedido $pedido): void
    {
        if ($authUser->isInterno()) {
            return;
        }

        if ($authUser->isCliente() && $pedido->user_id === $authUser->id) {
            return;
        }

        abort(403);
    }

    private function garantirQuePodeCancelar(User $authUser, Pedido $pedido): void
    {
        $this->garantirQuePodeVisualizar($authUser, $pedido);

        if ($pedido->status !== 'aberto') {
            abort(403, 'Somente pedidos abertos podem ser cancelados.');
        }
    }

    private function garantirQuePodeFinalizar(User $authUser, Pedido $pedido): void
    {
        $this->garantirQuePodeVisualizar($authUser, $pedido);

        if (! $authUser->isInterno()) {
            abort(403, 'Somente a equipe pode finalizar pedidos.');
        }

        if ($pedido->status !== 'aberto') {
            abort(403, 'Somente pedidos abertos podem ser finalizados.');
        }
    }
}