<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Novo pedido
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Crie um novo pedido para {{ $usuario->name }}.
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

    @php
        $produtosJs = $produtos->map(function ($produto, $index) {
            return [
                'id' => $produto->id,
                'index' => $index,
                'preco' => (float) $produto->preco,
                'quantidade' => (int) old("itens.$index.quantidade", 0),
            ];
        })->values()->toArray();
    @endphp

    <div class="py-8">
        <div class="mx-auto max-w-7xl space-y-6 sm:px-6 lg:px-8">

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
                                Filtro de produtos
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Filtre os produtos antes de montar o pedido.
                            </p>
                        </div>

                        <div class="p-6">
                            <form method="GET" action="{{ route('clientes.pedidos.create', $usuario) }}" class="grid grid-cols-1 gap-4 md:grid-cols-4">
                                <div class="md:col-span-3">
                                    <label for="busca" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Buscar produto
                                    </label>
                                    <input
                                        id="busca"
                                        name="busca"
                                        type="text"
                                        value="{{ request('busca') }}"
                                        placeholder="Nome ou descrição do produto"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    >
                                </div>

                                <div class="flex items-end gap-2">
                                    <button
                                        type="submit"
                                        class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-gray-700 dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                                    >
                                        Filtrar
                                    </button>

                                    <a
                                        href="{{ route('clientes.pedidos.create', $usuario) }}"
                                        class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                    >
                                        Limpar
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div
                        class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800"
                        x-data="{
                            produtos: @js($produtosJs),
                            quantities: @js(collect($produtosJs)->mapWithKeys(fn ($produto) => [$produto['id'] => $produto['quantidade']])->toArray()),

                            quantidadeNormalizada(produtoId) {
                                let valor = parseInt(this.quantities[produtoId] ?? 0);

                                if (isNaN(valor) || valor < 0) {
                                    valor = 0;
                                }

                                return valor;
                            },

                            subtotal(produtoId, preco) {
                                return this.quantidadeNormalizada(produtoId) * parseFloat(preco);
                            },

                            totalPedido() {
                                return this.produtos.reduce((total, produto) => {
                                    return total + this.subtotal(produto.id, produto.preco);
                                }, 0);
                            },

                            quantidadeTotalItens() {
                                return this.produtos.reduce((total, produto) => {
                                    return total + this.quantidadeNormalizada(produto.id);
                                }, 0);
                            },

                            dinheiro(valor) {
                                return valor.toLocaleString('pt-BR', {
                                    minimumFractionDigits: 2,
                                    maximumFractionDigits: 2
                                });
                            }
                        }"
                    >
                        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Seleção de produtos
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Ajuste a quantidade dos produtos desejados. Todos começam em zero.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('clientes.pedidos.store', $usuario) }}" class="p-6">
                            @csrf

                            <div class="space-y-6">
                                @if ($produtos->count())
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                            <thead class="bg-gray-50 dark:bg-gray-900/50">
                                                <tr>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        Produto
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        Preço
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        Quantidade
                                                    </th>
                                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                                        Subtotal
                                                    </th>
                                                </tr>
                                            </thead>

                                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                                @foreach ($produtos as $index => $produto)
                                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                            <input
                                                                type="hidden"
                                                                name="itens[{{ $index }}][produto_id]"
                                                                value="{{ $produto->id }}"
                                                            >

                                                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                                                {{ $produto->nome }}
                                                            </div>

                                                            @if ($produto->descricao)
                                                                <div class="text-gray-500 dark:text-gray-400">
                                                                    {{ \Illuminate\Support\Str::limit($produto->descricao, 80) }}
                                                                </div>
                                                            @endif
                                                        </td>

                                                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                            R$ {{ number_format($produto->preco, 2, ',', '.') }}
                                                        </td>

                                                        <td class="px-4 py-4 text-sm">
                                                            <input
                                                                type="number"
                                                                name="itens[{{ $index }}][quantidade]"
                                                                x-model="quantities[{{ $produto->id }}]"
                                                                value="{{ old("itens.$index.quantidade", 0) }}"
                                                                min="0"
                                                                step="1"
                                                                class="block w-28 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                            >
                                                        </td>

                                                        <td class="px-4 py-4 text-sm font-medium text-gray-900 dark:text-gray-100">
                                                            R$ <span x-text="dinheiro(subtotal({{ $produto->id }}, {{ (float) $produto->preco }}))"></span>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    @if ($produtos->hasPages())
                                        <div class="mt-6">
                                            {{ $produtos->links() }}
                                        </div>
                                    @endif
                                @else
                                    <div class="rounded-lg border border-dashed border-gray-300 px-6 py-10 text-center dark:border-gray-700">
                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            Não há produtos ativos disponíveis para este filtro.
                                        </p>
                                    </div>
                                @endif

                                <div class="border-t border-gray-200 pt-6 dark:border-gray-700">
                                    <label for="endereco_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Endereço de entrega
                                    </label>

                                    <select
                                        id="endereco_id"
                                        name="endereco_id"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        required
                                    >
                                        <option value="">Selecione um endereço</option>

                                        @foreach ($enderecos as $endereco)
                                            <option
                                                value="{{ $endereco->id }}"
                                                @selected(old('endereco_id', optional($enderecos->firstWhere('is_principal', true))->id) == $endereco->id)
                                            >
                                                {{ $endereco->rua }}, {{ $endereco->numero }}
                                                @if ($endereco->complemento)
                                                    - {{ $endereco->complemento }}
                                                @endif
                                                - {{ $endereco->bairro }} - {{ $endereco->cidade }}/{{ $endereco->uf }}
                                                @if ($endereco->is_principal)
                                                    (Principal)
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="rounded-lg border border-indigo-200 bg-indigo-50 px-4 py-4 dark:border-indigo-800 dark:bg-indigo-900/20">
                                    <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                                        <div>
                                            <p class="text-sm text-indigo-700 dark:text-indigo-300">
                                                Quantidade total de itens
                                            </p>
                                            <p class="text-lg font-semibold text-indigo-900 dark:text-indigo-100" x-text="quantidadeTotalItens()"></p>
                                        </div>

                                        <div class="text-left sm:text-right">
                                            <p class="text-sm text-indigo-700 dark:text-indigo-300">
                                                Total atual do pedido
                                            </p>
                                            <p class="text-xl font-bold text-indigo-900 dark:text-indigo-100">
                                                R$ <span x-text="dinheiro(totalPedido())"></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 flex flex-wrap items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                                <a
                                    href="{{ route('clientes.pedidos.index', $usuario) }}"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                >
                                    Cancelar
                                </a>

                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                                >
                                    Criar pedido
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="space-y-6">
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

                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Endereços ativos
                            </h3>
                        </div>

                        <div class="space-y-4 px-6 py-6 text-sm">
                            @forelse ($enderecos as $endereco)
                                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                                    <p class="font-medium text-gray-900 dark:text-gray-100">
                                        {{ $endereco->rua }}, {{ $endereco->numero }}
                                    </p>
                                    <p class="mt-1 text-gray-600 dark:text-gray-300">
                                        {{ $endereco->bairro }} - {{ $endereco->cidade }}/{{ $endereco->uf }}
                                    </p>
                                    <p class="mt-1 text-gray-600 dark:text-gray-300">
                                        CEP: {{ $endereco->cep }}
                                    </p>

                                    @if ($endereco->is_principal)
                                        <span class="mt-2 inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
                                            Principal
                                        </span>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Este cliente não possui endereços ativos para entrega.
                                </p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>