<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pedidos
        </h2>
    </x-slot>

    @php
        $clienteIdAtual = $clienteId ?? request('cliente_id');
    @endphp

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-end sm:justify-between">
                <form method="GET" action="{{ route('pedidos.index') }}" class="flex flex-col gap-2 sm:flex-row sm:items-end">
                    <div>
                        <label for="cliente_id" class="block mb-1">Filtrar por cliente</label>
                        <select name="cliente_id" id="cliente_id" class="border rounded px-3 py-2 w-full sm:w-80">
                            <option value="">Todos</option>
                            @foreach($clientes as $cliente)
                                <option value="{{ $cliente->id }}"
                                    {{ (string) $clienteIdAtual === (string) $cliente->id ? 'selected' : '' }}>
                                    {{ $cliente->nome }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Filtrar
                        </button>

                        <a href="{{ route('pedidos.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                            Limpar
                        </a>
                    </div>
                </form>

                <a href="{{ route('pedidos.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded text-center">
                    Novo Pedido
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <table class="min-w-full border">
                    <thead>
                        <tr>
                            <th class="border px-3 py-2 text-left">ID</th>
                            <th class="border px-3 py-2 text-left">Cliente</th>
                            <th class="border px-3 py-2 text-left">Data</th>
                            <th class="border px-3 py-2 text-left">Status</th>
                            <th class="border px-3 py-2 text-left">Itens</th>
                            <th class="border px-3 py-2 text-left">Total</th>
                            <th class="border px-3 py-2 text-left">Detalhes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pedidos as $pedido)
                            <tr>
                                <td class="border px-3 py-2">{{ $pedido->id }}</td>
                                <td class="border px-3 py-2">{{ $pedido->cliente->nome }}</td>
                                <td class="border px-3 py-2">{{ $pedido->data_pedido }}</td>
                                <td class="border px-3 py-2">{{ $pedido->status }}</td>
                                <td class="border px-3 py-2">{{ $pedido->itens_count ?? ($pedido->itens->count() ?? '') }}</td>
                                <td class="border px-3 py-2">
                                    R$ {{ number_format((float) $pedido->valor_total, 2, ',', '.') }}
                                </td>
                                <td class="border px-3 py-2">
                                    <a href="{{ route('pedidos.show', $pedido) }}" class="text-indigo-600">
                                        Ver
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="border px-3 py-2 text-center">
                                    Nenhum pedido encontrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <div class="mt-4">
                </div>
            </div>
        </div>
    </div>
</x-app-layout>