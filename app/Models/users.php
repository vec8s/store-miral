<?php

declare(strict_types=1);

namespace App\Models;

use App\Domains\Identity\Enums\UserStatus as EnumsUserStatus;
use App\Enums\UserStatus;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\Identity\Models\User as IdentityUser;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Carbon\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $phone
 * @property string|null $avatar
 * @property string|null $salla_customer_id
 * @property UserStatus $status
 * @property \Carbon\Carbon|null $last_login_at
 * @property string|null $remember_token
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 */
final class User extends IdentityUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory;

    use Notifiable;
    use SoftDeletes;

    protected $table = 'users';

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'avatar',
        'salla_customer_id',
        'status',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'status' => EnumsUserStatus::class,
        'last_login_at' => 'datetime',
    ];

    public function wishlists(): HasMany
    {
        return $this->hasMany(\App\Domains\Wishlist\Models\Wishlist::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(\App\Domains\Reviews\Models\Review::class);
    }

    public function isActive(): bool
    {
        return $this->status === UserStatus::ACTIVE;
    }

    public function scopeActive($query)
    {
        return $query->where('status', UserStatus::ACTIVE);
    }
}
