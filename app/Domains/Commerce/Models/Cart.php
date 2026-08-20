<?php

declare(strict_types=1);

namespace App\Domains\Commerce\Models;

use App\Domains\Identity\Models\User;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'session_id',
        'version',
        'currency',
        'meta',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'version' => 'integer',
            'meta' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
