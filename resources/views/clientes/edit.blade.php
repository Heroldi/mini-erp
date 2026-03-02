<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar cliente
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('clientes.update', $cliente) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label>Nome</label>
                        <input type="text" name="nome" value="{{ old('nome', $cliente->nome) }}" class="w-full border rounded px-3 py-2">
                        @error('nome') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label>Email</label>
                        <input type="text" name="email" value="{{ old('email', $cliente->email) }}" class="w-full border rounded px-3 py-2">
                        @error('email') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label>Telefone</label>
                        <input type="text" name="telefone" value="{{ old('telefone', $cliente->telefone) }}" class="w-full border rounded px-3 py-2">
                        @error('telefone') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Atualizar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>