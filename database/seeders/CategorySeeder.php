<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        if (! $tenant) {
            return;
        }

        foreach (['Ferramentas', 'Andaimes', 'Máquinas leves'] as $nome) {
            Category::updateOrCreate(
                ['tenant_id' => $tenant->id, 'nome' => $nome],
                []
            );
        }
    }
}
