<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                    Cliente
                </h2>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    Visualize, edite os dados e acesse ações relacionadas a este cliente.
                </p>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a
                    href="{{ route('clientes.index') }}"
                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Voltar para clientes
                </a>

                <a
                    href="{{ route('clientes.enderecos.index', $usuario) }}"
                    class="inline-flex items-center rounded-md border border-indigo-300 bg-white px-4 py-2 text-sm font-semibold text-indigo-700 shadow-sm hover:bg-indigo-50 transition dark:border-indigo-700 dark:bg-gray-800 dark:text-indigo-300 dark:hover:bg-indigo-900/30"
                >
                    Gerenciar endereços
                </a>

                <a
                    href="{{ route('clientes.pedidos.index', $usuario) }}"
                    class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition"
                >
                    Gerenciar pedidos
                </a>
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

            @if (session('generated_password'))
                <div class="rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
                    <p class="font-semibold">Senha provisória gerada:</p>
                    <p class="mt-1">
                        Email: <span class="font-medium">{{ session('generated_email') }}</span>
                    </p>
                    <p>
                        Senha: <span class="font-medium">{{ session('generated_password') }}</span>
                    </p>
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-lg border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200">
                    <p class="font-semibold">Ocorreram erros ao salvar:</p>
                    <ul class="mt-2 list-disc space-y-1 pl-5">
                        @foreach ($errors->all() as $erro)
                            <li>{{ $erro }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="lg:col-span-2">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Dados do cliente
                            </h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                                Atualize as informações principais do cadastro.
                            </p>
                        </div>

                        <form method="POST" action="{{ route('clientes.update', $usuario) }}" class="space-y-6 px-6 py-6">
                            @csrf
                            @method('PATCH')

                            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                                <div class="md:col-span-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Nome
                                    </label>
                                    <input
                                        id="name"
                                        name="name"
                                        type="text"
                                        value="{{ old('name', $usuario->name) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        required
                                    >
                                    @error('name')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Email
                                    </label>
                                    <input
                                        id="email"
                                        type="email"
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
                                        id="cpf"
                                        name="cpf"
                                        type="text"
                                        value="{{ old('cpf', $usuario->cpf) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 {{ $podeEditarCpf ? '' : 'bg-gray-100 dark:bg-gray-700' }}"
                                        {{ $podeEditarCpf ? 'required' : 'disabled' }}
                                    >
                                    @if (! $podeEditarCpf)
                                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                            Somente admin pode alterar CPF.
                                        </p>
                                    @endif
                                    @error('cpf')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="telefone" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Telefone
                                    </label>
                                    <input
                                        id="telefone"
                                        name="telefone"
                                        type="text"
                                        value="{{ old('telefone', $usuario->telefone) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        required
                                    >
                                    @error('telefone')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="data_nascimento" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Data de nascimento
                                    </label>
                                    <input
                                        id="data_nascimento"
                                        name="data_nascimento"
                                        type="date"
                                        value="{{ old('data_nascimento', optional($usuario->data_nascimento)->format('Y-m-d')) }}"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                        required
                                    >
                                    @error('data_nascimento')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div>
                                    <label for="ativo" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                        Status do cliente
                                    </label>

                                    @if ($podeEditarAtivo)
                                        <select
                                            id="ativo"
                                            name="ativo"
                                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                            required
                                        >
                                            <option value="1" @selected(old('ativo', (int) $usuario->ativo) == 1)>Ativo</option>
                                            <option value="0" @selected(old('ativo', (int) $usuario->ativo) == 0)>Inativo</option>
                                        </select>
                                    @else
                                        <input
                                            type="text"
                                            value="{{ $usuario->ativo ? 'Ativo' : 'Inativo' }}"
                                            class="mt-1 block w-full rounded-md border-gray-300 bg-gray-100 shadow-sm dark:border-gray-700 dark:bg-gray-700 dark:text-gray-200"
                                            disabled
                                        >
                                    @endif

                                    @error('ativo')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                            <div class="flex flex-wrap items-center justify-end gap-3 border-t border-gray-200 pt-6 dark:border-gray-700">
                                <a
                                    href="{{ route('clientes.index') }}"
                                    class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition dark:border-gray-700 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                                >
                                    Cancelar
                                </a>

                                <button
                                    type="submit"
                                    class="inline-flex items-center rounded-md bg-gray-800 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 transition dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                                >
                                    Salvar alterações
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div class="border-b border-gray-200 px-6 py-5 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                Resumo do cliente
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

                             <div>
                                <p class="text-gray-500 dark:text-gray-400">Telefone</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $usuario->telefone }}</p>
                            </div>

                             <div>
                                <p class="text-gray-500 dark:text-gray-400">Data de Nascimento</p>
                                <p class="font-medium text-gray-900 dark:text-gray-100">{{ optional($usuario->data_nascimento)->format('Y-m-d') }}</p>
                                
                            </div>

                            <div>
                                <p class="text-gray-500 dark:text-gray-400">Status</p>
                                @if ($usuario->ativo)
                                    <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>