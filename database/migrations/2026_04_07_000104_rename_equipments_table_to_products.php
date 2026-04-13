<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('equipments') && ! Schema::hasTable('products')) {
            Schema::rename('equipments', 'products');
        }

        if (! Schema::hasTable('products')) {
            Schema::create('products', function (Blueprint $table) {
                $table->id();
                $table->foreignId('tenant_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
                $table->string('cod_produto')->nullable();
                $table->string('nome');
                $table->string('marca')->nullable();
                $table->text('descricao')->nullable();
                $table->decimal('valor_diaria', 10, 2);
                $table->enum('status', ['disponivel', 'locado'])->default('disponivel');
                $table->timestamps();
            });
        }

        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'tenant_id')) {
                $table->foreignId('tenant_id')->nullable()->after('id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('products', 'category_id')) {
                $table->foreignId('category_id')->nullable()->after('tenant_id')->constrained()->nullOnDelete();
            }

            if (! Schema::hasColumn('products', 'cod_produto')) {
                $table->string('cod_produto')->nullable()->after('category_id');
            }

            if (! Schema::hasColumn('products', 'marca')) {
                $table->string('marca')->nullable()->after('nome');
            }
        });

        DB::table('products')
            ->whereNull('cod_produto')
            ->orderBy('id')
            ->get()
            ->each(function ($product): void {
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['cod_produto' => 'PRD-' . str_pad((string) $product->id, 5, '0', STR_PAD_LEFT)]);
            });

        Schema::table('products', function (Blueprint $table) {
            if (! $this->indexExists('products', 'products_tenant_id_cod_produto_unique')) {
                $table->unique(['tenant_id', 'cod_produto']);
            }
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('products')) {
            return;
        }

        Schema::table('products', function (Blueprint $table) {
            if ($this->indexExists('products', 'products_tenant_id_cod_produto_unique')) {
                $table->dropUnique('products_tenant_id_cod_produto_unique');
            }
        });

        if (! Schema::hasTable('equipments')) {
            Schema::rename('products', 'equipments');
        }
    }

    private function indexExists(string $table, string $index): bool
    {
        $indexes = DB::select('show index from `' . $table . '` where Key_name = ?', [$index]);

        return $indexes !== [];
    }
};
