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
                        x-model="createForm.cep"
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
                        x-model="createForm.rua"
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
                        x-model="createForm.numero"
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
                        x-model="createForm.complemento"
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
                        x-model="createForm.bairro"
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
                        x-model="createForm.cidade"
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
                        x-model="createForm.uf"
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
                        x-model="createForm.ativo"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        required
                    >
                        <option value="1">Ativo</option>
                        <option value="0">Inativo</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="create_is_principal" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                        Principal
                    </label>
                    <select
                        id="create_is_principal"
                        name="is_principal"
                        x-model="createForm.is_principal"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                        required
                    >
                        <option value="0">Não</option>
                        <option value="1">Sim</option>
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