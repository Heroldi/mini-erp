<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Meus endereços
        </h2>
    </x-slot>

    @php
        $camposEndereco = ['cep', 'rua', 'numero', 'complemento', 'bairro', 'cidade', 'uf'];

        $temErrosEndereco = $errors->hasAny($camposEndereco);

        $abrirModalCriacao = $temErrosEndereco && old('form_context') === 'create';
        $enderecoEditarId = $temErrosEndereco && old('form_context') === 'edit'
            ? (int) old('endereco_id')
            : null;
    @endphp

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>

    <div
        class="py-6"
        x-data="{
            createModalOpen: @js($abrirModalCriacao),
            editModalOpen: @js($enderecoEditarId),
        }"
    >
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (session('success'))
                <div class="rounded-lg border border-green-200 bg-green-50 px-4 py-3 text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->has('endereco'))
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
                    {{ $errors->first('endereco') }}
                </div>
            @endif

            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                <div class="flex items-center justify-between gap-4 mb-4">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Seus endereços</h3>
                        <p class="text-sm text-gray-600 dark:text-gray-400">
                            Aqui você pode cadastrar, editar, ativar, inativar e definir o endereço principal.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="createModalOpen = true"
                        class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition"
                    >
                        Cadastrar novo endereço
                    </button>
                </div>

                @if ($user->enderecos->isEmpty())
                    <div class="border border-dashed border-gray-300 dark:border-gray-700 rounded p-6 text-center text-gray-600 dark:text-gray-400">
                        Você ainda não possui endereços cadastrados.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach ($user->enderecos as $endereco)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 bg-white dark:bg-gray-800">
                                <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-4">
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
                                            @else
                                                <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded dark:bg-red-900/40 dark:text-red-300">
                                                    Inativo
                                                </span>
                                            @endif
                                        </div>

                                        <div class="text-sm text-gray-800 dark:text-gray-200 space-y-1">
                                            <p><strong>Rua:</strong> {{ $endereco->rua }}, {{ $endereco->numero }}</p>

                                            @if ($endereco->complemento)
                                                <p><strong>Complemento:</strong> {{ $endereco->complemento }}</p>
                                            @endif

                                            <p><strong>Bairro:</strong> {{ $endereco->bairro }}</p>
                                            <p><strong>Cidade/UF:</strong> {{ $endereco->cidade }}/{{ $endereco->uf }}</p>
                                            <p><strong>CEP:</strong> {{ $endereco->cep }}</p>
                                        </div>
                                    </div>

                                    <div class="flex flex-col gap-2 md:w-52">
                                        <button
                                            type="button"
                                            @click="editModalOpen = {{ $endereco->id }}"
                                            class="inline-flex items-center justify-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 transition dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                                        >
                                            Editar
                                        </button>

                                        @if (! $endereco->is_principal && $endereco->ativo)
                                            <form
                                                action="{{ route('enderecos.principal', $endereco) }}"
                                                method="POST"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="w-full inline-flex items-center justify-center rounded-md border border-indigo-300 bg-white px-4 py-2 text-sm font-semibold text-indigo-700 shadow-sm hover:bg-indigo-50 transition dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/30"
                                                >
                                                    Definir como principal
                                                </button>
                                            </form>
                                        @endif

                                        <form
                                            action="{{ route('enderecos.toggle-ativo', $endereco) }}"
                                            method="POST"
                                        >
                                            @csrf
                                            @method('PATCH')

                                            <button
                                                type="submit"
                                                class="w-full inline-flex items-center justify-center rounded-md px-4 py-2 text-sm font-semibold transition {{ $endereco->ativo ? 'bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-300 dark:hover:bg-red-900/50' : 'bg-green-100 text-green-800 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-300 dark:hover:bg-green-900/50' }}"
                                            >
                                                {{ $endereco->ativo ? 'Inativar' : 'Ativar' }}
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div
                                x-show="editModalOpen === {{ $endereco->id }}"
                                x-cloak
                                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
                            >
                                <div
                                    @click.away="editModalOpen = null"
                                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700"
                                >
                                    <div class="flex items-center justify-between mb-4">
                                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Editar endereço</h3>

                                        <button
                                            type="button"
                                            @click="editModalOpen = null"
                                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-xl leading-none"
                                        >
                                            &times;
                                        </button>
                                    </div>

                                    <form
                                        action="{{ route('enderecos.update', $endereco) }}"
                                        method="POST"
                                    >
                                        @csrf
                                        @method('PUT')

                                        <input type="hidden" name="form_context" value="edit">
                                        <input type="hidden" name="endereco_id" value="{{ $endereco->id }}">

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            <div>
                                                <label for="cep_edit_{{ $endereco->id }}" class="block mb-1 text-gray-700 dark:text-gray-300">CEP</label>
                                                <input
                                                    type="text"
                                                    name="cep"
                                                    id="cep_edit_{{ $endereco->id }}"
                                                    value="{{ old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id ? old('cep') : $endereco->cep }}"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                >
                                                @if (old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id)
                                                    @error('cep')
                                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </div>

                                            <div>
                                                <label for="uf_edit_{{ $endereco->id }}" class="block mb-1 text-gray-700 dark:text-gray-300">UF</label>
                                                <input
                                                    type="text"
                                                    name="uf"
                                                    id="uf_edit_{{ $endereco->id }}"
                                                    value="{{ old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id ? old('uf') : $endereco->uf }}"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                >
                                                @if (old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id)
                                                    @error('uf')
                                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </div>

                                            <div class="md:col-span-2">
                                                <label for="rua_edit_{{ $endereco->id }}" class="block mb-1 text-gray-700 dark:text-gray-300">Rua</label>
                                                <input
                                                    type="text"
                                                    name="rua"
                                                    id="rua_edit_{{ $endereco->id }}"
                                                    value="{{ old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id ? old('rua') : $endereco->rua }}"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                >
                                                @if (old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id)
                                                    @error('rua')
                                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </div>

                                            <div>
                                                <label for="numero_edit_{{ $endereco->id }}" class="block mb-1 text-gray-700 dark:text-gray-300">Número</label>
                                                <input
                                                    type="text"
                                                    name="numero"
                                                    id="numero_edit_{{ $endereco->id }}"
                                                    value="{{ old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id ? old('numero') : $endereco->numero }}"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                >
                                                @if (old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id)
                                                    @error('numero')
                                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </div>

                                            <div>
                                                <label for="complemento_edit_{{ $endereco->id }}" class="block mb-1 text-gray-700 dark:text-gray-300">Complemento</label>
                                                <input
                                                    type="text"
                                                    name="complemento"
                                                    id="complemento_edit_{{ $endereco->id }}"
                                                    value="{{ old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id ? old('complemento') : $endereco->complemento }}"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                >
                                                @if (old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id)
                                                    @error('complemento')
                                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </div>

                                            <div>
                                                <label for="bairro_edit_{{ $endereco->id }}" class="block mb-1 text-gray-700 dark:text-gray-300">Bairro</label>
                                                <input
                                                    type="text"
                                                    name="bairro"
                                                    id="bairro_edit_{{ $endereco->id }}"
                                                    value="{{ old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id ? old('bairro') : $endereco->bairro }}"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                >
                                                @if (old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id)
                                                    @error('bairro')
                                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </div>

                                            <div>
                                                <label for="cidade_edit_{{ $endereco->id }}" class="block mb-1 text-gray-700 dark:text-gray-300">Cidade</label>
                                                <input
                                                    type="text"
                                                    name="cidade"
                                                    id="cidade_edit_{{ $endereco->id }}"
                                                    value="{{ old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id ? old('cidade') : $endereco->cidade }}"
                                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                                >
                                                @if (old('form_context') === 'edit' && (int) old('endereco_id') === $endereco->id)
                                                    @error('cidade')
                                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                                    @enderror
                                                @endif
                                            </div>
                                        </div>

                                        <div class="mt-6 flex items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                @click="editModalOpen = null"
                                                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700"
                                            >
                                                Cancelar
                                            </button>

                                            <button
                                                type="submit"
                                                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition"
                                            >
                                                Salvar alterações
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div
                x-show="createModalOpen"
                x-cloak
                class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 px-4"
            >
                <div
                    @click.away="createModalOpen = false"
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-lg w-full max-w-2xl p-6 max-h-[90vh] overflow-y-auto border border-gray-200 dark:border-gray-700"
                >
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-100">Cadastrar novo endereço</h3>

                        <button
                            type="button"
                            @click="createModalOpen = false"
                            class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 text-xl leading-none"
                        >
                            &times;
                        </button>
                    </div>

                    <form action="{{ route('enderecos.store') }}" method="POST">
                        @csrf

                        <input type="hidden" name="form_context" value="create">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="cep" class="block mb-1 text-gray-700 dark:text-gray-300">CEP</label>
                                <input
                                    type="text"
                                    name="cep"
                                    id="cep"
                                    value="{{ old('form_context') === 'create' ? old('cep') : '' }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                >
                                @if (old('form_context') === 'create')
                                    @error('cep')
                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div>
                                <label for="uf" class="block mb-1 text-gray-700 dark:text-gray-300">UF</label>
                                <input
                                    type="text"
                                    name="uf"
                                    id="uf"
                                    value="{{ old('form_context') === 'create' ? old('uf') : '' }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                >
                                @if (old('form_context') === 'create')
                                    @error('uf')
                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div class="md:col-span-2">
                                <label for="rua" class="block mb-1 text-gray-700 dark:text-gray-300">Rua</label>
                                <input
                                    type="text"
                                    name="rua"
                                    id="rua"
                                    value="{{ old('form_context') === 'create' ? old('rua') : '' }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                >
                                @if (old('form_context') === 'create')
                                    @error('rua')
                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div>
                                <label for="numero" class="block mb-1 text-gray-700 dark:text-gray-300">Número</label>
                                <input
                                    type="text"
                                    name="numero"
                                    id="numero"
                                    value="{{ old('form_context') === 'create' ? old('numero') : '' }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                >
                                @if (old('form_context') === 'create')
                                    @error('numero')
                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div>
                                <label for="complemento" class="block mb-1 text-gray-700 dark:text-gray-300">Complemento</label>
                                <input
                                    type="text"
                                    name="complemento"
                                    id="complemento"
                                    value="{{ old('form_context') === 'create' ? old('complemento') : '' }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                >
                                @if (old('form_context') === 'create')
                                    @error('complemento')
                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div>
                                <label for="bairro" class="block mb-1 text-gray-700 dark:text-gray-300">Bairro</label>
                                <input
                                    type="text"
                                    name="bairro"
                                    id="bairro"
                                    value="{{ old('form_context') === 'create' ? old('bairro') : '' }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                >
                                @if (old('form_context') === 'create')
                                    @error('bairro')
                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>

                            <div>
                                <label for="cidade" class="block mb-1 text-gray-700 dark:text-gray-300">Cidade</label>
                                <input
                                    type="text"
                                    name="cidade"
                                    id="cidade"
                                    value="{{ old('form_context') === 'create' ? old('cidade') : '' }}"
                                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                >
                                @if (old('form_context') === 'create')
                                    @error('cidade')
                                        <div class="text-red-600 dark:text-red-400 text-sm mt-1">{{ $message }}</div>
                                    @enderror
                                @endif
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end gap-2">
                            <button
                                type="button"
                                @click="createModalOpen = false"
                                class="inline-flex items-center rounded-md border border-gray-300 px-4 py-2 text-sm font-semibold text-gray-700 bg-white hover:bg-gray-50 transition dark:border-gray-600 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-700"
                            >
                                Cancelar
                            </button>

                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition"
                            >
                                Salvar endereço
                            </button>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>