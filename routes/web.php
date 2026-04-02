<?php

use App\Http\Controllers\PedidoController;
use App\Http\Controllers\EnderecoController;
use App\Http\Controllers\ProdutoController;
use App\Http\Controllers\ProfileCompletionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ForcePasswordChangeController;
use App\Http\Controllers\ClienteManagementController;
use App\Http\Controllers\ClienteEnderecoManagementController;
use App\Http\Controllers\ClientePedidoManagementController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\EquipeManagementController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'active'])->group(function () {
    Route::get('/trocar-senha-obrigatoria', [ForcePasswordChangeController::class, 'edit'])
        ->name('password.force.edit');

    Route::patch('/trocar-senha-obrigatoria', [ForcePasswordChangeController::class, 'update'])
        ->name('password.force.update');
});

Route::middleware(['auth', 'active', 'password.changed'])->scopeBindings()->group(function () {
        Route::get('/completar-cadastro', [ProfileCompletionController::class, 'edit'])
            ->name('profile.complete');

        Route::post('/completar-cadastro', [ProfileCompletionController::class, 'update'])
            ->name('profile.complete.store');
    });

Route::middleware(['auth', 'active', 'profile.complete', 'password.changed'])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::singleton('profile', ProfileController::class)->only(['edit', 'update']);

    Route::resource('pedidos', PedidoController::class)->except(['create', 'store', 'destroy']);

    Route::patch('pedidos/{pedido}/cancelar', [PedidoController::class, 'cancel'])
        ->name('pedidos.cancel');
           
    Route::patch('pedidos/{pedido}/finalizar', [PedidoController::class, 'finalize'])
        ->name('pedidos.finalize');

    Route::get('/comprar', [CompraController::class, 'index'])
        ->name('compras.index');

    Route::post('/comprar/checkout', [CompraController::class, 'prepareCheckout'])
        ->name('compras.prepare-checkout');

    Route::get('/comprar/checkout', [CompraController::class, 'checkout'])
        ->name('compras.checkout');

    Route::post('/comprar', [CompraController::class, 'store'])
        ->name('compras.store');

    Route::resource('enderecos', EnderecoController::class)->only(['index', 'store', 'update']);

    Route::patch('enderecos/{endereco}/ativo', [EnderecoController::class, 'toggleAtivo'])
        ->name('enderecos.toggle-ativo');

    Route::patch('enderecos/{endereco}/principal', [EnderecoController::class, 'setPrincipal'])
        ->name('enderecos.principal');

    Route::middleware('role:atendente,admin')->group(function () {

        Route::resource('produtos', ProdutoController::class)->except(['show', 'destroy']);

        Route::patch('produtos/{produto}/toggle-ativo', [ProdutoController::class, 'toggleAtivo'])
            ->name('produtos.toggle-ativo');

        Route::resource('clientes', ClienteManagementController::class)
            ->parameters(['clientes' => 'usuario'])
            ->except(['show', 'destroy']);

        Route::resource('equipe', EquipeManagementController::class)
        ->parameters(['equipe' => 'usuario'])
        ->except(['show', 'destroy']);

        Route::prefix('clientes/{usuario}')->name('clientes.')->group(function () {

                Route::resource('enderecos', ClienteEnderecoManagementController::class)->only(['index', 'store', 'update']);

                Route::patch('enderecos/{endereco}/toggle-ativo', [ClienteEnderecoManagementController::class, 'toggleAtivo'])
                    ->name('enderecos.toggle-ativo');

                Route::patch('enderecos/{endereco}/principal', [ClienteEnderecoManagementController::class, 'setPrincipal'])
                    ->name('enderecos.set-principal');

                Route::resource('pedidos', ClientePedidoManagementController::class)->except(['edit', 'update', 'destroy']);

                Route::patch('pedidos/{pedido}/finalizar', [ClientePedidoManagementController::class, 'finalize'])
                    ->name('pedidos.finalize');

                Route::patch('pedidos/{pedido}/cancelar', [ClientePedidoManagementController::class, 'cancel'])
                    ->name('pedidos.cancel');
        });
    });
});

require __DIR__.'/auth.php';