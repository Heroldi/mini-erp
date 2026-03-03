<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Novo produto
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6">
                <form action="{{ route('produtos.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label>Nome</label>
                        <input type="text" name="nome" value="{{ old('nome') }}" class="w-full border rounded px-3 py-2">
                        @error('nome') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label>Descrição</label>
                        <input type="text" name="descricao" value="{{ old('descricao') }}" class="w-full border rounded px-3 py-2">
                        @error('descricao') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <div class="mb-4">
                        <label>Preço</label>
                        <input type="number" step="any" name="preco" value="{{ old('preco') }}" class="w-full border rounded px-3 py-2">
                        @error('preco') <div class="text-red-600 text-sm">{{ $message }}</div> @enderror
                    </div>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded">
                        Salvar
                    </button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>