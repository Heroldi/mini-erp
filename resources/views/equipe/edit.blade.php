<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Editar membro da equipe
            </h2>

            <a
                href="{{ route('equipe.index') }}"
                class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
            >
                Voltar
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
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
                <form method="POST" action="{{ route('equipe.update', $usuario) }}" class="p-6 space-y-6">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Nome
                            </label>
                            <input
                                type="text"
                                name="name"
                                id="name"
                                value="{{ old('name', $usuario->name) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Email
                            </label>
                            <input
                                type="email"
                                id="email"
                                value="{{ $usuario->email }}"
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                disabled
                            >
                            <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                O email é imutável.
                            </p>
                        </div>

                        <div>
                            <label for="cpf" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                CPF
                            </label>
                            <input
                                type="text"
                                name="cpf"
                                id="cpf"
                                value="{{ old('cpf', $usuario->cpf) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="telefone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Telefone
                            </label>
                            <input
                                type="text"
                                name="telefone"
                                id="telefone"
                                value="{{ old('telefone', $usuario->telefone) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="data_nascimento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Data de nascimento
                            </label>
                            <input
                                type="date"
                                name="data_nascimento"
                                id="data_nascimento"
                                value="{{ old('data_nascimento', optional($usuario->data_nascimento)->format('Y-m-d')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                required
                            >
                        </div>

                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Função
                            </label>

                            @if ($podeEditarRole)
                                <select
                                    name="role_id"
                                    id="role_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    required
                                >
                                    @foreach ($rolesDisponiveis as $role)
                                        <option value="{{ $role->id }}" @selected(old('role_id', $usuario->role_id) == $role->id)>
                                            {{ ucfirst($role->nome) }}
                                        </option>
                                    @endforeach
                                </select>
                            @else
                                <input
                                    type="hidden"
                                    name="role_id"
                                    value="{{ $usuario->role_id }}"
                                >

                                <input
                                    type="text"
                                    value="{{ ucfirst($usuario->role?->nome) }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                    disabled
                                >
                            @endif
                        </div>

                        <div>
                            <label for="ativo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                Status
                            </label>

                            @if ($podeEditarAtivo)
                                <select
                                    name="ativo"
                                    id="ativo"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                    required
                                >
                                    <option value="1" @selected(old('ativo', (int) $usuario->ativo) == 1)>Ativo</option>
                                    <option value="0" @selected(old('ativo', (int) $usuario->ativo) == 0)>Inativo</option>
                                </select>
                            @else
                                <input
                                    type="hidden"
                                    name="ativo"
                                    value="{{ (int) $usuario->ativo }}"
                                >

                                <input
                                    type="text"
                                    value="{{ $usuario->ativo ? 'Ativo' : 'Inativo' }}"
                                    class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                    disabled
                                >

                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                    Você não pode inativar seu próprio usuário.
                                </p>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                        <a
                            href="{{ route('equipe.index') }}"
                            class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                        >
                            Cancelar
                        </a>

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
    </div>
</x-app-layout>