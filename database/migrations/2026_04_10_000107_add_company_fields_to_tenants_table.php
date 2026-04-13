<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->string('cep', 9)->nullable()->after('telefone');
            $table->string('endereco')->nullable()->after('cep');
            $table->string('numero', 20)->nullable()->after('endereco');
            $table->string('complemento')->nullable()->after('numero');
            $table->string('bairro')->nullable()->after('complemento');
            $table->string('cidade')->nullable()->after('bairro');
            $table->string('uf', 2)->nullable()->after('cidade');
        });
    }

    public function down(): void
    {
        Schema::table('tenants', function (Blueprint $table) {
            $table->dropColumn([
                'cep',
                'endereco',
                'numero',
                'complemento',
                'bairro',
                'cidade',
                'uf',
            ]);
        });
    }
};
