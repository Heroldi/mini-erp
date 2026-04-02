<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pedido #{{ $pedido->id }}
        </h2>
    </x-slot>

    @php
        $authUser = auth()->user();
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                        <div class="space-y-1">
                            <div class="text-gray-700 dark:text-gray-300">
                                <strong class="text-gray-900 dark:text-gray-100">Cliente:</strong> {{ $pedido->user->name }}
                            </div>
                            <div class="text-gray-700 dark:text-gray-300">
                                <strong class="text-gray-900 dark:text-gray-100">Data:</strong> {{ $pedido->data_pedido }}
                            </div>
                            <div class="text-gray-700 dark:text-gray-300">
                                <strong class="text-gray-900 dark:text-gray-100">Status:</strong>
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
                            <div class="text-gray-700 dark:text-gray-300">
                                <strong class="text-gray-900 dark:text-gray-100">Total:</strong>
                                R$ {{ number_format((float) $pedido->valor_total, 2, ',', '.') }}
                            </div>
                        </div>

                        <div class="flex flex-wrap gap-2 mt-3 sm:mt-0">
                            <a
                                href="{{ route('pedidos.index') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Voltar
                            </a>

                            @if ($pedido->status === 'aberto')
                                @if ($authUser?->isInterno())
                                    <form method="POST" action="{{ route('pedidos.finalize', $pedido) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center rounded-md bg-green-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 transition"
                                        >
                                            Finalizar
                                        </button>
                                    </form>

                                    <form method="POST" action="{{ route('pedidos.cancel', $pedido) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition"
                                        >
                                            Cancelar
                                        </button>
                                    </form>
                                @elseif ($authUser?->isCliente())
                                    <form method="POST" action="{{ route('pedidos.cancel', $pedido) }}">
                                        @csrf
                                        @method('PATCH')

                                        <button
                                            type="submit"
                                            class="inline-flex items-center rounded-md bg-red-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-500 transition"
                                        >
                                            Cancelar
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </div>
                    </div>

                    <div class="mt-6">
                        <h3 class="font-semibold mb-3 text-gray-900 dark:text-gray-100">
                            Itens do Pedido
                        </h3>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Produto
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Qtd
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Preço Unit.
                                        </th>
                                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Subtotal
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($pedido->itens as $item)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                            <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $item->produto->nome }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $item->quantidade }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                R$ {{ number_format((float) $item->preco_unitario, 2, ',', '.') }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-8 text-sm text-gray-500 dark:text-gray-400 text-center">
                                                Pedido sem itens (não deveria acontecer).
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>