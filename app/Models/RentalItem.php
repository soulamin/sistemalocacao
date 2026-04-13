<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentalItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenant_id',
        'rental_id',
        'product_id',
        'valor_diaria',
    ];

    protected function casts(): array
    {
        return [
            'valor_diaria' => 'decimal:2',
        ];
    }

    public function rental(): BelongsTo
    {
        return $this->belongsTo(Rental::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function equipment(): BelongsTo
    {
        return $this->product();
    }
}
