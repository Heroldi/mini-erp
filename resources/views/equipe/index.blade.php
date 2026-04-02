<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Equipe
            </h2>

            <a
                href="{{ route('equipe.create') }}"
                class="inline-flex items-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition"
            >
                Novo membro
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

            @if (session('generated_password'))
                <div class="mb-4 rounded-lg border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
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

                    <form method="GET" action="{{ route('equipe.index') }}" class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                        <div>
                            <label for="nome" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Nome
                            </label>
                            <input
                                type="text"
                                name="nome"
                                id="nome"
                                value="{{ request('nome') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                placeholder="Nome"
                            >
                        </div>

                        <div>
                            <label for="cpf" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                CPF
                            </label>
                            <input
                                type="text"
                                name="cpf"
                                id="cpf"
                                value="{{ request('cpf') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                placeholder="CPF"
                            >
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Email
                            </label>
                            <input
                                type="text"
                                name="email"
                                id="email"
                                value="{{ request('email') }}"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                                placeholder="Email"
                            >
                        </div>

                        <div>
                            <label for="role_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Função
                            </label>
                            <select
                                name="role_id"
                                id="role_id"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            >
                                <option value="">Todas</option>
                                @foreach ($rolesDisponiveis as $role)
                                    <option value="{{ $role->id }}" @selected((string) request('role_id') === (string) $role->id)>
                                        {{ ucfirst($role->nome) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label for="ativo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Status
                            </label>
                            <select
                                name="ativo"
                                id="ativo"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100"
                            >
                                <option value="">Todos</option>
                                <option value="1" @selected(request('ativo') === '1')>Ativos</option>
                                <option value="0" @selected(request('ativo') === '0')>Inativos</option>
                            </select>
                        </div>

                        <div class="md:col-span-5 flex items-end gap-2">
                            <button
                                type="submit"
                                class="inline-flex items-center rounded-md bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 transition dark:bg-gray-100 dark:text-gray-900 dark:hover:bg-gray-300"
                            >
                                Filtrar
                            </button>

                            <a
                                href="{{ route('equipe.index') }}"
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
                                        Nome
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Email
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        CPF
                                    </th>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
                                        Função
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
                                @forelse ($usuarios as $usuario)
                                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                                        <td class="px-4 py-4 text-sm">
                                            <div class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $usuario->name }}
                                            </div>
                                            <div class="text-gray-500 dark:text-gray-400">
                                                {{ $usuario->telefone }}
                                            </div>
                                        </td>

                                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $usuario->email }}
                                        </td>

                                        <td class="px-4 py-4 text-sm text-gray-700 dark:text-gray-300">
                                            {{ $usuario->cpf }}
                                        </td>

                                        <td class="px-4 py-4 text-sm">
                                            @if ($usuario->role?->nome === 'admin')
                                                <span class="inline-flex items-center rounded-full bg-indigo-100 px-3 py-1 text-xs font-semibold text-indigo-800 dark:bg-indigo-900/40 dark:text-indigo-300">
                                                    Admin
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-800 dark:bg-blue-900/40 dark:text-blue-300">
                                                    Atendente
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 text-sm">
                                            @if ($usuario->ativo)
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-3 py-1 text-xs font-semibold text-green-800 dark:bg-green-900/40 dark:text-green-300">
                                                    Ativo
                                                </span>
                                            @else
                                                <span class="inline-flex items-center rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-800 dark:bg-red-900/40 dark:text-red-300">
                                                    Inativo
                                                </span>
                                            @endif
                                        </td>

                                        <td class="px-4 py-4 text-sm text-right">
                                            <a
                                                href="{{ route('equipe.edit', $usuario) }}"
                                                class="inline-flex items-center rounded-md border border-indigo-300 px-3 py-2 text-xs font-semibold text-indigo-700 hover:bg-indigo-50 transition dark:border-indigo-700 dark:text-indigo-300 dark:hover:bg-indigo-900/30"
                                            >
                                                Editar
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-4 py-8 text-center text-sm text-gray-500 dark:text-gray-400">
                                            Nenhum membro da equipe encontrado.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if ($usuarios->hasPages())
                        <div class="mt-6">
                            {{ $usuarios->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>