<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-white leading-tight">
            Clientes
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="mb-4 p-3 rounded bg-green-100 text-green-800">
                        {{ session('success') }}
                    </div>
                @endif

                <a href="{{ route('clientes.create') }}" class="inline-block mb-4 px-4 py-2 bg-indigo-600 text-white rounded">
                    Novo cliente
                </a>

                <table class="min-w-full border">
                    <thead>
                        <tr>
                            <th class="border px-3 py-2 text-left">Nome</th>
                            <th class="border px-3 py-2 text-left">Email</th>
                            <th class="border px-3 py-2 text-left">Telefone</th>
                            <th class="border px-3 py-2 text-left">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($clientes as $cliente)
                            <tr>
                                <td class="border px-3 py-2">{{ $cliente->nome }}</td>
                                <td class="border px-3 py-2">{{ $cliente->email }}</td>
                                <td class="border px-3 py-2">{{ $cliente->telefone }}</td>
                                <td class="border px-3 py-2">
                                    <a href="{{ route('clientes.edit', $cliente) }}" class="text-blue-600">Editar</a>

                                    <form action="{{ route('clientes.destroy', $cliente) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 ml-2">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="border px-3 py-2 text-center">
                                    Nenhum cliente cadastrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</x-app-layout>