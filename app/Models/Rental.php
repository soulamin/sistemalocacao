<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rental extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'client_id',
        'empresa_responsavel',
        'recibo_codigo',
        'data_inicio',
        'data_fim',
        'valor_total',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio' => 'date',
            'data_fim' => 'date',
            'valor_total' => 'decimal:2',
        ];
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'rental_items', 'rental_id', 'product_id')
            ->withPivot(['tenant_id', 'valor_diaria'])
            ->withTimestamps();
    }

    public function equipments(): BelongsToMany
    {
        return $this->products();
    }
}
