<?php

declare(strict_types=1);

namespace App\Domains\Wishlist\Models;

use App\Domains\Identity\Models\User;
use App\Domains\Wishlist\Enums\WishlistVisibility;
use App\Shared\Models\BaseModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Wishlist extends BaseModel
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'name',
        'slug',
        'visibility',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'visibility' => WishlistVisibility::class,
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(WishlistItem::class);
    }
}
