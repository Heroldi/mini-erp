<x-app-layout>
    <div class="max-w-2xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-semibold text-gray-900 mb-2">
                Troca obrigatória de senha
            </h1>

            <p class="text-sm text-gray-600 mb-6">
                Sua conta foi criada pelo sistema. Antes de continuar, defina uma nova senha.
            </p>

            <form method="POST" action="{{ route('password.force.update') }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">
                        Nova senha
                    </label>
                    <input
                        id="password"
                        name="password"
                        type="password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        required
                        autofocus
                        autocomplete="new-password"
                    >
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                        Confirmar nova senha
                    </label>
                    <input
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                        required
                        autocomplete="new-password"
                    >
                </div>

                <div class="flex items-center justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                    >
                        Salvar nova senha
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>