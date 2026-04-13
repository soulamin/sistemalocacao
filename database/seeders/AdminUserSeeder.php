<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $tenant = Tenant::first();

        User::updateOrCreate(
            ['email' => 'admin@locacao.com'],
            [
                'tenant_id' => $tenant?->id,
                'name' => 'Administrador',
                'password' => Hash::make('12345678'),
            ]
        );
    }
}
