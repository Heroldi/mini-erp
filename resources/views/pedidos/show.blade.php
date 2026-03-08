<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pedido #{{ $pedido->id }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col gap-2 sm:flex-row sm:items-start sm:justify-between">
                    <div class="space-y-1">
                        <div><strong>Cliente:</strong> {{ $pedido->cliente->nome }}</div>
                        <div><strong>Data:</strong> {{ $pedido->data_pedido }}</div>
                        <div><strong>Status:</strong> {{ $pedido->status }}</div>
                        <div><strong>Total:</strong> R$ {{ number_format((float) $pedido->valor_total, 2, ',', '.') }}</div>
                    </div>

                    <div class="flex gap-2 mt-3 sm:mt-0">
                        <a href="{{ route('pedidos.edit', $pedido) }}" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Editar
                        </a>

                        <form action="{{ route('pedidos.destroy', $pedido) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded">
                                Excluir
                            </button>
                        </form>

                        <a href="{{ route('pedidos.index') }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                            Voltar
                        </a>
                    </div>
                </div>

                <div class="mt-6">
                    <h3 class="font-semibold mb-3">Itens do Pedido</h3>

                    <table class="min-w-full border">
                        <thead>
                            <tr>
                                <th class="border px-3 py-2 text-left">Produto</th>
                                <th class="border px-3 py-2 text-left">Qtd</th>
                                <th class="border px-3 py-2 text-left">Preço Unit.</th>
                                <th class="border px-3 py-2 text-left">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pedido->itens as $item)
                                <tr>
                                    <td class="border px-3 py-2">{{ $item->produto->nome }}</td>
                                    <td class="border px-3 py-2">{{ $item->quantidade }}</td>
                                    <td class="border px-3 py-2">
                                        R$ {{ number_format((float) $item->preco_unitario, 2, ',', '.') }}
                                    </td>
                                    <td class="border px-3 py-2">
                                        R$ {{ number_format((float) $item->subtotal, 2, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="border px-3 py-2 text-center">
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
</x-app-layout>