<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Editar Pedido #{{ $pedido->id }}
        </h2>
    </x-slot>

    @php
        // Se voltou de validação, usa old('itens').
        // Senão, monta a lista a partir dos itens do pedido.
        $itensForm = old('itens');

        if (!$itensForm) {
            $itensForm = $pedido->itens
                ->map(fn($item) => [
                    'produto_id' => $item->produto_id,
                    'quantidade' => $item->quantidade,
                ])
                ->toArray();
        }

        // Garante ao menos 1 linha na tela
        if (!is_array($itensForm) || count($itensForm) === 0) {
            $itensForm = [['produto_id' => '', 'quantidade' => 1]];
        }
    @endphp

    <div class="py-6">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            @if (session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form id="form-update" action="{{ route('pedidos.update', $pedido) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label for="cliente_id" class="block mb-1">Cliente</label>
                            <select name="cliente_id" id="cliente_id" class="w-full border rounded px-3 py-2">
                                <option value="">Selecione</option>
                                @foreach($clientes as $cliente)
                                    <option value="{{ $cliente->id }}"
                                        {{ old('cliente_id', $pedido->cliente_id) == $cliente->id ? 'selected' : '' }}>
                                        {{ $cliente->nome }}
                                    </option>
                                @endforeach
                            </select>
                            @error('cliente_id')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="data_pedido" class="block mb-1">Data do Pedido</label>
                            <input type="date"
                                   name="data_pedido"
                                   id="data_pedido"
                                   value="{{ old('data_pedido', $pedido->data_pedido) }}"
                                   class="w-full border rounded px-3 py-2">
                            @error('data_pedido')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div>
                            <label for="status" class="block mb-1">Status</label>
                            <input type="text"
                                   name="status"
                                   id="status"
                                   value="{{ old('status', $pedido->status) }}"
                                   class="w-full border rounded px-3 py-2">
                            @error('status')
                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <div class="flex items-center justify-between gap-3">
                            <h3 class="font-semibold">Itens do Pedido</h3>

                            <button type="button" id="add-item" class="bg-gray-700 text-white px-3 py-2 rounded">
                                Adicionar item
                            </button>
                        </div>

                        @error('itens')
                            <div class="mt-3 text-red-600 text-sm">{{ $message }}</div>
                        @enderror

                        <table class="min-w-full border mt-3">
                            <thead>
                                <tr>
                                    <th class="border px-3 py-2 text-left">Produto</th>
                                    <th class="border px-3 py-2 text-left">Quantidade</th>
                                    <th class="border px-3 py-2 text-left">Ação</th>
                                </tr>
                            </thead>
                            <tbody id="itens-body">
                                @foreach($itensForm as $i => $item)
                                    <tr class="item-row">
                                        <td class="border px-3 py-2">
                                            <select name="itens[{{ $i }}][produto_id]"
                                                    class="w-full border rounded px-3 py-2 item-produto">
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
                                                   name="itens[{{ $i }}][quantidade]"
                                                   min="1"
                                                   step="1"
                                                   class="w-full border rounded px-3 py-2 item-quantidade"
                                                   value="{{ $item['quantidade'] ?? 1 }}">

                                            @error("itens.$i.quantidade")
                                                <div class="text-red-600 text-sm mt-1">{{ $message }}</div>
                                            @enderror
                                        </td>

                                        <td class="border px-3 py-2">
                                            <button type="button" class="text-red-600 remove-item">
                                                Remover
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <p class="text-sm text-gray-600 mt-2">
                            Dica: para salvar, precisa ter pelo menos um item com produto selecionado.
                        </p>
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

                    <div class="mt-6 flex flex-col gap-2 sm:flex-row sm:justify-between">
                        <div class="flex gap-2">
                            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">
                                Salvar alterações
                            </button>

                            <a href="{{ route('pedidos.show', $pedido) }}" class="bg-gray-500 text-white px-4 py-2 rounded">
                                Cancelar
                            </a>
                        </div>

                        <button type="submit"
                                form="delete-pedido"
                                class="bg-red-600 text-white px-4 py-2 rounded"
                                onclick="return confirm('Tem certeza que deseja excluir este pedido?')">
                            Excluir Pedido
                        </button>
                    </div>
                </form>

                <form id="delete-pedido" action="{{ route('pedidos.destroy', $pedido) }}" method="POST" class="hidden">
                    @csrf
                    @method('DELETE')
                </form>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const updateForm = document.getElementById('form-update');
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

            function hasAtLeastOneSelectedProduct() {
                const rows = body.querySelectorAll('.item-row');
                for (const row of rows) {
                    const sel = row.querySelector('.item-produto');
                    if (sel && sel.value && sel.value.trim() !== '') {
                        return true;
                    }
                }
                return false;
            }

            addBtn.addEventListener('click', () => {
                const clone = template.cloneNode(true);
                clone.removeAttribute('id');

                // garante defaults
                const sel = clone.querySelector('.item-produto');
                const qty = clone.querySelector('.item-quantidade');
                if (sel) sel.value = '';
                if (qty) qty.value = 1;

                body.appendChild(clone);
                reindex();
            });

            body.addEventListener('click', (e) => {
                if (!e.target.classList.contains('remove-item')) return;

                const row = e.target.closest('.item-row');
                if (row) row.remove();

                ensureAtLeastOneRow();
                reindex();
            });

            // Antes de enviar, garante nomes certos e pelo menos 1 produto selecionado
            updateForm.addEventListener('submit', (e) => {
                reindex();

                if (!hasAtLeastOneSelectedProduct()) {
                    e.preventDefault();
                    alert('Adicione pelo menos um item com produto selecionado antes de salvar.');
                }
            });

            // Ao carregar
            reindex();
        })();
    </script>
</x-app-layout>