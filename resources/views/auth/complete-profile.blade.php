<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Completar cadastro
        </h2>
    </x-slot>

    @php
        $enderecosOld = old('enderecos', []);

        $errosEndereco = collect($errors->toArray())
            ->filter(function ($messages, $key) {
                return str_starts_with($key, 'enderecos.');
            })
            ->flatten()
            ->unique()
            ->values();
    @endphp

    <div
        class="py-6"
        x-data='{
            enderecos: @json($enderecosOld),
            addEndereco() {
                this.enderecos.push({
                    cep: "",
                    rua: "",
                    numero: "",
                    complemento: "",
                    bairro: "",
                    cidade: "",
                    uf: ""
                });
            },
            removeEndereco(index) {
                this.enderecos.splice(index, 1);
            }
        }'
    >
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <form action="{{ route('profile.complete.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Dados pessoais
                    </h3>

                    <div class="mb-4">
                        <label for="name" class="block mb-1">Nome</label>
                        <input
                            type="text"
                            name="name"
                            id="name"
                            value="{{ old('name', $user->name) }}"
                            class="w-full border rounded px-3 py-2"
                        >
                        @error('name')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="cpf" class="block mb-1">CPF</label>
                        <input
                            type="text"
                            name="cpf"
                            id="cpf"
                            value="{{ old('cpf', $user->cpf) }}"
                            class="w-full border rounded px-3 py-2"
                        >
                        @error('cpf')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="telefone" class="block mb-1">Telefone</label>
                        <input
                            type="text"
                            name="telefone"
                            id="telefone"
                            value="{{ old('telefone', $user->telefone) }}"
                            class="w-full border rounded px-3 py-2"
                        >
                        @error('telefone')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div>
                        <label for="data_nascimento" class="block mb-1">Data de nascimento</label>
                        <input
                            type="date"
                            name="data_nascimento"
                            id="data_nascimento"
                            value="{{ old('data_nascimento', optional($user->data_nascimento)->format('Y-m-d')) }}"
                            class="w-full border rounded px-3 py-2"
                        >
                        @error('data_nascimento')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                   <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-800">
                                Novos endereços (opcional)
                            </h3>

                            <div class="mt-3 flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="addEndereco()"
                                    class="bg-gray-800 text-white px-4 py-2 rounded"
                                >
                                    Adicionar endereço
                                </button>

                                <div class="relative group">
                                    <button
                                        type="button"
                                        class="w-8 h-8 rounded-full border border-gray-300 text-gray-600 flex items-center justify-center text-sm bg-white cursor-default"
                                        aria-label="Informações sobre endereços"
                                    >
                                        &#9432;
                                    </button>

                                    <div
                                        class="absolute left-0 mt-2 w-72 rounded-lg bg-gray-900 text-white text-xs px-3 py-2 shadow-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition z-20"
                                    >
                                        Você pode cadastrar endereço agora ou deixar para depois, quando também poderá editar e gerenciar seus endereços.
                                    </div>
                                </div>
                            </div>
                        </div>
</div>

                    @if ($errosEndereco->isNotEmpty())
                        <div class="mb-4 rounded border border-red-200 bg-red-50 px-4 py-3 text-red-700">
                            <div class="font-medium mb-2">Revise os endereços adicionados:</div>
                            <ul class="list-disc pl-5 space-y-1 text-sm">
                                @foreach ($errosEndereco as $erro)
                                    <li>{{ $erro }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div x-show="enderecos.length === 0" class="text-sm text-gray-600">
                        Nenhum novo endereço adicionado nesta tela.
                    </div>

                    <div class="space-y-4">
                        <template x-for="(endereco, index) in enderecos" :key="index">
                            <div class="border rounded-lg p-4 bg-gray-50">
                                <div class="flex items-center justify-between mb-4">
                                    <h4 class="font-medium text-gray-800">
                                        Novo endereço <span x-text="index + 1"></span>
                                    </h4>

                                    <button
                                        type="button"
                                        @click="removeEndereco(index)"
                                        class="text-red-600 text-sm"
                                    >
                                        Remover
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div>
                                        <label class="block mb-1">CEP</label>
                                        <input
                                            type="text"
                                            x-model="endereco.cep"
                                            :name="`enderecos[${index}][cep]`"
                                            class="w-full border rounded px-3 py-2 bg-white"
                                        >
                                    </div>

                                    <div>
                                        <label class="block mb-1">UF</label>
                                        <input
                                            type="text"
                                            x-model="endereco.uf"
                                            :name="`enderecos[${index}][uf]`"
                                            class="w-full border rounded px-3 py-2 bg-white"
                                        >
                                    </div>

                                    <div class="md:col-span-2">
                                        <label class="block mb-1">Rua</label>
                                        <input
                                            type="text"
                                            x-model="endereco.rua"
                                            :name="`enderecos[${index}][rua]`"
                                            class="w-full border rounded px-3 py-2 bg-white"
                                        >
                                    </div>

                                    <div>
                                        <label class="block mb-1">Número</label>
                                        <input
                                            type="text"
                                            x-model="endereco.numero"
                                            :name="`enderecos[${index}][numero]`"
                                            class="w-full border rounded px-3 py-2 bg-white"
                                        >
                                    </div>

                                    <div>
                                        <label class="block mb-1">Complemento</label>
                                        <input
                                            type="text"
                                            x-model="endereco.complemento"
                                            :name="`enderecos[${index}][complemento]`"
                                            class="w-full border rounded px-3 py-2 bg-white"
                                        >
                                    </div>

                                    <div>
                                        <label class="block mb-1">Bairro</label>
                                        <input
                                            type="text"
                                            x-model="endereco.bairro"
                                            :name="`enderecos[${index}][bairro]`"
                                            class="w-full border rounded px-3 py-2 bg-white"
                                        >
                                    </div>

                                    <div>
                                        <label class="block mb-1">Cidade</label>
                                        <input
                                            type="text"
                                            x-model="endereco.cidade"
                                            :name="`enderecos[${index}][cidade]`"
                                            class="w-full border rounded px-3 py-2 bg-white"
                                        >
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="bg-white shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4">
                        Endereços já cadastrados
                    </h3>

                    @if ($user->enderecos->isEmpty())
                        <p class="text-sm text-gray-600">
                            Nenhum endereço cadastrado até o momento.
                        </p>
                    @else
                        <div class="space-y-3">
                            @foreach ($user->enderecos as $endereco)
                                <div class="border rounded p-4">
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        @if ($endereco->is_principal)
                                            <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                                Principal
                                            </span>
                                        @endif

                                        @if ($endereco->ativo)
                                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                                Ativo
                                            </span>
                                        @else
                                            <span class="text-xs bg-red-100 text-red-800 px-2 py-1 rounded">
                                                Inativo
                                            </span>
                                        @endif
                                    </div>

                                    <div class="text-sm text-gray-800">
                                        <p>{{ $endereco->rua }}, {{ $endereco->numero }}</p>

                                        @if ($endereco->complemento)
                                            <p>{{ $endereco->complemento }}</p>
                                        @endif

                                        <p>{{ $endereco->bairro }} - {{ $endereco->cidade }}/{{ $endereco->uf }}</p>
                                        <p>CEP: {{ $endereco->cep }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2 rounded">
                        Salvar cadastro
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>