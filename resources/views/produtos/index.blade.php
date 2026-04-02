<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Produtos
            </h2>

            <a href="{{ route('produtos.create') }}"
               class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition">
                Novo produto
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
                    <div class="font-semibold mb-1">Ocorreram erros:</div>
                    <ul class="list-disc pl-5 space-y-1">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <form method="GET" action="{{ route('produtos.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                        <div class="md:col-span-2">
                            <label for="busca" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Buscar
                            </label>
                            <input
                                type="text"
                                name="busca"
                                id="busca"
                                value="{{ request('busca') }}"
                                placeholder="Nome ou descrição do produto"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            >
                        </div>

                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select
                                name="status"
                                id="status"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            >
                                <option value="">Todos</option>
                                <option value="1" @selected(request('status') === '1')>Ativos</option>
                                <option value="0" @selected(request('status') === '0')>Inativos</option>
                            </select>
                        </div>

                        <div class="flex items-end gap-2">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 transition dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                            >
                                Filtrar
                            </button>

                            <a
                                href="{{ route('produtos.index') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Limpar
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        ID
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Nome
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Descrição
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Preço
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Status
                                    </th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Ações
                                    </th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($produtos as $produto)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $produto->id }}
                                        </td>

                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $produto->nome }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            @if ($produto->descricao)
                                                {{ \Illuminate\Support\Str::limit($produto->descricao, 80) }}
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">Sem descrição</span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                        </td>

                                        <td class="px-4 py-4 text-sm">
                                            @if ($produto->ativo)
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                                    Ativo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                                    Inativo
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 text-sm">
                                            <div class="flex justify-end gap-2">
                                                <a
                                                    href="{{ route('produtos.edit', $produto) }}"
                                                    class="inline-flex items-center rounded-md border border-indigo-300 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-50 transition dark:border-indigo-700 dark:text-indigo-300 dark:hover:bg-indigo-900/30"
                                                >
                                                    Editar
                                                </a>

                                                <form method="POST" action="{{ route('produtos.toggle-ativo', $produto) }}">
                                                    @csrf
                                                    @method('PATCH')

                                                    <button
                                                        type="submit"
                                                        class="inline-flex items-center rounded-md px-3 py-2 text-xs font-semibold text-white transition {{ $produto->ativo ? 'bg-red-600 hover:bg-red-500' : 'bg-green-600 hover:bg-green-500' }}"
                                                    >
                                                        {{ $produto->ativo ? 'Inativar' : 'Ativar' }}
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Nenhum produto encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($produtos->hasPages())
                        <div class="mt-6">
                            {{ $produtos->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>