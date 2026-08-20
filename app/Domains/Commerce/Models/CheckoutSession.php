<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Models;

use App\Domains\Identity\Models\User;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckoutSession extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'salla_connection_id',
        'uuid',
        'user_id',
        'cart_id',
        'version',
        'idempotency_key',
        'salla_cart_id',
        'checkout_url',
        'amount_snapshot',
        'currency',
        'cart_version',
        'status',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'cart_version' => 'integer',
            'amount_snapshot' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
