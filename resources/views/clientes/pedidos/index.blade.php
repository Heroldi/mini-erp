<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Pedidos do cliente
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Gerencie os pedidos de {{ $usuario->name }}.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('clientes.edit', $usuario) }}"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Voltar para cliente
                </a>

                <a
                    href="{{ route('clientes.pedidos.create', $usuario) }}"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                >
                    Novo pedido
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

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                        Lista de pedidos
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Consulte os pedidos já criados para este cliente.
                    </p>
                </div>

                <div class="p-6">
                    <form method="GET" action="{{ route('clientes.pedidos.index', $usuario) }}" class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Status
                            </label>
                            <select
                                id="status"
                                name="status"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            >
                                <option value="">Todos</option>
                                <option value="aberto" @selected(request('status') === 'aberto')>Aberto</option>
                                <option value="finalizado" @selected(request('status') === 'finalizado')>Finalizado</option>
                                <option value="cancelado" @selected(request('status') === 'cancelado')>Cancelado</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2 md:col-span-3">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                            >
                                Filtrar
                            </button>

                            <a
                                href="{{ route('clientes.pedidos.index', $usuario) }}"
                                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Limpar
                            </a>
                        </div>
                    </form>

                    @if ($pedidos->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Pedido
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Data
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Itens
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Status
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Total
                                        </th>
                                        <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Ações
                                        </th>
                                    </tr>
                                </thead>

                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach ($pedidos as $pedido)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                            <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                #{{ $pedido->id }}
                                            </td>

                                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                {{ \Carbon\Carbon::parse($pedido->data_pedido)->format('d/m/Y') }}
                                            </td>

                                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $pedido->itens_count }}
                                            </td>

                                            <td class="px-4 py-4 text-sm">
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

                                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                R$ {{ number_format($pedido->valor_total, 2, ',', '.') }}
                                            </td>

                                            <td class="px-4 py-4 text-sm text-right">
                                                <a
                                                    href="{{ route('clientes.pedidos.show', [$usuario, $pedido]) }}"
                                                    class="inline-flex items-center rounded-md border border-indigo-300 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-50 dark:border-indigo-700 dark:text-indigo-300 dark:hover:bg-indigo-900/30"
                                                >
                                                    Ver mais
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($pedidos->hasPages())
                            <div class="mt-6">
                                {{ $pedidos->links() }}
                            </div>
                        @endif
                    @else
                        <div class="rounded-lg border border-dashed border-gray-300 px-6 py-10 text-center dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Este cliente ainda não possui pedidos cadastrados.
                            </p>

                            <a
                                href="{{ route('clientes.pedidos.create', $usuario) }}"
                                class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                            >
                                Criar primeiro pedido
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>