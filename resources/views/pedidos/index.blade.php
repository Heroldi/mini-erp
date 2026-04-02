<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Pedidos
        </h2>
    </x-slot>

    @php
        $userIdAtual = $userId ?? request('user_id');
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @if (auth()->user()?->isInterno())
                        <form method="GET" action="{{ route('pedidos.index') }}" class="mb-6">
                            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                                <div>
                                    <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        Nome do cliente
                                    </label>
                                    <input
                                        type="text"
                                        name="nome"
                                        id="nome"
                                        value="{{ request('nome') }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        placeholder="Digite o nome"
                                    >
                                </div>

                                <div>
                                    <label for="cpf" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                        CPF
                                    </label>
                                    <input
                                        type="text"
                                        name="cpf"
                                        id="cpf"
                                        value="{{ request('cpf') }}"
                                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        placeholder="Digite o CPF"
                                    >
                                </div>

                                <div class="flex items-end gap-2 md:col-span-2">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 transition dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                                    >
                                        Filtrar
                                    </button>

                                    <a
                                        href="{{ route('pedidos.index') }}"
                                        class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700"
                                    >
                                        Limpar
                                    </a>
                                </div>
                            </div>
                        </form>
                    @endif

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        ID
                                    </th>
                                    @if (auth()->user()?->isInterno())
                                        <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Cliente
                                        </th>
                                    @endif
                                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Data
                                    </th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Status
                                    </th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Itens
                                    </th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Total
                                    </th>
                                    <th class="px-3 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Detalhes
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse($pedidos as $pedido)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                        <td class="px-3 py-4 text-sm text-gray-900 dark:text-gray-100">
                                            {{ $pedido->id }}
                                        </td>

                                        @if (auth()->user()?->isInterno())
                                            <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $pedido->user->name }}
                                            </td>
                                        @endif

                                        <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $pedido->data_pedido }}
                                        </td>

                                        <td class="px-3 py-4 text-sm">
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
                                        </td>

                                        <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $pedido->itens_count ?? ($pedido->itens->count() ?? '') }}
                                        </td>

                                        <td class="px-3 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            R$ {{ number_format((float) $pedido->valor_total, 2, ',', '.') }}
                                        </td>

                                        <td class="px-3 py-4 text-sm">
                                            <a
                                                href="{{ route('pedidos.show', $pedido) }}"
                                                class="inline-flex items-center rounded-md border border-indigo-300 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-50 transition dark:border-indigo-700 dark:text-indigo-300 dark:hover:bg-indigo-900/30"
                                            >
                                                Ver
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="{{ auth()->user()?->isInterno() ? 7 : 6 }}" class="px-3 py-8 text-sm text-gray-500 dark:text-gray-400 text-center">
                                            Nenhum pedido encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-6">
                        {{ $pedidos->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>