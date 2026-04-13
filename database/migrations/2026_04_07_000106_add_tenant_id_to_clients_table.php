<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('clients')) {
            return;
        }

        Schema::table('clients', function (Blueprint $table) {
            if (! Schema::hasColumn('clients', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropUnique('clients_documento_unique');
        });

        if (! $this->indexExists('clients', 'clients_tenant_id_documento_unique')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->unique(['tenant_id', 'documento']);
            });
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('clients')) {
            return;
        }

        if ($this->indexExists('clients', 'clients_tenant_id_documento_unique')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->dropUnique('clients_tenant_id_documento_unique');
            });
        }

        Schema::table('clients', function (Blueprint $table) {
            if (Schema::hasColumn('clients', 'tenant_id')) {
                $table->dropConstrainedForeignId('tenant_id');
            }
        });

        if (! $this->indexExists('clients', 'clients_documento_unique')) {
            Schema::table('clients', function (Blueprint $table) {
                $table->unique('documento');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select('show index from `' . $table . '` where Key_name = ?', [$index]);

        return $indexes !== [];
    }
};
