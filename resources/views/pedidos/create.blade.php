<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Novo Pedido
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form action="{{ route('pedidos.store') }}" method="POST">
                    @csrf

                    <div class="mb-4">
                        <label for="user_id" class="block mb-1">Cliente</label>
                        <select name="user_id" id="user_id" class="w-full border rounded px-3 py-2">
                            <option value="">Selecione</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('user_id')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="data_pedido" class="block mb-1">Data do Pedido</label>
                        <input type="date"
                               name="data_pedido"
                               id="data_pedido"
                               value="{{ old('data_pedido') }}"
                               class="w-full border rounded px-3 py-2">
                        @error('data_pedido')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="status" class="block mb-1">Status</label>
                        <input type="text"
                               name="status"
                               id="status"
                               value="{{ old('status') }}"
                               class="w-full border rounded px-3 py-2">
                        @error('status')
                            <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <h3 class="font-semibold mb-3">Itens do Pedido</h3>

                        @error('itens')
                            <div class="mb-3 text-red-600 text-sm">{{ $message }}</div>
                        @enderror

                        @php
                            $itensOld = old('itens', [['produto_id' => '', 'quantidade' => 1]]);
                        @endphp

                        <table class="min-w-full border" id="itens-table">
                            <thead>
                                <tr>
                                    <th class="border px-3 py-2 text-left">Produto</th>
                                    <th class="border px-3 py-2 text-left">Quantidade</th>
                                    <th class="border px-3 py-2 text-left">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="itens-body">
                                @foreach($itensOld as $i => $item)
                                    <tr class="item-row">
                                        <td class="border px-3 py-2">
                                            <select class="w-full border rounded px-3 py-2 item-produto">
                                                <option value="">Selecione</option>
                                                @foreach($produtos as $produto)
                                                    <option value="{{ $produto->id }}"
                                                        {{ (string)($item['produto_id'] ?? '') === (string)$produto->id ? 'selected' : '' }}>
                                                        {{ $produto->nome }}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error("itens.$i.produto_id")
                                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        <td class="border px-3 py-2">
                                            <input type="number"
                                                min="1"
                                                step="1"
                                                class="w-full border rounded px-3 py-2 item-quantidade"
                                                value="{{ $item['quantidade'] ?? 1 }}">

                                            @error("itens.$i.quantidade")
                                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        <td class="border px-3 py-2">
                                            <button type="button" class="text-red-600 remove-item">Remover</button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="mt-3">
                            <button type="button" id="add-item" class="bg-gray-700 text-white px-3 py-2 rounded">
                                Adicionar item
                            </button>
                        </div>
                    </div>

                    <table class="hidden">
                        <tbody>
                            <tr id="item-template" class="item-row">
                                <td class="border px-3 py-2">
                                    <select class="w-full border rounded px-3 py-2 item-produto">
                                        <option value="">Selecione</option>
                                        @foreach($produtos as $produto)
                                            <option value="{{ $produto->id }}">{{ $produto->nome }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="border px-3 py-2">
                                    <input type="number" min="1" step="1" value="1"
                                        class="w-full border rounded px-3 py-2 item-quantidade">
                                </td>
                                <td class="border px-3 py-2">
                                    <button type="button" class="text-red-600 remove-item">Remover</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <script>
                    (function () {
                        const body = document.getElementById('itens-body');
                        const addBtn = document.getElementById('add-item');
                        const template = document.getElementById('item-template');

                        function reindex() {
                            const rows = body.querySelectorAll('.item-row');
                            rows.forEach((row, i) => {
                                const sel = row.querySelector('.item-produto');
                                const qty = row.querySelector('.item-quantidade');

                                sel.name = `itens[${i}][produto_id]`;
                                qty.name = `itens[${i}][quantidade]`;
                            });
                        }

                        function ensureAtLeastOneRow() {
                            const rows = body.querySelectorAll('.item-row');
                            if (rows.length === 0) {
                                const clone = template.cloneNode(true);
                                clone.removeAttribute('id');
                                body.appendChild(clone);
                            }
                        }

                        addBtn.addEventListener('click', () => {
                            const clone = template.cloneNode(true);
                            clone.removeAttribute('id');
                            body.appendChild(clone);
                            reindex();
                        });

                        body.addEventListener('click', (e) => {
                            if (!e.target.classList.contains('remove-item')) return;

                            const row = e.target.closest('.item-row');
                            row.remove();

                            ensureAtLeastOneRow();
                            reindex();
                        });

                        reindex();
                    })();
                    </script>

                    <div class="flex gap-2 mt-4">
                        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                            Salvar
                        </button>

                        <a href="{{ route('pedidos.index') }}"
                           class="bg-gray-500 text-white px-4 py-2 rounded">
                            Cancelar
                        </a>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>