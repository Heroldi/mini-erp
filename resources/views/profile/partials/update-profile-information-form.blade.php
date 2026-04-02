<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Meus dados
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Atualize os dados permitidos da sua conta.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="name" value="Nome" />
            <x-text-input
                id="name"
                name="name"
                type="text"
                class="mt-1 block w-full"
                :value="old('name', $user->name)"
                required
                autofocus
                autocomplete="name"
            />
            <x-input-error class="mt-2" :messages="$errors->get('name')" />
        </div>

        <div>
            <x-input-label for="email" value="E-mail" />
            <x-text-input
                id="email"
                name="email"
                type="email"
                class="mt-1 block w-full bg-gray-100 dark:bg-gray-800"
                :value="$user->email"
                disabled
                readonly
            />
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                O e-mail não pode ser alterado.
            </p>
        </div>

        <div>
            <x-input-label for="cpf" value="CPF" />
            <x-text-input
                id="cpf"
                name="cpf"
                type="text"
                class="mt-1 block w-full bg-gray-100 dark:bg-gray-800"
                :value="$user->cpf"
                disabled
                readonly
            />
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                O CPF não pode ser alterado.
            </p>
        </div>

        <div>
            <x-input-label for="telefone" value="Telefone" />
            <x-text-input
                id="telefone"
                name="telefone"
                type="text"
                class="mt-1 block w-full"
                :value="old('telefone', $user->telefone)"
                required
                autocomplete="tel"
            />
            <x-input-error class="mt-2" :messages="$errors->get('telefone')" />
        </div>

        <div>
            <x-input-label for="data_nascimento" value="Data de nascimento" />
            <x-text-input
                id="data_nascimento"
                name="data_nascimento"
                type="date"
                class="mt-1 block w-full"
                :value="old('data_nascimento', optional($user->data_nascimento)->format('Y-m-d'))"
                required
            />
            <x-input-error class="mt-2" :messages="$errors->get('data_nascimento')" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>Salvar</x-primary-button>

            @if (session('success'))
                <p
                    x-data="{ show: true }"
                    x-show="show"
                    x-transition
                    x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-gray-600 dark:text-gray-400"
                >{{ session('success') }}</p>
            @endif
        </div>
    </form>
</section>