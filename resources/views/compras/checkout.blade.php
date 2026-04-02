<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Checkout
        </h2>
    </x-slot>

    @php
        $modoEnderecoInicial = old('address_mode', $enderecos->isNotEmpty() ? 'existing' : 'new');
        $enderecoSelecionadoId = old('endereco_id', $enderecos->first()?->id);
    @endphp

    <div
        class="py-6"
        x-data="{
            addressMode: @js($modoEnderecoInicial),
            selectedEnderecoId: @js($enderecoSelecionadoId)
        }"
    >
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
                    <div class="font-medium mb-2">Revise os dados antes de concluir:</div>
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
                <div class="xl:col-span-2 space-y-6">
                    <form action="{{ route('compras.store') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                            <div class="flex items-center justify-between gap-4 mb-4">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">
                                        Endereço de entrega
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        Escolha um endereço existente ou cadastre um novo.
                                    </p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="addressMode = 'existing'"
                                        class="px-4 py-2 rounded border transition"
                                        :class="addressMode === 'existing'
                                            ? 'bg-indigo-600 text-white border-indigo-600'
                                            : 'bg-white text-gray-800 border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-600'"
                                        @disabled($enderecos->isEmpty())
                                    >
                                        Usar existente
                                    </button>

                                    <button
                                        type="button"
                                        @click="addressMode = 'new'"
                                        class="px-4 py-2 rounded border transition"
                                        :class="addressMode === 'new'
                                            ? 'bg-indigo-600 text-white border-indigo-600'
                                            : 'bg-white text-gray-800 border-gray-300 dark:bg-gray-900 dark:text-gray-100 dark:border-gray-600'"
                                    >
                                        Novo endereço
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="address_mode" :value="addressMode">

                            <div x-show="addressMode === 'existing'" x-cloak>
                                @if ($enderecos->isEmpty())
                                    <div class="rounded border border-dashed border-gray-300 dark:border-gray-600 p-4 text-sm text-gray-600 dark:text-gray-400">
                                        Você ainda não possui endereço ativo. Cadastre um novo endereço para concluir.
                                    </div>
                                @else
                                    <div class="space-y-3">
                                        @foreach ($enderecos as $endereco)
                                            <label class="block border border-gray-200 dark:border-gray-700 rounded-lg p-4 cursor-pointer bg-white dark:bg-gray-800">
                                                <div class="flex items-start gap-3">
                                                    <input
                                                        type="radio"
                                                        name="endereco_id"
                                                        value="{{ $endereco->id }}"
                                                        x-model="selectedEnderecoId"
                                                        @click="addressMode = 'existing'"
                                                        class="mt-1"
                                                        {{ (string) $enderecoSelecionadoId === (string) $endereco->id ? 'checked' : '' }}
                                                    >

                                                    <div class="flex-1">
                                                        <div class="flex items-center gap-2 mb-2 flex-wrap">
                                                            @if ($endereco->is_principal)
                                                                <span class="text-xs bg-indigo-100 text-indigo-800 px-2 py-1 rounded dark:bg-indigo-900/40 dark:text-indigo-300">
                                                                    Principal
                                                                </span>
                                                            @endif

                                                            @if ($endereco->ativo)
                                                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded dark:bg-green-900/40 dark:text-green-300">
                                                                    Ativo
                                                                </span>
                                                            @endif
                                                        </div>

                                                        <div class="text-sm text-gray-800 dark:text-gray-200 space-y-1">
                                                            <p>{{ $endereco->rua }}, {{ $endereco->numero }}</p>

                                                            @if ($endereco->complemento)
                                                                <p>{{ $endereco->complemento }}</p>
                                                            @endif

                                                            <p>{{ $endereco->bairro }} - {{ $endereco->cidade }}/{{ $endereco->uf }}</p>
                                                            <p>CEP: {{ $endereco->cep }}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <div x-show="addressMode === 'new'" x-cloak>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label for="novo_endereco_cep" class="block mb-1 text-gray-700 dark:text-gray-300">CEP</label>
                                        <input
                                            type="text"
                                            name="novo_endereco[cep]"
                                            id="novo_endereco_cep"
                                            value="{{ old('novo_endereco.cep') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                        @error('novo_endereco.cep')
                                            <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="novo_endereco_uf" class="block mb-1 text-gray-700 dark:text-gray-300">UF</label>
                                        <input
                                            type="text"
                                            name="novo_endereco[uf]"
                                            id="novo_endereco_uf"
                                            value="{{ old('novo_endereco.uf') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                        @error('novo_endereco.uf')
                                            <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div class="md:col-span-2">
                                        <label for="novo_endereco_rua" class="block mb-1 text-gray-700 dark:text-gray-300">Rua</label>
                                        <input
                                            type="text"
                                            name="novo_endereco[rua]"
                                            id="novo_endereco_rua"
                                            value="{{ old('novo_endereco.rua') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                        @error('novo_endereco.rua')
                                            <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="novo_endereco_numero" class="block mb-1 text-gray-700 dark:text-gray-300">Número</label>
                                        <input
                                            type="text"
                                            name="novo_endereco[numero]"
                                            id="novo_endereco_numero"
                                            value="{{ old('novo_endereco.numero') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                        @error('novo_endereco.numero')
                                            <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="novo_endereco_complemento" class="block mb-1 text-gray-700 dark:text-gray-300">Complemento</label>
                                        <input
                                            type="text"
                                            name="novo_endereco[complemento]"
                                            id="novo_endereco_complemento"
                                            value="{{ old('novo_endereco.complemento') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                        @error('novo_endereco.complemento')
                                            <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="novo_endereco_bairro" class="block mb-1 text-gray-700 dark:text-gray-300">Bairro</label>
                                        <input
                                            type="text"
                                            name="novo_endereco[bairro]"
                                            id="novo_endereco_bairro"
                                            value="{{ old('novo_endereco.bairro') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                        @error('novo_endereco.bairro')
                                            <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="novo_endereco_cidade" class="block mb-1 text-gray-700 dark:text-gray-300">Cidade</label>
                                        <input
                                            type="text"
                                            name="novo_endereco[cidade]"
                                            id="novo_endereco_cidade"
                                            value="{{ old('novo_endereco.cidade') }}"
                                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        >
                                        @error('novo_endereco.cidade')
                                            <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <a
                                href="{{ route('compras.index') }}"
                                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Voltar
                            </a>

                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition"
                            >
                                Confirmar pedido
                            </button>
                        </div>
                    </form>
                </div>

                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100 mb-4">
                            Resumo do pedido
                        </h3>

                        <div class="space-y-4">
                            @foreach ($itensSelecionados as $item)
                                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 last:border-b-0 last:pb-0">
                                    <div class="font-medium text-gray-800 dark:text-gray-100">
                                        {{ $item['produto']->nome }}
                                    </div>

                                    <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                        Quantidade: {{ $item['quantidade'] }}
                                    </div>

                                    <div class="text-sm text-gray-600 dark:text-gray-400">
                                        Unitário: R$ {{ number_format($item['preco_unitario'], 2, ',', '.') }}
                                    </div>

                                    <div class="font-semibold text-gray-900 dark:text-gray-100 mt-1">
                                        Subtotal: R$ {{ number_format($item['subtotal'], 2, ',', '.') }}
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700 flex items-center justify-between">
                            <span class="text-gray-600 dark:text-gray-400">Total</span>
                            <span class="text-xl font-bold text-gray-900 dark:text-gray-100">
                                R$ {{ number_format($total, 2, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>