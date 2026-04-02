<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pedido extends Model
{
    protected $fillable = [
    'user_id',
    'data_pedido',
    'status',
    'valor_total',
    'entrega_cep',
    'entrega_rua',
    'entrega_numero',
    'entrega_complemento',
    'entrega_bairro',
    'entrega_cidade',
    'entrega_uf'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function itens(): HasMany
    {
        return $this->hasMany(ItemPedido::class);
    }
}
