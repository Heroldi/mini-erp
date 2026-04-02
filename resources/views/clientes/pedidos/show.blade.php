<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Detalhes do pedido
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Pedido #{{ $pedido->id }} de {{ $usuario->name }}.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('clientes.pedidos.index', $usuario) }}"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Voltar para pedidos
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
                    <p class="font-semibold">Ocorreram erros:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2 space-y-6">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Itens do pedido
                            </h3>
                        </div>

                        <div class="p-6">
                            @if ($pedido->itens->count())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                        <thead class="bg-gray-50 dark:bg-gray-900/50">
                                            <tr>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                    Produto
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                    Quantidade
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                    Preço unitário
                                                </th>
                                                <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                    Subtotal
                                                </th>
                                            </tr>
                                        </thead>

                                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                            @foreach ($pedido->itens as $item)
                                                <tr>
                                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                        {{ $item->produto?->nome ?? 'Produto removido' }}
                                                    </td>

                                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                        {{ $item->quantidade }}
                                                    </td>

                                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                        R$ {{ number_format($item->preco_unitario, 2, ',', '.') }}
                                                    </td>

                                                    <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                        R$ {{ number_format($item->subtotal, 2, ',', '.') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>

                                        <tfoot class="bg-gray-50 dark:bg-gray-900/50">
                                            <tr>
                                                <td colspan="3" class="px-4 py-4 text-right text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    Total
                                                </td>
                                                <td class="px-4 py-4 text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                    R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            @else
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Este pedido não possui itens.
                                </p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Resumo do pedido
                            </h3>
                        </div>

                        <div class="space-y-4 px-6 py-6 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Pedido</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">#{{ $pedido->id }}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Data</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ \Carbon\Carbon::parse($pedido->data_pedido)->format('d/m/Y') }}
                                </p>
                            </div>

                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Status</p>
                                @if ($pedido->status === 'aberto')
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                                        Aberto
                                    </span>
                                @elseif ($pedido->status === 'finalizado')
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                        Finalizado
                                    </span>
                                @elseif ($pedido->status === 'cancelado')
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                        Cancelado
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                        {{ ucfirst($pedido->status) }}
                                    </span>
                                @endif
                            </div>

                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Valor total</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    @if ($pedido->status === 'aberto')
                        <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                            <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                    Ações do pedido
                                </h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                    Este pedido ainda está aberto e pode ser finalizado ou cancelado.
                                </p>
                            </div>

                            <div class="space-y-3 px-6 py-6">
                                <form method="POST" action="{{ route('clientes.pedidos.finalize', [$usuario, $pedido]) }}">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-green-500"
                                    >
                                        Finalizar pedido
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('clientes.pedidos.cancel', [$usuario, $pedido]) }}">
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="inline-flex w-full items-center justify-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-red-500"
                                    >
                                        Cancelar pedido
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Endereço de entrega
                            </h3>
                        </div>

                        <div class="space-y-3 px-6 py-6 text-sm text-gray-700 dark:text-gray-300">
                            <div>
                                <p class="font-medium text-gray-900 dark:text-gray-100">
                                    {{ $pedido->entrega_rua }}, {{ $pedido->entrega_numero }}
                                </p>

                                @if ($pedido->entrega_complemento)
                                    <p>{{ $pedido->entrega_complemento }}</p>
                                @endif

                                <p>{{ $pedido->entrega_bairro }}</p>
                                <p>{{ $pedido->entrega_cidade }}/{{ $pedido->entrega_uf }}</p>
                                <p>CEP: {{ $pedido->entrega_cep }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Cliente
                            </h3>
                        </div>

                        <div class="space-y-4 px-6 py-6 text-sm">
                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Nome</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $usuario->name }}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Email</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $usuario->email }}</p>
                            </div>

                            <div>
                                <p class="text-gray-500 dark:text-gray-400">CPF</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $usuario->cpf }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>