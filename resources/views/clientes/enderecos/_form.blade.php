<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Endereços do cliente
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Gerencie os endereços de {{ $usuario->name }}.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('clientes.edit', $usuario) }}"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Voltar para cliente
                </a>

                <button
                    type="button"
                    @click="createOpen = true"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                >
                    Novo endereço
                </button>
            </div>
        </div>
    </x-slot>

    @php
        $enderecosData = $enderecos->getCollection()
            ->mapWithKeys(function ($endereco) {
                return [
                    $endereco->id => [
                        'id' => $endereco->id,
                        'cep' => $endereco->cep,
                        'rua' => $endereco->rua,
                        'numero' => $endereco->numero,
                        'complemento' => $endereco->complemento,
                        'bairro' => $endereco->bairro,
                        'cidade' => $endereco->cidade,
                        'uf' => $endereco->uf,
                        'ativo' => (int) $endereco->ativo,
                        'is_principal' => (int) $endereco->is_principal,
                    ],
                ];
            })
            ->toArray();
    @endphp

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div
        class="py-8"
        x-data="{
            createOpen: {{ $errors->any() && old('form_mode') === 'create' ? 'true' : 'false' }},
            editOpen: {{ $errors->any() && old('form_mode') === 'edit' ? 'true' : 'false' }},
            editingId: {{ old('editing_endereco_id') ? (int) old('editing_endereco_id') : 'null' }},
            enderecos: @js($enderecosData),

            get enderecoAtual() {
                if (!this.editingId || !this.enderecos[this.editingId]) {
                    return {
                        id: null,
                        cep: '',
                        rua: '',
                        numero: '',
                        complemento: '',
                        bairro: '',
                        cidade: '',
                        uf: '',
                        ativo: 1,
                        is_principal: 0,
                    };
                }

                return this.enderecos[this.editingId];
            },

            abrirEdicao(id) {
                this.editingId = id;
                this.editOpen = true;
            },

            fecharCreate() {
                this.createOpen = false;
            },

            fecharEdit() {
                this.editOpen = false;
                this.editingId = null;
            },

            actionUpdate() {
                if (!this.editingId) {
                    return '';
                }

                return '{{ url('clientes/' . $usuario->id . '/enderecos') }}/' + this.editingId;
            }
        }"
    >
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
                        Lista de endereços
                    </h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Cadastre, edite, defina o principal e ative ou inative os endereços deste cliente.
                    </p>
                </div>

                <div class="p-6">
                    @if ($enderecos->count())
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-900/50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Endereço
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            CEP
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                            Principal
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
                                    @foreach ($enderecos as $endereco)
                                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                <div class="font-medium text-gray-900 dark:text-gray-100">
                                                    {{ $endereco->rua }}, {{ $endereco->numero }}
                                                </div>

                                                <div class="text-gray-500 dark:text-gray-400">
                                                    {{ $endereco->bairro }} - {{ $endereco->cidade }}/{{ $endereco->uf }}
                                                </div>

                                                @if ($endereco->complemento)
                                                    <div class="text-gray-500 dark:text-gray-400">
                                                        Complemento: {{ $endereco->complemento }}
                                                    </div>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                                {{ $endereco->cep }}
                                            </td>

                                            <td class="px-4 py-4 text-sm">
                                                @if ($endereco->is_principal)
                                                    <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
                                                        Principal
                                                    </span>
                                                @else
                                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-700 dark:bg-gray-700 dark:text-gray-300">
                                                        Não
                                                    </span>
                                                @endif
                                            </td>

                                            <td class="px-4 py-4 text-sm">
                                                @if ($endereco->ativo)
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
                                                <div class="flex flex-wrap justify-end gap-2">
                                                    <button
                                                        type="button"
                                                        @click="abrirEdicao({{ $endereco->id }})"
                                                        class="inline-flex items-center rounded-md border border-indigo-300 px-3 py-2 text-xs font-semibold text-indigo-700 transition hover:bg-indigo-50 dark:border-indigo-700 dark:text-indigo-300 dark:hover:bg-indigo-900/30"
                                                    >
                                                        Editar
                                                    </button>

                                                    @if (! $endereco->is_principal && $endereco->ativo)
                                                        <form method="POST" action="{{ route('clientes.enderecos.set-principal', [$usuario, $endereco]) }}">
                                                            @csrf
                                                            @method('PATCH')

                                                            <button
                                                                type="submit"
                                                                class="inline-flex items-center rounded-md border border-amber-300 px-3 py-2 text-xs font-semibold text-amber-700 transition hover:bg-amber-50 dark:border-amber-700 dark:text-amber-300 dark:hover:bg-amber-900/30"
                                                            >
                                                                Definir principal
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form method="POST" action="{{ route('clientes.enderecos.toggle-ativo', [$usuario, $endereco]) }}">
                                                        @csrf
                                                        @method('PATCH')

                                                        <button
                                                            type="submit"
                                                            class="inline-flex items-center rounded-md px-3 py-2 text-xs font-semibold text-white transition {{ $endereco->ativo ? 'bg-red-600 hover:bg-red-500' : 'bg-green-600 hover:bg-green-500' }}"
                                                        >
                                                            {{ $endereco->ativo ? 'Inativar' : 'Ativar' }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        @if ($enderecos->hasPages())
                            <div class="mt-6">
                                {{ $enderecos->links() }}
                            </div>
                        @endif
                    @else
                        <div class="rounded-lg border border-dashed border-gray-300 px-6 py-10 text-center dark:border-gray-700">
                            <p class="text-sm text-gray-500 dark:text-gray-400">
                                Este cliente ainda não possui endereços cadastrados.
                            </p>

                            <button
                                type="button"
                                @click="createOpen = true"
                                class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                            >
                                Cadastrar primeiro endereço
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div
            x-cloak
            x-show="createOpen"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
        >
            <div
                @click.outside="fecharCreate()"
                class="w-full max-w-3xl rounded-xl bg-white shadow-xl dark:bg-gray-800"
            >
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Novo endereço
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Cadastre um novo endereço para {{ $usuario->name }}.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="fecharCreate()"
                        class="text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-200"
                    >
                        ✕
                    </button>
                </div>

                <form method="POST" action="{{ route('clientes.enderecos.store', $usuario) }}" class="space-y-6 px-6 py-6">
                    @csrf

                    <input type="hidden" name="form_mode" value="create">

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="create_cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                CEP
                            </label>
                            <input
                                id="create_cep"
                                name="cep"
                                type="text"
                                value="{{ old('form_mode') === 'create' ? old('cep') : '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="create_rua" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Rua
                            </label>
                            <input
                                id="create_rua"
                                name="rua"
                                type="text"
                                value="{{ old('form_mode') === 'create' ? old('rua') : '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="create_numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Número
                            </label>
                            <input
                                id="create_numero"
                                name="numero"
                                type="text"
                                value="{{ old('form_mode') === 'create' ? old('numero') : '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="create_complemento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Complemento
                            </label>
                            <input
                                id="create_complemento"
                                name="complemento"
                                type="text"
                                value="{{ old('form_mode') === 'create' ? old('complemento') : '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            >
                        </div>

                        <div>
                            <label for="create_bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Bairro
                            </label>
                            <input
                                id="create_bairro"
                                name="bairro"
                                type="text"
                                value="{{ old('form_mode') === 'create' ? old('bairro') : '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="create_cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Cidade
                            </label>
                            <input
                                id="create_cidade"
                                name="cidade"
                                type="text"
                                value="{{ old('form_mode') === 'create' ? old('cidade') : '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="create_uf" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                UF
                            </label>
                            <input
                                id="create_uf"
                                name="uf"
                                type="text"
                                maxlength="2"
                                value="{{ old('form_mode') === 'create' ? old('uf') : '' }}"
                                class="mt-1 block w-full rounded-md border-gray-300 uppercase shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="create_ativo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Status
                            </label>
                            <select
                                id="create_ativo"
                                name="ativo"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                                <option value="1" @selected(old('form_mode') === 'create' ? old('ativo', '1') == '1' : true)>Ativo</option>
                                <option value="0" @selected(old('form_mode') === 'create' ? old('ativo') == '0' : false)>Inativo</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="create_is_principal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Principal
                            </label>
                            <select
                                id="create_is_principal"
                                name="is_principal"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                                <option value="0" @selected(old('form_mode') === 'create' ? old('is_principal', '0') == '0' : true)>Não</option>
                                <option value="1" @selected(old('form_mode') === 'create' ? old('is_principal') == '1' : false)>Sim</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <button
                            type="button"
                            @click="fecharCreate()"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                        >
                            Salvar endereço
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div
            x-cloak
            x-show="editOpen"
            x-transition.opacity
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
        >
            <div
                @click.outside="fecharEdit()"
                class="w-full max-w-3xl rounded-xl bg-white shadow-xl dark:bg-gray-800"
            >
                <div class="flex items-center justify-between border-b border-gray-200 px-6 py-4 dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                            Editar endereço
                        </h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Atualize os dados do endereço selecionado.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="fecharEdit()"
                        class="text-gray-400 transition hover:text-gray-600 dark:hover:text-gray-200"
                    >
                        ✕
                    </button>
                </div>

                <form :action="actionUpdate()" method="POST" class="space-y-6 px-6 py-6">
                    @csrf
                    @method('PATCH')

                    <input type="hidden" name="form_mode" value="edit">
                    <input type="hidden" name="editing_endereco_id" :value="editingId">

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div>
                            <label for="edit_cep" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                CEP
                            </label>
                            <input
                                id="edit_cep"
                                name="cep"
                                type="text"
                                :value="old('form_mode') === 'edit' ? '{{ old('cep') }}' : enderecoAtual.cep"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="edit_rua" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Rua
                            </label>
                            <input
                                id="edit_rua"
                                name="rua"
                                type="text"
                                :value="old('form_mode') === 'edit' ? '{{ old('rua') }}' : enderecoAtual.rua"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="edit_numero" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Número
                            </label>
                            <input
                                id="edit_numero"
                                name="numero"
                                type="text"
                                :value="old('form_mode') === 'edit' ? '{{ old('numero') }}' : enderecoAtual.numero"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="edit_complemento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Complemento
                            </label>
                            <input
                                id="edit_complemento"
                                name="complemento"
                                type="text"
                                :value="old('form_mode') === 'edit' ? '{{ old('complemento') }}' : enderecoAtual.complemento"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            >
                        </div>

                        <div>
                            <label for="edit_bairro" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Bairro
                            </label>
                            <input
                                id="edit_bairro"
                                name="bairro"
                                type="text"
                                :value="old('form_mode') === 'edit' ? '{{ old('bairro') }}' : enderecoAtual.bairro"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="edit_cidade" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Cidade
                            </label>
                            <input
                                id="edit_cidade"
                                name="cidade"
                                type="text"
                                :value="old('form_mode') === 'edit' ? '{{ old('cidade') }}' : enderecoAtual.cidade"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="edit_uf" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                UF
                            </label>
                            <input
                                id="edit_uf"
                                name="uf"
                                type="text"
                                maxlength="2"
                                :value="old('form_mode') === 'edit' ? '{{ old('uf') }}' : enderecoAtual.uf"
                                class="mt-1 block w-full rounded-md border-gray-300 uppercase shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="edit_ativo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Status
                            </label>
                            <select
                                id="edit_ativo"
                                name="ativo"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                                <option value="1" :selected="(old('form_mode') === 'edit' ? '{{ old('ativo', '1') }}' : String(enderecoAtual.ativo)) === '1'">Ativo</option>
                                <option value="0" :selected="(old('form_mode') === 'edit' ? '{{ old('ativo', '0') }}' : String(enderecoAtual.ativo)) === '0'">Inativo</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label for="edit_is_principal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Principal
                            </label>
                            <select
                                id="edit_is_principal"
                                name="is_principal"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                                <option value="0" :selected="(old('form_mode') === 'edit' ? '{{ old('is_principal', '0') }}' : String(enderecoAtual.is_principal)) === '0'">Não</option>
                                <option value="1" :selected="(old('form_mode') === 'edit' ? '{{ old('is_principal', '0') }}' : String(enderecoAtual.is_principal)) === '1'">Sim</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <button
                            type="button"
                            @click="fecharEdit()"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm transition hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                        >
                            Cancelar
                        </button>

                        <button
                            type="submit"
                            class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                        >
                            Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>