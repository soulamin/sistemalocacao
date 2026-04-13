<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rentals')) {
            return;
        }

        Schema::table('rentals', function (Blueprint $table) {
            if (! Schema::hasColumn('rentals', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('rentals', 'empresa_responsavel')) {
                $table->string('empresa_responsavel')->nullable()->after('client_id');
            }

            if (! Schema::hasColumn('rentals', 'recibo_codigo')) {
                $table->string('recibo_codigo')->nullable()->after('empresa_responsavel');
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('rentals')) {
            return;
        }

        Schema::table('rentals', function (Blueprint $table) {
            if (Schema::hasColumn('rentals', 'recibo_codigo')) {
                $table->dropColumn('recibo_codigo');
            }

            if (Schema::hasColumn('rentals', 'empresa_responsavel')) {
                $table->dropColumn('empresa_responsavel');
            }

            if (Schema::hasColumn('rentals', 'tenant_id')) {
                $table->dropConstrainedForeignId('tenant_id');
            }
        });
    }
};
