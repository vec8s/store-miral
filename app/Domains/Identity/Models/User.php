<?php

declare(strict_types=1);

namespace App\Domains\Identity\Models;

use App\Domains\Commerce\Models\Address;
use App\Domains\Commerce\Models\Cart;
use App\Domains\Commerce\Models\Order;
use App\Domains\Identity\Enums\RoleCode;
use App\Domains\Media\Models\Media;
use App\Domains\Reviews\Models\Review;
use App\Domains\Wishlist\Models\Wishlist;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;

class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    protected $table = 'users';

    protected $fillable = [
        'salla_customer_id',
        'auth_provider',
        'auth_provider_id',
        'name',
        'email',
        'phone',
        'password',
        'avatar_url',
        'locale',
        'preferences',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'preferences' => 'array',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withPivot(['assigned_at', 'assigned_by_id']);
    }

    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable');
    }

    public function scopeRole($query, string $code)
    {
        return $query->whereHas('roles', fn ($q) => $q->where('code', $code));
    }

    public function hasRole(RoleCode|string $code): bool
    {
        $needle = $code instanceof RoleCode ? $code->value : $code;

        return $this->relationLoaded('roles')
            ? $this->roles->contains('code', $needle)
            : $this->roles()->where('code', $needle)->exists();
    }

    public function hasAnyRole(array $codes): bool
    {
        $values = array_map(
            fn ($c) => $c instanceof RoleCode ? $c->value : $c,
            $codes,
        );

        return $this->relationLoaded('roles')
            ? $this->roles->whereIn('code', $values)->isNotEmpty()
            : $this->roles()->whereIn('code', $values)->exists();
    }

    public function assignRole(RoleCode|string $code, ?int $assignedById = null): void
    {
        $needle = $code instanceof RoleCode ? $code->value : $code;
        $role = Role::where('code', $needle)->first();
        if ($role) {
            $this->roles()->syncWithoutDetaching([
                $role->id => [
                    'assigned_at' => now(),
                    'assigned_by_id' => $assignedById,
                ],
            ]);
        }
    }
}
