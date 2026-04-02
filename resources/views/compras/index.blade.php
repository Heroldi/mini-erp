<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Comprar agora
        </h2>
    </x-slot>

    @php
        $quantidadesOld = collect(old('itens', []))
            ->filter(fn ($item) => is_array($item) && isset($item['produto_id']))
            ->mapWithKeys(function ($item) {
                return [
                    (int) $item['produto_id'] => (int) ($item['quantidade'] ?? 0),
                ];
            });

        $produtosPayload = $produtos->getCollection()
            ->map(function ($produto) use ($quantidadesOld) {
                return [
                    'id' => $produto->id,
                    'nome' => $produto->nome,
                    'descricao' => $produto->descricao,
                    'preco' => (float) $produto->preco,
                    'quantidade' => (int) ($quantidadesOld[$produto->id] ?? 0),
                ];
            })
            ->values();
    @endphp

    <div
        class="py-6"
        x-data="{
            produtos: @js($produtosPayload),
            formatCurrency(valor) {
                return new Intl.NumberFormat('pt-BR', {
                    style: 'currency',
                    currency: 'BRL',
                }).format(valor);
            },
            total() {
                return this.produtos.reduce((acc, produto) => {
                    return acc + (produto.preco * produto.quantidade);
                }, 0);
            },
            totalItens() {
                return this.produtos.reduce((acc, produto) => {
                    return acc + produto.quantidade;
                }, 0);
            },
            aumentar(produto) {
                produto.quantidade++;
            },
            diminuir(produto) {
                if (produto.quantidade > 0) {
                    produto.quantidade--;
                }
            }
        }"
    >
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                            Produtos disponíveis
                        </h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Escolha as quantidades e depois siga para o checkout.
                        </p>
                    </div>

                    <div class="md:text-right">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Itens selecionados</div>
                        <div class="font-semibold text-gray-800 dark:text-gray-100" x-text="totalItens()"></div>

                        <div class="text-sm text-gray-600 dark:text-gray-400 mt-2">Total parcial</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-gray-100" x-text="formatCurrency(total())"></div>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <form method="GET" action="{{ route('compras.index') }}" class="grid grid-cols-1 md:grid-cols-[1fr_auto] gap-3">
                    <div>
                        <label for="busca" class="block mb-1 text-sm font-medium text-gray-700 dark:text-gray-300">
                            Buscar produto
                        </label>
                        <input
                            type="text"
                            name="busca"
                            id="busca"
                            value="{{ $busca }}"
                            placeholder="Digite nome ou descrição"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        >
                    </div>

                    <div class="flex items-end gap-2">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 transition dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                        >
                            Filtrar
                        </button>

                        @if ($busca)
                            <a
                                href="{{ route('compras.index') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Limpar
                            </a>
                        @endif
                    </div>
                </form>
            </div>

            <form method="POST" action="{{ route('compras.prepare-checkout') }}" class="space-y-6">
                @csrf

                @if ($errors->has('itens'))
                    <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
                        {{ $errors->first('itens') }}
                    </div>
                @endif

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    @if ($produtos->isEmpty())
                        <div class="text-center text-gray-600 dark:text-gray-400 py-8">
                            Nenhum produto encontrado.
                        </div>
                    @else
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <template x-for="(produto, index) in produtos" :key="produto.id">
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 flex flex-col gap-4 bg-white dark:bg-gray-800">
                                    <input
                                        type="hidden"
                                        :name="`itens[${index}][produto_id]`"
                                        :value="produto.id"
                                    >

                                    <input
                                        type="hidden"
                                        :name="`itens[${index}][quantidade]`"
                                        :value="produto.quantidade"
                                    >

                                    <div>
                                        <h4 class="font-semibold text-gray-800 dark:text-gray-100" x-text="produto.nome"></h4>

                                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 min-h-[40px]">
                                            <span x-text="produto.descricao ? produto.descricao : 'Sem descrição.'"></span>
                                        </p>
                                    </div>

                                    <div class="flex items-center justify-between gap-4">
                                        <div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">Preço unitário</div>
                                            <div class="font-semibold text-gray-900 dark:text-gray-100" x-text="formatCurrency(produto.preco)"></div>
                                        </div>

                                        <div class="flex items-center gap-3">
                                            <button
                                                type="button"
                                                @click="diminuir(produto)"
                                                class="w-10 h-10 rounded border border-gray-300 dark:border-gray-600 text-lg text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-900"
                                            >
                                                -
                                            </button>

                                            <div class="min-w-[32px] text-center font-semibold text-gray-900 dark:text-gray-100" x-text="produto.quantidade"></div>

                                            <button
                                                type="button"
                                                @click="aumentar(produto)"
                                                class="w-10 h-10 rounded border border-gray-300 dark:border-gray-600 text-lg text-gray-800 dark:text-gray-100 bg-white dark:bg-gray-900"
                                            >
                                                +
                                            </button>
                                        </div>
                                    </div>

                                    <div class="pt-2 border-t border-gray-200 dark:border-gray-700">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">Subtotal</div>
                                        <div class="font-semibold text-gray-900 dark:text-gray-100" x-text="formatCurrency(produto.preco * produto.quantidade)"></div>
                                    </div>
                                </div>
                            </template>
                        </div>
                    @endif
                </div>

                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Itens selecionados</div>
                            <div class="font-semibold text-gray-800 dark:text-gray-100" x-text="totalItens()"></div>
                        </div>

                        <div class="md:text-right">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Total parcial</div>
                            <div class="text-2xl font-bold text-gray-900 dark:text-gray-100" x-text="formatCurrency(total())"></div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition"
                        >
                            Finalizar pedido
                        </button>
                    </div>
                </div>
            </form>

            @if ($produtos->hasPages())
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    {{ $produtos->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>