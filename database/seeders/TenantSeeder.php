<?php

namespace Database\Seeders;

use App\Models\Tenant;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        Tenant::updateOrCreate(
            ['slug' => 'locadora-matriz'],
            [
                'nome' => 'Locadora Matriz',
                'documento' => '12.345.678/0001-90',
                'telefone' => '(11) 4000-1000',
            ]
        );
    }
}
