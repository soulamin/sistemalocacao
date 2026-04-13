<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('rental_items')) {
            return;
        }

        Schema::table('rental_items', function (Blueprint $table) {
            if (Schema::hasColumn('rental_items', 'equipment_id')) {
                if ($this->indexExists('rental_items', 'rental_items_rental_id_equipment_id_unique')) {
                    $table->dropUnique('rental_items_rental_id_equipment_id_unique');
                }

                if ($this->foreignKeyExists('rental_items', 'rental_items_equipment_id_foreign')) {
                    $table->dropForeign('rental_items_equipment_id_foreign');
                }
            }
        });

        if (Schema::hasColumn('rental_items', 'equipment_id') && ! Schema::hasColumn('rental_items', 'product_id')) {
            Schema::table('rental_items', function (Blueprint $table) {
                $table->renameColumn('equipment_id', 'product_id');
            });
        }

        Schema::table('rental_items', function (Blueprint $table) {
            if (Schema::hasColumn('rental_items', 'product_id')) {
                if (! $this->foreignKeyExists('rental_items', 'rental_items_product_id_foreign')) {
                    $table->foreign('product_id')->references('id')->on('products')->restrictOnDelete();
                }

                if (! Schema::hasColumn('rental_items', 'tenant_id')) {
                    $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
                }

                if (! $this->indexExists('rental_items', 'rental_items_rental_id_product_id_unique')) {
                    $table->unique(['rental_id', 'product_id']);
                }
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('rental_items')) {
            return;
        }

        Schema::table('rental_items', function (Blueprint $table) {
            if (Schema::hasColumn('rental_items', 'product_id')) {
                if ($this->indexExists('rental_items', 'rental_items_rental_id_product_id_unique')) {
                    $table->dropUnique('rental_items_rental_id_product_id_unique');
                }

                if ($this->foreignKeyExists('rental_items', 'rental_items_product_id_foreign')) {
                    $table->dropForeign('rental_items_product_id_foreign');
                }
            }

            if (Schema::hasColumn('rental_items', 'tenant_id')) {
                $table->dropConstrainedForeignId('tenant_id');
            }
        });

        if (Schema::hasColumn('rental_items', 'product_id') && ! Schema::hasColumn('rental_items', 'equipment_id')) {
            Schema::table('rental_items', function (Blueprint $table) {
                $table->renameColumn('product_id', 'equipment_id');
            });
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select('show index from `'.$table.'` where Key_name = ?', [$index]);

        return $indexes !== [];
    }

    private function foreignKeyExists(string $table, string $foreignKey): bool
    {
        $foreignKeys = DB::select(
            'select constraint_name from information_schema.table_constraints where table_schema = database() and table_name = ? and constraint_type = ? and constraint_name = ?',
            [$table, 'FOREIGN KEY', $foreignKey]
        );

        return $foreignKeys !== [];
    }
};
