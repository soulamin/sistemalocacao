<?php

namespace App\Models;

use App\Models\Concerns\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Product extends Model
{
    use BelongsToTenant;
    use HasFactory;

    protected $table = 'products';

    protected $fillable = [
        'tenant_id',
        'category_id',
        'cod_produto',
        'nome',
        'marca',
        'descricao',
        'valor_diaria',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'valor_diaria' => 'decimal:2',
        ];
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function rentals(): BelongsToMany
    {
        return $this->belongsToMany(Rental::class, 'rental_items', 'product_id', 'rental_id')
            ->withPivot(['tenant_id', 'valor_diaria'])
            ->withTimestamps();
    }

    public function hasActiveRental(): bool
    {
        return $this->rentals()->where('status', 'ativa')->exists();
    }
}
