<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('clients')) {
            return;
        }

        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasColumn('clients', 'cep')) {
                $table->string('cep', 9)->nullable()->after('documento');
            }

            if (! Schema::hasColumn('clients', 'endereco')) {
                $table->string('endereco')->nullable()->after('cep');
            }

            if (! Schema::hasColumn('clients', 'numero')) {
                $table->string('numero', 20)->nullable()->after('endereco');
            }

            if (! Schema::hasColumn('clients', 'complemento')) {
                $table->string('complemento', 120)->nullable()->after('numero');
            }

            if (! Schema::hasColumn('clients', 'bairro')) {
                $table->string('bairro', 120)->nullable()->after('complemento');
            }

            if (! Schema::hasColumn('clients', 'uf')) {
                $table->string('uf', 2)->nullable()->after('bairro');
            }

            if (! Schema::hasColumn('clients', 'cidade')) {
                $table->string('cidade', 120)->nullable()->after('uf');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('clients')) {
            return;
        }

        Schema::table('clients', function (Blueprint $table) {
            $columns = ['cidade', 'uf', 'bairro', 'complemento', 'numero', 'endereco', 'cep'];

            foreach ($columns as $column) {
                if (Schema::hasColumn('clients', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
