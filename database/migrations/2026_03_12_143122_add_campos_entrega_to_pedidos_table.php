<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->string('entrega_cep', 9)->after('valor_total');
            $table->string('entrega_rua')->after('entrega_cep');
            $table->string('entrega_numero', 20)->after('entrega_rua');
            $table->string('entrega_complemento')->nullable()->after('entrega_numero');
            $table->string('entrega_bairro')->after('entrega_complemento');
            $table->string('entrega_cidade')->after('entrega_bairro');
            $table->string('entrega_uf', 2)->after('entrega_cidade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn([
                'entrega_cep',
                'entrega_rua',
                'entrega_numero',
                'entrega_complemento',
                'entrega_bairro',
                'entrega_cidade',
                'entrega_uf',
            ]);
        });
    }
};
