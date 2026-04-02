<x-app-layout>
    @php
        $enderecosData = $enderecos->getCollection()
            ->mapWithKeys(function ($endereco) {
                return [
                    $endereco->id => [
                        'id' => $endereco->id,
                        'cep' => $endereco->cep,
                        'rua' => $endereco->rua,
                        'numero' => $endereco->numero,
                        'complemento' => $endereco->complemento ?? '',
                        'bairro' => $endereco->bairro,
                        'cidade' => $endereco->cidade,
                        'uf' => $endereco->uf,
                        'ativo' => $endereco->ativo ? '1' : '0',
                        'is_principal' => $endereco->is_principal ? '1' : '0',
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
        x-data="{
            createOpen: {{ $errors->any() && old('form_mode') === 'create' ? 'true' : 'false' }},
            editOpen: {{ $errors->any() && old('form_mode') === 'edit' ? 'true' : 'false' }},
            editingId: {{ old('form_mode') === 'edit' && old('editing_endereco_id') ? (int) old('editing_endereco_id') : 'null' }},

            enderecos: @js($enderecosData),

            createForm: {
                cep: @js(old('form_mode') === 'create' ? old('cep', '') : ''),
                rua: @js(old('form_mode') === 'create' ? old('rua', '') : ''),
                numero: @js(old('form_mode') === 'create' ? old('numero', '') : ''),
                complemento: @js(old('form_mode') === 'create' ? old('complemento', '') : ''),
                bairro: @js(old('form_mode') === 'create' ? old('bairro', '') : ''),
                cidade: @js(old('form_mode') === 'create' ? old('cidade', '') : ''),
                uf: @js(old('form_mode') === 'create' ? old('uf', '') : ''),
                ativo: @js(old('form_mode') === 'create' ? old('ativo', '1') : '1'),
                is_principal: @js(old('form_mode') === 'create' ? old('is_principal', '0') : '0'),
            },

            editForm: {
                cep: @js(old('form_mode') === 'edit' ? old('cep', '') : ''),
                rua: @js(old('form_mode') === 'edit' ? old('rua', '') : ''),
                numero: @js(old('form_mode') === 'edit' ? old('numero', '') : ''),
                complemento: @js(old('form_mode') === 'edit' ? old('complemento', '') : ''),
                bairro: @js(old('form_mode') === 'edit' ? old('bairro', '') : ''),
                cidade: @js(old('form_mode') === 'edit' ? old('cidade', '') : ''),
                uf: @js(old('form_mode') === 'edit' ? old('uf', '') : ''),
                ativo: @js(old('form_mode') === 'edit' ? old('ativo', '1') : '1'),
                is_principal: @js(old('form_mode') === 'edit' ? old('is_principal', '0') : '0'),
            },

            abrirCreate() {
                this.createOpen = true;
            },

            fecharCreate() {
                this.createOpen = false;
            },

            abrirEdicao(id) {
                if (!this.enderecos[id]) {
                    return;
                }

                this.editingId = id;
                this.editForm = { ...this.enderecos[id] };
                this.editOpen = true;
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
        x-on:abrir-create-endereco.window="abrirCreate()"
    >
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
                        onclick="window.dispatchEvent(new CustomEvent('abrir-create-endereco'))"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                    >
                        Novo endereço
                    </button>
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
                                    @click="abrirCreate()"
                                    class="mt-4 inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-500"
                                >
                                    Cadastrar primeiro endereço
                                </button>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        @include('clientes.enderecos._create_modal')
        @include('clientes.enderecos._edit_modal')
    </div>
</x-app-layout>