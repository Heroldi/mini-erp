<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Produtos
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                <a href="{{ route('produtos.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded">
                    Novo produto
                </a>

                <table class="min-w-full border">
                    <thead>
                        <tr>
                            <th class="border px-3 py-2 text-left">Nome</th>
                            <th class="border px-3 py-2 text-left">Descrição</th>
                            <th class="border px-3 py-2 text-left">Preço</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produtos as $produto)
                            <tr>
                                <td class="border px-3 py-2">{{ $produto->nome }}</td>
                                <td class="border px-3 py-2">{{ $produto->descricao }}</td>
                                <td class="border px-3 py-2">{{ $produto->preco }}</td>
                                <td class="border px-3 py-2">
                                    <a href="{{ route('produtos.edit', $produto) }}" class="text-blue-600">Editar</a>

                                    <form action="{{ route('produtos.destroy', $produto) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 ml-2">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border px-3 py-2 text-center">
                                    Nenhum produto cadastrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>